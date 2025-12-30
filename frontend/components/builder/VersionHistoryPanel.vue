<script setup lang="ts">
import { ref } from 'vue'
import type { ResumeVersion } from '~/composables/useVersions'

defineProps<{
    versions: ResumeVersion[]
}>()

const emit = defineEmits<{
    (e: 'save', name: string): void
    (e: 'restore', id: string): void
    (e: 'delete', id: string): void
}>()

const newVersionName = ref('')
const isCreating = ref(false)

const handleCreate = () => {
    isCreating.value = true
    newVersionName.value = `Version ${new Date().toLocaleDateString()}`
    
    // Auto-focus input in next tick (simplified here)
    setTimeout(() => {
        document.getElementById('version-name-input')?.focus()
    }, 100)
}

const confirmCreate = () => {
    if (newVersionName.value.trim()) {
        emit('save', newVersionName.value)
        isCreating.value = false
        newVersionName.value = ''
    }
}

const cancelCreate = () => {
    isCreating.value = false
    newVersionName.value = ''
}

const formatDate = (date: Date) => {
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
    }).format(new Date(date))
}
</script>

<template>
    <div class="h-full flex flex-col bg-white">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <h3 class="font-bold text-slate-800">Version History</h3>
            <span class="text-xs text-slate-400">{{ versions.length }} saved</span>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            
            <!-- Create New -->
            <div v-if="!isCreating" class="mb-6">
                <button @click="handleCreate" 
                    class="w-full py-3 border-2 border-dashed border-slate-200 rounded-xl flex flex-col items-center justify-center text-slate-500 hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all group">
                    <div class="w-8 h-8 rounded-full bg-slate-100 group-hover:bg-indigo-100 flex items-center justify-center mb-1 transition-colors">
                        <i class="fa-solid fa-plus text-sm"></i>
                    </div>
                    <span class="text-xs font-bold">Save Current Version</span>
                </button>
            </div>

            <div v-else class="mb-6 bg-indigo-50 border border-indigo-100 rounded-xl p-3">
                <label class="block text-xs font-bold text-indigo-900 mb-1">Version Name</label>
                <input 
                    id="version-name-input"
                    v-model="newVersionName" 
                    type="text" 
                    class="w-full text-sm border-slate-200 rounded-lg mb-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="e.g. Software Engineer V1"
                    @keyup.enter="confirmCreate"
                    @keyup.esc="cancelCreate"
                />
                <div class="flex gap-2 justify-end">
                    <button @click="cancelCreate" class="px-2 py-1 text-xs font-medium text-slate-500 hover:text-slate-700">Cancel</button>
                    <button @click="confirmCreate" class="px-3 py-1 text-xs font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Save</button>
                </div>
            </div>

            <!-- List -->
            <div v-if="versions.length === 0 && !isCreating" class="text-center py-8 text-slate-400">
                <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 opacity-50"></i>
                <p class="text-xs">No saved versions yet.</p>
            </div>

            <div v-for="version in versions" :key="version.id" 
                class="group bg-white border border-slate-100 rounded-xl p-3 hover:shadow-md hover:border-indigo-100 transition-all relative">
                
                <div class="flex justify-between items-start mb-1">
                    <h4 class="font-bold text-slate-700 text-sm truncate pr-6">{{ version.name }}</h4>
                    <button @click="$emit('delete', version.id)" 
                        class="text-slate-300 hover:text-rose-500 p-1 -mr-1 -mt-1 opacity-0 group-hover:opacity-100 transition-all"
                        title="Delete Version">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
                
                <p class="text-[10px] text-slate-400 mb-3 flex items-center gap-1">
                    <i class="fa-regular fa-clock"></i>
                    {{ formatDate(version.timestamp) }}
                </p>

                <button @click="$emit('restore', version.id)" 
                    class="w-full py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors flex items-center justify-center gap-1">
                    <i class="fa-solid fa-rotate-left"></i>
                    Restore this version
                </button>
            </div>

        </div>
    </div>
</template>
