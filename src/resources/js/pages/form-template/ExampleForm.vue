<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from './FormSchema.vue';

defineOptions({ layout: AppLayout });

interface FormField {
  key: string;
  label: string;
  type: 'text' | 'email' | 'number' | 'password' | 'select' | 'checkbox' | 'textarea';
  rules?: ((v: any) => boolean | string)[];
  placeholder?: string;
  items?: any[];
}

interface TableHeader {
  title: string;
  key: string;
  sortable?: boolean;
  align?: 'start' | 'center' | 'end';
}

interface FormConfig {
  fields: FormField[];
}

interface TableConfig {
  headers: TableHeader[];
}

interface ItemForm {
  fields: FormField[];
}

const config: FormConfig = {
  fields: [
    {
      key: 'name',
      label: 'Name',
      type: 'text',
      placeholder: 'Enter full name',
      rules: [
        (v: string) => !!v || 'Name is required',
        (v: string) => (v && v.length >= 3) || 'Name must be at least 3 characters',
      ],
    },
    {
      key: 'email',
      label: 'Email',
      type: 'email',
      placeholder: 'Enter email address',
      rules: [
        (v: string) => !!v || 'Email is required',
        (v: string) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v || '') || 'Email is invalid',
      ],
    },
  ],
};

const tableConfig: TableConfig = {
  headers: [
    { title: 'Name', key: 'name', sortable: true },
    { title: 'Email', key: 'email', sortable: true },
  ],
};

const itemForm: ItemForm = {
  fields: [],
};
</script>

<template>
  <v-container fluid>
    <v-sheet>
      <FormSchema :config="config" :table-config="tableConfig" :item-form="itemForm" />
    </v-sheet>
  </v-container>
</template>

<style scoped>
/* Custom styles */
</style>
