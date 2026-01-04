import { ref } from 'vue'
import axios, { type AxiosInstance, type AxiosRequestConfig } from 'axios'

const api: AxiosInstance = axios.create({
  baseURL: window.location.origin,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Add CSRF token to requests
api.interceptors.request.use((config) => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (token) {
    config.headers['X-CSRF-TOKEN'] = token
  }
  return config
})

// Handle errors globally
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Redirect to login
      window.location.href = '/login'
    }
    return Promise.reject(error)
  },
)

export function useApi() {
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const request = async (method: 'get' | 'post' | 'put' | 'delete' | 'patch', url: string, data?: any, config?: AxiosRequestConfig) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await api[method](url, data, config)
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'An error occurred'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  return {
    api,
    isLoading,
    error,
    get: (url: string, config?: AxiosRequestConfig) => request('get', url, undefined, config),
    post: (url: string, data?: any, config?: AxiosRequestConfig) => request('post', url, data, config),
    put: (url: string, data?: any, config?: AxiosRequestConfig) => request('put', url, data, config),
    delete: (url: string, config?: AxiosRequestConfig) => request('delete', url, undefined, config),
    patch: (url: string, data?: any, config?: AxiosRequestConfig) => request('patch', url, data, config),
  }
}
