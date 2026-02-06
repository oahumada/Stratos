<template>
  <div class="changeset-modal" role="dialog" aria-modal="true" :aria-labelledby="`changeset-title-${id}`" tabindex="-1" ref="modalRef">
    <h3 :id="`changeset-title-${id}`">{{ title }}</h3>
    <div v-if="preview && preview.ops && preview.ops.length">
      <p><strong>Operaciones:</strong> {{ preview.ops.length }}</p>
      <ol>
        <li v-for="(op, i) in preview.ops" :key="i" :class="opClass(op.type)">
          <div class="op-header">
            <div>
              <span class="op-badge">{{ opIcon(op.type) }}</span>
              <strong>{{ i + 1 }}.</strong>
              <em class="op-type">{{ op.type }}</em>
            </div>
            <div class="op-actions">
              <button type="button" @click="toggle(i)" :aria-expanded="!collapsed[i]" :aria-controls="`op-details-${i}`">{{ collapsed[i] ? 'Mostrar' : 'Ocultar' }}</button>
              <button type="button" @click="ignoreOp(i)" :aria-label="`Ignorar operación ${i + 1}: ${op.type}`">Ignorar</button>
              <button type="button" @click="revertOp(i)" :aria-label="preview.ops[i] && preview.ops[i]._reverted ? `Deshacer revertir operación ${i + 1}` : `Revertir operación ${i + 1}`">{{ preview.ops[i] && preview.ops[i]._reverted ? 'Deshacer Revertir' : 'Revertir' }}</button>
            </div>
          </div>
          <transition name="fade">
            <div v-show="!collapsed[i]" class="op-details" :id="`op-details-${i}`">
              <pre>{{ formatOp(op) }}</pre>
              <div class="op-row-actions">
                <button type="button" @click="copyOp(i)" :aria-label="`Copiar operación ${i + 1}`">Copiar op</button>
                <span v-if="op._reverted" class="op-reverted">Revertida</span>
              </div>
            </div>
          </transition>
        </li>
      </ol>
    </div>
    <div v-else>
      <pre v-if="preview">{{ JSON.stringify(preview, null, 2) }}</pre>
      <div v-else>No preview available</div>
    </div>
    <div class="actions">
      <button @click="$emit('close')">Cerrar</button>
      <button @click="apply" :disabled="loading || !canApply">Aplicar</button>
      <button v-if="canApply" @click="approve" :disabled="loading">Aprobar</button>
      <button v-if="canApply" @click="reject" :disabled="loading">Rechazar</button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { useChangeSetStore } from '@/stores/changeSetStore';

export default defineComponent({
  props: {
    id: { type: Number, required: true },
    title: { type: String, default: 'ChangeSet' },
  },
  setup(props, { emit }) {
    const store = useChangeSetStore();
    const preview = ref<any>(null);
    const loading = ref(false);
    const canApply = ref(false);

    const collapsed = ref<boolean[]>([]);
    const ignored = ref<Record<number, boolean>>({});
    const modalRef = ref<HTMLElement | null>(null);

    const onKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape' || e.key === 'Esc') {
        emit('close');
      }
    };

    const loadPreview = async () => {
      loading.value = true;
      try {
        const res = await store.previewChangeSet(props.id);
        preview.value = res.preview ?? res.data ?? res;
        // initialize collapsed state
        const ops = preview.value?.ops ?? [];
        collapsed.value = ops.map(() => false);
        try {
          const pem = await store.canApplyChangeSet(props.id);
          canApply.value = pem?.can_apply ?? pem?.data?.can_apply ?? false;
        } catch (e) {
          canApply.value = false;
        }
      } finally {
        loading.value = false;
      }
    };

    const fmt = (v: any) => {
      try {
        return JSON.stringify(v, null, 2);
      } catch (e) {
        return String(v);
      }
    };

    const formatOp = (op: any) => {
      const clone = { ...op };
      // Hide large nested fields when possible
      if (clone.payload && typeof clone.payload === 'object') {
        clone.payload = clone.payload;
      }
      return fmt(clone);
    };

    const revertOp = (i: number) => {
      if (!preview.value || !Array.isArray(preview.value.ops)) return;
      const op = preview.value.ops[i];
      if (!op) return;
      // toggle reverted flag in-place for UI only
      op._reverted = !op._reverted;
      // keep reverted ops hidden from apply list by marking them as ignored in the preview view
      if (op._reverted) {
        ignored.value[i] = true;
        preview.value.ops = preview.value.ops.map((o: any, idx: number) => (ignored.value[idx] ? { ...o, _ignored: true } : o)).filter((o: any) => !o._ignored);
        // ensure collapsed indexes align
        collapsed.value = preview.value.ops.map(() => false);
      } else {
        // undo revert: reload preview to ensure original op is back (best-effort)
        ignored.value = {};
        loadPreview();
      }
    };

    const toggle = (i: number) => {
      collapsed.value[i] = !collapsed.value[i];
    };

    const ignoreOp = (i: number) => {
      ignored.value[i] = true;
      // remove from preview view without mutating original
      if (preview.value && Array.isArray(preview.value.ops)) {
        preview.value.ops = preview.value.ops.map((op: any, idx: number) => (ignored.value[idx] ? { ...op, _ignored: true } : op)).filter((o: any) => !o._ignored);
      }
    };

    const copyOp = (i: number) => {
      const op = preview.value?.ops?.[i];
      if (!op) return;
      const text = JSON.stringify(op, null, 2);
      navigator.clipboard?.writeText(text).catch(() => {});
    };

    const opClass = (type: string) => {
      if (!type) return 'op-default';
      if (type.startsWith('create')) return 'op-create';
      if (type.startsWith('update')) return 'op-update';
      if (type.startsWith('delete') || type.includes('sunset')) return 'op-delete';
      return 'op-default';
    };

    const opIcon = (type: string) => {
      if (!type) return '⚙️';
      if (type.startsWith('create')) return '➕';
      if (type.startsWith('update')) return '✏️';
      if (type.startsWith('delete') || type.includes('sunset')) return '🗑️';
      return '⚙️';
    };

    const apply = async () => {
      loading.value = true;
      try {
        // send ignored indexes to backend so apply can skip reverted/ignored ops
        const ignoredIndexes = Object.keys(ignored.value).filter((k) => ignored.value[Number(k)]).map((k) => Number(k));
        const payload = ignoredIndexes.length ? { ignored_indexes: ignoredIndexes } : undefined;
        await store.applyChangeSet(props.id, payload);
        window.location.reload();
      } finally {
        loading.value = false;
      }
    };

    const approve = async () => {
      loading.value = true;
      try {
        await store.approveChangeSet(props.id);
        window.location.reload();
      } finally {
        loading.value = false;
      }
    };

    const reject = async () => {
      loading.value = true;
      try {
        await store.rejectChangeSet(props.id);
        window.location.reload();
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      nextTick(() => {
        try {
          modalRef.value?.focus();
        } catch (e) {
          // ignore
        }
        window.addEventListener('keydown', onKeyDown);
      });
    });

    onBeforeUnmount(() => {
      window.removeEventListener('keydown', onKeyDown);
    });

    loadPreview();

    return { preview, loading, apply, canApply, approve, reject, formatOp, collapsed, toggle, ignoreOp, copyOp, opClass, opIcon, revertOp, ignored, modalRef };
  },
});
</script>

<style scoped>
.changeset-modal { padding: 1rem; }
.actions { margin-top: 1rem; }
</style>

<style scoped>
.op-create { background: #e6ffed; border-left: 4px solid #2ecc71; padding: 0.5rem; margin-bottom: 0.5rem; }
.op-update { background: #fffbe6; border-left: 4px solid #f1c40f; padding: 0.5rem; margin-bottom: 0.5rem; }
.op-delete { background: #ffecec; border-left: 4px solid #e74c3c; padding: 0.5rem; margin-bottom: 0.5rem; }
.op-default { background: #f4f6f8; border-left: 4px solid #95a5a6; padding: 0.5rem; margin-bottom: 0.5rem; }
.op-badge { margin-right: 0.5rem; }
.op-type { margin-left: 0.5rem; color: #2c3e50; }
.op-details { margin-top: 0.25rem; }
</style>
