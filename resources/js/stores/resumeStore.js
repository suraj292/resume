import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useResumeStore = defineStore('resume', () => {
  // State
  const formData = ref({
    // Personal Information
    fullName: '',
    title: '',
    email: '',
    phone: '',
    location: '',
    linkedin: '',
    github: '',
    portfolio: '',
    
    // Professional Summary
    summary: '',
    
    // Skills (categorized)
    skills: {
      backend: [],
      frontend: [],
      devops: [],
      other: []
    },
    
    // Work Experience
    experience: [],
    
    // Education
    education: [],
    
    // Achievements
    achievements: []
  })

  const selectedTemplate = ref('software-engineer')
  const selectedColor = ref('indigo')
  const customColor = ref('')
  const currentPage = ref(1)
  const totalPages = ref(1)
  
  // History for undo/redo
  const history = ref([])
  const historyIndex = ref(-1)
  const maxHistory = 50

  // Computed
  const accentColor = computed(() => {
    if (customColor.value) return customColor.value
    const colorMap = {
      indigo: '#4f46e5',
      emerald: '#059669',
      rose: '#e11d48',
      slate: '#334155',
      amber: '#d97706',
      violet: '#7c3aed'
    }
    return colorMap[selectedColor.value] || '#4f46e5'
  })

  const hasExperience = computed(() => {
    return formData.value.experience.some(exp => exp.position || exp.company)
  })

  const hasEducation = computed(() => {
    return formData.value.education.some(edu => edu.degree)
  })

  const hasSkills = computed(() => {
    return Object.values(formData.value.skills).some(arr => arr.length > 0)
  })

  const hasAchievements = computed(() => {
    return formData.value.achievements.some(ach => ach)
  })

  // Actions
  const updateField = (field, value) => {
    saveToHistory()
    if (field.includes('.')) {
      const parts = field.split('.')
      let obj = formData.value
      for (let i = 0; i < parts.length - 1; i++) {
        obj = obj[parts[i]]
      }
      obj[parts[parts.length - 1]] = value
    } else {
      formData.value[field] = value
    }
    saveToLocalStorage()
  }

  const addExperience = () => {
    saveToHistory()
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
    saveToLocalStorage()
  }

  const removeExperience = (index) => {
    saveToHistory()
    formData.value.experience.splice(index, 1)
    saveToLocalStorage()
  }

  const addEducation = () => {
    saveToHistory()
    formData.value.education.push({
      id: Date.now(),
      degree: '',
      institution: '',
      year: '',
      percentage: ''
    })
    saveToLocalStorage()
  }

  const removeEducation = (index) => {
    saveToHistory()
    formData.value.education.splice(index, 1)
    saveToLocalStorage()
  }

  const addSkill = (category) => {
    const skill = prompt('Enter skill name:')
    if (skill && skill.trim()) {
      saveToHistory()
      formData.value.skills[category].push(skill.trim())
      saveToLocalStorage()
    }
  }

  const removeSkill = (category, index) => {
    saveToHistory()
    formData.value.skills[category].splice(index, 1)
    saveToLocalStorage()
  }

  const addAchievement = () => {
    saveToHistory()
    formData.value.achievements.push('')
    saveToLocalStorage()
  }

  const removeAchievement = (index) => {
    saveToHistory()
    formData.value.achievements.splice(index, 1)
    saveToLocalStorage()
  }

  const setTemplate = (templateId) => {
    selectedTemplate.value = templateId
    saveToLocalStorage()
  }

  const setColor = (colorId) => {
    selectedColor.value = colorId
    customColor.value = ''
    saveToLocalStorage()
  }

  const setCustomColor = (color) => {
    customColor.value = color
    selectedColor.value = 'custom'
    saveToLocalStorage()
  }

  // History Management
  const saveToHistory = () => {
    const snapshot = JSON.stringify(formData.value)
    if (historyIndex.value < history.value.length - 1) {
      history.value = history.value.slice(0, historyIndex.value + 1)
    }
    history.value.push(snapshot)
    if (history.value.length > maxHistory) {
      history.value.shift()
    } else {
      historyIndex.value++
    }
  }

  const undo = () => {
    if (historyIndex.value > 0) {
      historyIndex.value--
      formData.value = JSON.parse(history.value[historyIndex.value])
      saveToLocalStorage()
    }
  }

  const redo = () => {
    if (historyIndex.value < history.value.length - 1) {
      historyIndex.value++
      formData.value = JSON.parse(history.value[historyIndex.value])
      saveToLocalStorage()
    }
  }

  const canUndo = computed(() => historyIndex.value > 0)
  const canRedo = computed(() => historyIndex.value < history.value.length - 1)

  // LocalStorage
  const saveToLocalStorage = () => {
    try {
      localStorage.setItem('resumeData', JSON.stringify({
        formData: formData.value,
        selectedTemplate: selectedTemplate.value,
        selectedColor: selectedColor.value,
        customColor: customColor.value
      }))
    } catch (error) {
      console.error('Error saving to localStorage:', error)
    }
  }

  const loadFromLocalStorage = () => {
    try {
      const saved = localStorage.getItem('resumeData')
      if (saved) {
        const data = JSON.parse(saved)
        formData.value = data.formData || formData.value
        selectedTemplate.value = data.selectedTemplate || selectedTemplate.value
        selectedColor.value = data.selectedColor || selectedColor.value
        customColor.value = data.customColor || customColor.value
      }
    } catch (error) {
      console.error('Error loading from localStorage:', error)
    }
  }

  const clearData = () => {
    if (confirm('Are you sure you want to clear all data?')) {
      formData.value = {
        fullName: '',
        title: '',
        email: '',
        phone: '',
        location: '',
        linkedin: '',
        github: '',
        portfolio: '',
        summary: '',
        skills: { backend: [], frontend: [], devops: [], other: [] },
        experience: [],
        education: [],
        achievements: []
      }
      history.value = []
      historyIndex.value = -1
      saveToLocalStorage()
    }
  }

  const exportData = () => {
    const data = {
      formData: formData.value,
      selectedTemplate: selectedTemplate.value,
      selectedColor: selectedColor.value,
      customColor: customColor.value,
      exportedAt: new Date().toISOString()
    }
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `resume-data-${Date.now()}.json`
    a.click()
    URL.revokeObjectURL(url)
  }

  const importData = (jsonData) => {
    try {
      const data = typeof jsonData === 'string' ? JSON.parse(jsonData) : jsonData
      saveToHistory()
      formData.value = data.formData || formData.value
      selectedTemplate.value = data.selectedTemplate || selectedTemplate.value
      selectedColor.value = data.selectedColor || selectedColor.value
      customColor.value = data.customColor || customColor.value
      saveToLocalStorage()
      return { success: true }
    } catch (error) {
      console.error('Error importing data:', error)
      return { success: false, error: error.message }
    }
  }

  // Initialize
  loadFromLocalStorage()

  return {
    // State
    formData,
    selectedTemplate,
    selectedColor,
    customColor,
    currentPage,
    totalPages,
    history,
    historyIndex,
    
    // Computed
    accentColor,
    hasExperience,
    hasEducation,
    hasSkills,
    hasAchievements,
    canUndo,
    canRedo,
    
    // Actions
    updateField,
    addExperience,
    removeExperience,
    addEducation,
    removeEducation,
    addSkill,
    removeSkill,
    addAchievement,
    removeAchievement,
    setTemplate,
    setColor,
    setCustomColor,
    undo,
    redo,
    saveToLocalStorage,
    loadFromLocalStorage,
    clearData,
    exportData,
    importData
  }
})
