<script setup lang="ts">
import type { ExperienceItem } from '~/types/resume'

// Define model for update:modelValue
const experience = defineModel<ExperienceItem[]>({ required: true })

const addExperience = () => {
    experience.value.push({
        id: Date.now(),
        position: '',
        company: '',
        location: '',
        startDate: '',
        endDate: '',
        current: false,
        responsibilities: ['']
    })
}

const removeExperience = (index: number) => {
    if (experience.value.length > 1) {
        experience.value.splice(index, 1)
    }
}

const addResponsibility = (expIndex: number) => {
    const exp = experience.value[expIndex]
    if (exp) {
        exp.responsibilities.push('')
    }
}

const removeResponsibility = (expIndex: number, respIndex: number) => {
    const exp = experience.value[expIndex]
    if (exp && exp.responsibilities.length > 1) {
        exp.responsibilities.splice(respIndex, 1)
    }
}
</script>

<template>
    <div>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-slate-900">Work Experience</h3>
            <button @click="addExperience" class="text-indigo-600 hover:text-indigo-700 text-sm font-bold">+ Add
                Experience</button>
        </div>
        <div class="space-y-4">
             <div v-if="experience.length === 0" class="text-center py-8 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-briefcase text-indigo-400 text-xl"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-700">No Experience Added</h4>
                <p class="text-xs text-slate-500 mt-1 mb-3">Add your previous jobs to show your career history.</p>
                <button 
                    @click="addExperience"
                    class="text-xs font-bold px-4 py-2 bg-white border border-slate-300 rounded-lg hover:border-indigo-400 hover:text-indigo-600 transition-all shadow-sm"
                    type="button"
                >
                    Add First Experience
                </button>
            </div>

            <div v-for="(exp, index) in experience" :key="exp.id"
                class="bg-white border border-slate-200 p-4 rounded-2xl">
                <div class="flex justify-between items-start mb-3">
                    <h4 class="text-sm font-bold text-slate-900">Experience {{ index + 1 }}</h4>
                    <button @click="removeExperience(index)" v-if="experience.length > 1"
                        class="text-red-600 hover:text-red-700">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <input v-model="exp.position" type="text" placeholder="Position"
                        class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    <input v-model="exp.company" type="text" placeholder="Company"
                        class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    <input v-model="exp.startDate" type="text" placeholder="Start Date"
                        class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    <div class="flex items-center gap-2">
                        <input v-if="!exp.current" v-model="exp.endDate" type="text" placeholder="End Date"
                            class="px-3 py-2 border border-slate-300 rounded-lg text-sm w-full">
                        <span v-else class="px-3 py-2 bg-slate-100 text-slate-500 rounded-lg text-sm w-full text-center">Present</span>
                    </div>
                </div>
                 <!-- Added simple current checkbox for convenience -->
                 <div class="flex items-center gap-2 mb-3">
                    <input type="checkbox" v-model="exp.current" :id="'current-'+exp.id" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label :for="'current-'+exp.id" class="text-xs text-slate-600">Currently working here</label>
                 </div>

                <div class="mb-2">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-slate-700">Responsibilities</label>
                        <button @click="addResponsibility(index)"
                            class="text-indigo-600 hover:text-indigo-700 text-xs font-bold">+ Add</button>
                    </div>
                    <div v-for="(resp, rIndex) in exp.responsibilities" :key="rIndex" class="flex gap-2 mb-2">
                        <div class="flex-1 relative"> 
                             <input v-model="exp.responsibilities[rIndex]" type="text" placeholder="Responsibility"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm pr-8">
                             <!-- Drag handle placeholder if needed later -->
                        </div>
                       
                        <button @click="removeResponsibility(index, rIndex)" v-if="exp.responsibilities.length > 1"
                            class="text-red-600 hover:text-red-700">
                            <i class="fa-solid fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
