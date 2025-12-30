<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'import', file: File): void
}>()

const fileInput = ref<HTMLInputElement | null>(null)
const selectedFile = ref<File | null>(null)
const isDragOver = ref(false)

const handleDrop = (e: DragEvent) => {
  isDragOver.value = false
  const files = e.dataTransfer?.files
  const file = files?.[0]
  if (file) {
    validateAndSelect(file)
  }
}

const handleFileSelect = (e: Event) => {
  const input = e.target as HTMLInputElement
  const file = input.files?.[0]
  if (file) {
    validateAndSelect(file)
  }
}

const validateAndSelect = (file: File) => {
  if (file.type !== 'application/pdf') {
    alert('Please upload a PDF file')
    return
  }
  selectedFile.value = file
}

const triggerFileInput = () => {
    fileInput.value?.click()
}

const handleImport = () => {
    if (selectedFile.value) {
        emit('import', selectedFile.value)
    }
}
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" @click.self="emit('close')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
      
      <!-- Header -->
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <i class="fa-brands fa-linkedin text-[#0077b5] text-xl"></i>
            Import from LinkedIn
        </h3>
        <button @click="emit('close')" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100">
          <i class="fa-solid fa-times"></i>
        </button>
      </div>

      <div class="p-6">
        <!-- Instructions -->
        <div class="mb-6 bg-blue-50 border border-blue-100 rounded-xl p-4">
            <h4 class="text-sm font-semibold text-blue-900 mb-2">How to export your profile:</h4>
            <ol class="text-sm text-blue-800 space-y-2 list-decimal list-inside">
                <li>Go to your <a href="https://www.linkedin.com/in/" target="_blank" rel="noopener" class="underline hover:text-blue-600 font-medium">LinkedIn Profile</a></li>
                <li>Click the <span class="font-bold bg-white px-1.5 py-0.5 rounded border border-blue-200 text-xs">More...</span> button in your introduction section</li>
                <li>Select <span class="font-bold bg-white px-1.5 py-0.5 rounded border border-blue-200 text-xs">Save to PDF</span></li>
                <li>Upload the downloaded PDF below</li>
            </ol>
        </div>

        <!-- Upload Area -->
        <div 
            class="border-2 border-dashed rounded-xl p-8 text-center transition-all duration-200 cursor-pointer group"
            :class="[
                isDragOver ? 'border-indigo-500 bg-indigo-50' : 'border-slate-300 hover:border-indigo-400 hover:bg-slate-50',
                selectedFile ? 'bg-indigo-50 border-indigo-200' : ''
            ]"
            @dragover.prevent="isDragOver = true"
            @dragleave.prevent="isDragOver = false"
            @drop.prevent="handleDrop"
            @click="triggerFileInput"
        >
            <input 
                ref="fileInput"
                type="file" 
                accept=".pdf"
                class="hidden" 
                @change="handleFileSelect"
            >

            <div v-if="!selectedFile">
                <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                    <i class="fa-solid fa-cloud-arrow-up text-indigo-500 text-xl"></i>
                </div>
                <p class="font-medium text-slate-700">Click to upload or drag & drop</p>
                <p class="text-xs text-slate-400 mt-1">Supports PDF export from LinkedIn</p>
            </div>

            <div v-else class="flex items-center justify-center gap-3">
                <i class="fa-solid fa-file-pdf text-red-500 text-2xl"></i>
                <div class="text-left">
                    <p class="font-medium text-slate-800 text-sm truncate max-w-[200px]">{{ selectedFile.name }}</p>
                    <p class="text-xs text-slate-500">{{ (selectedFile.size / 1024).toFixed(1) }} KB</p>
                </div>
                <button @click.stop="selectedFile = null" class="ml-2 text-slate-400 hover:text-red-500 p-1">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
        <button 
            @click="emit('close')"
            class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-200/50 rounded-lg transition-colors"
        >
            Cancel
        </button>
        <button 
            @click="handleImport"
            :disabled="!selectedFile"
            class="px-4 py-2 text-sm font-medium text-white bg-[#0077b5] hover:bg-[#006097] disabled:opacity-50 disabled:cursor-not-allowed rounded-lg shadow-sm shadow-blue-200 transition-all flex items-center gap-2"
        >
            <span>Import Profile</span>
            <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>

    </div>
  </div>
</template>
