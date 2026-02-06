<template>
  <div class="changeset-modal">
    <h3>{{ title }}</h3>
    <pre v-if="preview">{{ JSON.stringify(preview, null, 2) }}</pre>
    <div v-else>No preview available</div>
    <div class="actions">
      <button @click="$emit('close')">Cerrar</button>
      <button @click="apply" :disabled="loading || !canApply">Aplicar</button>
      <button v-if="canApply" @click="approve" :disabled="loading">Aprobar</button>
      <button v-if="canApply" @click="reject" :disabled="loading">Rechazar</button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { useChangeSetStore } from '@/stores/changeSetStore';

export default defineComponent({
  props: {
    id: { type: Number, required: true },
    title: { type: String, default: 'ChangeSet' },
  },
  setup(props) {
    const store = useChangeSetStore();
    const preview = ref<any>(null);
    const loading = ref(false);
    const canApply = ref(false);

    const loadPreview = async () => {
      loading.value = true;
      try {
        const res = await store.previewChangeSet(props.id);
        preview.value = res.preview ?? res.data ?? res;
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

    const apply = async () => {
      loading.value = true;
      try {
        await store.applyChangeSet(props.id);
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

    loadPreview();

    return { preview, loading, apply, canApply, approve, reject };
  },
});
</script>

<style scoped>
.changeset-modal { padding: 1rem; }
.actions { margin-top: 1rem; }
</style>
