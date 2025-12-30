<script setup lang="ts">
defineProps<{
  isProcessing?: boolean
  lastSaved?: string | Date
}>()

defineEmits<{
  (e: 'preview'): void
  (e: 'save'): void
  (e: 'generate'): void
}>()
</script>

<template>
  <div class="fixed bottom-0 left-0 right-0 z-40 lg:hidden px-4 pb-4 pt-2 bg-gradient-to-t from-white via-white to-white/90 backdrop-blur-sm">
    <div class="bg-slate-900 rounded-2xl p-2 shadow-xl flex items-center justify-between gap-2 max-w-md mx-auto">
      
      <!-- AI Generate -->
      <button @click="$emit('generate')" :disabled="isProcessing"
        class="flex-1 flex flex-col items-center justify-center py-2 px-1 rounded-xl hover:bg-slate-800 transition-colors text-white disabled:opacity-50">
        <i class="fa-solid fa-wand-magic-sparkles text-indigo-400 mb-1"></i>
        <span class="text-[10px] font-bold">AI Assistant</span>
      </button>

      <!-- Save -->
      <button @click="$emit('save')" :disabled="isProcessing"
        class="flex-1 flex flex-col items-center justify-center py-2 px-1 rounded-xl hover:bg-slate-800 transition-colors text-white disabled:opacity-50">
        <div class="relative">
          <i v-if="isProcessing" class="fa-solid fa-spinner fa-spin text-amber-400 mb-1"></i>
          <i v-else class="fa-solid fa-cloud-arrow-up text-amber-400 mb-1"></i>
        </div>
        <span class="text-[10px] font-bold">{{ isProcessing ? 'Saving...' : 'Save' }}</span>
      </button>

      <!-- Preview -->
      <button @click="$emit('preview')" 
        class="flex-1 flex flex-col items-center justify-center py-2 px-1 rounded-xl bg-indigo-600 hover:bg-indigo-500 transition-colors text-white shadow-lg shadow-indigo-900/50">
        <i class="fa-solid fa-eye mb-1"></i>
        <span class="text-[10px] font-bold">Preview</span>
      </button>

    </div>
  </div>
</template>
