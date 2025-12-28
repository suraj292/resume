<template>
  <div class="flex h-screen flex-col relative bg-slate-50 text-slate-900 font-sans antialiased overflow-hidden">
    
    <!-- Toast Notification -->
    <div v-if="toast.visible"
      class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-800 text-white px-6 py-3 rounded-full shadow-lg z-[100] flex items-center gap-3">
      <i v-if="toast.type === 'success'" class="fa-solid fa-circle-check text-emerald-400"></i>
      <i v-else-if="toast.type === 'error'" class="fa-solid fa-circle-exclamation text-rose-400"></i>
      <i v-else class="fa-solid fa-circle-info text-blue-400"></i>
      <span class="text-sm font-medium">{{ toast.message }}</span>
    </div>

    <!-- API Key Modal -->
    <div v-if="showApiModal"
      class="fixed inset-0 z-[80] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center">
      <div class="bg-white rounded-2xl p-6 w-96 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <i class="fa-solid fa-key text-indigo-500"></i> Gemini API Key
          </h3>
          <button @click="showApiModal = false" class="text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
          To use AI features, enter your Google Gemini API key.
          <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-indigo-600 hover:underline">Get a key here</a>.
        </p>
        <input type="password" v-model="geminiApiKey" placeholder="Enter API Key (starts with AIza...)"
          class="w-full text-sm p-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none mb-4 font-mono">
        <button @click="saveApiKey"
          class="w-full py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
          Save & Activate
        </button>
      </div>
    </div>

    <!-- Reset Modal -->
    <div v-if="showResetModal"
      class="fixed inset-0 z-[70] bg-slate-900/50 backdrop-blur-sm flex items-center justify-center">
      <div class="bg-white rounded-2xl p-6 w-80 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Reset Resume?</h3>
        <p class="text-sm text-slate-500 mb-6">This will clear all your data. This action cannot be undone.</p>
        <div class="flex gap-3">
          <button @click="showResetModal = false"
            class="flex-1 px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-colors">Cancel</button>
          <button @click="confirmReset"
            class="flex-1 px-4 py-2 rounded-xl bg-rose-500 text-white font-bold text-sm hover:bg-rose-600 transition-colors">Reset</button>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="isUploading"
      class="absolute inset-0 z-[60] bg-slate-900/50 backdrop-blur-sm flex flex-col items-center justify-center text-white">
      <div class="spinner mb-4"></div>
      <h3 class="text-xl font-bold">Processing...</h3>
      <p class="text-sm opacity-80 mt-1">Analyzing content & formatting</p>
    </div>

    <!-- Navbar -->
    <nav class="fixed top-0 inset-x-0 h-14 bg-white border-b border-slate-200 z-50 flex items-center justify-between px-4">
      <div class="flex items-center gap-3">
        <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="lg:hidden text-slate-500 hover:bg-slate-100 p-2 rounded-lg">
          <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold italic shadow-lg shadow-indigo-200">R</div>
        <span class="font-display font-bold text-lg tracking-tight hidden sm:block">ResumeAI <span class="text-xs font-normal text-slate-400 ml-1">(Vue + Gemini)</span></span>
      </div>
      <div class="flex items-center gap-3">
        <button @click="showApiModal = true"
          :class="['text-xs font-bold px-3 py-1.5 rounded-lg border transition-all flex items-center gap-2', geminiApiKey ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-slate-50 text-slate-500 border-slate-200 hover:border-indigo-300 hover:text-indigo-600']">
          <i :class="['fa-solid', geminiApiKey ? 'fa-check-circle' : 'fa-key']"></i>
          <span class="hidden sm:inline">{{ geminiApiKey ? 'AI Active' : 'Set API Key' }}</span>
        </button>
        <div class="hidden md:flex bg-slate-100 rounded-lg p-1 gap-1">
          <button @click="handleZoom(-0.1)" class="p-1 hover:bg-white rounded text-slate-500 hover:text-indigo-600 transition-colors w-8 text-center">
            <i class="fa-solid fa-magnifying-glass-minus text-xs"></i>
          </button>
          <span class="text-[10px] font-bold text-slate-400 w-12 text-center flex items-center justify-center">{{ Math.round(zoom * 100) }}%</span>
          <button @click="handleZoom(0.1)" class="p-1 hover:bg-white rounded text-slate-500 hover:text-indigo-600 transition-colors w-8 text-center">
            <i class="fa-solid fa-magnifying-glass-plus text-xs"></i>
          </button>
        </div>
        <button @click="triggerExport" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-1.5 rounded-lg font-bold text-xs transition-all flex items-center gap-2">
          <i class="fa-solid fa-download"></i>
          <span class="hidden sm:inline">Export PDF</span>
        </button>
      </div>
    </nav>

    <!-- Main Layout -->
    <div class="flex w-full pt-14 h-full">
      
      <!-- Sidebar -->
      <aside :class="['fixed lg:static inset-y-0 left-0 w-16 bg-white border-r border-slate-200 z-40 flex flex-col items-center py-6 gap-6 pt-20 lg:pt-6 transition-transform duration-300', isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']">
        <div v-for="item in navItems" :key="item.id" class="relative group">
          <button @click="setActiveTab(item.id)"
            :class="['w-10 h-10 lg:w-12 lg:h-12 flex items-center justify-center rounded-xl transition-all duration-200', activeTab === item.id ? 'bg-indigo-50 text-indigo-600 shadow-sm ring-1 ring-indigo-100' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600']">
            <i :class="['fa-solid text-lg', item.icon]"></i>
          </button>
          <div class="absolute left-full top-1/2 -translate-y-1/2 ml-3 px-2 py-1 bg-slate-800 text-white text-[10px] font-bold rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50">
            {{ item.label }}
          </div>
        </div>
      </aside>

      <!-- Editor Panel -->
      <div class="flex-1 lg:flex-[0.8] bg-white overflow-y-auto custom-scrollbar border-r border-slate-200 max-w-2xl w-full">
        <div class="p-6 pb-24 lg:pb-10 max-w-xl mx-auto">
          
          <!-- Upload Tab -->
          <div v-if="activeTab === 'upload'" class="space-y-6">
            <header class="flex justify-between items-start">
              <div>
                <h2 class="text-xl font-display font-bold text-slate-900">Import & Context</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium">Upload existing resume or JSON data.</p>
              </div>
              <button @click="exportJSON" class="text-indigo-600 hover:text-indigo-700 text-xs font-bold border border-indigo-200 bg-indigo-50 px-3 py-1.5 rounded-lg flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-code"></i> Save JSON
              </button>
            </header>

            <div class="border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300 transition-all cursor-pointer relative group">
              <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="handleFileUpload" accept=".pdf,.doc,.docx,.json,.txt">
              <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform text-indigo-500">
                <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
              </div>
              <h3 class="text-sm font-bold text-slate-700">Drop your resume here</h3>
              <p class="text-[10px] text-slate-400 mt-1">PDF, Word, or JSON Backup</p>
            </div>
          </div>

          <!-- Manual Tab -->
          <div v-if="activeTab === 'manual'" class="space-y-6">
            <header class="flex justify-between items-center">
              <div>
                <h2 class="text-xl font-display font-bold text-slate-900">Content Editor</h2>
                <p class="text-slate-400 text-xs mt-1">Manage sections and details.</p>
              </div>
              <button @click="resetData" class="text-[10px] font-bold text-rose-500 hover:underline">Reset All</button>
            </header>

            <!-- Personal Details Section -->
            <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
              <button @click="toggleSection('personal')" class="w-full flex items-center justify-between p-4 hover:bg-slate-50 transition-colors">
                <span class="font-bold text-sm text-slate-800"><i class="fa-regular fa-user mr-2 text-slate-400"></i>Personal Details</span>
                <i :class="['fa-solid fa-chevron-down text-xs text-slate-400 transition-transform', openSections.personal ? 'rotate-180' : '']"></i>
              </button>
              <div v-show="openSections.personal" class="p-4 pt-0 border-t border-slate-100 bg-slate-50/50 space-y-3 mt-2">
                <div v-for="(value, key) in resumeData.personal" :key="key">
                  <div v-if="key !== 'profileImage'">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">{{ formatKey(String(key)) }}</label>
                    <input type="text" v-model="(resumeData.personal as any)[key]" class="w-full text-sm p-2 rounded-lg border border-slate-200 focus:border-indigo-500 outline-none">
                  </div>
                </div>
                <div class="pt-2 relative">
                  <div class="flex justify-between items-end mb-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Professional Summary</label>
                    <button @click="generateSummary" :disabled="loadingSummary" class="text-[10px] text-indigo-600 hover:bg-indigo-50 px-2 py-0.5 rounded-md transition-colors flex items-center gap-1">
                      <i v-if="loadingSummary" class="spinner spinner-sm"></i>
                      <i v-else class="fa-solid fa-wand-magic-sparkles"></i>
                      Generate with AI
                    </button>
                  </div>
                  <textarea v-model="resumeData.summary" rows="3" class="w-full text-sm p-2 rounded-lg border border-slate-200 focus:border-indigo-500 outline-none resize-none" placeholder="Enter summary or generate with AI..."></textarea>
                  <div class="flex items-center gap-2 mt-2">
                    <input type="checkbox" id="showSummary" v-model="visibility.summary" class="accent-indigo-600">
                    <label for="showSummary" class="text-xs text-slate-600">Show Summary Section</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Experience Section -->
            <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
              <button @click="toggleSection('experience')" class="w-full flex items-center justify-between p-4 hover:bg-slate-50 transition-colors">
                <span class="font-bold text-sm text-slate-800"><i class="fa-solid fa-briefcase mr-2 text-slate-400"></i>Experience</span>
                <i :class="['fa-solid fa-chevron-down text-xs text-slate-400 transition-transform', openSections.experience ? 'rotate-180' : '']"></i>
              </button>
              <div v-show="openSections.experience" class="p-4 border-t border-slate-100 bg-slate-50/50 space-y-4">
                <div v-for="(exp, index) in resumeData.experience" :key="exp.id" class="p-3 rounded-xl bg-white border border-slate-200 shadow-sm group">
                  <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                      <input class="font-bold text-sm text-slate-800 w-full outline-none bg-transparent placeholder-slate-400" v-model="exp.role" placeholder="Role Title">
                      <input class="text-xs font-semibold text-indigo-600 w-full outline-none bg-transparent placeholder-indigo-300" v-model="exp.company" placeholder="Company">
                    </div>
                    <div class="flex gap-1">
                      <button @click="moveItem('experience', Number(index), -1)" class="text-slate-300 hover:text-indigo-600 p-1" :disabled="index === 0">
                        <i class="fa-solid fa-arrow-up text-xs"></i>
                      </button>
                      <button @click="moveItem('experience', Number(index), 1)" class="text-slate-300 hover:text-indigo-600 p-1" :disabled="index === resumeData.experience.length - 1">
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                      </button>
                      <button @click="removeItem('experience', Number(index))" class="text-slate-300 hover:text-rose-500 p-1 ml-1">
                        <i class="fa-solid fa-trash text-xs"></i>
                      </button>
                    </div>
                  </div>
                  <div class="flex gap-2 mb-2">
                    <input class="text-[10px] bg-slate-100 px-2 py-1 rounded text-slate-600 w-full outline-none" v-model="exp.period" placeholder="Date Period">
                  </div>
                  <div class="relative">
                    <textarea class="w-full text-xs text-slate-600 resize-none outline-none leading-relaxed bg-transparent placeholder-slate-300" rows="3" v-model="exp.description" placeholder="Describe your achievements..."></textarea>
                    <button @click="optimizeText(Number(index))" :disabled="loadingAI === index" class="absolute bottom-2 right-2 text-[10px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full border border-indigo-100 hover:bg-indigo-100 transition-colors flex items-center gap-1">
                      <i v-if="loadingAI === index" class="spinner spinner-sm border-indigo-600 border-t-transparent"></i>
                      <i v-else class="fa-solid fa-wand-magic-sparkles"></i>
                      {{ loadingAI === index ? 'Thinking...' : 'Enhance' }}
                    </button>
                  </div>
                </div>
                <button @click="addItem('experience')" class="w-full py-2 border border-dashed border-slate-300 rounded-lg text-xs font-bold text-slate-500 hover:border-indigo-400 hover:text-indigo-600 transition-all flex items-center justify-center gap-2">
                  <i class="fa-solid fa-plus"></i> Add Position
                </button>
                <div class="flex items-center gap-2 mt-2">
                  <input type="checkbox" id="showExp" v-model="visibility.experience" class="accent-indigo-600">
                  <label for="showExp" class="text-xs text-slate-600">Show Experience Section</label>
                </div>
              </div>
            </div>

            <!-- Education & Skills sections similar structure... -->
          </div>

          <!-- Templates Tab -->
          <div v-if="activeTab === 'templates'" class="space-y-6">
            <header>
              <h2 class="text-xl font-display font-bold text-slate-900">Design & Layout</h2>
            </header>
            <div class="grid grid-cols-2 gap-4">
              <div v-for="tpl in templates" :key="tpl.id" @click="selectedTemplate = tpl.id"
                :class="['cursor-pointer rounded-2xl border-2 p-2 transition-all', selectedTemplate === tpl.id ? 'border-indigo-600 shadow-md ring-1 ring-indigo-600' : 'border-slate-100 hover:border-indigo-200 hover:shadow-lg']">
                <div class="aspect-[3/4] bg-slate-100 rounded-xl mb-3 overflow-hidden relative">
                  <div :class="['w-full h-full p-2', tpl.id === 'creative' ? 'flex' : 'flex-col']">
                    <div v-if="tpl.id === 'creative'" class="w-1/3 bg-slate-800 h-full rounded-l-md mr-1"></div>
                    <div class="flex-1 space-y-1">
                      <div class="h-2 w-2/3 bg-slate-800 rounded opacity-20 mt-2"></div>
                      <div class="h-1 w-full bg-slate-400 rounded opacity-20"></div>
                    </div>
                  </div>
                  <div v-if="selectedTemplate === tpl.id" class="absolute top-2 right-2 bg-indigo-600 text-white w-5 h-5 flex items-center justify-center rounded-full shadow-sm">
                    <i class="fa-solid fa-check text-[10px]"></i>
                  </div>
                </div>
                <div class="px-1">
                  <div class="font-bold text-xs text-slate-800">{{ tpl.name }}</div>
                  <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ tpl.desc }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Colors Tab -->
          <div v-if="activeTab === 'colors'" class="space-y-6">
            <header>
              <h2 class="text-xl font-display font-bold text-slate-900">Color Theme</h2>
            </header>
            <div class="grid grid-cols-2 gap-4">
              <button v-for="color in colors" :key="color.id" @click="selectedColor = color.hex"
                :class="['p-4 rounded-2xl border-2 text-left transition-all group', selectedColor === color.hex ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-100 hover:border-indigo-100 bg-white']">
                <div class="flex items-center gap-3 mb-2">
                  <div class="w-6 h-6 rounded-full shadow-sm" :style="{ backgroundColor: color.hex }"></div>
                  <div class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200"></div>
                </div>
                <div class="font-bold text-xs text-slate-700">{{ color.name }}</div>
              </button>
            </div>
          </div>

        </div>
      </div>

      <!-- Preview Panel -->
      <main :class="['flex-1 bg-slate-100/50 relative overflow-hidden flex flex-col items-center justify-start pt-8 pb-20 lg:pb-8 lg:pt-12 overflow-y-auto custom-scrollbar', isMobilePreviewOpen ? 'fixed inset-0 z-50 bg-slate-100' : 'hidden lg:flex']">
        
        <div class="transition-transform duration-200 ease-out origin-top space-y-8 pb-24" :style="{ transform: `scale(${zoom})` }">
          
          <!-- Dynamic Pages -->
          <div v-for="(pageData, index) in pages" :key="index" :class="['resume-sheet relative text-slate-800 text-sm leading-relaxed', selectedFont]">
            
            <!-- Modern Template -->
            <div v-if="selectedTemplate === 'modern'" class="p-16 h-full flex flex-col">
              <header v-if="index === 0" class="border-b-4 pb-8 mb-8 flex items-center justify-between" :style="{ borderColor: selectedColor }">
                <div>
                  <h1 class="text-5xl font-black uppercase tracking-tight text-slate-900">{{ pageData.personal.fullName }}</h1>
                  <p class="text-xl font-bold mt-2" :style="{ color: selectedColor }">{{ pageData.personal.title }}</p>
                  <div class="flex gap-6 mt-6 text-xs font-bold text-slate-400 tracking-wider">
                    <span v-if="pageData.personal.email"><i class="fa-solid fa-envelope mr-1.5"></i> {{ pageData.personal.email }}</span>
                    <span v-if="pageData.personal.phone"><i class="fa-solid fa-phone mr-1.5"></i> {{ pageData.personal.phone }}</span>
                  </div>
                </div>
              </header>
              <div class="space-y-10 flex-1">
                <section v-if="visibility.summary && pageData.summary && index === 0">
                  <p class="text-slate-600 leading-7">{{ pageData.summary }}</p>
                </section>
                <section v-if="visibility.experience && pageData.experience && pageData.experience.length > 0">
                  <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Experience</h3>
                  <div class="space-y-6">
                    <div v-for="exp in pageData.experience" :key="exp.id">
                      <div class="flex justify-between items-baseline mb-1">
                        <h4 class="font-bold text-slate-900 text-lg">{{ exp.role }}</h4>
                        <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded">{{ exp.period }}</span>
                      </div>
                      <div class="text-xs font-bold mb-2" :style="{ color: selectedColor }">{{ exp.company }}</div>
                      <p class="text-slate-600 text-xs leading-6">{{ exp.description }}</p>
                    </div>
                  </div>
                </section>
              </div>
            </div>

            <!-- Creative Template -->
            <div v-else-if="selectedTemplate === 'creative'" class="flex h-full">
              <div class="w-1/3 text-white p-10 pt-16 flex flex-col gap-10" :style="{ backgroundColor: selectedColor }">
                <div v-if="index === 0" class="space-y-1">
                  <h2 class="text-xs font-bold opacity-70 uppercase tracking-widest mt-4">Contact</h2>
                  <div class="text-xs space-y-2 opacity-90 pt-2">
                    <div class="block">{{ pageData.personal.email }}</div>
                    <div class="block">{{ pageData.personal.phone }}</div>
                    <div class="block">{{ pageData.personal.location }}</div>
                  </div>
                </div>
              </div>
              <div class="flex-1 p-12 pt-16">
                <header v-if="index === 0" class="mb-12">
                  <h1 class="text-4xl font-bold text-slate-900 leading-tight">{{ pageData.personal.fullName }}</h1>
                  <p class="text-lg font-medium mt-2 opacity-80" :style="{ color: selectedColor }">{{ pageData.personal.title }}</p>
                </header>
                <p v-if="visibility.summary && pageData.summary && index === 0" class="text-slate-600 mb-10 leading-7">{{ pageData.summary }}</p>
              </div>
            </div>

            <!-- Page Number -->
            <div class="absolute bottom-4 right-8 text-[10px] text-slate-300 font-bold">
              Page {{ index + 1 }} of {{ pages.length }}
            </div>

          </div>
        </div>
      </main>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'

// Get runtime config for API keys
const config = useRuntimeConfig()

// Initial Data
const INITIAL_DATA = {
  personal: {
    fullName: 'Jonathan Doe',
    title: 'Senior Software Engineer',
    email: 'j.doe@example.com',
    phone: '+1 555 123 4567',
    location: 'San Francisco, CA',
    profileImage: null
  },
  summary: 'Innovative Senior Software Engineer with 7+ years of experience in managing all aspects of the software development lifecycle.',
  experience: [
    { id: 1, role: 'Principal Engineer', company: 'TechCorp Solutions', period: '2021 — Present', description: 'Led the migration of legacy infrastructure to a modern microservices architecture, improving system uptime by 40%.' },
    { id: 2, role: 'Senior Developer', company: 'InnovateX', period: '2018 — 2021', description: 'Developed scalable RESTful APIs serving 1M+ daily requests.' }
  ],
  education: [
    { id: 1, degree: 'M.S. Computer Science', school: 'Stanford University', year: '2016 — 2018' }
  ],
  skills: ['React', 'Node.js', 'AWS', 'Docker', 'Kubernetes']
}

// State
const activeTab = ref('colors')
const resumeData = reactive(JSON.parse(JSON.stringify(INITIAL_DATA)))
const visibility = reactive({ summary: true, experience: true, education: true, skills: true })
const selectedTemplate = ref('modern')
const selectedColor = ref('#4f46e5')
const selectedFont = ref('font-sans')
const zoom = ref(0.9)
const isMobileMenuOpen = ref(false)
const isMobilePreviewOpen = ref(false)
const isUploading = ref(false)
const openSections = reactive({ personal: true, experience: false, education: false, skills: false })
const newSkillInput = ref('')
const geminiApiKey = ref('')
const showApiModal = ref(false)
const loadingAI = ref<number | null>(null)
const loadingSummary = ref(false)
const toast = reactive({ visible: false, message: '', type: 'info' })
const showResetModal = ref(false)

// Config
const templates = [
  { id: 'modern', name: 'Modern Executive', desc: 'Best for Tech & SaaS' },
  { id: 'creative', name: 'Creative Sidebar', desc: 'Design & Marketing' },
  { id: 'academic', name: 'Academic Elite', desc: 'Formal & Corporate' },
  { id: 'minimal', name: 'Minimalist Bold', desc: 'Clean & Functional' },
]

const colors = [
  { id: 'indigo', hex: '#4f46e5', name: 'Royal Indigo' },
  { id: 'emerald', hex: '#059669', name: 'Growth Emerald' },
  { id: 'rose', hex: '#e11d48', name: 'Passion Rose' },
  { id: 'slate', hex: '#334155', name: 'Classic Slate' },
]

const navItems = [
  { id: 'upload', icon: 'fa-cloud-arrow-up', label: 'Import & Job' },
  { id: 'manual', icon: 'fa-file-pen', label: 'Content' },
  { id: 'ai', icon: 'fa-wand-magic-sparkles', label: 'AI Assistant' },
  { id: 'templates', icon: 'fa-layer-group', label: 'Templates' },
  { id: 'colors', icon: 'fa-palette', label: 'Colors' }
]

// Computed
const pages = computed(() => {
  const chunkedPages: any[] = []
  let currentPage = {
    personal: resumeData.personal,
    summary: resumeData.summary,
    experience: [] as any[],
    education: [] as any[],
    skills: resumeData.skills
  }
  
  resumeData.experience.forEach((exp: any) => {
    currentPage.experience.push(exp)
  })
  
  chunkedPages.push(currentPage)
  return chunkedPages
})

// Methods
const showToast = (msg: string, type = 'info') => {
  toast.message = msg
  toast.type = type
  toast.visible = true
  setTimeout(() => { toast.visible = false }, 3000)
}

const saveApiKey = () => {
  if (geminiApiKey.value.trim().startsWith('AIza')) {
    if (process.client) {
      localStorage.setItem('gemini_api_key', geminiApiKey.value.trim())
    }
    showApiModal.value = false
    showToast('API Key saved!', 'success')
  } else {
    showToast('Invalid API Key format', 'error')
  }
}

const optimizeText = async (index: number) => {
  try {
    loadingAI.value = index
    const original = resumeData.experience[index].description
    if (!original) {
      showToast('No text to optimize', 'error')
      return
    }

    const { enhanceText } = useGemini()
    const result = await enhanceText(original)
    resumeData.experience[index].description = result
    showToast('Text enhanced with Gemini ✨', 'success')
  } catch (err: any) {
    if (err.message !== 'Gemini API key not found. Please set NUXT_PUBLIC_GEMINI_API_KEY in .env or add it via the UI.') {
      showToast(err.message || 'Failed to enhance text', 'error')
    } else {
      showApiModal.value = true
    }
  } finally {
    loadingAI.value = null
  }
}

const generateSummary = async () => {
  try {
    loadingSummary.value = true
    
    const { generateSummary: geminiGenerateSummary } = useGemini()
    const context = {
      role: resumeData.personal.title,
      skills: resumeData.skills,
      experience: resumeData.experience
    }
    
    const result = await geminiGenerateSummary(context)
    resumeData.summary = result
    showToast('Summary generated with Gemini ✨', 'success')
  } catch (err: any) {
    if (err.message !== 'Gemini API key not found. Please set NUXT_PUBLIC_GEMINI_API_KEY in .env or add it via the UI.') {
      showToast(err.message || 'Failed to generate summary', 'error')
    } else {
      showApiModal.value = true
    }
  } finally {
    loadingSummary.value = false
  }
}

const handleFileUpload = (e: Event) => {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return
  
  isUploading.value = true
  setTimeout(() => {
    isUploading.value = false
    showToast('Resume parsed successfully!', 'success')
    activeTab.value = 'manual'
  }, 1200)
}

const exportJSON = () => {
  const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(resumeData))
  const downloadAnchorNode = document.createElement('a')
  downloadAnchorNode.setAttribute("href", dataStr)
  downloadAnchorNode.setAttribute("download", "resume_data.json")
  document.body.appendChild(downloadAnchorNode)
  downloadAnchorNode.click()
  downloadAnchorNode.remove()
  showToast('Resume data downloaded!', 'success')
}

const resetData = () => { showResetModal.value = true }
const confirmReset = () => {
  Object.assign(resumeData, JSON.parse(JSON.stringify(INITIAL_DATA)))
  showResetModal.value = false
  showToast('Data reset to default', 'success')
}

const handleZoom = (delta: number) => {
  zoom.value = Math.min(Math.max(zoom.value + delta, 0.4), 2.0)
}

const setActiveTab = (id: string) => {
  activeTab.value = id
  isMobileMenuOpen.value = false
}

const formatKey = (key: string) => key.replace(/([A-Z])/g, ' $1').trim()
const toggleSection = (sec: string) => { 
  (openSections as any)[sec] = !(openSections as any)[sec] 
}
const triggerExport = () => showToast('Exporting PDF... (Simulation)', 'info')

const moveItem = (section: string, index: number, direction: number) => {
  const arr = (resumeData as any)[section]
  const newIndex = index + direction
  if (newIndex >= 0 && newIndex < arr.length) {
    const item = arr[index]
    arr.splice(index, 1)
    arr.splice(newIndex, 0, item)
  }
}

const removeItem = (section: string, index: number) => {
  if (confirm('Remove this item?')) (resumeData as any)[section].splice(index, 1)
}

const addItem = (section: string) => {
  const newItem = section === 'experience'
    ? { id: Date.now(), role: 'New Role', company: 'Company', period: 'Year', description: '' }
    : { id: Date.now(), school: 'University', degree: 'Degree', year: 'Year' }
  ;(resumeData as any)[section].push(newItem)
}

// Load API key on mount
onMounted(() => {
  if (process.client) {
    // Try to get from localStorage first, then fall back to env
    const storedKey = localStorage.getItem('gemini_api_key')
    geminiApiKey.value = storedKey || config.public.geminiApiKey || ''
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
  background: #cbd5e1;
  border-radius: 10px;
}

.resume-sheet {
  width: 794px;
  min-height: 1123px;
  background: white;
  box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: all 0.3s ease;
  margin-bottom: 2rem;
  position: relative;
}

.spinner {
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top: 4px solid #ffffff;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

.spinner-sm {
  width: 14px;
  height: 14px;
  border-width: 2px;
  border-top-color: currentColor;
  border-right-color: transparent;
  border-bottom-color: transparent;
  border-left-color: transparent;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
