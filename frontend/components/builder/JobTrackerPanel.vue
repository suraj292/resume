<script setup lang="ts">
import { ref, computed } from 'vue'
import type { JobApplication, JobStatus } from '~/composables/useJobTracker'

const props = defineProps<{
    applications: JobApplication[]
}>()

const emit = defineEmits<{
    (e: 'add', job: Omit<JobApplication, 'id' | 'createdAt'>): void
    (e: 'update-status', id: string, status: JobStatus): void
    (e: 'delete', id: string): void
}>()

const showAddModal = ref(false)
const newJob = ref({
    company: '',
    position: '',
    status: 'wishlist' as JobStatus,
    location: '',
    salary: ''
})

const columns = [
    { id: 'wishlist', label: 'Wishlist', color: 'bg-slate-100 border-slate-200 text-slate-600' },
    { id: 'applied', label: 'Applied', color: 'bg-blue-50 border-blue-100 text-blue-600' },
    { id: 'interview', label: 'Interview', color: 'bg-amber-50 border-amber-100 text-amber-600' },
    { id: 'offer', label: 'Offer', color: 'bg-emerald-50 border-emerald-100 text-emerald-600' },
    { id: 'rejected', label: 'Rejected', color: 'bg-rose-50 border-rose-100 text-rose-600' }
]

const getJobsByStatus = (status: JobStatus) => {
    return props.applications.filter(app => app.status === status)
}

const handleAdd = () => {
    if (!newJob.value.company || !newJob.value.position) return

    emit('add', { ...newJob.value })
    
    // Reset
    newJob.value = {
        company: '',
        position: '',
        status: 'wishlist',
        location: '',
        salary: ''
    }
    showAddModal.value = false
}

const formatDate = (ts: number) => {
    return new Date(ts).toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
}

// Simple move logic (since full drag and drop might be complex for a single file component without libs)
const moveJob = (id: string, currentStatus: JobStatus, direction: 'next' | 'prev') => {
    const statusOrder: JobStatus[] = ['wishlist', 'applied', 'interview', 'offer', 'rejected']
    const currentIndex = statusOrder.indexOf(currentStatus)
    
    let nextIndex = currentIndex
    if (direction === 'next' && currentIndex < statusOrder.length - 1) nextIndex++
    if (direction === 'prev' && currentIndex > 0) nextIndex--
    
    if (nextIndex !== currentIndex && statusOrder[nextIndex]) {
        emit('update-status', id, statusOrder[nextIndex]!)
    }
}
</script>

<template>
    <div class="h-full flex flex-col bg-white">
        <!-- Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <div>
                <h3 class="font-bold text-slate-800">Job Applications</h3>
                <p class="text-xs text-slate-400">{{ applications.length }} tracked jobs</p>
            </div>
            <button @click="showAddModal = true" 
                class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add Job
            </button>
        </div>

        <!-- Add Modal (Simplified inline overlay) -->
        <div v-if="showAddModal" class="absolute inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl animate-in zoom-in-95 duration-200">
                <h3 class="font-bold text-lg mb-4">Add Application</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Company *</label>
                        <input v-model="newJob.company" type="text" class="w-full text-sm border-slate-200 rounded-lg" placeholder="Google" autofocus />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Position *</label>
                        <input v-model="newJob.position" type="text" class="w-full text-sm border-slate-200 rounded-lg" placeholder="Frontend Engineer" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Status</label>
                        <select v-model="newJob.status" class="w-full text-sm border-slate-200 rounded-lg">
                            <option v-for="col in columns" :key="col.id" :value="col.id">{{ col.label }}</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                             <label class="block text-xs font-bold text-slate-500 mb-1">Location</label>
                             <input v-model="newJob.location" type="text" class="w-full text-sm border-slate-200 rounded-lg" placeholder="Remote" />
                        </div>
                        <div>
                             <label class="block text-xs font-bold text-slate-500 mb-1">Salary</label>
                             <input v-model="newJob.salary" type="text" class="w-full text-sm border-slate-200 rounded-lg" placeholder="$120k" />
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 justify-end mt-6">
                    <button @click="showAddModal = false" class="px-3 py-2 text-xs font-medium text-slate-500">Cancel</button>
                    <button @click="handleAdd" :disabled="!newJob.company || !newJob.position" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 disabled:opacity-50">Add Job</button>
                </div>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="flex-1 overflow-x-auto overflow-y-hidden p-4">
            <div class="flex gap-4 h-full min-w-max">
                <div v-for="col in columns" :key="col.id" class="w-64 flex flex-col h-full rounded-xl bg-slate-50 border border-slate-100">
                    <!-- Column Header -->
                    <div :class="['p-3 border-b font-bold text-xs flex justify-between items-center rounded-t-xl', col.color]">
                        <span>{{ col.label }}</span>
                        <span class="bg-white/50 px-1.5 py-0.5 rounded text-[10px]">{{ getJobsByStatus(col.id as JobStatus).length }}</span>
                    </div>

                    <!-- Cards -->
                    <div class="p-2 flex-1 overflow-y-auto space-y-2 custom-scrollbar">
                        <div v-for="job in getJobsByStatus(col.id as JobStatus)" :key="job.id" 
                            class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm hover:shadow-md transition-all group relative">
                            
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-bold text-sm text-slate-800">{{ job.company }}</h4>
                                <button @click="$emit('delete', job.id)" class="text-slate-300 hover:text-rose-500 -mr-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                            
                            <p class="text-xs text-slate-600 font-medium mb-2">{{ job.position }}</p>
                            
                            <div class="flex flex-wrap gap-1 mb-3">
                                <span v-if="job.location" class="text-[10px] flex items-center gap-1 text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded">
                                    <i class="fa-solid fa-location-dot text-[9px]"></i> {{ job.location }}
                                </span>
                                <span v-if="job.salary" class="text-[10px] flex items-center gap-1 text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded font-medium">
                                    <i class="fa-solid fa-money-bill-wave text-[9px]"></i> {{ job.salary }}
                                </span>
                            </div>

                            <!-- Move Controls -->
                            <div class="flex justify-between items-center pt-2 border-t border-slate-50 mt-2">
                                <button 
                                    @click="moveJob(job.id, col.id as JobStatus, 'prev')" 
                                    :disabled="col.id === 'wishlist'"
                                    class="text-xs text-slate-300 hover:text-indigo-600 disabled:opacity-0 transition-colors"
                                    title="Move Left"
                                >
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                
                                <span class="text-[10px] text-slate-300">{{ formatDate(job.createdAt) }}</span>
                                
                                <button 
                                    @click="moveJob(job.id, col.id as JobStatus, 'next')" 
                                    :disabled="col.id === 'rejected'"
                                    class="text-xs text-slate-300 hover:text-indigo-600 disabled:opacity-0 transition-colors"
                                    title="Move Right"
                                >
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
