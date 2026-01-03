import { defineStore } from "pinia";

export const useDataStore = defineStore("DataStore", {
    state: () => ({
        selected: {},
        userSelected: {},
        pacienteSelected: [],
    }),
    getters: {
        getUserSelected: (state) => state.userSelected,
        getPacienteSelected: (state) => state.pacienteSelected,
    },
    actions: {
        async setUserSelected(user: {}) {
            this.userSelected = user;
        },
        async setPaciente(paciente = null) {
            if (paciente) {
                this.pacienteSelected = paciente;
            }
            return this.pacienteSelected;
        },
        
        async clearServerCatalogs() {
            try {
                const { post } = await import("@/apiHelper");
                await post("/api/catalogs/clear-cache");
                console.log('Server catalogs cache cleared');
            } catch (error) {
                console.error('Error clearing server cache:', error);
            }
        },
    },
});
            