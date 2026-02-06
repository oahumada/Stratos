<template>
  <div class="changeset-modal">
    <h3>{{ title }}</h3>
    <pre v-if="preview">{{ JSON.stringify(preview, null, 2) }}</pre>
    <div v-else>No preview available</div>
    <div class="actions">
      <button @click="$emit('close')">Cerrar</button>
      <button @click="apply" :disabled="loading">Aplicar</button>
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

    const loadPreview = async () => {
      loading.value = true;
      try {
        const res = await store.previewChangeSet(props.id);
        preview.value = res.preview ?? res.data ?? res;
      } finally {
        loading.value = false;
      }
    };

    const apply = async () => {
      loading.value = true;
      try {
        await store.applyChangeSet(props.id);
        // emit event or refresh
        window.location.reload();
      } finally {
        loading.value = false;
      }
    };

    loadPreview();

    return { preview, loading, apply };
  },
});
</script>

<style scoped>
.changeset-modal { padding: 1rem; }
.actions { margin-top: 1rem; }
</style>
