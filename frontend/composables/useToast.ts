export interface Toast {
    id: number
    message: string
    type: 'success' | 'error' | 'info' | 'warning'
    duration?: number
}

// Global state for toasts
const toasts = ref<Toast[]>([])
let toastId = 0

export const useToast = () => {
    const addToast = (message: string, type: 'success' | 'error' | 'info' | 'warning' = 'info', duration = 3000) => {
        const id = ++toastId
        const toast: Toast = { id, message, type, duration }
        toasts.value.push(toast)

        if (duration > 0) {
            setTimeout(() => {
                removeToast(id)
            }, duration)
        }
    }

    const removeToast = (id: number) => {
        const index = toasts.value.findIndex(t => t.id === id)
        if (index !== -1) {
            toasts.value.splice(index, 1)
        }
    }

    const helper = (type: 'success' | 'error' | 'info' | 'warning') => (message: string, titleOrDuration?: string | number, duration?: number) => {
        let d = typeof titleOrDuration === 'number' ? titleOrDuration : duration
        addToast(message, type, d)
    }

    const success = helper('success')
    const error = helper('error')
    const info = helper('info')
    const warning = helper('warning')

    return {
        toasts,
        addToast,
        removeToast,
        success,
        error,
        info,
        warning
    }
}
