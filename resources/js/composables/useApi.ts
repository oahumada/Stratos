import { initSanctum } from '@/apiHelper';
import axios, { type AxiosInstance, type AxiosRequestConfig } from 'axios';
import { ref } from 'vue';

const baseURL = import.meta.env.VITE_API_BASE_URL || window.location.origin;

const api: AxiosInstance = axios.create({
    baseURL,
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

// Add CSRF token and ensure Sanctum cookie exists
api.interceptors.request.use(async (config) => {
    const hasXsrf = document.cookie.includes('XSRF-TOKEN=');
    if (!hasXsrf) {
        await initSanctum().catch(() => null);
    }

    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
});

// Handle errors globally
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            window.location.href = '/login';
        }
        return Promise.reject(error);
    },
);

export function useApi() {
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    const request = async (
        method: 'get' | 'post' | 'put' | 'delete' | 'patch',
        url: string,
        data?: any,
        config?: AxiosRequestConfig,
    ) => {
        isLoading.value = true;
        error.value = null;

        try {
            let finalConfig = config;

            // For GET requests, if data is provided, treat it as query params
            if (method === 'get' && data && typeof data === 'object') {
                finalConfig = {
                    ...config,
                    params: data,
                };
                data = undefined;
            }

            let response;
            if (method === 'get') {
                response = await api.get(url, finalConfig);
            } else {
                response = await (api as any)[method](url, data, finalConfig);
            }
            return response.data;
        } catch (err: any) {
            // If the server returned HTML (e.g. error page), avoid surfacing raw HTML
            let friendly = 'An error occurred';
            if (err?.response) {
                const d = err.response.data;
                if (d && typeof d === 'object' && d.message) {
                    friendly = d.message;
                } else if (d && typeof d === 'string') {
                    const s = d.trim();
                    if (s.startsWith('<')) {
                        // HTML response (likely a 500 error page)
                        friendly = 'Server error (returned HTML). Check server logs for details.';
                    } else {
                        // Non-JSON string response — show a trimmed preview
                        friendly = s.length > 200 ? s.slice(0, 200) + '...' : s;
                    }
                } else if (err.message) {
                    friendly = err.message;
                }
            } else if (err.message) {
                friendly = err.message;
            }

            error.value = friendly;
            // Attach a sanitized message to the thrown error for callers
            const thrown = err instanceof Error ? err : new Error(String(err));
            (thrown as any).friendlyMessage = friendly;
            throw thrown;
        } finally {
            isLoading.value = false;
        }
    };

    return {
        api,
        isLoading,
        error,
        get: (url: string, params?: any, config?: AxiosRequestConfig) => {
            const finalConfig: AxiosRequestConfig = {
                ...(config || {}),
                params,
            };
            return request('get', url, undefined, finalConfig);
        },
        post: (url: string, data?: any, config?: AxiosRequestConfig) =>
            request('post', url, data, config),
        put: (url: string, data?: any, config?: AxiosRequestConfig) =>
            request('put', url, data, config),
        delete: (url: string, config?: AxiosRequestConfig) =>
            request('delete', url, undefined, config),
        patch: (url: string, data?: any, config?: AxiosRequestConfig) =>
            request('patch', url, data, config),
    };
}
