<script setup lang="ts">
// defineProps and defineEmits are auto-imported

interface Education {
  id: number | string
  degree: string
  institution: string
  year: string
  percentage: string
}

const props = defineProps<{
  education: Education[]
}>()

const emit = defineEmits<{
  (e: 'update:education', value: Education[]): void
}>()

const addEducation = () => {
  const newEducation = [
    ...props.education,
    {
      id: Date.now(),
      degree: '',
      institution: '',
      year: '',
      percentage: ''
    }
  ]
  emit('update:education', newEducation)
}

const removeEducation = (index: number) => {
  if (props.education.length > 1) {
    const newEducation = [...props.education]
    newEducation.splice(index, 1)
    emit('update:education', newEducation)
  }
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
        <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
        Education
      </h3>
      <button 
        @click="addEducation"
        class="text-sm font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1"
        type="button"
      >
        <i class="fa-solid fa-plus"></i>
        Add Education
      </button>
    </div>

    <div v-if="education.length === 0" class="text-center py-8 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
      <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-3">
        <i class="fa-solid fa-graduation-cap text-indigo-400 text-xl"></i>
      </div>
      <h4 class="text-sm font-bold text-slate-700">No Education Added</h4>
      <p class="text-xs text-slate-500 mt-1 mb-3">Add your degree or school details to show your qualifications.</p>
      <button 
        @click="addEducation"
        class="text-xs font-bold px-4 py-2 bg-white border border-slate-300 rounded-lg hover:border-indigo-400 hover:text-indigo-600 transition-all shadow-sm"
        type="button"
      >
        Add First Education
      </button>
    </div>

    <div v-for="(edu, index) in education" :key="edu.id" class="p-4 bg-slate-50 rounded-xl border border-slate-200 relative group animate-fade-in-up">
      <button 
        v-if="education.length > 1"
        @click="removeEducation(index)"
        class="absolute top-2 right-2 text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity p-2"
        type="button"
        title="Remove education"
      >
        <i class="fa-solid fa-trash-can"></i>
      </button>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1">Degree / Course</label>
          <input 
            v-model="edu.degree"
            type="text"
            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 placeholder-slate-400 transition-shadow"
            placeholder="e.g. Bachelor of Technology in Computer Science"
          />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1">University / Institution</label>
          <input 
            v-model="edu.institution"
            type="text"
            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 placeholder-slate-400 transition-shadow"
            placeholder="e.g. Indian Institute of Technology, Delhi"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Year of Passing</label>
          <input 
            v-model="edu.year"
            type="text"
            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 placeholder-slate-400 transition-shadow"
            placeholder="e.g. 2024"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Percentage / CGPA</label>
          <input 
            v-model="edu.percentage"
            type="text"
            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 placeholder-slate-400 transition-shadow"
            placeholder="e.g. 9.5 CGPA"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
