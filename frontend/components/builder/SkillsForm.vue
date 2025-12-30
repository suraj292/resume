<script setup lang="ts">
import type { SkillCategories } from '~/types/resume'

const skills = defineModel<SkillCategories>({ required: true })

const totalSkills = computed(() => {
    return (skills.value.backend?.length || 0) + 
           (skills.value.frontend?.length || 0) + 
           (skills.value.devops?.length || 0) + 
           (skills.value.other?.length || 0)
})

const addSkill = (category: keyof SkillCategories) => {
    // Basic prompt for now, can be improved to a modal later
    const skill = prompt(`Enter new ${category} skill:`)
    if (skill && skill.trim()) {
        if (!skills.value[category]) {
            skills.value[category] = []
        }
        skills.value[category]!.push(skill.trim())
    }
}

const removeSkill = (category: keyof SkillCategories, index: number) => {
    if (skills.value[category]) {
        skills.value[category]!.splice(index, 1)
    }
}
</script>

<template>
    <div>
        <h3 class="text-lg font-bold text-slate-900 mb-4">Skills</h3>
        
        <div v-if="totalSkills === 0" class="text-center py-6 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50 mb-6">
            <h4 class="text-sm font-bold text-slate-700">No Skills Added</h4>
            <p class="text-xs text-slate-500 mt-1">Add your technical skills to highlight your expertise.</p>
        </div>

        <div class="space-y-4">
            <!-- Backend Skills -->
            <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-sm font-bold text-slate-700">Backend</label>
                    <button @click="addSkill('backend')"
                        class="text-indigo-600 hover:text-indigo-700 text-xs font-bold">+ Add</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span v-for="(skill, index) in skills.backend" :key="index"
                        class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">
                        {{ skill }}
                        <button @click="removeSkill('backend', index)" class="hover:text-blue-900">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </span>
                </div>
            </div>

            <!-- Frontend Skills -->
            <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-sm font-bold text-slate-700">Frontend</label>
                    <button @click="addSkill('frontend')"
                        class="text-indigo-600 hover:text-indigo-700 text-xs font-bold">+ Add</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span v-for="(skill, index) in skills.frontend" :key="index"
                        class="inline-flex items-center gap-2 bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-medium">
                        {{ skill }}
                        <button @click="removeSkill('frontend', index)" class="hover:text-green-900">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </span>
                </div>
            </div>

            <!-- DevOps Skills -->
            <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-sm font-bold text-slate-700">DevOps</label>
                    <button @click="addSkill('devops')"
                        class="text-indigo-600 hover:text-indigo-700 text-xs font-bold">+ Add</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span v-for="(skill, index) in skills.devops" :key="index"
                        class="inline-flex items-center gap-2 bg-orange-50 text-orange-700 px-3 py-1 rounded-full text-xs font-medium">
                        {{ skill }}
                        <button @click="removeSkill('devops', index)" class="hover:text-orange-900">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </span>
                </div>
            </div>

            <!-- Other Skills -->
            <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-sm font-bold text-slate-700">Other</label>
                    <button @click="addSkill('other')"
                        class="text-indigo-600 hover:text-indigo-700 text-xs font-bold">+ Add</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span v-for="(skill, index) in skills.other" :key="index"
                        class="inline-flex items-center gap-2 bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-medium">
                        {{ skill }}
                        <button @click="removeSkill('other', index)" class="hover:text-purple-900">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
