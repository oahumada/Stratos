import { defineStore } from "pinia";
import { initSanctum } from "@/apiHelper";
import { usePage } from "@inertiajs/vue3";

// Define a User type if not already imported
type User = {
    id: number;
    name: string;
    email: string;
    // Add other user properties as needed
};

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null as User | null,
        isAuthenticated: false,
        isLoading: false,
        authInitialized: false,
    }),

    getters: {
        getUser: (state) => state.user,
        getIsAuthenticated: (state) => state.isAuthenticated,
        isAuthLoading: (state) => state.isLoading,
        isAuthReady: (state) => state.authInitialized,
    },

    actions: {
        async initializeAuth() {
            if (this.authInitialized) return;

            this.isLoading = true;
            try {
                // Inicializar Sanctum primero
                await initSanctum();

                // Verificar si hay usuario autenticado desde Inertia
                const page = usePage();
                const userFromPage = page.props.auth?.user;

                if (userFromPage) {
                    this.user = userFromPage;
                    this.isAuthenticated = true;
                    console.log(
                        "Auth initialized with user:",
                        userFromPage.email,
                    );
                } else {
                    this.logout();
                }

                this.authInitialized = true;
            } catch (error) {
                console.error("Error initializing auth:", error);
                this.logout();
            } finally {
                this.isLoading = false;
            }
        },

        async login(user: any) {
            try {
                // CRÍTICO: Asegurarse de que Sanctum esté inicializado correctamente
                await initSanctum();

                this.user = user;
                this.isAuthenticated = true;
                this.authInitialized = true;

                // NUEVO: Esperar un momento para que se sincronicen los tokens
                await new Promise((resolve) => setTimeout(resolve, 100));

                console.log("User logged in:", user.email);
            } catch (error) {
                console.error("Login error:", error);
                throw error;
            }
        },

        // NUEVO: Método para reinicializar después del login
        async reinitializeAfterLogin(user: any) {
            try {
                this.isLoading = true;

                // Forzar nueva inicialización de Sanctum
                await initSanctum();

                // Pequeño delay para asegurar sincronización
                await new Promise((resolve) => setTimeout(resolve, 200));

                this.user = user;
                this.isAuthenticated = true;
                this.authInitialized = true;

                console.log("Auth reinitialized after login:", user.email);
            } catch (error) {
                console.error("Reinitialize error:", error);
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        // NUEVO: Método para verificar y refrescar auth cuando sea necesario
        async ensureAuthValid() {
            if (!this.isAuthenticated || !this.authInitialized) {
                console.log("Auth not valid, reinitializing...");
                await this.initializeAuth();
            }
            return this.isAuthenticated;
        },

        // NUEVO: Método para manejar errores de auth desde cualquier lugar
        async handleAuthError(error: any) {
            console.error("Auth error occurred:", error);

            if (
                error.response?.status === 401 ||
                error.response?.status === 419
            ) {
                console.log("Critical auth error, logging out...");
                this.logout();

                // Redirigir al login si es necesario
                if (
                    typeof window !== "undefined" &&
                    window.location.pathname !== "/login"
                ) {
                    window.location.href = "/login";
                }
            }
        },

        // MEJORADO: Logout más completo
        logout() {
            this.user = null;
            this.isAuthenticated = false;
            this.authInitialized = false;

            // Limpiar tokens del localStorage si los usas
            if (typeof window !== "undefined") {
                localStorage.removeItem("auth_token");
                sessionStorage.removeItem("auth_token");
            }

            console.log("User logged out completely");
        },

        updateUser(user: any) {
            if (this.isAuthenticated) {
                // VERIFICAR: Asegurar que el user tenga ID
                if (user && user.id) {
                    this.user = { ...this.user, ...user };
                    console.log("User updated with ID:", user.id);
                } else {
                    console.error(
                        "Attempting to update user without ID:",
                        user,
                    );
                    this.user = user;
                }
            }
        },
    },
});
