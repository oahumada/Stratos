import { defineStore } from 'pinia';
import axios from 'axios';

export const useTransformStore = defineStore('transform', {
  actions: {
    async transformCompetency(competencyId: number, payload: any) {
      const res = await axios.post(`/api/competencies/${competencyId}/transform`, payload);
      return res.data.data;
    }
    ,
    async getVersions(competencyId: number) {
      const res = await axios.get(`/api/competencies/${competencyId}/versions`);
      return res.data.data;
    }
  }
});
