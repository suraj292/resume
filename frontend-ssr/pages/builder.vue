<script setup>
definePageMeta({
  middleware: ['auth']
})

const config = useRuntimeConfig()

// Replace localStorage with useState for SSR compatibility
const resumeData = useState('resumeData', () => ({
  personalInfo: {
    name: '',
    email: '',
    phone: '',
    location: '',
    linkedin: '',
    website: ''
  },
  summary: '',
  experience: [],
  education: [],
  skills: {
    technical: [],
    soft: []
  },
  certifications: [],
  projects: []
}))

const selectedTemplate = useState('selectedTemplate', () => 'modern')
const selectedColor = useState('selectedColor', () => 'indigo')
const activeTab = ref('personal')
const loading = ref(false)
const uploadProgress = ref(0)

// File upload (client-only)
const handleFileUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  loading.value = true
  uploadProgress.value = 0

  try {
    const formData = new FormData()
    formData.append('file', file)

    const data = await $fetch('/api/extract-text', {
      method: 'POST',
      baseURL: config.public.apiBase,
      body: formData,
      onUploadProgress: (progressEvent) => {
        uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
      }
    })

    if (data.resumeData) {
      resumeData.value = { ...resumeData.value, ...data.resumeData }
    }
  } catch (error) {
    console.error('File upload failed:', error)
    alert('Failed to upload file. Please try again.')
  } finally {
    loading.value = false
    uploadProgress.value = 0
  }
}

// AI Generation (client-only)
const generateWithAI = async () => {
  loading.value = true
  try {
    const data = await $fetch('/api/ai/optimize-resume', {
      method: 'POST',
      baseURL: config.public.apiBase,
      body: {
        resumeData: resumeData.value,
        targetRole: 'Software Engineer'
      }
    })

    if (data.optimizedData) {
      resumeData.value = { ...resumeData.value, ...data.optimizedData }
    }
  } catch (error) {
    console.error('AI generation failed:', error)
    alert('AI optimization failed. Please try again.')
  } finally {
    loading.value = false
  }
}

// Download PDF (client-only)
const downloadPDF = () => {
  if (process.client) {
    // Trigger PDF generation
    window.print()
  }
}

// Add experience entry
const addExperience = () => {
  resumeData.value.experience.push({
    position: '',
    company: '',
    location: '',
    startDate: '',
    endDate: '',
    current: false,
    description: ''
  })
}

// Remove experience entry
const removeExperience = (index) => {
  resumeData.value.experience.splice(index, 1)
}

// Add education entry
const addEducation = () => {
  resumeData.value.education.push({
    degree: '',
    institution: '',
    location: '',
    graduationDate: '',
    gpa: ''
  })
}

// Remove education entry
const removeEducation = (index) => {
  resumeData.value.education.splice(index, 1)
}

useHead({
  title: 'Resume Builder'
})
</script>

<template>
  <div class="min-h-screen bg-slate-50">
    <div class="container mx-auto px-6 py-8">
      <div class="grid lg:grid-cols-2 gap-8">
        <!-- Editor Panel -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h1 class="text-3xl font-black mb-6">Resume Builder</h1>

          <!-- File Upload -->
          <div class="mb-6">
            <label class="block w-full py-4 border-2 border-dashed border-slate-300 rounded-lg text-center cursor-pointer hover:border-indigo-500 transition-colors">
              <input 
                type="file"
                accept=".pdf,.docx"
                @change="handleFileUpload"
                class="hidden"
              >
              <i class="fa-solid fa-upload text-2xl text-slate-400 mb-2"></i>
              <p class="text-sm text-slate-600">Upload Resume (PDF/DOCX)</p>
              <p v-if="loading" class="text-xs text-indigo-600 mt-2">Uploading... {{ uploadProgress }}%</p>
            </label>
          </div>

          <!-- Tabs -->
          <div class="flex gap-2 mb-6 overflow-x-auto">
            <button 
              @click="activeTab = 'personal'"
              :class="activeTab === 'personal' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'"
              class="px-4 py-2 rounded-lg font-bold transition-colors whitespace-nowrap"
            >
              Personal Info
            </button>
            <button 
              @click="activeTab = 'summary'"
              :class="activeTab === 'summary' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'"
              class="px-4 py-2 rounded-lg font-bold transition-colors whitespace-nowrap"
            >
              Summary
            </button>
            <button 
              @click="activeTab = 'experience'"
              :class="activeTab === 'experience' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'"
              class="px-4 py-2 rounded-lg font-bold transition-colors whitespace-nowrap"
            >
              Experience
            </button>
            <button 
              @click="activeTab = 'education'"
              :class="activeTab === 'education' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'"
              class="px-4 py-2 rounded-lg font-bold transition-colors whitespace-nowrap"
            >
              Education
            </button>
            <button 
              @click="activeTab = 'skills'"
              :class="activeTab === 'skills' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'"
              class="px-4 py-2 rounded-lg font-bold transition-colors whitespace-nowrap"
            >
              Skills
            </button>
          </div>

          <!-- Form Sections -->
          <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
            <!-- Personal Info Tab -->
            <div v-if="activeTab === 'personal'" class="space-y-4">
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                <input 
                  v-model="resumeData.personalInfo.name"
                  type="text"
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                  placeholder="John Doe"
                >
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                <input 
                  v-model="resumeData.personalInfo.email"
                  type="email"
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                  placeholder="john@example.com"
                >
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Phone</label>
                <input 
                  v-model="resumeData.personalInfo.phone"
                  type="tel"
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                  placeholder="+1 (555) 000-0000"
                >
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Location</label>
                <input 
                  v-model="resumeData.personalInfo.location"
                  type="text"
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                  placeholder="San Francisco, CA"
                >
              </div>
            </div>

            <!-- Summary Tab -->
            <div v-if="activeTab === 'summary'" class="space-y-4">
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Professional Summary</label>
                <textarea 
                  v-model="resumeData.summary"
                  rows="6"
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                  placeholder="Write a brief summary of your professional background..."
                ></textarea>
              </div>
            </div>

            <!-- Experience Tab -->
            <div v-if="activeTab === 'experience'" class="space-y-4">
              <div v-for="(exp, index) in resumeData.experience" :key="index" class="p-4 border border-slate-200 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="font-bold text-slate-900">Experience {{ index + 1 }}</h3>
                  <button @click="removeExperience(index)" class="text-red-600 hover:text-red-700">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </div>
                <div class="space-y-3">
                  <input 
                    v-model="exp.position"
                    type="text"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg"
                    placeholder="Job Title"
                  >
                  <input 
                    v-model="exp.company"
                    type="text"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg"
                    placeholder="Company Name"
                  >
                  <textarea 
                    v-model="exp.description"
                    rows="3"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg"
                    placeholder="Job description..."
                  ></textarea>
                </div>
              </div>
              <button @click="addExperience" class="w-full py-3 border-2 border-dashed border-slate-300 rounded-lg text-slate-600 hover:border-indigo-500 hover:text-indigo-600 transition-colors">
                <i class="fa-solid fa-plus mr-2"></i> Add Experience
              </button>
            </div>

            <!-- Education Tab -->
            <div v-if="activeTab === 'education'" class="space-y-4">
              <div v-for="(edu, index) in resumeData.education" :key="index" class="p-4 border border-slate-200 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="font-bold text-slate-900">Education {{ index + 1 }}</h3>
                  <button @click="removeEducation(index)" class="text-red-600 hover:text-red-700">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </div>
                <div class="space-y-3">
                  <input 
                    v-model="edu.degree"
                    type="text"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg"
                    placeholder="Degree"
                  >
                  <input 
                    v-model="edu.institution"
                    type="text"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg"
                    placeholder="Institution"
                  >
                </div>
              </div>
              <button @click="addEducation" class="w-full py-3 border-2 border-dashed border-slate-300 rounded-lg text-slate-600 hover:border-indigo-500 hover:text-indigo-600 transition-colors">
                <i class="fa-solid fa-plus mr-2"></i> Add Education
              </button>
            </div>

            <!-- Skills Tab -->
            <div v-if="activeTab === 'skills'" class="space-y-4">
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Technical Skills (comma-separated)</label>
                <input 
                  :value="resumeData.skills.technical.join(', ')"
                  @input="resumeData.skills.technical = $event.target.value.split(',').map(s => s.trim())"
                  type="text"
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                  placeholder="JavaScript, Python, React, Node.js"
                >
              </div>
            </div>
          </div>

          <!-- AI Actions -->
          <div class="mt-8 flex gap-4">
            <button 
              @click="generateWithAI"
              :disabled="loading"
              class="flex-1 py-3 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 transition-colors disabled:opacity-50"
            >
              <i class="fa-solid fa-wand-magic-sparkles mr-2"></i>
              {{ loading ? 'Optimizing...' : 'AI Optimize' }}
            </button>
            <button 
              @click="downloadPDF"
              class="flex-1 py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition-colors"
            >
              <i class="fa-solid fa-download mr-2"></i>
              Download PDF
            </button>
          </div>
        </div>

        <!-- Preview Panel -->
        <div class="bg-white rounded-2xl shadow-lg p-8 sticky top-8">
          <h2 class="text-2xl font-black mb-6">Preview</h2>
          <ClientOnly>
            <div class="bg-white border border-slate-200 rounded-lg p-8 shadow-sm">
              <!-- Simple Preview -->
              <div class="space-y-6">
                <div class="text-center border-b pb-4">
                  <h1 class="text-2xl font-bold">{{ resumeData.personalInfo.name || 'Your Name' }}</h1>
                  <p class="text-slate-600">{{ resumeData.personalInfo.email }}</p>
                  <p class="text-slate-600 text-sm">{{ resumeData.personalInfo.phone }} • {{ resumeData.personalInfo.location }}</p>
                </div>

                <div v-if="resumeData.summary">
                  <h2 class="text-lg font-bold mb-2">Summary</h2>
                  <p class="text-sm text-slate-700">{{ resumeData.summary }}</p>
                </div>

                <div v-if="resumeData.experience.length > 0">
                  <h2 class="text-lg font-bold mb-2">Experience</h2>
                  <div v-for="(exp, index) in resumeData.experience" :key="index" class="mb-4">
                    <h3 class="font-semibold">{{ exp.position }}</h3>
                    <p class="text-sm text-slate-600">{{ exp.company }}</p>
                    <p class="text-sm text-slate-700 mt-1">{{ exp.description }}</p>
                  </div>
                </div>

                <div v-if="resumeData.education.length > 0">
                  <h2 class="text-lg font-bold mb-2">Education</h2>
                  <div v-for="(edu, index) in resumeData.education" :key="index" class="mb-2">
                    <h3 class="font-semibold">{{ edu.degree }}</h3>
                    <p class="text-sm text-slate-600">{{ edu.institution }}</p>
                  </div>
                </div>

                <div v-if="resumeData.skills.technical.length > 0">
                  <h2 class="text-lg font-bold mb-2">Skills</h2>
                  <div class="flex flex-wrap gap-2">
                    <span v-for="skill in resumeData.skills.technical" :key="skill" class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                      {{ skill }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </ClientOnly>
        </div>
      </div>
    </div>
  </div>
</template>
