<script setup>

const config = useRuntimeConfig()

// Auth
const { isAuthenticated, fetchUser } = useAuth()
const showAuthModal = ref(false)

// Check authentication on mount
onMounted(async () => {
  await fetchUser()
  if (!isAuthenticated.value) {
    showAuthModal.value = true
  }
})

// State
const activeTab = ref('upload')
const sidebarOpen = ref(false)
const previewModalOpen = ref(false)
const lastSaved = ref('2m ago')

// Form Data - Comprehensive Resume Structure
const formData = ref({
  fullName: '',
  title: '',
  email: '',
  phone: '',
  location: '',
  linkedin: '',
  github: '',
  portfolio: '',
  summary: '',
  skills: {
    backend: [],
    frontend: [],
    devops: [],
    other: []
  },
  experience: [
    {
      id: 1,
      position: '',
      company: '',
      location: '',
      startDate: '',
      endDate: '',
      current: false,
      responsibilities: ['']
    }
  ],
  education: [
    {
      id: 1,
      degree: '',
      institution: '',
      year: '',
      percentage: ''
    }
  ],
  achievements: ['']
})

// Loading States
const isProcessing = ref(false)
const uploadError = ref('')
const successMessage = ref('')

// Input Modes
const resumeInputMode = ref('upload')
const jobInputMode = ref('paste')
const resumeText = ref('')
const jobDescription = ref('')

// AI Settings
const atsScore = ref(0)
const selectedTone = ref('Professional')
const tones = ['Professional', 'Creative', 'Direct']

// ATS Analysis Results
const atsAnalysisCompleted = ref(false)
const scoreGrade = ref('Not Analyzed')
const experienceLevel = ref('Not Detected')
const keywordMatchPercentage = ref(0)
const wordCount = ref(0)
const criticalIssuesCount = ref(0)
const matchedKeywords = ref([])
const missingKeywords = ref([])
const formattingChecks = ref([])
const criticalIssues = ref([])
const contentAnalysis = ref({
  action_verbs_percentage: 0,
  quantifiable_results_percentage: 0,
  avg_bullet_length: 0,
  reading_level: 'Unknown'
})

// Page management
const currentPage = ref(1)
const totalPages = ref(1)

// Templates from JSON
const templatesData = await import('~/data/templates.json').then(m => m.default || m)
const templatesFromJSON = ref(templatesData)
const selectedTemplate = ref(templatesData[0]?.id || 'software-engineer')

// Colors
const selectedColor = ref('indigo')
const customColor = ref('')
const colorPalettes = ref([
  { id: 'indigo', name: 'Royal Indigo', category: 'Default Corporate', hex: '#4f46e5' },
  { id: 'emerald', name: 'Growth Emerald', category: 'Finance & Healthcare', hex: '#059669' },
  { id: 'rose', name: 'Passion Rose', category: 'Creative & NGO', hex: '#e11d48' },
  { id: 'slate', name: 'Classic Slate', category: 'Modern Minimalist', hex: '#334155' },
  { id: 'amber', name: 'Solar Amber', category: 'High Energy & Sales', hex: '#d97706' },
  { id: 'violet', name: 'Deep Violet', category: 'Luxury & Visionary', hex: '#7c3aed' }
])

// Personal Fields
const personalFields = ref([
  { id: 'fullName', label: 'Full Name', type: 'text', placeholder: 'e.g. Suraj Sharma' },
  { id: 'title', label: 'Professional Title', type: 'text', placeholder: 'e.g. Senior Full-Stack Developer' },
  { id: 'email', label: 'Email Address', type: 'email', placeholder: 'surajkumarsharma123@gmail.com' },
  { id: 'phone', label: 'Phone Number', type: 'tel', placeholder: '+91 7042611736' },
  { id: 'location', label: 'Location / Address', type: 'text', placeholder: 'Sahibabad, Ghaziabad' },
  { id: 'linkedin', label: 'LinkedIn Profile', type: 'url', placeholder: 'linkedin.com/in/yourprofile' },
  { id: 'github', label: 'GitHub Profile', type: 'url', placeholder: 'github.com/yourusername' },
  { id: 'portfolio', label: 'Portfolio Website', type: 'url', placeholder: 'yourwebsite.com' }
])

// Tabs
const tabs = ref([
  { id: 'upload', name: 'Import & Job', icon: 'fa-cloud-arrow-up' },
  { id: 'manual', name: 'Manual Info', icon: 'fa-pen-to-square' },
  { id: 'ai', name: 'AI Assistant', icon: 'fa-wand-magic-sparkles' },
  { id: 'templates', name: 'Templates', icon: 'fa-layer-group' },
  { id: 'colors', name: 'Color Palette', icon: 'fa-palette' }
])

// Computed
const currentAccentColor = computed(() => {
  if (customColor.value) return customColor.value
  const palette = colorPalettes.value.find(p => p.id === selectedColor.value)
  return palette ? palette.hex : '#4f46e5'
})

const scoreCircleDashoffset = computed(() => {
  const circumference = 264
  return circumference - (atsScore.value / 100) * circumference
})

const previewKey = computed(() => {
  return JSON.stringify({
    fullName: formData.value.fullName,
    title: formData.value.title,
    email: formData.value.email,
    template: selectedTemplate.value
  })
})

// Methods
const switchTab = (tabId) => {
  activeTab.value = tabId
  if (process.client && window.innerWidth < 1024) {
    sidebarOpen.value = false
    document.body.style.overflow = ''
  }
}

const toggleMobileSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
  if (process.client) {
    document.body.style.overflow = sidebarOpen.value ? 'hidden' : ''
  }
}

const openMobilePreview = () => {
  previewModalOpen.value = true
  if (process.client) document.body.style.overflow = 'hidden'
}

const closeMobilePreview = () => {
  previewModalOpen.value = false
  if (process.client) document.body.style.overflow = ''
}

const selectTemplate = (templateId) => {
  selectedTemplate.value = templateId
  const template = templatesFromJSON.value.find(t => t.id === templateId)
  successMessage.value = `✓ Applied ${template?.name || 'template'}`
  setTimeout(() => successMessage.value = '', 2000)
}

const selectColor = (color) => {
  selectedColor.value = color.id
  customColor.value = ''
}

const handleCustomColor = () => {
  selectedColor.value = 'custom'
}

const handleResumeUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  isProcessing.value = true
  uploadError.value = ''
  successMessage.value = ''

  try {
    const formDataObj = new FormData()
    formDataObj.append('file', file)

    const data = await $fetch(`${config.public.apiBase}/api/extract-text`, {
      method: 'POST',
      body: formDataObj
    })

    if (data.text) {
      resumeText.value = data.text
      parseResumeContent(data.text)
      successMessage.value = 'Resume uploaded and content extracted successfully!'
      setTimeout(() => successMessage.value = '', 3000)
    }
  } catch (error) {
    console.error('Resume upload error:', error)
    uploadError.value = error.data?.error || 'Failed to extract text from resume'
  } finally {
    isProcessing.value = false
  }
}

const parseResumeContent = (text) => {
  if (!text) return

  const emailMatch = text.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/)
  if (emailMatch) formData.value.email = emailMatch[0]

  const phoneMatch = text.match(/(?:\+?91[-\s]?)?[6-9]\d{9}|(?:\+?1[-.]?)?(?:\(?\d{3}\)?[-.]?)?\d{3}[-.]?\d{4}/)
  if (phoneMatch) formData.value.phone = phoneMatch[0]

  const lines = text.split('\n').map(l => l.trim()).filter(l => l)
  if (lines.length > 0) {
    const firstLine = lines[0]
    if (firstLine.length < 50 && /[A-Z]/.test(firstLine)) {
      formData.value.fullName = firstLine
    }
  }

  successMessage.value = '✓ Resume content extracted!'
  setTimeout(() => successMessage.value = '', 3000)
}

const addExperience = () => {
  formData.value.experience.push({
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

const removeExperience = (index) => {
  if (formData.value.experience.length > 1) {
    formData.value.experience.splice(index, 1)
  }
}

const addResponsibility = (expIndex) => {
  formData.value.experience[expIndex].responsibilities.push('')
}

const removeResponsibility = (expIndex, respIndex) => {
  if (formData.value.experience[expIndex].responsibilities.length > 1) {
    formData.value.experience[expIndex].responsibilities.splice(respIndex, 1)
  }
}

const addEducation = () => {
  formData.value.education.push({
    id: Date.now(),
    degree: '',
    institution: '',
    year: '',
    percentage: ''
  })
}

const removeEducation = (index) => {
  if (formData.value.education.length > 1) {
    formData.value.education.splice(index, 1)
  }
}

const addSkill = (category) => {
  if (process.client) {
    const skill = prompt(`Enter new ${category} skill:`)
    if (skill && skill.trim()) {
      formData.value.skills[category].push(skill.trim())
    }
  }
}

const removeSkill = (category, index) => {
  formData.value.skills[category].splice(index, 1)
}

const addAchievement = () => {
  formData.value.achievements.push('')
}

const removeAchievement = (index) => {
  if (formData.value.achievements.length > 1) {
    formData.value.achievements.splice(index, 1)
  }
}

const exportPDF = () => {
  if (process.client) {
    window.print()
  }
}

useHead({
  title: 'Resume Builder'
})
</script>

<template>
  <!-- Auth Required Modal -->
  <AuthRequiredModal v-if="showAuthModal" @close="showAuthModal = false" />

  <div class="builder-app bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex flex-col">
    
    <!-- Builder Action Bar -->
    <div class="h-14 bg-slate-900 text-white flex items-center justify-between px-4 z-40 flex-shrink-0 sticky top-0">
      <div class="flex items-center gap-3">
        <button @click="toggleMobileSidebar" class="lg:hidden w-8 h-8 flex items-center justify-center text-white hover:bg-slate-800 rounded-lg transition-all">
          <i class="fa-solid fa-bars text-base"></i>
        </button>
        <h1 class="font-bold text-sm sm:text-base">Resume Builder</h1>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-xs font-medium text-slate-400 hidden sm:block italic">Draft saved {{ lastSaved }}</span>
        <button @click="exportPDF" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 sm:px-4 py-1.5 rounded-lg font-bold text-xs transition-all flex items-center gap-2">
          <i class="fa-solid fa-download text-[10px]"></i>
          <span class="hidden sm:inline">Export PDF</span>
          <span class="sm:hidden">Export</span>
        </button>
      </div>
    </div>

    <div class="flex flex-1 overflow-hidden lg:flex-row">
      
      <!-- Icon-Only Sidebar -->
      <aside 
        id="mobile-sidebar"
        :class="['w-16 bg-white border-r border-slate-200 flex flex-col items-center py-6 gap-6 z-40 fixed left-0 top-28 bottom-0 transition-transform duration-300 lg:relative lg:translate-x-0 lg:top-0', { 'mobile-sidebar-hidden': !sidebarOpen }]">
        <div class="flex flex-col gap-4">
          <div v-for="tab in tabs" :key="tab.id" class="nav-item">
            <button 
              @click="switchTab(tab.id)" 
              :class="['nav-btn w-10 h-10 flex items-center justify-center rounded-xl transition-all', activeTab === tab.id ? 'nav-btn-active bg-indigo-50 text-indigo-600' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600']">
              <i :class="`fa-solid ${tab.icon} text-base`"></i>
            </button>
            <div class="tooltip">{{ tab.name }}</div>
          </div>
        </div>
      </aside>

      <!-- Dynamic Workspace -->
      <main class="flex-1 flex overflow-hidden">
        
        <!-- Editor Content (Left) -->
        <section class="flex-1 lg:flex-[0.8] bg-white overflow-y-auto custom-scrollbar border-r border-slate-200 w-full">
          <div class="max-w-xl mx-auto py-6 sm:py-10 px-4 sm:px-6 pb-24 lg:pb-10">
            
            <!-- Tab: Upload & Job Context -->
            <div v-show="activeTab === 'upload'" class="space-y-6">
              <h2 class="text-2xl font-black text-slate-900 mb-4">Import Resume & Job Description</h2>
              
              <!-- Success/Error Messages -->
              <div v-if="successMessage" class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm font-medium">
                {{ successMessage }}
              </div>
              
              <div v-if="uploadError" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm font-medium">
                {{ uploadError }}
              </div>
              
              <!-- Resume Upload -->
              <div class="space-y-3">
                <h3 class="font-bold text-slate-900">Your Resume</h3>
                <div class="flex gap-2">
                  <button 
                    @click="resumeInputMode = 'upload'" 
                    :class="['px-4 py-2 rounded-lg font-bold text-sm transition-colors', resumeInputMode === 'upload' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600']">
                    Upload File
                  </button>
                  <button 
                    @click="resumeInputMode = 'paste'" 
                    :class="['px-4 py-2 rounded-lg font-bold text-sm transition-colors', resumeInputMode === 'paste' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600']">
                    Paste Text
                  </button>
                </div>
                
                <div v-if="resumeInputMode === 'upload'" class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:border-indigo-500 transition-colors">
                  <input 
                    type="file" 
                    @change="handleResumeUpload" 
                    accept=".pdf,.docx" 
                    class="hidden" 
                    id="resume-upload">
                  <label for="resume-upload" class="cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-slate-400 mb-2"></i>
                    <p class="text-sm text-slate-600 font-medium">Click to upload or drag and drop</p>
                    <p class="text-xs text-slate-500 mt-1">PDF or DOCX (Max 10MB)</p>
                  </label>
                </div>
                
                <div v-else class="space-y-2">
                  <textarea 
                    v-model="resumeText" 
                    rows="8" 
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="Paste your resume text here..."></textarea>
                  <button 
                    @click="parseResumeContent(resumeText)" 
                    :disabled="!resumeText || isProcessing"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold text-sm transition-colors disabled:opacity-50">
                    {{ isProcessing ? 'Processing...' : 'Parse Resume' }}
                  </button>
                </div>
              </div>
              
              <!-- Job Description -->
              <div class="space-y-3">
                <h3 class="font-bold text-slate-900">Job Description (Optional)</h3>
                <textarea 
                  v-model="jobDescription" 
                  rows="6" 
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                  placeholder="Paste the job description here for AI optimization..."></textarea>
              </div>
            </div>

            <!-- Tab: Manual Info -->
            <div v-show="activeTab === 'manual'" class="space-y-8">
              <h2 class="text-2xl font-black text-slate-900 mb-4">Manual Information</h2>
              
              <!-- Personal Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-900">Personal Information</h3>
                <div v-for="field in personalFields" :key="field.id" class="space-y-2">
                  <label class="block text-sm font-bold text-slate-700">{{ field.label }}</label>
                  <input 
                    v-model="formData[field.id]" 
                    :type="field.type" 
                    :placeholder="field.placeholder"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
              </div>
              
              <!-- Professional Summary -->
              <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-900">Professional Summary</h3>
                <textarea 
                  v-model="formData.summary" 
                  rows="6" 
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                  placeholder="Write a compelling professional summary..."></textarea>
              </div>
              
              <!-- Skills -->
              <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-900">Skills</h3>
                <div v-for="(skillsArray, category) in formData.skills" :key="category" class="space-y-2">
                  <div class="flex justify-between items-center">
                    <label class="block text-sm font-bold text-slate-700 capitalize">{{ category }}</label>
                    <button 
                      @click="addSkill(category)" 
                      class="text-xs text-indigo-600 hover:text-indigo-700 font-bold">
                      + Add Skill
                    </button>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <span 
                      v-for="(skill, index) in skillsArray" 
                      :key="index" 
                      class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium flex items-center gap-2">
                      {{ skill }}
                      <button @click="removeSkill(category, index)" class="text-indigo-600 hover:text-indigo-800">
                        <i class="fa-solid fa-times text-xs"></i>
                      </button>
                    </span>
                  </div>
                </div>
              </div>
              
              <!-- Work Experience -->
              <div class="space-y-4">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-bold text-slate-900">Work Experience</h3>
                  <button @click="addExperience" class="text-sm text-indigo-600 hover:text-indigo-700 font-bold">
                    + Add Experience
                  </button>
                </div>
                <div v-for="(exp, expIndex) in formData.experience" :key="exp.id" class="p-4 border border-slate-200 rounded-lg space-y-3">
                  <div class="flex justify-between items-start">
                    <h4 class="font-bold text-slate-900">Experience {{ expIndex + 1 }}</h4>
                    <button @click="removeExperience(expIndex)" class="text-red-600 hover:text-red-700">
                      <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                  </div>
                  <input 
                    v-model="exp.position" 
                    type="text" 
                    placeholder="Position" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm">
                  <input 
                    v-model="exp.company" 
                    type="text" 
                    placeholder="Company" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm">
                  <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">Responsibilities</label>
                    <div v-for="(resp, respIndex) in exp.responsibilities" :key="respIndex" class="flex gap-2">
                      <input 
                        v-model="exp.responsibilities[respIndex]" 
                        type="text" 
                        placeholder="Responsibility" 
                        class="flex-1 px-4 py-2 border border-slate-300 rounded-lg text-sm">
                      <button @click="removeResponsibility(expIndex, respIndex)" class="text-red-600 hover:text-red-700">
                        <i class="fa-solid fa-minus"></i>
                      </button>
                    </div>
                    <button @click="addResponsibility(expIndex)" class="text-xs text-indigo-600 hover:text-indigo-700 font-bold">
                      + Add Responsibility
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- Education -->
              <div class="space-y-4">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-bold text-slate-900">Education</h3>
                  <button @click="addEducation" class="text-sm text-indigo-600 hover:text-indigo-700 font-bold">
                    + Add Education
                  </button>
                </div>
                <div v-for="(edu, eduIndex) in formData.education" :key="edu.id" class="p-4 border border-slate-200 rounded-lg space-y-3">
                  <div class="flex justify-between items-start">
                    <h4 class="font-bold text-slate-900">Education {{ eduIndex + 1 }}</h4>
                    <button @click="removeEducation(eduIndex)" class="text-red-600 hover:text-red-700">
                      <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                  </div>
                  <input 
                    v-model="edu.degree" 
                    type="text" 
                    placeholder="Degree" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm">
                  <input 
                    v-model="edu.institution" 
                    type="text" 
                    placeholder="Institution" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm">
                </div>
              </div>
              
              <!-- Achievements -->
              <div class="space-y-4">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-bold text-slate-900">Achievements</h3>
                  <button @click="addAchievement" class="text-sm text-indigo-600 hover:text-indigo-700 font-bold">
                    + Add Achievement
                  </button>
                </div>
                <div v-for="(achievement, achIndex) in formData.achievements" :key="achIndex" class="flex gap-2">
                  <input 
                    v-model="formData.achievements[achIndex]" 
                    type="text" 
                    placeholder="Achievement" 
                    class="flex-1 px-4 py-2 border border-slate-300 rounded-lg text-sm">
                  <button @click="removeAchievement(achIndex)" class="text-red-600 hover:text-red-700">
                    <i class="fa-solid fa-trash text-sm"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Tab: AI Assistant -->
            <div v-show="activeTab === 'ai'" class="space-y-6">
              <h2 class="text-2xl font-black text-slate-900 mb-4">AI Assistant</h2>
              <p class="text-sm text-slate-600">Use AI to optimize your resume content</p>
              
              <div class="space-y-4">
                <button class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-4 rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2">
                  <i class="fa-solid fa-wand-magic-sparkles"></i>
                  Generate Complete Resume
                </button>
                
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-4 rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2">
                  <i class="fa-solid fa-robot"></i>
                  Optimize for ATS
                </button>
                
                <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-4 rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2">
                  <i class="fa-solid fa-list"></i>
                  Improve Bullet Points
                </button>
              </div>
            </div>

            <!-- Tab: Templates -->
            <div v-show="activeTab === 'templates'" class="space-y-6">
              <h2 class="text-2xl font-black text-slate-900 mb-4">Choose Template</h2>
              
              <div class="grid grid-cols-2 gap-4">
                <div 
                  v-for="template in templatesFromJSON" 
                  :key="template.id" 
                  @click="selectTemplate(template.id)"
                  :class="['template-card border-2 rounded-lg p-4 cursor-pointer transition-all hover:shadow-lg', selectedTemplate === template.id ? 'template-card-active border-indigo-600' : 'border-slate-200']">
                  <div class="aspect-[3/4] bg-slate-100 rounded mb-2"></div>
                  <h3 class="font-bold text-sm text-slate-900">{{ template.name }}</h3>
                  <p class="text-xs text-slate-600">{{ template.type }}</p>
                </div>
              </div>
            </div>

            <!-- Tab: Colors -->
            <div v-show="activeTab === 'colors'" class="space-y-6">
              <h2 class="text-2xl font-black text-slate-900 mb-4">Color Palette</h2>
              
              <div class="grid grid-cols-2 gap-4">
                <div 
                  v-for="color in colorPalettes" 
                  :key="color.id" 
                  @click="selectColor(color)"
                  :class="['color-card border-2 rounded-lg p-4 cursor-pointer transition-all hover:shadow-lg', selectedColor === color.id ? 'color-card-active border-indigo-600' : 'border-slate-200']">
                  <div :style="{ backgroundColor: color.hex }" class="w-full h-16 rounded mb-2"></div>
                  <h3 class="font-bold text-sm text-slate-900">{{ color.name }}</h3>
                  <p class="text-xs text-slate-600">{{ color.category }}</p>
                </div>
              </div>
              
              <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-700">Custom Color</label>
                <input 
                  v-model="customColor" 
                  type="color" 
                  @input="handleCustomColor"
                  class="w-full h-12 rounded-lg cursor-pointer">
              </div>
            </div>

          </div>
        </section>

        <!-- Preview Panel (Right) -->
        <section class="hidden lg:block lg:flex-[1.2] bg-slate-100 overflow-y-auto custom-scrollbar">
          <div class="p-8">
            <div class="bg-white rounded-lg shadow-lg p-8 min-h-[1056px]">
              <ClientOnly>
                <div class="space-y-6">
                  <div class="text-center border-b pb-4">
                    <h1 class="text-3xl font-bold">{{ formData.fullName || 'Your Name' }}</h1>
                    <p class="text-lg text-slate-600">{{ formData.title || 'Professional Title' }}</p>
                    <p class="text-sm text-slate-600 mt-2">
                      {{ formData.email }} • {{ formData.phone }} • {{ formData.location }}
                    </p>
                  </div>

                  <div v-if="formData.summary">
                    <h2 class="text-xl font-bold mb-2">Professional Summary</h2>
                    <p class="text-sm text-slate-700">{{ formData.summary }}</p>
                  </div>

                  <div v-if="Object.values(formData.skills).some(arr => arr.length > 0)">
                    <h2 class="text-xl font-bold mb-2">Skills</h2>
                    <div v-for="(skillsArray, category) in formData.skills" :key="category">
                      <div v-if="skillsArray.length > 0" class="mb-2">
                        <h3 class="text-sm font-bold capitalize text-slate-900">{{ category }}</h3>
                        <p class="text-sm text-slate-700">{{ skillsArray.join(', ') }}</p>
                      </div>
                    </div>
                  </div>

                  <div v-if="formData.experience.length > 0 && formData.experience[0].position">
                    <h2 class="text-xl font-bold mb-2">Experience</h2>
                    <div v-for="(exp, index) in formData.experience" :key="index" class="mb-4">
                      <h3 class="font-bold">{{ exp.position }}</h3>
                      <p class="text-sm text-slate-600">{{ exp.company }}</p>
                      <ul class="list-disc list-inside text-sm text-slate-700 mt-1">
                        <li v-for="(resp, rIndex) in exp.responsibilities" :key="rIndex">{{ resp }}</li>
                      </ul>
                    </div>
                  </div>

                  <div v-if="formData.education.length > 0 && formData.education[0].degree">
                    <h2 class="text-xl font-bold mb-2">Education</h2>
                    <div v-for="(edu, index) in formData.education" :key="index" class="mb-2">
                      <h3 class="font-bold">{{ edu.degree }}</h3>
                      <p class="text-sm text-slate-600">{{ edu.institution }}</p>
                    </div>
                  </div>
                </div>
              </ClientOnly>
            </div>
          </div>
        </section>

      </main>
    </div>

    <!-- Mobile Preview Modal -->
    <div v-if="previewModalOpen" class="mobile-preview-modal lg:hidden active">
      <div class="mobile-preview-content">
        <div class="p-4 bg-white border-b flex justify-between items-center sticky top-0 z-10">
          <h2 class="font-bold text-lg">Preview</h2>
          <button @click="closeMobilePreview" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200">
            <i class="fa-solid fa-times"></i>
          </button>
        </div>
        <div class="p-4">
          <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center border-b pb-4">
              <h1 class="text-2xl font-bold">{{ formData.fullName || 'Your Name' }}</h1>
              <p class="text-slate-600">{{ formData.title || 'Professional Title' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Floating Preview Button (Mobile Only) -->
    <button @click="openMobilePreview" class="preview-fab lg:hidden" aria-label="Preview Resume">
      <i class="fa-solid fa-eye"></i>
    </button>

    <!-- Mobile Sidebar Overlay -->
    <div v-if="sidebarOpen" @click="toggleMobileSidebar" class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-30" style="top: 112px;"></div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}

.preview-container {
  background-color: #f8fafc;
  background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
  background-size: 24px 24px;
}

.nav-item {
  position: relative;
}

.tooltip {
  position: absolute;
  left: 100%;
  top: 50%;
  transform: translateY(-50%) translateX(10px);
  background: #1e293b;
  color: white;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  transition: all 0.2s ease;
  pointer-events: none;
  z-index: 100;
}

.nav-item:hover .tooltip {
  opacity: 1;
  visibility: visible;
  transform: translateY(-50%) translateX(15px);
}

.tooltip::before {
  content: '';
  position: absolute;
  right: 100%;
  top: 50%;
  margin-top: -4px;
  border-width: 4px;
  border-style: solid;
  border-color: transparent #1e293b transparent transparent;
}

.nav-btn-active {
  background-color: #eff6ff;
  color: #4f46e5;
  box-shadow: inset 0 0 0 1px #e0e7ff;
}

.template-card-active,
.color-card-active {
  border-color: #4f46e5 !important;
  box-shadow: 0 0 0 2px #4f46e5;
}

@media (max-width: 1023px) {
  .mobile-sidebar-hidden {
    transform: translateX(-100%);
  }

  .mobile-preview-modal {
    position: fixed;
    inset: 0;
    z-index: 60;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: none;
  }

  .mobile-preview-modal.active {
    display: flex;
  }

  .mobile-preview-content {
    background: white;
    width: 100%;
    height: 100%;
    overflow: auto;
    animation: slideUp 0.3s ease-out;
  }

  @keyframes slideUp {
    from {
      transform: translateY(100%);
    }
    to {
      transform: translateY(0);
    }
  }
}

.preview-fab {
  position: fixed;
  bottom: 80px;
  right: 20px;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  box-shadow: 0 8px 24px rgba(79, 70, 229, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 20px;
  z-index: 50;
  cursor: pointer;
  transition: all 0.3s ease;
}

.preview-fab:hover {
  transform: scale(1.1);
  box-shadow: 0 12px 32px rgba(79, 70, 229, 0.5);
}

.preview-fab:active {
  transform: scale(0.95);
}

@media (min-width: 1024px) {
  .preview-fab {
    display: none;
  }
}
</style>
