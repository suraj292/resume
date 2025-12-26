<script setup>

const config = useRuntimeConfig()
const route = useRoute()
const router = useRouter()

// Auth
const { isAuthenticated, fetchUser } = useAuth()
const showAuthModal = ref(false)

// Check authentication on mount
onMounted(async () => {
  // Handle OAuth callback
  if (route.query.social_auth === 'success' && route.query.token) {
    // Store the token in localStorage
    const token = String(route.query.token)
    localStorage.setItem('auth_token', token)
    console.log('Token stored:', token)
    
    // Remove token from URL for security
    await router.replace({ query: {} })
    
    // Small delay to ensure localStorage is written
    await new Promise(resolve => setTimeout(resolve, 100))
    
    // Fetch user data
    const userData = await fetchUser()
    console.log('User data after OAuth:', userData)
  } else {
    await fetchUser()
  }
  
  if (!isAuthenticated.value) {
    console.log('Not authenticated, showing modal')
    showAuthModal.value = false
  } else {
    console.log('Authenticated successfully')
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

const hasSkills = computed(() => {
  return Object.values(formData.value.skills).some(arr => arr.length > 0)
})

const hasExperience = computed(() => {
  return formData.value.experience.length > 0 && formData.value.experience[0].position
})

// Multi-page logic
const needsSecondPage = computed(() => {
  // If we have more than 2 experience entries, we likely need a second page
  const experienceCount = formData.value.experience.filter(exp => exp.position).length
  return experienceCount > 2
})

const getExperienceCountForPage1 = () => {
  // Show first 2 experience entries on page 1, rest on page 2
  const experienceCount = formData.value.experience.filter(exp => exp.position).length
  if (experienceCount <= 2) return experienceCount
  return 2
}

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

    // Request parsed structured data by adding parse=true parameter
    const data = await $fetch(`${config.public.apiBase}/api/extract-text?parse=true`, {
      method: 'POST',
      body: formDataObj
    })

    if (data.text) {
      resumeText.value = data.text
      
      // If we have parsed data from Gemini AI, use it
      if (data.parsedData) {
        populateFormWithParsedData(data.parsedData)
        successMessage.value = 'Resume uploaded and parsed successfully! All fields have been populated.'
      } else {
        // Fallback to basic parsing
        parseResumeContent(data.text)
        successMessage.value = 'Resume uploaded successfully! Basic information extracted.'
      }
      
      setTimeout(() => successMessage.value = '', 5000)
    }
  } catch (error) {
    console.error('Resume upload error:', error)
    uploadError.value = error.data?.error || 'Failed to extract text from resume'
  } finally {
    isProcessing.value = false
  }
}

const populateFormWithParsedData = (parsedData) => {
  // Populate personal information
  if (parsedData.fullName) formData.value.fullName = parsedData.fullName
  if (parsedData.title) formData.value.title = parsedData.title
  if (parsedData.email) formData.value.email = parsedData.email
  if (parsedData.phone) formData.value.phone = parsedData.phone
  if (parsedData.location) formData.value.location = parsedData.location
  if (parsedData.linkedin) formData.value.linkedin = parsedData.linkedin
  if (parsedData.github) formData.value.github = parsedData.github
  if (parsedData.portfolio) formData.value.portfolio = parsedData.portfolio
  if (parsedData.summary) formData.value.summary = parsedData.summary

  // Populate skills
  if (parsedData.skills) {
    formData.value.skills = {
      backend: parsedData.skills.backend || [],
      frontend: parsedData.skills.frontend || [],
      devops: parsedData.skills.devops || [],
      other: parsedData.skills.other || []
    }
  }

  // Populate experience
  if (parsedData.experience && parsedData.experience.length > 0) {
    formData.value.experience = parsedData.experience.map((exp, index) => ({
      id: index + 1,
      position: exp.position || '',
      company: exp.company || '',
      location: exp.location || '',
      startDate: exp.startDate || '',
      endDate: exp.endDate || '',
      current: exp.current || false,
      responsibilities: exp.responsibilities || ['']
    }))
  }

  // Populate education
  if (parsedData.education && parsedData.education.length > 0) {
    formData.value.education = parsedData.education.map((edu, index) => ({
      id: index + 1,
      degree: edu.degree || '',
      institution: edu.institution || '',
      year: edu.year || '',
      percentage: edu.percentage || ''
    }))
  }

  // Populate achievements
  if (parsedData.achievements && parsedData.achievements.length > 0) {
    formData.value.achievements = parsedData.achievements
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
    <!-- Navigation -->
    <CommonNavbar />
    
    <!-- Builder Action Bar -->
    <div class="h-14 bg-slate-900 text-white flex items-center justify-between px-4 z-40 flex-shrink-0 sticky top-20">
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
            <div v-show="activeTab === 'upload'" class="tab-content">
              <header class="mb-8">
                <h2 class="text-xl font-display font-bold text-slate-800">Import & Context</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium italic">Provide background for the AI to tailor your resume specifically to the role.</p>
              </header>

              <div class="space-y-10">
                <!-- Resume Context Section -->
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Current Resume</h3>
                    <div class="flex bg-slate-100 p-0.5 rounded-lg">
                      <button @click="resumeInputMode = 'upload'"
                        :class="['px-3 py-1 text-[9px] font-bold rounded-md transition-all', resumeInputMode === 'upload' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500']">
                        Upload
                      </button>
                      <button @click="resumeInputMode = 'paste'"
                        :class="['px-3 py-1 text-[9px] font-bold rounded-md transition-all', resumeInputMode === 'paste' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500']">
                        Paste Text
                      </button>
                    </div>
                  </div>
                  
                  <!-- Upload Mode -->
                  <div v-show="resumeInputMode === 'upload'"
                    class="group relative border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center bg-slate-50/50 hover:border-indigo-400 hover:bg-indigo-50/30 transition-all cursor-pointer">
                    <input type="file" @change="handleResumeUpload" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf,.doc,.docx">
                    <div class="flex flex-col items-center pointer-events-none">
                      <i class="fa-solid fa-file-pdf text-indigo-400 text-2xl mb-3"></i>
                      <p class="text-sm font-bold text-slate-700">Drop your current resume</p>
                      <p class="text-[9px] text-slate-400 mt-1">PDF or Word</p>
                    </div>
                  </div>

                  <!-- Paste Mode -->
                  <div v-show="resumeInputMode === 'paste'"
                    class="bg-white border border-slate-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500/10 focus-within:border-indigo-500 transition-all">
                    <textarea v-model="resumeText" rows="4"
                      class="w-full p-4 text-xs outline-none resize-none placeholder:text-slate-300"
                      placeholder="Paste your existing resume text here..."></textarea>
                  </div>
                </div>

                <!-- Job Description Context Section -->
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Target Job Description</h3>
                    <div class="flex bg-slate-100 p-0.5 rounded-lg">
                      <button @click="jobInputMode = 'paste'"
                        :class="['px-3 py-1 text-[9px] font-bold rounded-md transition-all', jobInputMode === 'paste' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500']">
                        Paste Text
                      </button>
                      <button @click="jobInputMode = 'upload'"
                        :class="['px-3 py-1 text-[9px] font-bold rounded-md transition-all', jobInputMode === 'upload' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500']">
                        Upload File
                      </button>
                    </div>
                  </div>

                  <!-- Paste Mode (Default) -->
                  <div v-show="jobInputMode === 'paste'"
                    class="bg-white border border-slate-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500/10 focus-within:border-indigo-500 transition-all">
                    <textarea v-model="jobDescription" rows="6"
                      class="w-full p-4 text-xs outline-none resize-none placeholder:text-slate-300"
                      placeholder="Paste the job requirements..."></textarea>
                  </div>

                  <!-- Upload Mode -->
                  <div v-show="jobInputMode === 'upload'"
                    class="group relative border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center bg-slate-50/50 hover:border-indigo-400 hover:bg-indigo-50/30 transition-all cursor-pointer">
                    <input type="file" @change="handleJobUpload" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf,.doc,.docx,.txt">
                    <div class="flex flex-col items-center pointer-events-none">
                      <i class="fa-solid fa-briefcase text-slate-300 text-2xl mb-3"></i>
                      <p class="text-sm font-bold text-slate-700">Upload Job Ad / PDF</p>
                    </div>
                  </div>
                </div>

                <!-- Save Button -->
                <button @click="saveContext" :disabled="isProcessing"
                  class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                  <i class="fa-solid fa-bolt-lightning text-amber-400"></i>
                  {{ isProcessing ? 'Saving...' : 'Save Context for AI' }}
                </button>

                <!-- Success/Error Messages -->
                <div v-if="successMessage" class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-xs font-medium">
                  {{ successMessage }}
                </div>
                <div v-if="uploadError" class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs font-medium">
                  {{ uploadError }}
                </div>
              </div>
            </div>

            <!-- Tab: Manual Content -->
            <div v-show="activeTab === 'manual'" class="tab-content">
              <header class="mb-8">
                <h2 class="text-xl font-display font-bold text-slate-800">Personal Details</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium">Reorder your information blocks by dragging the handles <i class="fa-solid fa-grip-vertical mx-1"></i>.</p>
              </header>

              <div class="space-y-4">
                <!-- Full Name -->
                <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
                  <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Full Name</label>
                    <input v-model="formData.fullName" type="text" placeholder="e.g. John Doe"
                      class="w-full bg-transparent font-bold text-slate-800 outline-none text-base">
                  </div>
                </div>

                <!-- Professional Title -->
                <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
                  <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Professional Title</label>
                    <input v-model="formData.title" type="text" placeholder="e.g. Software Engineer"
                      class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
                  </div>
                </div>

                <!-- Email -->
                <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
                  <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Email Address</label>
                    <input v-model="formData.email" type="email" placeholder="john@example.com"
                      class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
                  </div>
                </div>

                <!-- Phone -->
                <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
                  <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Phone Number</label>
                    <input v-model="formData.phone" type="tel" placeholder="+1 (555) 000-0000"
                      class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
                  </div>
                </div>

                <!-- Location -->
                <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
                  <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Location / Address</label>
                    <input v-model="formData.location" type="text" placeholder="New York, NY"
                      class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab: AI Assistant -->
            <div v-show="activeTab === 'ai'" class="tab-content">
              <header class="mb-8">
                <h2 class="text-xl font-display font-bold text-slate-800 flex items-center gap-2">
                  <i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i>
                  AI Resume Engine
                </h2>
                <p class="text-slate-400 text-xs mt-1 font-medium italic">Transform your draft into a job-winning resume with one click.</p>
              </header>

              <div class="space-y-6">
                <!-- Primary Actions -->
                <div class="grid grid-cols-1 gap-4">
                  <button @click="generateResume" :disabled="isProcessing"
                    class="group relative bg-gradient-to-br from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white p-6 rounded-3xl font-bold text-sm transition-all shadow-lg shadow-purple-200 hover:shadow-xl hover:shadow-purple-300 overflow-hidden disabled:opacity-50">
                    <div class="relative z-10 flex items-center justify-between">
                      <div class="text-left">
                        <div class="flex items-center gap-2 mb-1">
                          <i class="fa-solid fa-wand-magic-sparkles"></i>
                          <span>Generate Complete Resume</span>
                        </div>
                        <p class="text-[10px] font-normal opacity-90">Uses AI to write your entire resume</p>
                      </div>
                      <i class="fa-solid fa-arrow-right text-lg"></i>
                    </div>
                  </button>

                  <button @click="optimizeForATS" :disabled="isProcessing"
                    class="group relative bg-gradient-to-br from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white p-6 rounded-3xl font-bold text-sm transition-all shadow-lg shadow-indigo-200 hover:shadow-xl hover:shadow-indigo-300 disabled:opacity-50">
                    <div class="relative z-10 flex items-center justify-between">
                      <div class="text-left">
                        <div class="flex items-center gap-2 mb-1">
                          <i class="fa-solid fa-robot"></i>
                          <span>Optimize for ATS</span>
                        </div>
                        <p class="text-[10px] font-normal opacity-90">Beat applicant tracking systems</p>
                      </div>
                      <i class="fa-solid fa-arrow-right text-lg"></i>
                    </div>
                  </button>
                </div>

                <!-- Suggested AI Features -->
                <div class="pt-4 border-t border-slate-100">
                  <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Smart Suggestions</h3>
                  <div class="space-y-3">
                    <button @click="improveBulletPoints" :disabled="isProcessing"
                      class="w-full bg-white border-2 border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 p-4 rounded-2xl text-left transition-all group disabled:opacity-50">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                            <i class="fa-solid fa-list"></i>
                          </div>
                          <div>
                            <p class="font-bold text-sm text-slate-800">Improve Bullet Points</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Make achievements more impactful</p>
                          </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-300 group-hover:text-indigo-400 transition-colors"></i>
                      </div>
                    </button>

                    <button @click="analyzeSkillGap" :disabled="isProcessing"
                      class="w-full bg-white border-2 border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 p-4 rounded-2xl text-left transition-all group disabled:opacity-50">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                            <i class="fa-solid fa-chart-line"></i>
                          </div>
                          <div>
                            <p class="font-bold text-sm text-slate-800">Analyze Skill Gap</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Compare skills with job requirements</p>
                          </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-300 group-hover:text-indigo-400 transition-colors"></i>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Tone Selection -->
                <div class="p-5 bg-indigo-50/30 rounded-3xl border border-indigo-100">
                  <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Writing Tone</label>
                  <div class="flex gap-2">
                    <button v-for="tone in tones" :key="tone" @click="selectedTone = tone"
                      :class="['flex-1 px-3 py-2 rounded-xl text-xs font-bold transition-all', selectedTone === tone ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-slate-600 hover:bg-slate-50']">
                      {{ tone }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab: Templates -->
            <div v-show="activeTab === 'templates'" class="tab-content">
              <header class="mb-6">
                <h2 class="text-xl font-display font-bold text-slate-800">Choose a Layout</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium">Select a design that matches your industry and seniority level.</p>
              </header>

              <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:gap-6">
                <div v-for="template in templatesFromJSON" :key="template.id" @click="selectTemplate(template.id)"
                  :class="['template-card group relative bg-white border-2 p-2 rounded-2xl cursor-pointer hover:border-indigo-200 hover:shadow-lg transition-all', selectedTemplate === template.id ? 'template-card-active border-indigo-600' : 'border-slate-100']">
                  <div class="aspect-[3/4] bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl mb-2 sm:mb-3 flex items-center justify-center">
                    <i class="fa-solid fa-file-lines text-2xl sm:text-4xl text-slate-300"></i>
                  </div>
                  <div class="px-1 sm:px-2 pb-1 sm:pb-2">
                    <h3 class="font-bold text-xs sm:text-sm text-slate-800 truncate">{{ template.name }}</h3>
                    <p class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5 truncate">{{ template.type }}</p>
                  </div>
                  <div v-if="selectedTemplate === template.id" class="absolute top-2 sm:top-4 right-2 sm:right-4 w-5 h-5 sm:w-6 sm:h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-check text-white text-[10px] sm:text-xs"></i>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab: Colors -->
            <div v-show="activeTab === 'colors'" class="tab-content">
              <header class="mb-8">
                <h2 class="text-xl font-display font-bold text-slate-800">Accent Colors</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium italic">Apply a brand identity to your resume</p>
              </header>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button v-for="color in colorPalettes" :key="color.id" @click="selectColor(color)"
                  :class="['color-card text-left bg-white border-2 p-4 rounded-2xl cursor-pointer hover:border-indigo-200 hover:shadow-lg transition-all', selectedColor === color.id ? 'color-card-active border-indigo-600' : 'border-slate-100']">
                  <div :style="{ backgroundColor: color.hex }" class="w-full h-16 rounded-xl mb-3 shadow-inner"></div>
                  <h3 class="font-bold text-sm text-slate-800">{{ color.name }}</h3>
                  <p class="text-[10px] text-slate-400 mt-0.5">{{ color.category }}</p>
                  <div v-if="selectedColor === color.id" class="absolute top-4 right-4 w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-check text-white text-xs"></i>
                  </div>
                </button>

                <!-- Custom Color -->
                <div class="relative bg-white border-2 border-slate-100 p-4 rounded-2xl hover:border-indigo-200 transition-all">
                  <label for="custom-color-input" class="cursor-pointer">
                    <div class="w-full h-16 rounded-xl mb-3 shadow-inner overflow-hidden relative">
                      <input id="custom-color-input" v-model="customColor" @input="handleCustomColor" type="color"
                        class="absolute inset-0 w-full h-full cursor-pointer">
                    </div>
                    <h3 class="font-bold text-sm text-slate-800">Custom Color</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Choose your own</p>
                  </label>
                </div>
              </div>
            </div>

            <!-- Tab: Summary -->
            <div v-show="activeTab === 'summary'" class="space-y-8">
              <h2 class="text-2xl font-black text-slate-900 mb-4">Professional Summary</h2>
              <div class="space-y-4">
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
        <section class="hidden lg:flex flex-[1.5] preview-container items-start justify-center p-12 overflow-y-auto custom-scrollbar">
          <div class="w-full max-w-[800px] space-y-8">
            <!-- Page 1 -->
            <div id="resume-page-1" class="bg-white shadow-2xl w-full min-h-[1056px] max-h-[1056px] p-8 lg:p-16 origin-top transform transition-all duration-300 relative overflow-hidden">
              <!-- Page Number -->
              <div class="absolute bottom-4 right-4 text-[10px] text-slate-400 font-medium">Page 1</div>
              
              <div class="border-b-4 border-slate-900 pb-8 mb-8">
                <h1 :style="{ color: '#1e293b' }" class="text-3xl font-black text-slate-900 tracking-tight uppercase transition-colors duration-500">
                  {{ formData.fullName || 'Your Name' }}
                </h1>
                <p :style="{ color: currentAccentColor }" class="text-base font-bold mt-1 transition-colors duration-500">
                  {{ formData.title || 'Professional Title' }}
                </p>
                <div class="flex gap-4 mt-4 text-[11px] font-bold text-slate-400">
                  <span v-if="formData.email"><i class="fa-solid fa-envelope mr-1.5"></i>{{ formData.email }}</span>
                  <span v-if="formData.phone"><i class="fa-solid fa-phone mr-1.5"></i>{{ formData.phone }}</span>
                  <span v-if="formData.location"><i class="fa-solid fa-location-dot mr-1.5"></i>{{ formData.location }}</span>
                </div>
              </div>

              <!-- Content -->
              <div class="space-y-8">
                <div v-if="formData.summary">
                  <h3 :style="{ color: currentAccentColor }" class="text-xs font-black uppercase tracking-widest mb-3 transition-colors duration-500">Professional Summary</h3>
                  <p class="text-sm text-slate-700 leading-relaxed">{{ formData.summary }}</p>
                </div>

                <div v-if="hasSkills">
                  <h3 :style="{ color: currentAccentColor }" class="text-xs font-black uppercase tracking-widest mb-3 transition-colors duration-500">Skills</h3>
                  <div class="space-y-2">
                    <div v-for="(skillsArray, category) in formData.skills" :key="category">
                      <div v-if="skillsArray.length > 0">
                        <span class="text-xs font-bold capitalize text-slate-900">{{ category }}:</span>
                        <span class="text-xs text-slate-700 ml-2">{{ skillsArray.join(', ') }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="hasExperience" id="experience-section">
                  <h3 :style="{ color: currentAccentColor }" class="text-xs font-black uppercase tracking-widest mb-3 transition-colors duration-500">Experience</h3>
                  <div v-for="(exp, index) in formData.experience.slice(0, getExperienceCountForPage1())" :key="index" class="mb-4">
                    <h4 class="font-bold text-sm text-slate-900">{{ exp.position }}</h4>
                    <p class="text-xs text-slate-600">{{ exp.company }}</p>
                    <ul class="list-disc list-inside text-xs text-slate-700 mt-1 space-y-0.5">
                      <li v-for="(resp, rIndex) in exp.responsibilities.filter(r => r)" :key="rIndex">{{ resp }}</li>
                    </ul>
                  </div>
                </div>

                <div v-if="formData.education.length > 0 && formData.education[0].degree && !needsSecondPage">
                  <h3 :style="{ color: currentAccentColor }" class="text-xs font-black uppercase tracking-widest mb-3 transition-colors duration-500">Education</h3>
                  <div v-for="(edu, index) in formData.education" :key="index" class="mb-2">
                    <h4 class="font-bold text-sm text-slate-900">{{ edu.degree }}</h4>
                    <p class="text-xs text-slate-600">{{ edu.institution }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Page 2 (if content overflows) -->
            <div v-if="needsSecondPage" id="resume-page-2" class="bg-white shadow-2xl w-full min-h-[1056px] max-h-[1056px] p-8 lg:p-16 origin-top transform transition-all duration-300 relative overflow-hidden">
              <!-- Page Number -->
              <div class="absolute bottom-4 right-4 text-[10px] text-slate-400 font-medium">Page 2</div>
              
              <!-- Continued Experience -->
              <div v-if="hasExperience && getExperienceCountForPage1() < formData.experience.length">
                <h3 :style="{ color: currentAccentColor }" class="text-xs font-black uppercase tracking-widest mb-3 transition-colors duration-500">Experience (continued)</h3>
                <div v-for="(exp, index) in formData.experience.slice(getExperienceCountForPage1())" :key="index" class="mb-4">
                  <h4 class="font-bold text-sm text-slate-900">{{ exp.position }}</h4>
                  <p class="text-xs text-slate-600">{{ exp.company }}</p>
                  <ul class="list-disc list-inside text-xs text-slate-700 mt-1 space-y-0.5">
                    <li v-for="(resp, rIndex) in exp.responsibilities.filter(r => r)" :key="rIndex">{{ resp }}</li>
                  </ul>
                </div>
              </div>

              <!-- Education on Page 2 -->
              <div v-if="formData.education.length > 0 && formData.education[0].degree" class="mt-8">
                <h3 :style="{ color: currentAccentColor }" class="text-xs font-black uppercase tracking-widest mb-3 transition-colors duration-500">Education</h3>
                <div v-for="(edu, index) in formData.education" :key="index" class="mb-2">
                  <h4 class="font-bold text-sm text-slate-900">{{ edu.degree }}</h4>
                  <p class="text-xs text-slate-600">{{ edu.institution }}</p>
                  <p v-if="edu.year" class="text-xs text-slate-500">{{ edu.year }}</p>
                </div>
              </div>

              <!-- Achievements on Page 2 -->
              <div v-if="formData.achievements.length > 0 && formData.achievements[0]" class="mt-8">
                <h3 :style="{ color: currentAccentColor }" class="text-xs font-black uppercase tracking-widest mb-3 transition-colors duration-500">Achievements</h3>
                <ul class="list-disc list-inside text-xs text-slate-700 space-y-1">
                  <li v-for="(achievement, index) in formData.achievements" :key="index">{{ achievement }}</li>
                </ul>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>

    <!-- Mobile Preview Modal -->
    <div v-if="previewModalOpen" class="mobile-preview-modal lg:hidden active">
      <div class="mobile-preview-content">
        <div class="sticky top-0 z-10 bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
          <h3 class="font-display font-bold text-lg">Resume Preview</h3>
          <button @click="closeMobilePreview" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-slate-100 rounded-lg transition-all">
            <i class="fa-solid fa-xmark text-xl"></i>
          </button>
        </div>
        <div class="preview-container p-6">
          <div class="bg-white shadow-2xl w-full min-h-[1000px] p-6 mx-auto max-w-2xl">
            <div class="border-b-4 border-slate-900 pb-8 mb-8">
              <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase">{{ formData.fullName || 'Your Name' }}</h1>
              <p :style="{ color: currentAccentColor }" class="text-base font-bold mt-1">{{ formData.title || 'Professional Title' }}</p>
              <div class="flex flex-wrap gap-3 mt-4 text-[10px] font-bold text-slate-400">
                <span v-if="formData.email"><i class="fa-solid fa-envelope mr-1.5"></i>{{ formData.email }}</span>
                <span v-if="formData.phone"><i class="fa-solid fa-phone mr-1.5"></i>{{ formData.phone }}</span>
              </div>
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
    <div v-if="sidebarOpen" @click="toggleMobileSidebar" class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-30 mt-20"></div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700&display=swap');

.font-display {
  font-family: 'Plus Jakarta Sans', sans-serif;
}

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

.drag-handle:active {
  cursor: grabbing;
}

@media (max-width: 1023px) {
  #mobile-sidebar {
    margin-top: 25px;
  }

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
