<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
    modelValue: string // The generated cover letter
    isGenerating: boolean
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void
    (e: 'generate', jobDescription: string): void
}>()

const jobDescription = ref('')
const localCoverLetter = ref(props.modelValue)

// Sync local value with prop
watch(() => props.modelValue, (newVal) => {
    localCoverLetter.value = newVal
})

// Sync prop with local value
watch(localCoverLetter, (newVal) => {
    emit('update:modelValue', newVal)
})

const handleGenerate = () => {
    emit('generate', jobDescription.value)
}

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(localCoverLetter.value)
        // Ideally show a toast here, but for now relying on parent or simple UI feedback
    } catch (err) {
        console.error('Failed to copy', err)
    }
}
</script>

<template>
    <div class="h-full flex flex-col bg-white">
        <!-- Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <div>
                <h3 class="font-bold text-slate-800">Cover Letter</h3>
                <p class="text-xs text-slate-400">AI-powered generation</p>
            </div>
            <div class="flex gap-2">
                <button v-if="localCoverLetter" @click="copyToClipboard" 
                    class="text-xs font-bold text-indigo-600 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">
                    <i class="fa-regular fa-copy mr-1"></i> Copy
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
            <!-- Input Section -->
            <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100 mb-6 transition-all focus-within:ring-2 focus-within:ring-indigo-200">
                <label class="block text-xs font-bold text-indigo-900 mb-2 uppercase tracking-wide">
                    Step 1: Paste Job Description
                </label>
                <textarea 
                    v-model="jobDescription"
                    class="w-full bg-white border border-indigo-200 rounded-lg p-3 text-sm focus:outline-none focus:border-indigo-400 min-h-[120px] resize-none"
                    placeholder="Paste the full job description here. The AI will analyze it to tailor your cover letter..."
                ></textarea>
                <div class="mt-3 flex justify-end">
                    <button 
                        @click="handleGenerate" 
                        :disabled="isGenerating || !jobDescription.trim()"
                        class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-2"
                    >
                        <i v-if="isGenerating" class="fa-solid fa-spinner fa-spin"></i>
                        <i v-else class="fa-solid fa-wand-magic-sparkles"></i>
                        {{ isGenerating ? 'Writing...' : 'Generate Cover Letter' }}
                    </button>
                </div>
            </div>

            <!-- Output Section -->
            <div v-if="localCoverLetter" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                        Step 2: Review & Edit
                    </label>
                </div>
                <textarea 
                    v-model="localCoverLetter"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl p-6 text-sm leading-relaxed text-slate-800 focus:outline-none focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-50/50 transition-all min-h-[400px]"
                    spellcheck="false"
                ></textarea>
            </div>

            <!-- Empty State -->
            <div v-else-if="!isGenerating" class="text-center py-12 px-8 opacity-50">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-file-lines text-2xl text-slate-400"></i>
                </div>
                <p class="text-sm font-medium text-slate-500">
                    Paste a job description above and let AI write a tailored cover letter for you.
                </p>
            </div>
            
            <!-- Loading Skeleton -->
            <div v-else class="space-y-4 animate-pulse mt-8">
                <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                <div class="h-4 bg-slate-200 rounded w-full"></div>
                <div class="h-4 bg-slate-200 rounded w-full"></div>
                <div class="h-4 bg-slate-200 rounded w-5/6"></div>
                <div class="space-y-2 pt-4">
                     <div class="h-4 bg-slate-200 rounded w-full"></div>
                     <div class="h-4 bg-slate-200 rounded w-full"></div>
                </div>
            </div>
        </div>
    </div>
</template>
