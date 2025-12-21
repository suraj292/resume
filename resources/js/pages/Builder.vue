<template>
  <div class="builder-app bg-slate-50 text-slate-900 font-sans antialiased h-screen flex flex-col">
    
    <!-- Top Navigation -->
    <nav class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 z-50 flex-shrink-0">
      <div class="flex items-center gap-3">
        <router-link to="/" class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold italic shadow-indigo-200 shadow-lg hover:bg-indigo-700 transition-colors">
          R
        </router-link>
        <span class="font-display font-bold text-lg tracking-tight hidden md:block">ResumeAI</span>
      </div>
      <div class="flex items-center gap-4">
        <router-link to="/" class="text-sm text-slate-600 hover:text-slate-900 transition-colors hidden sm:inline">Home</router-link>
        <router-link to="/templates" class="text-sm text-slate-600 hover:text-slate-900 transition-colors hidden sm:inline">Templates</router-link>
        <router-link to="/ats-checker" class="text-sm text-slate-600 hover:text-slate-900 transition-colors hidden sm:inline">ATS Checker</router-link>
        <router-link to="/pricing" class="text-sm text-slate-600 hover:text-slate-900 transition-colors hidden sm:inline">Pricing</router-link>
      </div>
    </nav>

    <!-- Builder Action Bar -->
    <div class="h-14 bg-slate-900 text-white flex items-center justify-between px-4 z-40 flex-shrink-0">
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

    <!-- Main View -->
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

              <!-- Success Message -->
              <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-start gap-3 animate-slide-up">
                <i class="fa-solid fa-circle-check text-green-600 mt-0.5"></i>
                <p class="text-sm text-green-800">{{ successMessage }}</p>
              </div>

              <!-- Error Message -->
              <div v-if="uploadError" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3 animate-slide-up">
                <i class="fa-solid fa-circle-exclamation text-red-600 mt-0.5"></i>
                <p class="text-sm text-red-800">{{ uploadError }}</p>
              </div>

              <div class="space-y-10">
                <!-- Resume Context Section -->
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Current Resume</h3>
                    <div class="flex bg-slate-100 p-0.5 rounded-lg">
                      <button @click="resumeInputMode = 'upload'" :class="['px-3 py-1 text-[9px] font-bold rounded-md transition-all', resumeInputMode === 'upload' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500']">Upload</button>
                      <button @click="resumeInputMode = 'paste'" :class="['px-3 py-1 text-[9px] font-bold rounded-md transition-all', resumeInputMode === 'paste' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500']">Paste Text</button>
                    </div>
                  </div>
                  
                  <div v-if="resumeInputMode === 'upload'" :class="['group relative border-2 border-dashed rounded-3xl p-8 text-center transition-all cursor-pointer', isProcessing ? 'border-indigo-400 bg-indigo-50/30' : 'border-slate-200 bg-slate-50/50 hover:border-indigo-400 hover:bg-indigo-50/30']">
                    <input type="file" @change="handleResumeUpload" :disabled="isProcessing" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf,.doc,.docx">
                    <div class="flex flex-col items-center">
                      <i v-if="!isProcessing" class="fa-solid fa-file-pdf text-indigo-400 text-2xl mb-3"></i>
                      <i v-else class="fa-solid fa-spinner fa-spin text-indigo-600 text-2xl mb-3"></i>
                      <p class="text-sm font-bold text-slate-700">{{ isProcessing ? 'Processing resume...' : 'Drop your current resume' }}</p>
                      <p class="text-[9px] text-slate-400 mt-1">{{ isProcessing ? 'Extracting text...' : 'PDF or Word' }}</p>
                    </div>
                  </div>
                  
                  <div v-else class="bg-white border border-slate-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500/10 focus-within:border-indigo-500 transition-all">
                    <textarea v-model="resumeText" rows="4" class="w-full p-4 text-xs outline-none resize-none placeholder:text-slate-300" placeholder="Paste your existing resume text here..."></textarea>
                    <div v-if="resumeText" class="px-4 pb-2 text-[10px] text-slate-400">
                      {{ resumeText.length }} characters
                    </div>
                  </div>
                </div>

                <!-- Job Description Context Section -->
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Target Job Description</h3>
                    <div class="flex bg-slate-100 p-0.5 rounded-lg">
                      <button @click="jobInputMode = 'paste'" :class="['px-3 py-1 text-[9px] font-bold rounded-md transition-all', jobInputMode === 'paste' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500']">Paste Text</button>
                      <button @click="jobInputMode = 'upload'" :class="['px-3 py-1 text-[9px] font-bold rounded-md transition-all', jobInputMode === 'upload' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500']">Upload File</button>
                    </div>
                  </div>
                  
                  <div v-if="jobInputMode === 'paste'" class="bg-white border border-slate-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500/10 focus-within:border-indigo-500 transition-all">
                    <textarea v-model="jobDescription" rows="6" class="w-full p-4 text-xs outline-none resize-none placeholder:text-slate-300" placeholder="Paste the job requirements..."></textarea>
                    <div v-if="jobDescription" class="px-4 pb-2 text-[10px] text-slate-400">
                      {{ jobDescription.length }} characters
                    </div>
                  </div>
                  
                  <div v-else :class="['group relative border-2 border-dashed rounded-3xl p-8 text-center transition-all cursor-pointer', isProcessing ? 'border-indigo-400 bg-indigo-50/30' : 'border-slate-200 bg-slate-50/50 hover:border-indigo-400 hover:bg-indigo-50/30']">
                    <input type="file" @change="handleJobUpload" :disabled="isProcessing" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf,.doc,.docx,.txt">
                    <div class="flex flex-col items-center">
                      <i v-if="!isProcessing" class="fa-solid fa-briefcase text-slate-300 text-2xl mb-3"></i>
                      <i v-else class="fa-solid fa-spinner fa-spin text-indigo-600 text-2xl mb-3"></i>
                      <p class="text-sm font-bold text-slate-700">{{ isProcessing ? 'Processing file...' : 'Upload Job Ad / PDF' }}</p>
                    </div>
                  </div>
                </div>

                <button @click="saveContext" :disabled="isProcessing || (!resumeText && !jobDescription)" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                  <i v-if="!isProcessing" class="fa-solid fa-bolt-lightning text-amber-400"></i>
                  <i v-else class="fa-solid fa-spinner fa-spin"></i>
                  {{ isProcessing ? 'Processing...' : 'Save Context for AI' }}
                </button>
              </div>
            </div>

            <!-- Tab: Manual Content -->
            <div v-show="activeTab === 'manual'" class="tab-content">
              <header class="mb-8">
                <h2 class="text-xl font-display font-bold text-slate-800">Personal Details</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium">Reorder your information blocks by dragging the handles <i class="fa-solid fa-grip-vertical mx-1"></i>.</p>
              </header>

              <div class="space-y-4" ref="personalFieldsContainer">
                <div v-for="field in personalFields" :key="field.id" class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
                  <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ field.label }}</label>
                    <input v-model="formData[field.id]" :type="field.type" :placeholder="field.placeholder" class="w-full bg-transparent font-bold text-slate-800 outline-none text-base">
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
                  <button @click="generateResume" class="group relative bg-indigo-600 hover:bg-indigo-700 text-white p-5 rounded-3xl transition-all shadow-xl shadow-indigo-100 overflow-hidden text-left">
                    <div class="relative z-10">
                      <i class="fa-solid fa-sparkles mb-3 block text-indigo-200"></i>
                      <span class="block text-sm font-bold leading-tight">Generate Entire Resume</span>
                      <span class="block text-[10px] text-indigo-100 mt-1">Based on Job Context</span>
                    </div>
                    <i class="fa-solid fa-bolt absolute -right-2 -bottom-2 text-6xl text-white/10 group-hover:scale-110 transition-transform"></i>
                  </button>
                  
                  <button @click="optimizeForATS" class="group relative bg-white border border-slate-200 hover:border-indigo-400 p-5 rounded-3xl transition-all shadow-sm text-left">
                    <div class="relative z-10">
                      <i class="fa-solid fa-arrows-spin mb-3 block text-indigo-500"></i>
                      <span class="block text-sm font-bold text-slate-800 leading-tight">Optimize for ATS</span>
                      <span class="block text-[10px] text-slate-400 mt-1">Match keywords & format</span>
                    </div>
                  </button>
                </div>

                <!-- Smart Suggestions -->
                <div class="pt-4 border-t border-slate-100">
                  <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Smart Suggestions</h3>
                  <div class="space-y-3">
                    <!-- ATS Score -->
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center text-xs font-bold">{{ atsScore }}</div>
                        <div>
                          <p class="text-xs font-bold text-slate-700">ATS Match Score</p>
                          <p class="text-[10px] text-slate-400">Improve by adding "Kubernetes"</p>
                        </div>
                      </div>
                      <button class="text-[10px] font-bold text-indigo-600 hover:underline">Details</button>
                    </div>

                    <!-- AI Features -->
                    <div @click="improveBulletPoints" class="group flex items-center justify-between p-4 hover:bg-indigo-50/50 rounded-2xl border border-transparent hover:border-indigo-100 transition-all cursor-pointer">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                          <i class="fa-solid fa-list-check text-xs"></i>
                        </div>
                        <div>
                          <p class="text-xs font-bold text-slate-700">Bullet Point Improver</p>
                          <p class="text-[10px] text-slate-400">Rewrite with impact verbs</p>
                        </div>
                      </div>
                      <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-indigo-400 transition-colors"></i>
                    </div>

                    <div @click="analyzeSkillGap" class="group flex items-center justify-between p-4 hover:bg-indigo-50/50 rounded-2xl border border-transparent hover:border-indigo-100 transition-all cursor-pointer">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                          <i class="fa-solid fa-magnifying-glass-chart text-xs"></i>
                        </div>
                        <div>
                          <p class="text-xs font-bold text-slate-700">Skill Gap Analysis</p>
                          <p class="text-[10px] text-slate-400">Find what keywords are missing</p>
                        </div>
                      </div>
                      <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-indigo-400 transition-colors"></i>
                    </div>
                  </div>
                </div>

                <!-- Tone Selection -->
                <div class="p-5 bg-indigo-50/30 rounded-3xl border border-indigo-100">
                  <label class="text-[10px] font-bold text-indigo-900 uppercase tracking-widest block mb-3">AI Writing Tone</label>
                  <div class="flex gap-2">
                    <button v-for="tone in tones" :key="tone" @click="selectedTone = tone" :class="['flex-1 py-2 px-3 text-[10px] font-bold rounded-xl transition-all', selectedTone === tone ? 'bg-white text-indigo-600 shadow-sm border border-indigo-200' : 'text-slate-500 hover:bg-white']">{{ tone }}</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab: Templates -->
            <div v-show="activeTab === 'templates'" class="tab-content">
              <header class="mb-8">
                <h2 class="text-xl font-display font-bold text-slate-800">Choose a Layout</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium">Select a design that matches your industry and seniority level.</p>
              </header>

              <div class="grid grid-cols-2 gap-4 sm:gap-6">
                <div 
                  v-for="template in templatesFromJSON" 
                  :key="template.id" 
                  @click="selectTemplate(template.id)" 
                  :class="[
                    'template-card group relative bg-white border-2 p-3 rounded-2xl cursor-pointer hover:border-indigo-300 hover:shadow-xl transition-all duration-300',
                    selectedTemplate === template.id ? 'template-card-active border-indigo-500 shadow-lg ring-2 ring-indigo-100' : 'border-slate-200'
                  ]">
                  <!-- Template Thumbnail -->
                  <div :class="['aspect-[4/5] rounded-xl mb-3 overflow-hidden relative', template.thumbnail.bg]">
                    <TemplateRenderer :elements="template.thumbnail.elements" :mainClass="template.thumbnail.mainClass" />
                    
                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-indigo-600/0 group-hover:bg-indigo-600/10 transition-all duration-300 flex items-center justify-center">
                      <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white rounded-full p-2 shadow-lg">
                        <i class="fa-solid fa-eye text-indigo-600 text-sm"></i>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Template Info -->
                  <div class="px-2 pb-2">
                    <div class="flex items-center justify-between mb-1">
                      <p class="text-sm font-bold text-slate-800">{{ template.name }}</p>
                      <i v-if="selectedTemplate === template.id" class="fa-solid fa-circle-check text-indigo-600 text-base"></i>
                    </div>
                    <div class="flex flex-wrap gap-1 mb-2">
                      <span v-for="tag in template.tags" :key="tag" class="text-[9px] px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full font-semibold">{{ tag }}</span>
                    </div>
                    <p class="text-[10px] text-slate-500 leading-relaxed truncate sm:whitespace-normal sm:overflow-visible sm:text-overflow-clip">{{ template.description }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab: Colors -->
            <div v-show="activeTab === 'colors'" class="tab-content">
              <header class="mb-8">
                <h2 class="text-xl font-display font-bold text-slate-800">Accent Colors</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium italic">Apply a brand identity to your resume with professionally tuned palettes.</p>
              </header>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button v-for="color in colorPalettes" :key="color.id" @click="selectColor(color)" :class="['color-card group text-left bg-white border-2 p-4 rounded-2xl transition-all', selectedColor === color.id ? 'color-card-active border-indigo-500' : 'border-slate-100']">
                  <div class="flex items-center gap-3 mb-3">
                    <div :style="{ backgroundColor: color.hex }" class="w-8 h-8 rounded-full shadow-lg"></div>
                    <div :style="{ backgroundColor: color.hex, opacity: 0.1 }" class="w-8 h-8 rounded-full border border-slate-200"></div>
                  </div>
                  <p class="text-[11px] font-bold text-slate-700">{{ color.name }}</p>
                  <p class="text-[9px] text-slate-400 uppercase font-bold tracking-tighter">{{ color.category }}</p>
                </button>

                <!-- Custom Color -->
                <div class="color-card group relative bg-white border-2 border-slate-100 p-4 rounded-2xl transition-all hover:border-indigo-100">
                  <div class="flex items-center gap-3 mb-3">
                    <div :style="{ backgroundColor: customColor }" class="w-8 h-8 rounded-full shadow-lg flex items-center justify-center overflow-hidden border border-slate-200">
                      <i v-if="!customColor" class="fa-solid fa-plus text-[10px] text-slate-400"></i>
                    </div>
                    <input type="color" v-model="customColor" @input="handleCustomColor" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    <div class="w-8 h-8 rounded-full bg-slate-50 border border-slate-100"></div>
                  </div>
                  <p class="text-[11px] font-bold text-slate-700">Custom Hue</p>
                  <p class="text-[9px] text-slate-400 uppercase font-bold tracking-tighter">Click to pick color</p>
                </div>
              </div>
            </div>

          </div>
        </section>

        <!-- Preview (Right) - Desktop -->
        <section class="hidden lg:flex flex-[1.5] preview-container items-start justify-center p-12 overflow-y-auto custom-scrollbar">
          <div class="sticky top-0 w-full max-w-[800px]">
            <ResumePreview 
              :templateId="selectedTemplate" 
              :formData="formData" 
              :accentColor="currentAccentColor"
            />
          </div>
        </section>
      </main>
    </div>

    <!-- Mobile Preview Modal -->
    <div v-if="previewModalOpen" class="mobile-preview-modal lg:hidden active">
      <div class="mobile-preview-content">
        <!-- Close Button -->
        <div class="sticky top-0 z-10 bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
          <h3 class="font-display font-bold text-lg">Resume Preview</h3>
          <button @click="closeMobilePreview" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-slate-100 rounded-lg transition-all">
            <i class="fa-solid fa-xmark text-xl"></i>
          </button>
        </div>
        <!-- Preview Content -->
        <div class="preview-container p-6">
          <div class="mx-auto max-w-2xl">
            <ResumePreview 
              :templateId="selectedTemplate" 
              :formData="formData" 
              :accentColor="currentAccentColor"
            />
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

<script setup>
import { ref, computed, onMounted } from 'vue'
import Sortable from 'sortablejs'
import axios from 'axios'
import templatesData from '../data/templates.json'
import TemplateRenderer from '../components/TemplateRenderer.vue'
import ResumePreview from '../components/ResumePreview.vue'

// State
const activeTab = ref('upload')
const sidebarOpen = ref(false)
const previewModalOpen = ref(false)
const lastSaved = ref('2m ago')
const personalFieldsContainer = ref(null)

// Form Data
const formData = ref({
  fullName: '',
  title: '',
  email: '',
  phone: '',
  location: ''
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
const atsScore = ref(72)
const selectedTone = ref('Professional')
const tones = ['Professional', 'Creative', 'Direct']

// Templates from JSON
const templatesFromJSON = ref(templatesData)
const selectedTemplate = ref(templatesData[0]?.id || 'modernist')

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
  { id: 'fullName', label: 'Full Name', type: 'text', placeholder: 'e.g. John Doe' },
  { id: 'title', label: 'Professional Title', type: 'text', placeholder: 'e.g. Software Engineer' },
  { id: 'email', label: 'Email Address', type: 'email', placeholder: 'john@example.com' },
  { id: 'phone', label: 'Phone Number', type: 'tel', placeholder: '+1 (555) 000-0000' },
  { id: 'location', label: 'Location / Address', type: 'text', placeholder: 'New York, NY' }
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

// Methods
const switchTab = (tabId) => {
  activeTab.value = tabId
  // Close sidebar on mobile when a tab is clicked
  if (window.innerWidth < 1024) {
    sidebarOpen.value = false
    document.body.style.overflow = ''
  }
}

const toggleMobileSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
  if (sidebarOpen.value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}

const closeMobileSidebarIfNeeded = () => {
  if (window.innerWidth < 1024) {
    sidebarOpen.value = false
    document.body.style.overflow = ''
  }
}

const openMobilePreview = () => {
  previewModalOpen.value = true
  document.body.style.overflow = 'hidden'
}

const closeMobilePreview = () => {
  previewModalOpen.value = false
  document.body.style.overflow = ''
}

const selectTemplate = (templateId) => {
  selectedTemplate.value = templateId
  const template = templatesFromJSON.value.find(t => t.id === templateId)
  console.log('Template selected:', template?.name || templateId)
  // TODO: Update preview with selected template layout
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
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
    const formDataObj = new FormData()
    formDataObj.append('file', file)

    // Extract text from file using backend API
    const response = await axios.post(`${apiUrl}/api/extract-text`, formDataObj, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (response.data.text) {
      resumeText.value = response.data.text
      parseResumeContent(response.data.text)
      successMessage.value = 'Resume uploaded and content extracted successfully!'
      setTimeout(() => successMessage.value = '', 3000)
    }
  } catch (error) {
    console.error('Resume upload error:', error)
    uploadError.value = error.response?.data?.error || 'Failed to extract text from resume'
  } finally {
    isProcessing.value = false
  }
}

const handleJobUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  isProcessing.value = true
  uploadError.value = ''

  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
    const formDataObj = new FormData()
    formDataObj.append('file', file)

    const response = await axios.post(`${apiUrl}/api/extract-text`, formDataObj, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (response.data.text) {
      jobDescription.value = response.data.text
      successMessage.value = 'Job description extracted successfully!'
      setTimeout(() => successMessage.value = '', 3000)
    }
  } catch (error) {
    console.error('Job upload error:', error)
    uploadError.value = error.response?.data?.error || 'Failed to extract text from file'
  } finally {
    isProcessing.value = false
  }
}

const parseResumeContent = (text) => {
  if (!text) return

  // Extract email
  const emailMatch = text.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/)
  if (emailMatch) {
    formData.value.email = emailMatch[0]
  }

  // Extract phone
  const phoneMatch = text.match(/(?:\+?1[-.]?)?(?:\(?\d{3}\)?[-.]?)?\d{3}[-.]?\d{4}/)
  if (phoneMatch) {
    formData.value.phone = phoneMatch[0]
  }

  // Extract name (first 2-3 capitalized words at the beginning)
  const nameMatch = text.match(/^([A-Z][a-z]+(?:\s+[A-Z][a-z]+){1,2})/m)
  if (nameMatch) {
    formData.value.fullName = nameMatch[1]
  }

  // Extract location (look for city, state patterns)
  const locationMatch = text.match(/([A-Z][a-z]+(?:\s+[A-Z][a-z]+)?,\s*[A-Z]{2}(?:\s+\d{5})?)/)
  if (locationMatch) {
    formData.value.location = locationMatch[1]
  }

  // Extract title (look for common job titles)
  const titlePatterns = [
    /(?:Senior|Junior|Lead|Principal)?\s*(?:Software|Full[\s-]?Stack|Front[\s-]?End|Back[\s-]?End|Web|Mobile)\s*(?:Engineer|Developer|Architect)/i,
    /(?:Product|Project|Program)\s*Manager/i,
    /(?:Data|ML|AI)\s*(?:Scientist|Engineer|Analyst)/i,
    /(?:UX|UI)\s*(?:Designer|Developer)/i,
    /(?:DevOps|Cloud|Systems)\s*Engineer/i
  ]
  
  for (const pattern of titlePatterns) {
    const titleMatch = text.match(pattern)
    if (titleMatch) {
      formData.value.title = titleMatch[0]
      break
    }
  }

  console.log('Parsed resume data:', formData.value)
}

const saveContext = async () => {
  if (!resumeText.value && !jobDescription.value) {
    uploadError.value = 'Please provide resume content or job description'
    return
  }

  isProcessing.value = true
  uploadError.value = ''
  successMessage.value = ''

  try {
    // Parse resume text if available and not already parsed
    if (resumeText.value && !formData.value.email) {
      parseResumeContent(resumeText.value)
    }

    // Store context in localStorage for AI to use
    localStorage.setItem('resumeContext', JSON.stringify({
      resumeText: resumeText.value,
      jobDescription: jobDescription.value,
      savedAt: new Date().toISOString()
    }))

    successMessage.value = 'Context saved! Personal details have been populated. Switch to "Manual Info" tab to review.'
    setTimeout(() => {
      successMessage.value = ''
      // Auto-switch to manual tab to show populated data
      activeTab.value = 'manual'
    }, 2000)

  } catch (error) {
    console.error('Save context error:', error)
    uploadError.value = 'Failed to save context'
  } finally {
    isProcessing.value = false
  }
}

const generateResume = () => {
  console.log('Generating resume...')
  // TODO: Implement AI resume generation
}

const optimizeForATS = () => {
  console.log('Optimizing for ATS...')
  // TODO: Implement ATS optimization
}

const improveBulletPoints = () => {
  console.log('Improving bullet points...')
  // TODO: Implement bullet point improvement
}

const analyzeSkillGap = () => {
  console.log('Analyzing skill gap...')
  // TODO: Implement skill gap analysis
}

const exportPDF = () => {
  console.log('Exporting PDF...')
  // TODO: Implement PDF export
}

// Initialize drag and drop
onMounted(() => {
  if (personalFieldsContainer.value) {
    Sortable.create(personalFieldsContainer.value, {
      handle: '.drag-handle',
      animation: 150,
      ghostClass: 'sortable-ghost',
      chosenClass: 'sortable-chosen',
      dragClass: 'sortable-drag',
      onEnd: (evt) => {
        // Reorder the personalFields array
        const movedItem = personalFields.value.splice(evt.oldIndex, 1)[0]
        personalFields.value.splice(evt.newIndex, 0, movedItem)
      }
    })
  }
})
</script>

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

/* Tooltip Styles */
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

.drag-handle:active {
  cursor: grabbing;
}

.sortable-ghost {
  opacity: 0.4;
  background: #f1f5f9;
  border: 2px dashed #cbd5e1;
}

.sortable-chosen {
  opacity: 0.8;
}

.sortable-drag {
  transform: rotate(5deg);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.template-card-active,
.color-card-active {
  border-color: #4f46e5 !important;
  box-shadow: 0 0 0 2px #4f46e5;
}

/* Mobile Styles */
@media (max-width: 1023px) {
  .mobile-sidebar-hidden {
    transform: translateX(-100%);
  }
  
  /* Ensure main content is full width on mobile */
  main {
    margin-left: 0 !important;
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

/* Floating Preview Button */
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
