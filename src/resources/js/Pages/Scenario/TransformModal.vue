<template>
  <div class="transform-modal">
    <form @submit.prevent="submit">
      <div>
        <label>Nombre</label>
        <input v-model="form.name" required />
      </div>
      <div>
        <label>Descripción</label>
        <textarea v-model="form.description"></textarea>
      </div>
      <div>
        <button type="submit">Transformar</button>
        <button type="button" @click="$emit('close')">Cancelar</button>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive } from 'vue';
import axios from 'axios';

export default defineComponent({
  props: {
    competencyId: { type: Number, required: true }
  },
  setup(props, { emit }) {
    const form = reactive({ name: '', description: '' });

    async function submit() {
      try {
        const res = await axios.post(`/api/competencies/${props.competencyId}/transform`, form);
        emit('transformed', res.data.data);
      } catch (err) {
        console.error(err);
        alert('Error al transformar');
      }
    }

    return { form, submit };
  }
});
</script>

<style scoped>
.transform-modal { padding: 1rem; }
label { display:block; margin-top:0.5rem }
input,textarea { width:100% }
</style>
