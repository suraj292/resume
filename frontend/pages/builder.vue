<script setup lang="ts">
import type { ResumeData, ThemeConfig, TemplateMetadata } from '~/types/resume'
import { useResumeTemplate } from '~/composables/useResumeTemplate'
import { useResumeTheme } from '~/composables/useResumeTheme'
import toastr from 'toastr'
import 'toastr/build/toastr.min.css'

// Configure toastr
toastr.options = {
  closeButton: true,
  progressBar: true,
  positionClass: 'toast-top-right',
  timeOut: 5000,
  extendedTimeOut: 2000,
  showMethod: 'slideDown',
  hideMethod: 'slideUp',
  showDuration: 300,
  hideDuration: 200,
  preventDuplicates: true,
  newestOnTop: true,
  toastClass: 'toastr-custom',
  iconClasses: {
    error: 'toast-error',
    info: 'toast-info',
    success: 'toast-success',
    warning: 'toast-warning'
  }
}

const config = useRuntimeConfig()
const route = useRoute()
const router = useRouter()

// Auth
const { isAuthenticated, fetchUser } = useAuth()
const showAuthModal = ref(false)

// ATS Badge Animation Loop
const isPulsing = ref(false)
onMounted(() => {
  if (process.client) {
    setInterval(() => {
      isPulsing.value = true
      setTimeout(() => isPulsing.value = false, 2000)
    }, 4000)
  }
})


// Load dummy data function
const loadDummyData = async () => {
  try {
    // Show loading indicator
    // isProcessing.value = true
    
    // Add a delay to show the loading state
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    const dummyData = await import('~/data/dummydata.json').then(m => m.default || m)
    
    // Populate personal information
    formData.value.fullName = dummyData.name || ''
    formData.value.title = dummyData.title || ''
    formData.value.email = dummyData.contact?.email || ''
    formData.value.phone = dummyData.contact?.phone || ''
    formData.value.location = dummyData.contact?.location || ''
    formData.value.linkedin = dummyData.contact?.linkedin || ''
    formData.value.github = '' // Not in dummy data
    formData.value.portfolio = dummyData.contact?.website || ''
    formData.value.summary = dummyData.summary || ''
    
    // Populate skills - map from dummy data structure to form structure
    if (dummyData.skills) {
      // Categorize technical skills
      const technicalSkills = dummyData.skills.technical || []
      formData.value.skills = {
        backend: technicalSkills.filter((s: string) => 
          ['Node.js', 'Python', 'PostgreSQL', 'MongoDB', 'GraphQL', 'REST APIs'].includes(s)
        ),
        frontend: technicalSkills.filter((s: string) => 
          ['JavaScript', 'TypeScript', 'React'].includes(s)
        ),
        devops: technicalSkills.filter((s: string) => 
          ['AWS', 'Docker', 'Kubernetes'].includes(s)
        ),
        other: dummyData.skills.soft || []
      }
    }
    
    // Populate experience
    if (dummyData.experience && Array.isArray(dummyData.experience)) {
      formData.value.experience = dummyData.experience.map((exp: any, index: number) => ({
        id: index + 1,
        position: exp.position || '',
        company: exp.company || '',
        location: exp.location || '',
        startDate: exp.duration?.split(' - ')[0] || '',
        endDate: exp.duration?.split(' - ')[1] || '',
        current: exp.duration?.includes('Present') || false,
        responsibilities: exp.achievements || ['']
      }))
    }
    
    // Populate education
    if (dummyData.education && Array.isArray(dummyData.education)) {
      formData.value.education = dummyData.education.map((edu: any, index: number) => ({
        id: index + 1,
        degree: edu.degree || '',
        institution: edu.institution || '',
        year: edu.duration || '',
        percentage: edu.gpa || ''
      }))
    }
    
    // Populate achievements - can use certifications or projects
    const achievements: string[] = []
    if (dummyData.certifications && Array.isArray(dummyData.certifications)) {
      dummyData.certifications.forEach((cert: any) => {
        achievements.push(`${cert.name} - ${cert.issuer} (${cert.date})`)
      })
    }
    if (dummyData.projects && Array.isArray(dummyData.projects)) {
      dummyData.projects.forEach((project: any) => {
        achievements.push(`${project.name}: ${project.description}`)
      })
    }
    formData.value.achievements = achievements.length > 0 ? achievements : ['']
    
  } catch (error) {
    console.error('Failed to load dummy data:', error)
    toastr.error('Failed to load sample data', 'Error')
  } finally {
    // Hide loading indicator
    isProcessing.value = false
  }
}

// Check authentication on mount
onMounted(async () => {
  try {
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
    
    // Check auth status and show modal if not authenticated
    if (!isAuthenticated.value) {
      console.log('Not authenticated, showing modal')
      showAuthModal.value = true
    }
    
    // Load dummy data
    await loadDummyData()
  } catch (error) {
    console.log('Auth check failed (backend may be offline):', error)
    // Don't show auth modal if backend is offline - allow using the app without auth
    showAuthModal.value = false
    
    // Still try to load dummy data even if auth fails
    await loadDummyData()
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
    backend: [] as string[],
    frontend: [] as string[],
    devops: [] as string[],
    other: [] as string[]
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
const isTemplateLoading = ref(false)

// Input Modes
const resumeInputMode = ref('upload')
const jobInputMode = ref('paste')
const resumeText = ref('')
const jobDescription = ref('')

// AI Settings
const atsScore = ref(0)
const selectedTone = ref('Professional')
const tones = ['Professional', 'Creative', 'Direct']
const aiResults = ref({
  atsOptimization: null as any,
  improvedBullets: [] as string[],
  skillGapAnalysis: null as any
})

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
const templatesData = await import('~/data/templates.json').then(m => m.default || m) as TemplateMetadata[]
const templatesFromJSON = ref<TemplateMetadata[]>(templatesData)
const selectedTemplate = ref(templatesData[0]?.id || 'software-engineer')

// New template system
const { getTemplateComponent } = useResumeTemplate()
const { currentTheme, updateTheme } = useResumeTheme()

// Dynamic template component (using shallowRef to avoid unnecessary reactivity)
const currentTemplateComponent = shallowRef(null)
const previewScale = ref(0.97)

// Watch for template changes and load component dynamically
watch(selectedTemplate, async (newTemplateId) => {
  try {
    isTemplateLoading.value = true
    currentTemplateComponent.value = null // Clear old component
    
    await nextTick() // Ensure cleanup
    
    currentTemplateComponent.value = await getTemplateComponent(
      newTemplateId,
      templatesFromJSON.value
    )
    
    // Show success feedback
    const template = templatesFromJSON.value.find(t => t.id === newTemplateId)
    if (template) {
      successMessage.value = `✓ Template switched to ${template.name}`
      setTimeout(() => successMessage.value = '', 2000)
    }
  } catch (error) {
    console.error('Template loading failed:', error)
    uploadError.value = 'Failed to load template'
  } finally {
    isTemplateLoading.value = false
  }
}, { immediate: true })

// Colors
const selectedColor = ref('indigo')
const customColor = ref('#6366f1')
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

// Transform form data to ResumeData interface for templates
const resumeDataFormatted = computed<ResumeData>(() => ({
  basics: {
    fullName: formData.value.fullName || '',
    title: formData.value.title || '',
    email: formData.value.email || '',
    phone: formData.value.phone || '',
    location: formData.value.location || '',
    linkedin: formData.value.linkedin,
    github: formData.value.github,
    portfolio: formData.value.portfolio,
    summary: formData.value.summary
  },
  experience: formData.value.experience || [],
  education: formData.value.education || [],
  skills: formData.value.skills || {},
  achievements: formData.value.achievements?.filter((a: string) => a) || []
}))

// Current theme configuration
const currentThemeConfig = computed<ThemeConfig>(() => ({
  primaryColor: customColor.value || currentAccentColor.value,
  fontFamily: 'inter',
  spacing: 'normal',
  typographyScale: 'medium'
}))

// Update theme when color changes
watch(currentThemeConfig, (newTheme) => {
  updateTheme(newTheme)
})

// Zoom controls
const adjustZoom = (delta: number) => {
  previewScale.value = Math.max(0.3, Math.min(1.5, previewScale.value + delta))
}

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
  return formData.value.experience.length > 0 && formData.value.experience[0]?.position
})

// Get current template configuration
const currentTemplateConfig = computed(() => {
  return templatesFromJSON.value.find(t => t.id === selectedTemplate.value) || templatesFromJSON.value[0]
})

// Dynamic template styles based on selected template
const templateHeaderClass = computed(() => {
  const config = currentTemplateConfig.value
  if (!config) return 'border-b-4 border-slate-900 pb-8 mb-8'
  
  // Different header styles based on template
  switch (config.id) {
    case 'executive':
      return 'text-center border-b-2 border-slate-900 pb-8 mb-8'
    case 'creative-designer':
    case 'marketing-professional':
      return 'border-b-2 pb-6 mb-6'
    case 'data-scientist':
      return 'border-b border-slate-300 pb-6 mb-6'
    default:
      return 'border-b-4 border-slate-900 pb-8 mb-8'
  }
})

const templateNameClass = computed(() => {
  const config = currentTemplateConfig.value
  if (!config) return 'text-3xl font-black text-slate-900 tracking-tight uppercase'
  
  switch (config.id) {
    case 'executive':
      return 'text-4xl font-black text-slate-900 tracking-tight uppercase'
    case 'creative-designer':
    case 'content-creator':
      return 'text-3xl font-bold text-slate-900'
    default:
      return 'text-3xl font-black text-slate-900 tracking-tight uppercase'
  }
})

const templatePrimaryColor = computed(() => {
  const config = currentTemplateConfig.value
  return config?.layout?.primaryColor || currentAccentColor.value
})

// Smart multi-page logic - detect when content exceeds A4 page height
const needsSecondPage = computed(() => {
  // Estimate content height in pixels
  let estimatedHeight = 0
  
  // Header section: ~150px
  estimatedHeight += 150
  
  // Summary: base 100px + 1px per character (wrapping considered)
  if (formData.value.summary) {
    estimatedHeight += 100 + Math.ceil(formData.value.summary.length / 2)
  }
  
  // Skills section: base 100px + 5px per skill
  const skillCount = Object.values(formData.value.skills)
    .reduce((sum, arr) => sum + arr.length, 0)
  if (skillCount > 0) {
    estimatedHeight += 100 + (skillCount * 5)
  }
  
  // Experience: 150px base per entry + 30px per responsibility
  formData.value.experience.forEach(exp => {
    if (exp.position) {
      estimatedHeight += 150
      const respCount = exp.responsibilities.filter(r => r).length
      estimatedHeight += respCount * 30
    }
  })
  
  // Education: 80px per entry
  const eduCount = formData.value.education.filter(e => e.degree).length
  estimatedHeight += eduCount * 80
  
  // Achievements: base 100px + 30px per achievement
  const achCount = formData.value.achievements?.filter(a => a).length || 0
  if (achCount > 0) {
    estimatedHeight += 100 + (achCount * 30)
  }
  
  // Page capacity is ~1000px usable height (A4 page minus padding)
  // Show second page if content exceeds this threshold
  return estimatedHeight > 1000
})

// Split data for page 1 (when multi-page is needed)
const page1Data = computed<ResumeData>(() => {
  if (!needsSecondPage.value) {
    return resumeDataFormatted.value
  }
  
  // Page 1: Header, Summary, Skills, First 2 experiences
  return {
    ...resumeDataFormatted.value,
    experience: resumeDataFormatted.value.experience.slice(0, 2),
    education: [], // Move to page 2
    achievements: [] // Move to page 2
  }
})

// Split data for page 2 (when multi-page is needed)
const page2Data = computed<ResumeData | null>(() => {
  if (!needsSecondPage.value) {
    return null
  }
  
  // Page 2: Remaining experience, Education, Achievements (no header/summary duplicate)
  return {
    ...resumeDataFormatted.value,
    basics: {
      fullName: '', // Don't repeat name
      title: '', // Don't repeat title
      email: '', // Don't repeat contact info
      phone: '', // Don't repeat contact info
      location: '', // Don't repeat contact info
      linkedin: '', // Don't repeat social links
      github: '', // Don't repeat social links
      portfolio: '', // Don't repeat social links
      summary: '' // Don't repeat summary
    },
    experience: resumeDataFormatted.value.experience.slice(2),
    skills: {} // Don't repeat skills
  }
})

// Methods
const switchTab = (tabId: string) => {
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

const selectTemplate = (templateId: string) => {
  console.log('Selecting template:', templateId)
  selectedTemplate.value = templateId
  const template = templatesFromJSON.value.find(t => t.id === templateId)
  console.log('Template found:', template)
  successMessage.value = `✓ Applied ${template?.name || 'template'}`
  setTimeout(() => successMessage.value = '', 2000)
}

const handleCustomColor = () => {
  selectedColor.value = ''
  successMessage.value = '✓ Custom color applied'
  setTimeout(() => successMessage.value = '', 2000)
}

const adjustColorBrightness = (hex: string, percent: number): string => {
  // Remove # if present
  hex = hex.replace('#', '')
  
  // Convert to RGB
  let r = parseInt(hex.substring(0, 2), 16)
  let g = parseInt(hex.substring(2, 4), 16)
  let b = parseInt(hex.substring(4, 6), 16)
  
  // Adjust brightness
  r = Math.max(0, Math.min(255, r + (r * percent / 100)))
  g = Math.max(0, Math.min(255, g + (g * percent / 100)))
  b = Math.max(0, Math.min(255, b + (b * percent / 100)))
  
  // Convert back to hex
  const toHex = (n: number): string => {
    const hex = Math.round(n).toString(16)
    return hex.length === 1 ? '0' + hex : hex
  }
  
  return `#${toHex(r)}${toHex(g)}${toHex(b)}`
}

const getColorGradientStyle = (hex: string) => {
  const darkerColor = adjustColorBrightness(hex, -20)
  return {
    background: `linear-gradient(135deg, ${hex} 0%, ${darkerColor} 100%)`
  }
}

const selectColor = (color: { id: string }) => {
  selectedColor.value = color.id
  customColor.value = ''
}

const handleResumeUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  isProcessing.value = true
  uploadError.value = ''
  successMessage.value = ''

  try {
    const { extractTextFromFile, parseResumeToStructuredData } = useGemini()
    
    // Extract text from file using Gemini API
    toastr.info('Extracting text from file...', 'Processing')
    const text = await extractTextFromFile(file)
    
    if (text) {
      resumeText.value = text
      
      // Parse the extracted text into structured data using Gemini API
      try {
        toastr.info('Parsing resume data with AI...', 'Processing')
        const parsedData = await parseResumeToStructuredData(text)

        // console.log(parsedData);
        
        if (parsedData) {
          populateFormWithParsedData(parsedData)
          
          // Update ATS score if returned
          if (parsedData.atsScore) {
            atsScore.value = parsedData.atsScore
            atsAnalysisCompleted.value = true
          }
          
          toastr.success('Resume uploaded and parsed successfully! All fields have been populated.', 'Success')
        } else {
          // Fallback to basic parsing
          parseResumeContent(text)
          toastr.success('Resume uploaded successfully! Basic information extracted.', 'Success')
        }
      } catch (parseError: any) {
        console.error('Parse error:', parseError)
        // Fallback to basic parsing if AI parsing fails
        parseResumeContent(text)
        toastr.warning('Resume uploaded. Using basic text extraction.', 'Partial Success')
      }
    }
  } catch (error: any) {
    console.error('Resume upload error:', error)
    toastr.error(error.message || 'Failed to extract text from resume', 'Error')
  } finally {
    isProcessing.value = false
  }
}

const populateFormWithParsedData = (parsedData: any) => {
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
    formData.value.experience = parsedData.experience.map((exp: any, index: number) => ({
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
    formData.value.education = parsedData.education.map((edu: any, index: number) => ({
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

const parseResumeContent = (text: string) => {
  if (!text) return

  const emailMatch = text.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/)
  if (emailMatch) formData.value.email = emailMatch[0]

  const phoneMatch = text.match(/(?:\+?91[-\s]?)?[6-9]\d{9}|(?:\+?1[-.]?)?(?:\(?\d{3}\)?[-.]?)?\d{3}[-.]?\d{4}/)
  if (phoneMatch) formData.value.phone = phoneMatch[0]

  const lines = text.split('\n').map((l: string) => l.trim()).filter((l: string) => l)
  if (lines.length > 0) {
    const firstLine = lines[0]
    if (firstLine && firstLine.length < 50 && /[A-Z]/.test(firstLine)) {
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

const removeExperience = (index: number) => {
  if (formData.value.experience.length > 1) {
    formData.value.experience.splice(index, 1)
  }
}

const addResponsibility = (expIndex: number) => {
  formData.value.experience[expIndex]?.responsibilities.push('')
}

const removeResponsibility = (expIndex: number, respIndex: number) => {
  const exp = formData.value.experience[expIndex]
  if (exp && exp.responsibilities.length > 1) {
    exp.responsibilities.splice(respIndex, 1)
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

const removeEducation = (index: number) => {
  if (formData.value.education.length > 1) {
    formData.value.education.splice(index, 1)
  }
}

const addSkill = (category: keyof typeof formData.value.skills) => {
  if (process.client) {
    const skill = prompt(`Enter new ${category} skill:`)
    if (skill && skill.trim()) {
      formData.value.skills[category].push(skill.trim())
    }
  }
}

const removeSkill = (category: keyof typeof formData.value.skills, index: number) => {
  formData.value.skills[category].splice(index, 1)
}

const addAchievement = () => {
  formData.value.achievements.push('')
}

const removeAchievement = (index: number) => {
  if (formData.value.achievements.length > 1) {
    formData.value.achievements.splice(index, 1)
  }
}

// Job description upload handler
const handleJobUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  isProcessing.value = true
  uploadError.value = ''

  try {
    const formDataObj = new FormData()
    formDataObj.append('file', file)

    const data = await $fetch<{ text?: string }>(`${config.public.apiBase}/api/extract-text`, {
      method: 'POST',
      body: formDataObj
    })

    if (data.text) {
      jobDescription.value = data.text
      successMessage.value = 'Job description uploaded successfully!'
      setTimeout(() => successMessage.value = '', 3000)
    }
  } catch (error: any) {
    console.error('Job upload error:', error)
    toastr.error(error.data?.error || 'Failed to extract text from job description', 'Error')
  } finally {
    isProcessing.value = false
  }
}

// Save context for AI
const saveContext = async () => {
  try {
    isProcessing.value = true
    uploadError.value = ''
    
    // Simulate processing time to show loader
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Show success message using toastr
    toastr.success('Context saved for AI processing', 'Success')
    
  } catch (error: any) {
    console.error('Save context error:', error)
    const errorMsg = error.data?.message || error.message || 'Failed to save context'
    toastr.error(errorMsg, 'Error')
  } finally {
    isProcessing.value = false
  }
}

// AI method implementations
const generateResume = async () => {
  if (isProcessing.value) return
  
  try {
    isProcessing.value = true
    uploadError.value = ''
    
    // Prepare resume context from current data
    const resumeContext = `
Name: ${formData.value.fullName}
Title: ${formData.value.title}
Email: ${formData.value.email}
Phone: ${formData.value.phone}
Location: ${formData.value.location}
Summary: ${formData.value.summary}
    `.trim()
    
    const response = await $fetch<{ success: boolean; data?: any; message?: string; atsScore?: number }>(`${config.public.apiBase}/api/ai/generate-resume`, {
      method: 'POST',
      body: {
        resumeContext,
        jobDescription: jobDescription.value,
        tone: selectedTone.value,
        currentData: formData.value
      }
    })
    
    if (response.success && response.data) {
      // Update ATS score if returned
      if (response.atsScore) {
        atsScore.value = response.atsScore
        atsAnalysisCompleted.value = true
      }
      
      // Update form data with AI-generated content
      if (response.data.summary) {
        formData.value.summary = response.data.summary
      }
      
      if (response.data.experience && Array.isArray(response.data.experience)) {
        formData.value.experience = response.data.experience.map((exp: any) => ({
          id: Date.now() + Math.random(),
          position: exp.position || '',
          company: exp.company || '',
          startDate: exp.startDate || '',
          endDate: exp.endDate || '',
          location: exp.location || '',
          responsibilities: exp.responsibilities || []
        }))
      }
      
      if (response.data.skills) {
        formData.value.skills = {
          backend: response.data.skills.backend || [],
          frontend: response.data.skills.frontend || [],
          devops: response.data.skills.devops || [],
          other: response.data.skills.other || []
        }
      }
      
      // Show API message using toastr
      const message = response.message || 'Resume generated successfully!'
      toastr.success(message, 'Success')
    }
    
  } catch (error: any) {
    console.error('Generate resume error:', error)
    const errorMsg = error.data?.message || error.message || 'Failed to generate resume. Please try again.'
    toastr.error(errorMsg, 'Error')
  } finally {
    isProcessing.value = false
  }
}

const optimizeForATS = async () => {
  if (isProcessing.value) return
  
  try {
    isProcessing.value = true
    uploadError.value = ''
    
    const response = await $fetch<{ success: boolean; suggestions?: any; message?: string }>(`${config.public.apiBase}/api/ai/optimize-ats`, {
      method: 'POST',
      body: {
        currentResume: formData.value,
        jobDescription: jobDescription.value
      }
    })
    
    if (response.success && response.suggestions) {
      aiResults.value.atsOptimization = response.suggestions
      
      // Update global ATS score refs
      if (response.suggestions.atsScore) {
        atsScore.value = response.suggestions.atsScore
        atsAnalysisCompleted.value = true
      }
      
      // Show API message using toastr
      let message = response.message || ''
      if (!message && response.suggestions.atsScore) {
        message = `ATS Score: ${response.suggestions.atsScore}/100. Check suggestions below.`
      } else if (!message) {
        message = 'ATS optimization complete! Review suggestions.'
      }
      
      toastr.success(message, 'Success')
    }
    
  } catch (error: any) {
    console.error('ATS optimization error:', error)
    const errorMsg = error.data?.message || error.message || 'Failed to optimize for ATS. Please try again.'
    toastr.error(errorMsg, 'Error')
  } finally {
    isProcessing.value = false
  }
}

const improveBulletPoints = async () => {
  if (isProcessing.value) return
  
  try {
    isProcessing.value = true
    uploadError.value = ''
    
    // Collect all bullet points from experience
    const allBullets: string[] = []
    formData.value.experience.forEach(exp => {
      if (exp.responsibilities && exp.responsibilities.length > 0) {
        allBullets.push(...exp.responsibilities.filter(r => r.trim()))
      }
    })
    
    if (allBullets.length === 0) {
      uploadError.value = 'No bullet points found. Add some experience first.'
      setTimeout(() => uploadError.value = '', 3000)
      isProcessing.value = false
      return
    }
    
    const response = await $fetch<{ success: boolean; improvedBullets?: string[]; message?: string }>(`${config.public.apiBase}/api/ai/improve-bullets`, {
      method: 'POST',
      body: {
        bulletPoints: allBullets,
        tone: selectedTone.value
      }
    })
    
    if (response.success && response.improvedBullets) {
      aiResults.value.improvedBullets = response.improvedBullets
      
      // Show API message using toastr
      const message = response.message || `Improved ${response.improvedBullets.length} bullet points! Review below.`
      toastr.success(message, 'Success')
    }
    
  } catch (error: any) {
    console.error('Improve bullets error:', error)
    const errorMsg = error.data?.message || error.message || 'Failed to improve bullet points. Please try again.'
    toastr.error(errorMsg, 'Error')
  } finally {
    isProcessing.value = false
  }
}

const analyzeSkillGap = async () => {
  if (isProcessing.value) return
  
  if (!jobDescription.value.trim()) {
    uploadError.value = 'Please enter a job description first.'
    setTimeout(() => uploadError.value = '', 3000)
    return
  }
  
  try {
    isProcessing.value = true
    uploadError.value = ''
    
    const response = await $fetch<{ success: boolean; analysis?: any; message?: string }>(`${config.public.apiBase}/api/ai/skill-gap`, {
      method: 'POST',
      body: {
        currentSkills: formData.value.skills,
        jobDescription: jobDescription.value
      }
    })
    
    if (response.success && response.analysis) {
      aiResults.value.skillGapAnalysis = response.analysis
      
      // Show API message using toastr
      const matchPercentage = response.analysis.matchPercentage || 0
      const message = response.message || `Skill match: ${matchPercentage}%. Check analysis below.`
      toastr.success(message, 'Success')
    }
    
  } catch (error: any) {
    console.error('Skill gap analysis error:', error)
    const errorMsg = error.data?.message || error.message || 'Failed to analyze skill gap. Please try again.'
    toastr.error(errorMsg, 'Error')
  } finally {
    isProcessing.value = false
  }
}

// Apply improved bullets to experience section
const applyImprovedBullets = () => {
  if (aiResults.value.improvedBullets.length === 0) return
  
  // Distribute improved bullets across experience entries
  let bulletIndex = 0
  formData.value.experience.forEach((exp, expIndex) => {
    if (exp.responsibilities && bulletIndex < aiResults.value.improvedBullets.length) {
      const bulletsToAdd = Math.min(
        exp.responsibilities.length || 3,
        aiResults.value.improvedBullets.length - bulletIndex
      )
      
      exp.responsibilities = aiResults.value.improvedBullets.slice(
        bulletIndex,
        bulletIndex + bulletsToAdd
      )
      
      bulletIndex += bulletsToAdd
    }
  })
  
  toastr.success('Improved bullets applied to your experience!', 'Success')
}

// Add missing skills from skill gap analysis
const addMissingSkills = () => {
  if (!aiResults.value.skillGapAnalysis?.missingSkills) return
  
  const missingSkills = aiResults.value.skillGapAnalysis.missingSkills
  
  // Add to "other" category by default, or try to categorize
  missingSkills.forEach((skill: string) => {
    const skillLower = skill.toLowerCase()
    
    // Try to categorize the skill
    if (skillLower.includes('node') || skillLower.includes('python') || skillLower.includes('java') || 
        skillLower.includes('backend') || skillLower.includes('api') || skillLower.includes('database')) {
      if (!formData.value.skills.backend.includes(skill)) {
        formData.value.skills.backend.push(skill)
      }
    } else if (skillLower.includes('react') || skillLower.includes('vue') || skillLower.includes('angular') || 
               skillLower.includes('frontend') || skillLower.includes('css') || skillLower.includes('html')) {
      if (!formData.value.skills.frontend.includes(skill)) {
        formData.value.skills.frontend.push(skill)
      }
    } else if (skillLower.includes('docker') || skillLower.includes('kubernetes') || skillLower.includes('aws') || 
               skillLower.includes('devops') || skillLower.includes('ci/cd') || skillLower.includes('jenkins')) {
      if (!formData.value.skills.devops.includes(skill)) {
        formData.value.skills.devops.push(skill)
      }
    } else {
      if (!formData.value.skills.other.includes(skill)) {
        formData.value.skills.other.push(skill)
      }
    }
  })
  
  toastr.success(`Added ${missingSkills.length} missing skills to your resume!`, 'Success')
}

const exportPDF = async () => {
  if (!process.client) return
  
  try {
    // Show loading state
    isProcessing.value = true
    
    // Dynamically import required libraries
    const html2canvas = (await import('html2canvas')).default
    const { jsPDF } = await import('jspdf')
    
    // Get all resume pages
    const pages = document.querySelectorAll('.preview-container .resume-paper')
    if (!pages || pages.length === 0) {
      uploadError.value = 'Resume preview not found'
      isProcessing.value = false
      return
    }
    
    // Create PDF document
    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: 'a4'
    })
    
    // Process each page
    for (let i = 0; i < pages.length; i++) {
      const page = pages[i] as HTMLElement
      
      // Clone the page
      const clone = page.cloneNode(true) as HTMLElement
      
      // Reset transforms and margins
      clone.style.transform = 'none'
      clone.style.transformOrigin = 'initial'
      clone.style.marginTop = '0'
      clone.style.marginBottom = '0'
      clone.style.width = '210mm'
      clone.style.minHeight = '297mm'
      clone.style.position = 'absolute'
      clone.style.left = '-9999px'
      clone.style.top = '0'
      
      // Append to body temporarily
      document.body.appendChild(clone)
      
      // Capture as canvas
      const canvas = await html2canvas(clone, {
        scale: 2,
        useCORS: true,
        logging: false,
        width: 794,  // A4 width in pixels
        height: 1123 // A4 height in pixels
      })
      
      // Remove clone from DOM
      document.body.removeChild(clone)
      
      // Convert canvas to image
      const imgData = canvas.toDataURL('image/jpeg', 0.98)
      
      // Add new page if not first page
      if (i > 0) {
        pdf.addPage()
      }
      
      // Add image to PDF (A4 size: 210mm x 297mm)
      pdf.addImage(imgData, 'JPEG', 0, 0, 210, 297)
    }
    
    // Download the PDF
    pdf.save(`${formData.value.fullName || 'Resume'}_Resume.pdf`)
    
    // Show success message
    successMessage.value = '✓ PDF downloaded successfully!'
    setTimeout(() => successMessage.value = '', 3000)
    
  } catch (error) {
    console.error('PDF export error:', error)
    uploadError.value = 'Failed to export PDF. Please try again.'
    setTimeout(() => uploadError.value = '', 3000)
  } finally {
    isProcessing.value = false
  }
}

useHead({
  title: 'Resume Builder'
})
</script>

<template>
  <div v-if="atsScore > 0" :class="['ats-floating-badge', { 'ats-pulse-burst': isPulsing }]">
    <button @click="atsScore = 0" class="absolute -top-1 -right-1 w-4 h-4 bg-white/20 hover:bg-white/40 text-white rounded-full flex items-center justify-center text-[10px] transition-colors z-10">
      <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="ats-shimmer-sweep"></div>
    <div class="ats-floating-label">ATS Score</div>
    <div class="ats-floating-score-wrapper">
      <span class="ats-floating-score">{{ atsScore }}</span>
      <span class="ats-floating-total">/100</span>
    </div>
  </div>

  <!-- Auth Required Modal -->
  <AuthRequiredModal v-if="showAuthModal" @close="showAuthModal = false" />

  <!-- Full Screen Loading Overlay -->
  <div v-if="isProcessing" class="fixed inset-0 bg-black/30 backdrop-blur-md z-50 flex items-center justify-center">
    <div class="rounded-2xl p-8 flex flex-col items-center gap-4 max-w-sm mx-4">
      <div class="relative">
        <i class="fa-solid fa-spinner fa-spin text-6xl text-indigo-600"></i>
      </div>
      <h3 class="text-xl font-bold text-slate-900">Processing...</h3>
      <p class="text-sm text-slate-600 text-center">Saving your context for AI processing</p>
    </div>
  </div>

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
      <main class="flex-1 flex overflow-hidden h-[calc(100vh-136px)]">
        
        <!-- Editor Content (Left) - Fixed, No Scroll -->
        <section class="flex-1 lg:flex-[0.8] bg-white overflow-hidden border-r border-slate-200 w-full flex flex-col">
          <div class="max-w-xl mx-auto py-6 sm:py-10 px-4 sm:px-6 h-full overflow-y-auto custom-scrollbar">
            
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
                  <i :class="isProcessing ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-bolt-lightning'" class="text-amber-400"></i>
                  {{ isProcessing ? 'Saving...' : 'Save Context for AI' }}
                </button>

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

                <!-- LinkedIn -->
                <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
                  <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">LinkedIn Profile</label>
                    <input v-model="formData.linkedin" type="url" placeholder="linkedin.com/in/yourprofile"
                      class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
                  </div>
                </div>

                <!-- GitHub -->
                <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
                  <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">GitHub Profile</label>
                    <input v-model="formData.github" type="url" placeholder="github.com/yourusername"
                      class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
                  </div>
                </div>

                <!-- Portfolio -->
                <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
                  <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Portfolio Website</label>
                    <input v-model="formData.portfolio" type="url" placeholder="yourwebsite.com"
                      class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
                  </div>
                </div>

                <!-- Summary -->
                <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
                  <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Professional Summary</label>
                    <textarea v-model="formData.summary" rows="4" placeholder="Brief overview of your professional background..."
                      class="w-full bg-transparent font-normal text-slate-700 outline-none text-sm resize-none"></textarea>
                  </div>
                </div>
              </div>

              <!-- Skills Section -->
              <div class="mt-8">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Skills</h3>
                <div class="space-y-4">
                  <!-- Backend Skills -->
                  <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                    <div class="flex justify-between items-center mb-2">
                      <label class="text-sm font-bold text-slate-700">Backend</label>
                      <button @click="addSkill('backend')" class="text-indigo-600 hover:text-indigo-700 text-xs font-bold">+ Add</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                      <span v-for="(skill, index) in formData.skills.backend" :key="index" 
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
                      <button @click="addSkill('frontend')" class="text-indigo-600 hover:text-indigo-700 text-xs font-bold">+ Add</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                      <span v-for="(skill, index) in formData.skills.frontend" :key="index" 
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
                      <button @click="addSkill('devops')" class="text-indigo-600 hover:text-indigo-700 text-xs font-bold">+ Add</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                      <span v-for="(skill, index) in formData.skills.devops" :key="index" 
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
                      <button @click="addSkill('other')" class="text-indigo-600 hover:text-indigo-700 text-xs font-bold">+ Add</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                      <span v-for="(skill, index) in formData.skills.other" :key="index" 
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

              <!-- Experience Section -->
              <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-bold text-slate-900">Work Experience</h3>
                  <button @click="addExperience" class="text-indigo-600 hover:text-indigo-700 text-sm font-bold">+ Add Experience</button>
                </div>
                <div class="space-y-4">
                  <div v-for="(exp, index) in formData.experience" :key="exp.id" class="bg-white border border-slate-200 p-4 rounded-2xl">
                    <div class="flex justify-between items-start mb-3">
                      <h4 class="text-sm font-bold text-slate-900">Experience {{ index + 1 }}</h4>
                      <button @click="removeExperience(index)" v-if="formData.experience.length > 1" class="text-red-600 hover:text-red-700">
                        <i class="fa-solid fa-trash text-sm"></i>
                      </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                      <input v-model="exp.position" type="text" placeholder="Position" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                      <input v-model="exp.company" type="text" placeholder="Company" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                      <input v-model="exp.startDate" type="text" placeholder="Start Date" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                      <input v-model="exp.endDate" type="text" placeholder="End Date" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div class="mb-2">
                      <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-slate-700">Responsibilities</label>
                        <button @click="addResponsibility(index)" class="text-indigo-600 hover:text-indigo-700 text-xs font-bold">+ Add</button>
                      </div>
                      <div v-for="(resp, rIndex) in exp.responsibilities" :key="rIndex" class="flex gap-2 mb-2">
                        <input v-model="exp.responsibilities[rIndex]" type="text" placeholder="Responsibility" 
                          class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm">
                        <button @click="removeResponsibility(index, rIndex)" v-if="exp.responsibilities.length > 1" class="text-red-600 hover:text-red-700">
                          <i class="fa-solid fa-trash text-sm"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Education Section -->
              <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-bold text-slate-900">Education</h3>
                  <button @click="addEducation" class="text-indigo-600 hover:text-indigo-700 text-sm font-bold">+ Add Education</button>
                </div>
                <div class="space-y-4">
                  <div v-for="(edu, index) in formData.education" :key="edu.id" class="bg-white border border-slate-200 p-4 rounded-2xl">
                    <div class="flex justify-between items-start mb-3">
                      <h4 class="text-sm font-bold text-slate-900">Education {{ index + 1 }}</h4>
                      <button @click="removeEducation(index)" v-if="formData.education.length > 1" class="text-red-600 hover:text-red-700">
                        <i class="fa-solid fa-trash text-sm"></i>
                      </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                      <input v-model="edu.degree" type="text" placeholder="Degree" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                      <input v-model="edu.institution" type="text" placeholder="Institution" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                      <input v-model="edu.year" type="text" placeholder="Year" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                      <input v-model="edu.percentage" type="text" placeholder="GPA/Percentage" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                  </div>
                </div>
              </div>

              <!-- Achievements -->
              <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-bold text-slate-900">Achievements</h3>
                  <button @click="addAchievement" class="text-indigo-600 hover:text-indigo-700 text-sm font-bold">+ Add Achievement</button>
                </div>
                <div v-for="(achievement, achIndex) in formData.achievements" :key="achIndex" class="flex gap-2 mb-2">
                  <input v-model="formData.achievements[achIndex]" type="text" placeholder="Achievement" 
                    class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm">
                  <button @click="removeAchievement(achIndex)" class="text-red-600 hover:text-red-700">
                    <i class="fa-solid fa-trash text-sm"></i>
                  </button>
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

                <!-- Job Description Input -->
                <div class="p-5 bg-slate-50 rounded-3xl border border-slate-200">
                  <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Job Description (Optional)</label>
                  <textarea 
                    v-model="jobDescription" 
                    placeholder="Paste the job description here for better AI results..."
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm resize-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    rows="4"
                  ></textarea>
                  <p class="text-[10px] text-slate-400 mt-2">AI will tailor your resume to match this job description</p>
                </div>

                <!-- AI Results Display -->
                <div v-if="aiResults.atsOptimization" class="p-5 bg-blue-50 rounded-3xl border border-blue-200">
                  <h3 class="text-sm font-bold text-blue-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-robot"></i>
                    ATS Optimization Results
                  </h3>
                  
                  <!-- ATS Score -->
                  <div v-if="aiResults.atsOptimization.atsScore" class="mb-4 p-4 bg-white rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-xs font-bold text-slate-700">ATS Score</span>
                      <span class="text-2xl font-bold text-blue-600">{{ aiResults.atsOptimization.atsScore }}/100</span>
                    </div>
                    <div class="w-full bg-blue-200 rounded-full h-3">
                      <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" :style="{ width: aiResults.atsOptimization.atsScore + '%' }"></div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2">
                      {{ aiResults.atsOptimization.atsScore >= 80 ? 'Excellent! Your resume is well-optimized.' : 
                         aiResults.atsOptimization.atsScore >= 60 ? 'Good, but there\'s room for improvement.' : 
                         'Needs improvement to pass ATS systems.' }}
                    </p>
                  </div>

                  <!-- Missing Keywords -->
                  <div v-if="aiResults.atsOptimization.missingKeywords && aiResults.atsOptimization.missingKeywords.length > 0" class="mb-4 p-4 bg-white rounded-xl">
                    <p class="text-xs font-bold text-slate-700 mb-2 flex items-center gap-2">
                      <i class="fa-solid fa-exclamation-triangle text-amber-600"></i>
                      Missing Keywords ({{ aiResults.atsOptimization.missingKeywords.length }})
                    </p>
                    <div class="flex flex-wrap gap-2">
                      <span v-for="(keyword, idx) in aiResults.atsOptimization.missingKeywords" :key="idx" 
                        class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-[10px] font-bold">
                        {{ keyword }}
                      </span>
                    </div>
                  </div>

                  <!-- Priority Changes -->
                  <div v-if="aiResults.atsOptimization.priorityChanges && aiResults.atsOptimization.priorityChanges.length > 0" class="mb-4 p-4 bg-white rounded-xl">
                    <p class="text-xs font-bold text-slate-700 mb-2 flex items-center gap-2">
                      <i class="fa-solid fa-star text-amber-500"></i>
                      Priority Changes
                    </p>
                    <ul class="space-y-2">
                      <li v-for="(change, idx) in aiResults.atsOptimization.priorityChanges.slice(0, 3)" :key="idx" 
                        class="text-xs text-slate-600 flex items-start gap-2">
                        <i class="fa-solid fa-check-circle text-green-600 mt-0.5"></i>
                        <span>{{ change }}</span>
                      </li>
                    </ul>
                  </div>

                  <!-- All Suggestions -->
                  <div v-if="aiResults.atsOptimization.suggestions && aiResults.atsOptimization.suggestions.length > 0" class="p-4 bg-white rounded-xl">
                    <p class="text-xs font-bold text-slate-700 mb-2">All Suggestions ({{ aiResults.atsOptimization.suggestions.length }})</p>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                      <div v-for="(suggestion, idx) in aiResults.atsOptimization.suggestions" :key="idx" 
                        class="p-2 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex items-start gap-2">
                          <i class="fa-solid fa-lightbulb text-blue-600 mt-0.5 text-xs"></i>
                          <p class="text-xs text-slate-700 flex-1">{{ suggestion.action || suggestion }}</p>
                        </div>
                        <span v-if="suggestion.type" class="inline-block mt-1 px-2 py-0.5 bg-blue-200 text-blue-800 rounded text-[9px] font-bold uppercase">
                          {{ suggestion.type }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="aiResults.improvedBullets.length > 0" class="p-5 bg-emerald-50 rounded-3xl border border-emerald-200">
                  <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-emerald-900 flex items-center gap-2">
                      <i class="fa-solid fa-list"></i>
                      Improved Bullet Points ({{ aiResults.improvedBullets.length }})
                    </h3>
                    <button 
                      @click="applyImprovedBullets" 
                      class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors"
                    >
                      <i class="fa-solid fa-check mr-1"></i>
                      Apply All
                    </button>
                  </div>
                  <div class="space-y-3 max-h-96 overflow-y-auto">
                    <div v-for="(bullet, idx) in aiResults.improvedBullets" :key="idx" class="p-3 bg-white rounded-lg border border-emerald-200">
                      <div class="flex items-start gap-2 mb-2">
                        <i class="fa-solid fa-arrow-right text-emerald-600 mt-1"></i>
                        <p class="text-xs text-slate-700 flex-1">{{ bullet }}</p>
                      </div>
                    </div>
                  </div>
                  <p class="text-[10px] text-slate-500 mt-3 italic">
                    Click "Apply All" to update your experience section with these improved bullets
                  </p>
                </div>

                <div v-if="aiResults.skillGapAnalysis" class="p-5 bg-amber-50 rounded-3xl border border-amber-200">
                  <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-amber-900 flex items-center gap-2">
                      <i class="fa-solid fa-chart-line"></i>
                      Skill Gap Analysis
                    </h3>
                    <button 
                      v-if="aiResults.skillGapAnalysis.missingSkills && aiResults.skillGapAnalysis.missingSkills.length > 0"
                      @click="addMissingSkills" 
                      class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg transition-colors"
                    >
                      <i class="fa-solid fa-plus mr-1"></i>
                      Add Missing Skills
                    </button>
                  </div>
                  
                  <!-- Match Percentage -->
                  <div v-if="aiResults.skillGapAnalysis.matchPercentage !== undefined" class="mb-4 p-4 bg-white rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-xs font-bold text-slate-700">Match Percentage</span>
                      <span class="text-2xl font-bold text-amber-600">{{ aiResults.skillGapAnalysis.matchPercentage }}%</span>
                    </div>
                    <div class="w-full bg-amber-200 rounded-full h-3">
                      <div class="bg-amber-600 h-3 rounded-full transition-all duration-500" :style="{ width: aiResults.skillGapAnalysis.matchPercentage + '%' }"></div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2">
                      {{ aiResults.skillGapAnalysis.matchPercentage >= 80 ? 'Excellent match! You have most required skills.' : 
                         aiResults.skillGapAnalysis.matchPercentage >= 60 ? 'Good match, consider adding missing skills.' : 
                         'Significant skill gap. Focus on learning missing skills.' }}
                    </p>
                  </div>

                  <!-- Missing Skills -->
                  <div v-if="aiResults.skillGapAnalysis.missingSkills && aiResults.skillGapAnalysis.missingSkills.length > 0" class="mb-4 p-4 bg-white rounded-xl">
                    <p class="text-xs font-bold text-slate-700 mb-2 flex items-center gap-2">
                      <i class="fa-solid fa-times-circle text-red-600"></i>
                      Missing Skills ({{ aiResults.skillGapAnalysis.missingSkills.length }})
                    </p>
                    <div class="flex flex-wrap gap-2">
                      <span v-for="(skill, idx) in aiResults.skillGapAnalysis.missingSkills" :key="idx" 
                        class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-[10px] font-bold">
                        {{ skill }}
                      </span>
                    </div>
                  </div>

                  <!-- Matching Skills -->
                  <div v-if="aiResults.skillGapAnalysis.matchingSkills && aiResults.skillGapAnalysis.matchingSkills.length > 0" class="mb-4 p-4 bg-white rounded-xl">
                    <p class="text-xs font-bold text-slate-700 mb-2 flex items-center gap-2">
                      <i class="fa-solid fa-check-circle text-green-600"></i>
                      Matching Skills ({{ aiResults.skillGapAnalysis.matchingSkills.length }})
                    </p>
                    <div class="flex flex-wrap gap-2">
                      <span v-for="(skill, idx) in aiResults.skillGapAnalysis.matchingSkills" :key="idx" 
                        class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-[10px] font-bold">
                        {{ skill }}
                      </span>
                    </div>
                  </div>

                  <!-- Recommended Skills -->
                  <div v-if="aiResults.skillGapAnalysis.recommendedSkills && aiResults.skillGapAnalysis.recommendedSkills.length > 0" class="mb-4 p-4 bg-white rounded-xl">
                    <p class="text-xs font-bold text-slate-700 mb-2 flex items-center gap-2">
                      <i class="fa-solid fa-star text-blue-600"></i>
                      Recommended Skills ({{ aiResults.skillGapAnalysis.recommendedSkills.length }})
                    </p>
                    <div class="flex flex-wrap gap-2">
                      <span v-for="(skill, idx) in aiResults.skillGapAnalysis.recommendedSkills" :key="idx" 
                        class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-[10px] font-bold">
                        {{ skill }}
                      </span>
                    </div>
                  </div>

                  <!-- Suggestions -->
                  <div v-if="aiResults.skillGapAnalysis.suggestions && aiResults.skillGapAnalysis.suggestions.length > 0" class="p-4 bg-white rounded-xl">
                    <p class="text-xs font-bold text-slate-700 mb-2">Learning Recommendations</p>
                    <ul class="space-y-2">
                      <li v-for="(suggestion, idx) in aiResults.skillGapAnalysis.suggestions" :key="idx" 
                        class="text-xs text-slate-600 flex items-start gap-2">
                        <i class="fa-solid fa-graduation-cap text-amber-600 mt-0.5"></i>
                        <span>{{ suggestion }}</span>
                      </li>
                    </ul>
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
                  
                  <!-- Dynamic Template Thumbnail -->
                  <div :class="['aspect-[3/4] rounded-xl mb-2 sm:mb-3 overflow-hidden', template.thumbnail.bg]">
                    <div :class="['w-full h-full flex', template.thumbnail.mainClass]">
                      <template v-for="(element, idx) in template.thumbnail.elements" :key="idx">
                        <div :class="element.class">
                          <template v-if="element.children">
                            <template v-for="(child, cidx) in element.children" :key="cidx">
                              <div :class="child.class">
                                <template v-if="child.children">
                                  <div v-for="(grandchild, gidx) in child.children" :key="gidx" :class="grandchild.class"></div>
                                </template>
                              </div>
                            </template>
                          </template>
                        </div>
                      </template>
                    </div>
                  </div>

                  <div class="px-1 sm:px-2 pb-1 sm:pb-2">
                    <h3 class="font-bold text-xs sm:text-sm text-slate-800 truncate">{{ template.name }}</h3>
                    <p class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5 truncate">{{ template.type }}</p>
                    <div class="flex flex-wrap gap-1 mt-1">
                      <span v-for="tag in template.tags.slice(0, 2)" :key="tag" class="text-[8px] sm:text-[9px] px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded">{{ tag }}</span>
                    </div>
                  </div>
                  
                  <div v-if="selectedTemplate === template.id" class="absolute top-2 sm:top-4 right-2 sm:right-4 w-5 h-5 sm:w-6 sm:h-6 bg-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-check text-white text-[10px] sm:text-xs"></i>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab: Colors -->
            <div v-show="activeTab === 'colors'" class="tab-content">
              <header class="mb-6">
                <h2 class="text-xl font-display font-bold text-slate-800">Accent Colors</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium italic">Choose a color that represents your brand</p>
              </header>

              <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                <button v-for="color in colorPalettes" :key="color.id" @click="selectColor(color)"
                  :class="['color-card relative text-left bg-white border-2 p-3 sm:p-4 rounded-2xl cursor-pointer hover:border-indigo-200 hover:shadow-lg transition-all', selectedColor === color.id ? 'color-card-active border-indigo-600 ring-2 ring-indigo-100' : 'border-slate-100']">
                  
                  <!-- Color Preview with Gradient -->
                  <div class="relative w-full h-14 sm:h-16 rounded-xl mb-2 sm:mb-3 overflow-hidden shadow-inner">
                    <div :style="{ backgroundColor: color.hex }" class="absolute inset-0"></div>
                    <div :style="getColorGradientStyle(color.hex)" class="absolute inset-0"></div>
                  </div>

                  <h3 class="font-bold text-xs sm:text-sm text-slate-800">{{ color.name }}</h3>
                  <p class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5">{{ color.category }}</p>
                  
                  <div v-if="selectedColor === color.id" class="absolute top-2 sm:top-3 right-2 sm:right-3 w-5 h-5 sm:w-6 sm:h-6 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-check text-[10px] sm:text-xs" :style="{ color: color.hex }"></i>
                  </div>
                </button>

                <!-- Custom Color -->
                <div class="relative bg-gradient-to-br from-slate-50 to-slate-100 border-2 border-dashed border-slate-300 p-3 sm:p-4 rounded-2xl hover:border-indigo-300 transition-all">
                  <label for="custom-color-input" class="cursor-pointer block">
                    <div class="relative w-full h-14 sm:h-16 rounded-xl mb-2 sm:mb-3 overflow-hidden shadow-inner border-2 border-white">
                      <input id="custom-color-input" v-model="customColor" @input="handleCustomColor" type="color"
                        class="absolute inset-0 w-full h-full cursor-pointer opacity-0">
                      <div :style="{ backgroundColor: customColor || '#6366f1' }" class="absolute inset-0"></div>
                      <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fa-solid fa-palette text-white text-xl opacity-50"></i>
                      </div>
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-800">Custom Color</h3>
                    <p class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5">Pick your own</p>
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

          </div>
        </section>

        <!-- Preview Panel (Right) - Scrollable -->
        <section class="hidden lg:flex flex-[1.5] preview-container items-start justify-center p-12 overflow-y-auto custom-scrollbar h-full">
          <div class="w-full max-w-[900px]">
            <!-- Multi-Page Container with minimal gap -->
            <div class="space-y-8">
              <!-- Scaling wrapper for Page 1 -->
              <div :style="{ height: `calc(297mm * ${previewScale})`, display: 'flex', justifyContent: 'center' }">
                <!-- Page 1 -->
                <div 
                  id="resume-preview"
                  class="resume-paper bg-white shadow-2xl transition-all duration-300"
                  :class="{ 'overflow-hidden': needsSecondPage }"
                  :style="{ 
                    width: '210mm',
                    minHeight: '297mm',
                    height: '297mm',
                    overflow: 'hidden',
                    transform: `scale(${previewScale})`,
                    transformOrigin: 'top center'
                  }"
                >
                  <!-- Loading State -->
                  <div v-if="isTemplateLoading" class="p-16 text-center text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-2xl mb-4"></i>
                    <p>Loading template...</p>
                  </div>
                  
                  <!-- Dynamic Template Component - Page 1 -->
                  <div v-else-if="currentTemplateComponent" class="resume-content">
                    <ClientOnly>
                      <Transition name="fade" mode="out-in">
                        <component 
                          :key="selectedTemplate + '-page1'"
                          :is="currentTemplateComponent"
                          :data="page1Data"
                          :theme="currentThemeConfig"
                        />
                      </Transition>
                    </ClientOnly>
                  </div>
                  
                  <!-- Fallback Loading State -->
                  <div v-else class="p-16 text-center text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-2xl mb-4"></i>
                    <p>Loading template...</p>
                  </div>
                </div>
              </div>

              <!-- Scaling wrapper for Page 2 (Conditional) -->
              <div 
                v-if="needsSecondPage && currentTemplateComponent && page2Data"
                :style="{ height: `calc(297mm * ${previewScale})`, display: 'flex', justifyContent: 'center' }"
              >
                <!-- Page 2 -->
                <div 
                  class="resume-paper bg-white shadow-2xl transition-all duration-300"
                  :style="{ 
                    width: '210mm',
                    minHeight: '297mm',
                    height: 'auto',
                    maxHeight: '297mm',
                    overflow: 'hidden',
                    transform: `scale(${previewScale})`,
                    transformOrigin: 'top center'
                  }"
                >
                  <div class="resume-content">
                    <!-- Page Indicator -->
                    <div class="text-right text-xs text-slate-400 mb-6 font-medium">Page 2</div>
                    
                    <ClientOnly>
                      <Transition name="fade" mode="out-in">
                        <component 
                          :key="selectedTemplate + '-page2'"
                          :is="currentTemplateComponent"
                          :data="page2Data"
                          :theme="currentThemeConfig"
                        />
                      </Transition>
                    </ClientOnly>
                  </div>
                </div>
              </div>
            </div>

            <!-- Preview Controls -->
            <div class="preview-controls flex items-center justify-center gap-4 mt-6">
              <button 
                @click="adjustZoom(-0.1)" 
                class="zoom-btn w-10 h-10 flex items-center justify-center bg-white border-2 border-slate-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 transition-all"
              >
                <i class="fa-solid fa-minus text-slate-600"></i>
              </button>
              <span class="zoom-level text-sm font-bold text-slate-600 min-w-[4rem] text-center">
                {{ Math.round(previewScale * 100) }}%
              </span>
              <button 
                @click="adjustZoom(0.1)" 
                class="zoom-btn w-10 h-10 flex items-center justify-center bg-white border-2 border-slate-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 transition-all"
              >
                <i class="fa-solid fa-plus text-slate-600"></i>
              </button>
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
        <div class="preview-container p-4 space-y-4">
          <!-- Page 1 -->
          <div 
            :key="selectedTemplate + '-mobile-page1'" 
            class="bg-white shadow-2xl mx-auto overflow-hidden relative transition-all duration-300"
            :style="{ width: '100%', maxWidth: '210mm', minHeight: '297mm', height: '297mm', overflow: 'hidden' }"
          >
            <!-- Loading State -->
            <div v-if="isTemplateLoading" class="p-8 text-center text-slate-400">
              <i class="fa-solid fa-spinner fa-spin text-xl mb-3"></i>
              <p class="text-sm">Loading template...</p>
            </div>
            
            <!-- Dynamic Template Component - Page 1 -->
            <div v-else-if="currentTemplateComponent" class="resume-content">
              <ClientOnly>
                <Transition name="fade" mode="out-in">
                  <component 
                    :key="selectedTemplate + '-mobile'  "
                    :is="currentTemplateComponent"
                    :data="page1Data"
                    :theme="currentThemeConfig"
                  />
                </Transition>
              </ClientOnly>
            </div>
            
            <!-- Fallback Loading State -->
            <div v-else class="p-8 text-center text-slate-400">
              <i class="fa-solid fa-spinner fa-spin text-xl mb-3"></i>
              <p class="text-sm">Loading template...</p>
            </div>
          </div>

          <!-- Page 2 (Conditional) -->
          <div 
            v-if="needsSecondPage && currentTemplateComponent && page2Data"
            :key="selectedTemplate + '-mobile-page2'" 
            class="bg-white shadow-2xl mx-auto overflow-hidden relative transition-all duration-300"
            :style="{ width: '100%', maxWidth: '210mm', minHeight: '297mm', height: 'auto', maxHeight: '297mm', overflow: 'hidden' }"
          >
            <div class="resume-content">
              <!-- Page Indicator for Mobile -->
              <div class="text-right text-xs text-slate-400 mb-4 font-medium">Page 2</div>
              
              <ClientOnly>
                <Transition name="fade" mode="out-in">
                  <component 
                    :key="selectedTemplate + '-mobile-page2'"
                    :is="currentTemplateComponent"
                    :data="page2Data"
                    :theme="currentThemeConfig"
                  />
                </Transition>
              </ClientOnly>
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

/* Fade transition for template switching */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Ensure resume paper can grow dynamically */
.resume-paper {
  transition: height 0.3s ease;
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

/* Toastr Custom Styles - Ensure visibility */
#toast-container {
  z-index: 999999 !important;
}

#toast-container > div {
  opacity: 1 !important;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
  border-radius: 12px !important;
  padding: 16px 20px !important;
  min-width: 300px !important;
}

#toast-container .toast-success {
  background-color: #10b981 !important;
}

#toast-container .toast-error {
  background-color: #ef4444 !important;
}

#toast-container .toast-message {
  font-size: 14px !important;
}

#toast-container .toast-title {
  font-weight: 700 !important;
  font-size: 15px !important;
}

/* ATS Floating Badge Styles */
.ats-floating-badge {
  padding: 16px;
  background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%);
  width: 130px;
  height: 90px;
  position: fixed;
  z-index: 50;
  right: 24px;
  top: 50%;
  transform: translateY(-50%);
  border-radius: 24px;
  color: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  box-shadow: 0 25px 50px -12px rgba(234, 88, 12, 0.4);
  font-family: 'Inter', sans-serif;
  border: 1px solid rgba(255, 255, 255, 0.2);
  cursor: default;
  user-select: none;
  animation: ats-float 4s ease-in-out infinite;
  overflow: hidden;
}

.ats-shimmer-sweep {
  position: absolute;
  top: 0;
  left: 0;
  width: 50%;
  height: 100%;
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.3) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  transform: skewX(-25deg);
  animation: ats-shimmer 3s infinite;
}

.ats-pulse-burst {
  box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.7);
  animation: ats-float 4s ease-in-out infinite, pulse-glow 2s infinite;
}

@keyframes ats-float {
  0%, 100% { transform: translateY(-50%); }
  50% { transform: translateY(-58%); }
}

@keyframes ats-shimmer {
  0% { transform: translateX(-200%) skewX(-25deg); }
  100% { transform: translateX(300%) skewX(-25deg); }
}

@keyframes pulse-glow {
  0% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.7); }
  70% { box-shadow: 0 0 0 20px rgba(234, 88, 12, 0); }
  100% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0); }
}

.ats-floating-label {
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  opacity: 0.9;
  margin-bottom: 4px;
}

.ats-floating-score-wrapper {
  display: flex;
  align-items: baseline;
  gap: 1px;
}

.ats-floating-score {
  font-size: 32px;
  font-weight: 900;
  line-height: 1;
}

.ats-floating-total {
  font-size: 14px;
  font-weight: 600;
  opacity: 0.8;
}

/* Responsive adjustments for ATS Badge */
@media (max-width: 1024px) {
  .ats-floating-badge {
    width: 80px;
    height: 60px;
    right: 25px;
    top: 50%;
    transform: translateY(-50%);
    border-radius: 16px;
    padding: 10px;
    animation: ats-float 4s ease-in-out infinite;
    opacity: 0.8;
  }

  .ats-floating-score {
    font-size: 20px;
  }
  
  .ats-floating-label {
    font-size: 6px;
  }
  
  @keyframes ats-float-mobile {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
  }
}
</style>
