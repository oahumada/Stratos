import { ref } from 'vue'

export interface Notification {
  id: string
  type: 'success' | 'error' | 'warning' | 'info'
  message: string
  duration?: number
}

const notifications = ref<Notification[]>([])

export function useNotification() {
  const addNotification = (notification: Omit<Notification, 'id'>) => {
    const id = Math.random().toString(36).substring(7)
    const newNotification: Notification = {
      ...notification,
      id,
      duration: notification.duration || 3000,
    }

    notifications.value.push(newNotification)

    if (newNotification.duration) {
      setTimeout(() => {
        removeNotification(id)
      }, newNotification.duration)
    }

    return id
  }

  const removeNotification = (id: string) => {
    notifications.value = notifications.value.filter((n) => n.id !== id)
  }

  const clearAll = () => {
    notifications.value = []
  }

  const showSuccess = (message: string, duration?: number) => {
    return addNotification({
      type: 'success',
      message,
      duration,
    })
  }

  const showError = (message: string, duration?: number) => {
    return addNotification({
      type: 'error',
      message,
      duration: duration || 5000, // Longer duration for errors
    })
  }

  const showWarning = (message: string, duration?: number) => {
    return addNotification({
      type: 'warning',
      message,
      duration,
    })
  }

  const showInfo = (message: string, duration?: number) => {
    return addNotification({
      type: 'info',
      message,
      duration,
    })
  }

  return {
    notifications,
    addNotification,
    removeNotification,
    clearAll,
    showSuccess,
    showError,
    showWarning,
    showInfo,
  }
}
