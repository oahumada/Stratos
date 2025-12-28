<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '../form-template/FormSchema.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

defineOptions({ layout: AppLayout });

interface FormField {
  key: string;
  label: string;
  type: 'text' | 'email' | 'number' | 'password' | 'select' | 'checkbox' | 'textarea' | 'date' | 'time' | 'switch';
  rules?: ((v: any) => boolean | string)[];
  placeholder?: string;
  items?: any[];
}

interface TableHeader {
  text: string;
  value: string;
  type?: 'date' | 'text' | 'number';
  sortable?: boolean;
  filterable?: boolean;
}

interface Config {
  endpoints: {
    index: string;
    apiUrl: string;
  };
  titulo: string;
  descripcion?: string;
  permisos?: {
    crear: boolean;
    editar: boolean;
    eliminar: boolean;
  };
}

interface TableConfig {
  headers: TableHeader[];
  options?: Record<string, any>;
}

interface ItemForm {
  fields: FormField[];
  catalogs?: string[];
  layout?: string;
}

interface FilterConfig {
  field: string;
  type: 'text' | 'select' | 'date';
  label: string;
  items?: any[];
  placeholder?: string;
}

interface Role {
  id: number;
  name: string;
}

interface Person {
  id: number;
  name: string;
  email: string;
  department: string;
  role_id?: number;
}

// State
const roles = ref<Role[]>([]);

const config: Config = {
  endpoints: {
    index: '/api/people',
    apiUrl: '/api/people',
  },
  titulo: 'People Management',
  descripcion: 'Manage employees and their skills',
  permisos: {
    crear: true,
    editar: true,
    eliminar: true,
  },
};

const tableConfig: TableConfig = {
  headers: [
    { text: 'Name', value: 'name', sortable: true },
    { text: 'Email', value: 'email', sortable: true },
    { text: 'Department', value: 'department', sortable: true },
    { text: 'Role', value: 'role', sortable: false },
    { text: 'Skills', value: 'skills_count', sortable: true },
    { text: 'Hired', value: 'hired_at', type: 'date', sortable: true },
    { text: 'Actions', value: 'actions', sortable: false },
  ],
};

const itemForm: ItemForm = {
  fields: [
    {
      key: 'name',
      label: 'Full Name',
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
    {
      key: 'department',
      label: 'Department',
      type: 'text',
      placeholder: 'Enter department',
      rules: [(v: string) => !!v || 'Department is required'],
    },
    {
      key: 'role_id',
      label: 'Role',
      type: 'select',
      placeholder: 'Select a role',
      rules: [],
    },
    {
      key: 'hired_at',
      label: 'Hired Date',
      type: 'date',
      rules: [],
    },
  ],
  catalogs: ['role'],
};

// Computed for departments (from people data)
const departments = computed(() => {
  // This will be computed dynamically from the data
  return [];
});

// Filters configuration
const filters = computed<FilterConfig[]>(() => [
  {
    field: 'department',
    type: 'select',
    label: 'Department',
    items: [], // Will be populated dynamically
    placeholder: 'Select department',
  },
  {
    field: 'role_id',
    type: 'select',
    label: 'Role',
    items: roles.value.map(r => ({ id: r.id, name: r.name })),
    placeholder: 'Select role',
  },
]);

// Load roles for the form
const loadRoles = async () => {
  try {
    const response = await axios.get('/api/roles');
    roles.value = response.data.data || response.data;
  } catch (err) {
    console.error('Failed to load roles', err);
  }
};

// Lifecycle
onMounted(() => {
  loadRoles();
});

// Helper functions for custom templates (optional)
const getDepartmentColor = (dept: string): string => {
  const colors: Record<string, string> = {
    engineering: 'blue',
    sales: 'green',
    marketing: 'orange',
    hr: 'purple',
    finance: 'teal',
  };
  return colors[dept.toLowerCase()] || 'grey';
};

const formatDate = (date?: string): string => {
  if (!date) return '—';
  return new Date(date).toLocaleDateString();
};
</script>

<template>
  <FormSchema
    :config="config"
    :table-config="tableConfig"
    :item-form="itemForm"
    :filters="filters"
    :get-department-color="getDepartmentColor"
    :format-date="formatDate"
  />
</template>

<style scoped>
/* Custom styles */
</style>

