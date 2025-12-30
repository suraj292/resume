import { useRefHistory } from '@vueuse/core'
import type { Ref } from 'vue'

/**
 * Wrapper around VueUse's useRefHistory for consistent undo/redo behavior
 * @param source The ref to track history for
 * @param options Configuration options
 */
export function useHistory<T>(source: Ref<T>, options = {}) {
    const {
        history,
        undo,
        redo,
        canUndo,
        canRedo,
        clear,
        commit,
        last
    } = useRefHistory(source, {
        deep: true,
        capacity: 20, // Limit history to last 20 changes
        clone: (val: T) => JSON.parse(JSON.stringify(val)), // Deep clone to avoid reference issues
        ...options
    })

    // Keyboard shortcuts handler
    const setupKeyboardShortcuts = () => {
        if (process.client) {
            window.addEventListener('keydown', handleKeydown)
        }
    }

    const removeKeyboardShortcuts = () => {
        if (process.client) {
            window.removeEventListener('keydown', handleKeydown)
        }
    }

    const handleKeydown = (e: KeyboardEvent) => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'z') {
            e.preventDefault()
            if (e.shiftKey) {
                redo()
            } else {
                undo()
            }
        } else if ((e.metaKey || e.ctrlKey) && e.key === 'y') {
            e.preventDefault()
            redo()
        }
    }

    onMounted(() => {
        setupKeyboardShortcuts()
    })

    onUnmounted(() => {
        removeKeyboardShortcuts()
    })

    return {
        history,
        undo,
        redo,
        canUndo,
        canRedo,
        clear,
        commit,
        last
    }
}
