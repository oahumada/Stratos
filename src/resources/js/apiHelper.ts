import axios from "axios";

// Determinar la URL base según el entorno
const getBaseUrl = () => {
    if (typeof window !== "undefined") {
        const hostname = window.location.hostname;
        if (hostname === "talentia.appchain.cl") {
            return "https://talentia.appchain.cl";
        }
    }
    return ""; // Para desarrollo local usa rutas relativas
};

const BASE_URL = getBaseUrl();

// Configurar axios con las configuraciones base
axios.defaults.withCredentials = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Interceptor para agregar CSRF token automáticamente
axios.interceptors.request.use((config) => {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (token) {
        config.headers["X-CSRF-TOKEN"] = token;
    }
    return config;
});

// NUEVO: Estado global para evitar múltiples reinicializaciones simultáneas
let isRefreshingAuth = false;
let failedQueue: any[] = [];

const processQueue = (error: any, token: string | null = null) => {
    failedQueue.forEach(({ resolve, reject }) => {
        if (error) {
            reject(error);
        } else {
            resolve(token);
        }
    });

    failedQueue = [];
};

// MEJORADO: Interceptor más robusto
axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        // Manejar error 419 (CSRF token mismatch)
        if (error.response?.status === 419 && !originalRequest._retry) {
            console.warn("CSRF token mismatch (419), handling...");

            if (isRefreshingAuth) {
                // Si ya se está refreshing, agregar a la cola
                return new Promise((resolve, reject) => {
                    failedQueue.push({ resolve, reject });
                })
                    .then(() => {
                        return axios(originalRequest);
                    })
                    .catch((err) => {
                        return Promise.reject(err);
                    });
            }

            originalRequest._retry = true;
            isRefreshingAuth = true;

            try {
                console.log("Refreshing authentication tokens...");
                await initSanctum();
                await new Promise((resolve) => setTimeout(resolve, 300));

                const token = document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content");
                if (token) {
                    originalRequest.headers["X-CSRF-TOKEN"] = token;

                    // Actualizar header por defecto para futuras requests
                    axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

                    console.log("Auth tokens refreshed successfully");
                    processQueue(null, token);
                    return axios(originalRequest);
                } else {
                    throw new Error("No CSRF token available after refresh");
                }
            } catch (refreshError) {
                console.error("Auth refresh failed:", refreshError);
                processQueue(refreshError, null);

                // Si falla el refresh, puede ser que la sesión expiró
                try {
                    const { useAuthStore } = await import("@/stores/authStore");
                    const authStore = useAuthStore();
                    authStore.logout();

                    // Opcional: redirigir al login
                    if (typeof window !== "undefined") {
                        window.location.href = "/login";
                    }
                } catch (importError) {
                    console.error("Error importing auth store:", importError);
                }

                return Promise.reject(refreshError);
            } finally {
                isRefreshingAuth = false;
            }
        }

        // Manejar error 401 (Unauthorized)
        if (error.response?.status === 401) {
            console.warn("Unauthorized request (401), clearing auth state");
            try {
                const { useAuthStore } = await import("@/stores/authStore");
                const authStore = useAuthStore();
                authStore.logout();

                // Redirigir al login si estamos en el browser
                if (
                    typeof window !== "undefined" &&
                    window.location.pathname !== "/login"
                ) {
                    window.location.href = "/login";
                }
            } catch (importError) {
                console.error("Error importing auth store:", importError);
            }
        }

        return Promise.reject(error);
    },
);

// Función para inicializar Sanctum (obtener CSRF cookie)
export const initSanctum = async () => {
    try {
        await axios.get(`${BASE_URL}/sanctum/csrf-cookie`);

        // NUEVO: Forzar actualización del meta tag CSRF
        // Hacer una pequeña request para obtener el nuevo token
        try {
            const response = await axios.get(`${BASE_URL}/api/csrf-token`);
            if (response.data.csrf_token) {
                // Actualizar el meta tag con el nuevo token
                let metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute("content", response.data.csrf_token);
                } else {
                    // Crear meta tag si no existe
                    metaTag = document.createElement("meta");
                    metaTag.setAttribute("name", "csrf-token");
                    metaTag.setAttribute("content", response.data.csrf_token);
                    document
                        .getElementsByTagName("head")[0]
                        .appendChild(metaTag);
                }
                console.log("CSRF token updated in meta tag");
            }
        } catch (tokenError) {
            console.warn("Could not update CSRF token from API:", tokenError);
        }
    } catch (error) {
        console.error("Error al inicializar Sanctum:", error);
    }
};

// ALTERNATIVA: Función más agresiva para reinicializar Sanctum
export const forceSanctumRefresh = async () => {
    try {
        // Limpiar cookies existentes de sanctum
        document.cookie =
            "XSRF-TOKEN=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie =
            "laravel_session=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

        // Esperar un momento
        await new Promise((resolve) => setTimeout(resolve, 100));

        // Reinicializar Sanctum
        await initSanctum();

        // Esperar más tiempo para sincronización
        await new Promise((resolve) => setTimeout(resolve, 500));

        console.log("Sanctum aggressively refreshed");
    } catch (error) {
        console.error("Error in forceSanctumRefresh:", error);
        throw error;
    }
};

// Métodos CRUD genéricos
export const get = async (url: string, params = {}) => {
    console.log("params =>", params);
    try {
        const result = await axios.get(url, { params });
        return result.data;
    } catch (error) {
        throw error;
    }
};

export const post = async (url: string, data = {}) => {
    try {
        // Inicializar Sanctum para obtener CSRF cookie
        await initSanctum();

        const result = await axios.post(url, data);
        return result.data;
    } catch (error) {
        throw error;
    }
};

export const put = async (url: string, data = {}) => {
    try {
        // Inicializar Sanctum para obtener CSRF cookie
        await initSanctum();

        const result = await axios.put(url, data);
        return result.data;
    } catch (error) {
        throw error;
    }
};

export const remove = async (url: string, params = {}) => {
    try {
        // Inicializar Sanctum para obtener CSRF cookie
        await initSanctum();

        const result = await axios.delete(url, { params });
        return result.data;
    } catch (error) {
        throw error;
    }
};

// Búsqueda avanzada (opcional) - Search avanzado
export const search = async (url: string, data = {}) => {
    try {
        // Inicializar Sanctum para obtener CSRF cookie
        await initSanctum();
        console.log("url =>", url);
        console.log("data =>", data);

        // Enviar los datos directamente con la estructura que espera el backend
        const result = await axios.post(url, { data });
        return result.data;
    } catch (error) {
        throw error;
    }
};

// Mostrar registros para un usuario - Show
export async function show(url: string, id: number, params = {}) {
    console.log("params =>", params);
    try {
        const result = await axios.get(url + "/" + id, { params });
        return result.data;
    } catch (error) {
        throw error;
    }
}

// Carga de catálogos en bloque
/**
 * Obtiene los catálogos desde el backend usando los endpoints requeridos.
 * @param {Array} endpoints - Lista de catálogos a solicitar.
 * @returns {Promise<Object>} Respuesta de la API con los catálogos.
 */
export const fetchCatalogs = async (endpoints = []) => {
    try {
        // AGREGAR inicialización de Sanctum para obtener CSRF cookie
        await initSanctum();

        // Se envían los endpoints como parámetro GET
        const result = await axios.get(`${BASE_URL}/api/catalogs`, {
            params: { endpoints },
        });
        return result.data;
    } catch (error) {
        throw error;
    }
};
