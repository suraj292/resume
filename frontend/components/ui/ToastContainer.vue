<script setup lang="ts">
const { toasts, removeToast } = useToast()
</script>

<template>
    <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none">
        <TransitionGroup name="toast">
            <div v-for="toast in toasts" :key="toast.id" 
                class="pointer-events-auto min-w-[300px] max-w-md p-4 rounded-lg shadow-lg text-white flex items-start gap-3 transform transition-all duration-300"
                :class="{
                    'bg-emerald-600': toast.type === 'success',
                    'bg-rose-600': toast.type === 'error',
                    'bg-slate-800': toast.type === 'info',
                    'bg-amber-500': toast.type === 'warning'
                }"
            >
                <i class="mt-1 text-sm fa-solid" :class="{
                    'fa-circle-check': toast.type === 'success',
                    'fa-circle-exclamation': toast.type === 'error',
                    'fa-circle-info': toast.type === 'info',
                    'fa-triangle-exclamation': toast.type === 'warning'
                }"></i>
                <div class="flex-1 text-sm font-medium leading-relaxed">{{ toast.message }}</div>
                <button @click="removeToast(toast.id)" class="text-white/80 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style>
