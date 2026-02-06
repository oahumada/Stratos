import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useChangeSetStore = defineStore('changeSet', () => {
  const loading = ref(false);
  const error = ref<string | null>(null);

  const createChangeSet = async (scenarioId: number, payload: any) => {
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch(`/api/strategic-planning/scenarios/${scenarioId}/change-sets`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      if (!res.ok) throw new Error('Error creating ChangeSet');
      return await res.json();
    } catch (e: any) {
      error.value = e.message || 'Unknown error';
      throw e;
    } finally {
      loading.value = false;
    }
  };

  const previewChangeSet = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch(`/api/strategic-planning/change-sets/${id}/preview`);
      if (!res.ok) throw new Error('Error fetching preview');
      return await res.json();
    } catch (e: any) {
      error.value = e.message || 'Unknown error';
      throw e;
    } finally {
      loading.value = false;
    }
  };

  const applyChangeSet = async (id: number, payload?: any) => {
    loading.value = true;
    error.value = null;
    try {
      const opts: RequestInit = { method: 'POST' };
      if (payload) {
        opts.headers = { 'Content-Type': 'application/json' };
        opts.body = JSON.stringify(payload);
      }
      const res = await fetch(`/api/strategic-planning/change-sets/${id}/apply`, opts);
      if (!res.ok) throw new Error('Error applying ChangeSet');
      return await res.json();
    } catch (e: any) {
      error.value = e.message || 'Unknown error';
      throw e;
    } finally {
      loading.value = false;
    }
  };

  const canApplyChangeSet = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch(`/api/strategic-planning/change-sets/${id}/can-apply`);
      if (!res.ok) throw new Error('Error checking permissions');
      return await res.json();
    } catch (e: any) {
      error.value = e.message || 'Unknown error';
      throw e;
    } finally {
      loading.value = false;
    }
  };

  const approveChangeSet = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch(`/api/strategic-planning/change-sets/${id}/approve`, { method: 'POST' });
      if (!res.ok) throw new Error('Error approving ChangeSet');
      return await res.json();
    } catch (e: any) {
      error.value = e.message || 'Unknown error';
      throw e;
    } finally {
      loading.value = false;
    }
  };

  const rejectChangeSet = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch(`/api/strategic-planning/change-sets/${id}/reject`, { method: 'POST' });
      if (!res.ok) throw new Error('Error rejecting ChangeSet');
      return await res.json();
    } catch (e: any) {
      error.value = e.message || 'Unknown error';
      throw e;
    } finally {
      loading.value = false;
    }
  };

  return { loading, error, createChangeSet, previewChangeSet, applyChangeSet, canApplyChangeSet, approveChangeSet, rejectChangeSet };
});
