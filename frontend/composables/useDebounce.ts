import { ref, watch, type Ref } from 'vue'

/**
 * Composable for debouncing reactive values
 * @param value - The reactive value to debounce
 * @param delay - Delay in milliseconds (default: 300ms)
 * @returns Debounced value
 */
export function useDebounce<T>(value: Ref<T>, delay: number = 300): Ref<T> {
    const debouncedValue = ref(value.value) as Ref<T>
    let timeoutId: NodeJS.Timeout | null = null

    watch(value, (newValue) => {
        if (timeoutId) {
            clearTimeout(timeoutId)
        }

        timeoutId = setTimeout(() => {
            debouncedValue.value = newValue
        }, delay)
    })

    return debouncedValue
}

/**
 * Composable for debouncing function calls
 * @param fn - Function to debounce
 * @param delay - Delay in milliseconds (default: 300ms)
 * @returns Debounced function
 */
export function useDebounceFn<T extends (...args: any[]) => any>(
    fn: T,
    delay: number = 300
): (...args: Parameters<T>) => void {
    let timeoutId: NodeJS.Timeout | null = null

    return (...args: Parameters<T>) => {
        if (timeoutId) {
            clearTimeout(timeoutId)
        }

        timeoutId = setTimeout(() => {
            fn(...args)
        }, delay)
    }
}

/**
 * Composable for throttling function calls
 * @param fn - Function to throttle
 * @param delay - Delay in milliseconds (default: 300ms)
 * @returns Throttled function
 */
export function useThrottleFn<T extends (...args: any[]) => any>(
    fn: T,
    delay: number = 300
): (...args: Parameters<T>) => void {
    let lastCall = 0

    return (...args: Parameters<T>) => {
        const now = Date.now()

        if (now - lastCall >= delay) {
            lastCall = now
            fn(...args)
        }
    }
}
