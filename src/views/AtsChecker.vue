<script setup>
import { ref, computed, onMounted } from 'vue'
import gsap from 'gsap'

const activeTab = ref('upload')
const uploadedFile = ref(null)
const pastedText = ref('')
const isAnalyzing = ref(false)
const showResults = ref(false)
const score = ref(0)
const currentStep = ref('')

const analysisSteps = [
  'Parsing keywords and formatting...',
  'Extracting text data...',
  'Checking keywords against job description...',
  'Calculating impact score...'
]

const switchTab = (tab) => {
  activeTab.value = tab
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    uploadedFile.value = {
      name: file.name,
      size: file.size
    }
  }
}

const startAnalysis = () => {
  if (!uploadedFile.value && !pastedText.value.trim()) {
    alert('Please upload a resume or paste text first!')
    return
  }

  isAnalyzing.value = true
  showResults.value = false
  window.scrollTo({ top: 0, behavior: 'smooth' })

  let stepIndex = 0
  const stepInterval = setInterval(() => {
    if (stepIndex < analysisSteps.length) {
      currentStep.value = analysisSteps[stepIndex]
      stepIndex++
    }
  }, 1000)

  setTimeout(() => {
    clearInterval(stepInterval)
    isAnalyzing.value = false
    showResults.value = true
    animateScore(72)
  }, 4000)
}

const animateScore = (targetScore) => {
  gsap.to(score, {
    value: targetScore,
    duration: 1.5,
    ease: 'power2.out',
    onUpdate: () => {
      score.value = Math.round(score.value)
    }
  })
}

const scoreCircleDashoffset = computed(() => {
  const circumference = 264
  return circumference - (score.value / 100) * circumference
})

const resetAnalysis = () => {
  uploadedFile.value = null
  pastedText.value = ''
  showResults.value = false
  score.value = 0
  activeTab.value = 'upload'
}

const matchedKeywords = [
  'Project Management', 'Agile', 'Scrum', 'Team Leadership', 'Communication'
]

const missingKeywords = [
  'Python', 'Data Analysis', 'SQL', 'JIRA'
]

const formattingChecks = [
  { name: 'File Format', detail: 'PDF (Text-based)', status: 'pass', icon: 'fa-file-pdf' },
  { name: 'Section Headers', detail: 'Standard naming conventions', status: 'pass', icon: 'fa-heading' },
  { name: 'Graphics/Images', detail: 'Headshot detected', status: 'fail', icon: 'fa-image' },
  { name: 'Date Formatting', detail: 'Inconsistent formats used', status: 'warn', icon: 'fa-calendar' }
]
</script>

<template>
  <div class="flex-grow">

    <!-- HERO SECTION (Initial View) -->
    <div v-if="!showResults && !isAnalyzing">
      <section class="pt-16 pb-12 text-center px-6 max-w-4xl mx-auto animate-slide-up">
        <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 mb-4 leading-tight">
          Check How <span class="text-indigo-600">ATS-Friendly</span><br>Your Resume Is
        </h1>
        <p class="text-slate-500 text-lg mb-8 max-w-2xl mx-auto">
          Don't let a bot reject your application. Upload your resume to get an instant analysis of your keywords, formatting, and readability.
        </p>

        <div class="flex flex-wrap justify-center gap-6 mb-10 text-sm font-medium text-slate-600">
          <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-sm border border-slate-200 animate-pulse-slow">
            <i class="fa-solid fa-check-circle text-green-500"></i> Keyword Match
          </div>
          <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-sm border border-slate-200 animate-pulse-slow" style="animation-delay: 0.5s;">
            <i class="fa-solid fa-check-circle text-green-500"></i> Formatting Check
          </div>
          <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-sm border border-slate-200 animate-pulse-slow" style="animation-delay: 1s;">
            <i class="fa-solid fa-check-circle text-green-500"></i> Readability Score
          </div>
        </div>
      </section>

      <!-- RESUME INPUT SECTION -->
      <section class="px-6 pb-20 transition-all duration-500 animate-slide-up" style="animation-delay: 0.2s;">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden transform hover:scale-[1.01] transition-transform duration-500">

          <!-- Tabs Header -->
          <div class="flex border-b border-slate-100 bg-slate-50/50">
            <button
              @click="switchTab('upload')"
              :class="activeTab === 'upload' ? 'border-b-2 border-indigo-600 text-indigo-600 bg-white font-bold' : 'text-slate-500 font-medium hover:text-slate-700 hover:bg-slate-50'"
              class="flex-1 py-4 text-sm transition-colors flex items-center justify-center gap-2"
            >
              <i class="fa-solid fa-cloud-arrow-up"></i> Upload Resume
            </button>
            <button
              @click="switchTab('paste')"
              :class="activeTab === 'paste' ? 'border-b-2 border-indigo-600 text-indigo-600 bg-white font-bold' : 'text-slate-500 font-medium hover:text-slate-700 hover:bg-slate-50'"
              class="flex-1 py-4 text-sm transition-colors flex items-center justify-center gap-2"
            >
              <i class="fa-solid fa-paste"></i> Paste Text
            </button>
          </div>

          <div class="p-8">
            <!-- Tab A: Upload -->
            <div v-show="activeTab === 'upload'" class="tab-content">
              <div class="border-2 border-dashed border-slate-300 rounded-xl h-64 flex flex-col items-center justify-center bg-slate-50/50 hover:bg-indigo-50/20 hover:border-indigo-400 transition-all cursor-pointer group relative">
                <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleFileUpload" accept=".pdf,.docx,.txt">

                <!-- Default State -->
                <div v-if="!uploadedFile" class="text-center group-hover:-translate-y-1 transition-transform duration-300">
                  <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-4 text-indigo-600 text-2xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-file-arrow-up"></i>
                  </div>
                  <h3 class="text-lg font-bold text-slate-800">Drag & Drop or Click to Upload</h3>
                  <p class="text-slate-500 text-sm mt-1">Supports PDF, DOCX, TXT (Max 5MB)</p>
                </div>

                <!-- Success State -->
                <div v-else class="text-center">
                  <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 text-green-600 text-2xl animate-bounce">
                    <i class="fa-solid fa-check"></i>
                  </div>
                  <h3 class="text-lg font-bold text-slate-800">{{ uploadedFile.name }}</h3>
                  <p class="text-green-600 text-sm mt-1 font-medium">Ready for analysis</p>
                </div>
              </div>
            </div>

            <!-- Tab B: Paste -->
            <div v-show="activeTab === 'paste'" class="tab-content">
              <textarea
                v-model="pastedText"
                class="w-full h-64 p-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none resize-none text-sm leading-relaxed"
                placeholder="Copy and paste your resume content here..."
              ></textarea>
            </div>

            <!-- ANALYZE BUTTON -->
            <div class="mt-8">
              <button
                @click="startAnalysis"
                class="w-full py-4 rounded-xl bg-gradient-to-r from-slate-900 to-slate-800 hover:from-slate-800 hover:to-slate-700 text-white font-bold text-lg shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-3 relative overflow-hidden group"
              >
                <span class="relative z-10 group-hover:hidden">Analyze Resume</span>
                <span class="relative z-10 hidden group-hover:inline">Start Free Scan <i class="fa-solid fa-arrow-right ml-1"></i></span>
                <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- LOADING STATE (Overlay) -->
    <div v-if="isAnalyzing" class="fixed inset-0 bg-white z-50 flex flex-col items-center justify-center">
      <div class="relative w-64 h-80 border-2 border-slate-200 rounded-lg bg-white shadow-xl overflow-hidden mb-8 scale-75 md:scale-100">
        <!-- Mock Resume Lines -->
        <div class="p-6 space-y-4">
          <div class="w-20 h-20 bg-slate-100 rounded-full mb-4"></div>
          <div class="w-3/4 h-4 bg-slate-200 rounded"></div>
          <div class="w-1/2 h-4 bg-slate-200 rounded"></div>
          <div class="space-y-2 pt-4">
            <div class="w-full h-2 bg-slate-100 rounded"></div>
            <div class="w-full h-2 bg-slate-100 rounded"></div>
            <div class="w-full h-2 bg-slate-100 rounded"></div>
            <div class="w-3/4 h-2 bg-slate-100 rounded"></div>
          </div>
          <div class="space-y-2 pt-4">
            <div class="w-full h-2 bg-slate-100 rounded"></div>
            <div class="w-full h-2 bg-slate-100 rounded"></div>
          </div>
        </div>
        <!-- Scanning Line -->
        <div class="absolute top-0 left-0 w-full h-1 bg-indigo-500 shadow-[0_0_15px_rgba(99,102,241,0.8)] animate-scan-line"></div>
      </div>

      <h2 class="text-2xl font-display font-bold text-slate-900 mb-2">Analyzing Resume...</h2>
      <div class="text-slate-500 text-sm font-medium h-6 overflow-hidden animate-pulse">
        {{ currentStep }}
      </div>
    </div>

    <!-- RESULTS DASHBOARD -->
    <section v-if="showResults" class="container mx-auto px-6 py-12 pb-24">

      <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 animate-slide-up">
        <div>
          <h2 class="text-3xl font-bold text-slate-900">Analysis Report</h2>
          <p class="text-slate-500 text-sm">Scan completed just now</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
          <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
            <i class="fa-solid fa-download mr-2"></i> Download PDF
          </button>
          <button @click="resetAnalysis" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-medium hover:bg-slate-800 transition-colors shadow-lg">
            <i class="fa-solid fa-plus mr-2"></i> New Scan
          </button>
        </div>
      </div>

      <!-- Top Row: Score & Summary -->
      <div class="grid lg:grid-cols-3 gap-8 mb-8">

        <!-- Score Card -->
        <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-lg transition-shadow text-center border border-slate-100 animate-slide-up relative overflow-hidden" style="animation-delay: 0.1s;">
          <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 via-yellow-500 to-green-500"></div>
          <h3 class="text-slate-500 font-bold text-xs uppercase tracking-wider mb-6">Overall ATS Score</h3>

          <div class="relative w-48 h-48 mx-auto mb-6">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
              <circle class="text-slate-100" stroke-width="8" stroke="currentColor" fill="transparent" r="42" cx="50" cy="50" />
              <circle 
                class="text-indigo-600 transition-all duration-1000 ease-out" 
                stroke-width="8"
                stroke-linecap="round" 
                stroke="currentColor" 
                fill="transparent" 
                r="42" 
                cx="50" 
                cy="50"
                :stroke-dasharray="264"
                :stroke-dashoffset="scoreCircleDashoffset"
              />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
              <span class="text-6xl font-display font-bold text-slate-900 tracking-tighter">{{ score }}</span>
              <span class="text-sm font-medium text-slate-400">/100</span>
            </div>
          </div>

          <div class="inline-block px-4 py-1.5 rounded-full bg-yellow-100 text-yellow-800 font-bold text-sm mb-4">
            Needs Improvement
          </div>
          <p class="text-sm text-slate-500 leading-relaxed">
            Your resume is parseable but lacks specific keywords from the job description.
          </p>
        </div>

        <!-- Quick Stats & Issues -->
        <div class="lg:col-span-2 flex flex-col gap-6">
          <!-- Quick Stats -->
          <div class="grid grid-cols-3 gap-4 h-full">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-md hover:shadow-lg transition-shadow flex flex-col justify-center animate-slide-up" style="animation-delay: 0.2s;">
              <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-lg mb-3">
                <i class="fa-solid fa-check"></i>
              </div>
              <span class="text-2xl font-bold text-slate-900">12/18</span>
              <span class="text-xs text-slate-500 font-medium uppercase mt-1">Keywords Matched</span>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-md hover:shadow-lg transition-shadow flex flex-col justify-center animate-slide-up" style="animation-delay: 0.3s;">
              <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg mb-3">
                <i class="fa-solid fa-briefcase"></i>
              </div>
              <span class="text-2xl font-bold text-slate-900">Mid-Level</span>
              <span class="text-xs text-slate-500 font-medium uppercase mt-1">Experience Detected</span>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-md hover:shadow-lg transition-shadow flex flex-col justify-center animate-slide-up" style="animation-delay: 0.4s;">
              <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-lg mb-3">
                <i class="fa-solid fa-triangle-exclamation"></i>
              </div>
              <span class="text-2xl font-bold text-slate-900">3 Errors</span>
              <span class="text-xs text-slate-500 font-medium uppercase mt-1">Critical Issues</span>
            </div>
          </div>

          <!-- Critical Issues Box -->
          <div class="bg-red-50 border border-red-100 rounded-2xl p-6 flex-grow animate-slide-up" style="animation-delay: 0.5s;">
            <h4 class="font-bold text-red-800 mb-4 flex items-center gap-2">
              <i class="fa-solid fa-circle-exclamation"></i> Top Priorities to Fix
            </h4>
            <ul class="space-y-3">
              <li class="flex items-start gap-3 bg-white p-3 rounded-lg border border-red-100 shadow-sm">
                <i class="fa-solid fa-xmark text-red-500 mt-1"></i>
                <div>
                  <span class="block text-sm font-bold text-slate-800">Missing Hard Skills</span>
                  <span class="text-xs text-slate-600">You are missing 'Python', 'AWS', and 'Docker' which are critical for this role.</span>
                </div>
              </li>
              <li class="flex items-start gap-3 bg-white p-3 rounded-lg border border-red-100 shadow-sm">
                <i class="fa-solid fa-xmark text-red-500 mt-1"></i>
                <div>
                  <span class="block text-sm font-bold text-slate-800">Contact Info Parsing Error</span>
                  <span class="text-xs text-slate-600">Your email address is inside a header/footer which some ATS cannot read.</span>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Deep Dive Section -->
      <div class="grid lg:grid-cols-2 gap-8 mb-8">

        <!-- Skills Gap Analysis -->
        <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-lg transition-shadow border border-slate-100 animate-slide-up" style="animation-delay: 0.6s;">
          <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-lg text-slate-900">Skills Gap Analysis</h3>
            <span class="text-xs font-bold bg-slate-100 px-2 py-1 rounded text-slate-600">65% Match</span>
          </div>

          <div class="space-y-6">
            <div>
              <h4 class="text-xs font-bold text-green-600 uppercase mb-3 flex items-center gap-2">
                <i class="fa-solid fa-check"></i> Found on Resume
              </h4>
              <div class="flex flex-wrap gap-2">
                <span v-for="skill in matchedKeywords" :key="skill" class="px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-lg border border-green-100">{{ skill }}</span>
              </div>
            </div>

            <div class="border-t border-slate-100 pt-6">
              <h4 class="text-xs font-bold text-red-500 uppercase mb-3 flex items-center gap-2">
                <i class="fa-solid fa-xmark"></i> Missing (Add These!)
              </h4>
              <div class="flex flex-wrap gap-2">
                <span v-for="skill in missingKeywords" :key="skill" class="px-3 py-1.5 bg-red-50 text-red-600 text-sm font-medium rounded-lg border border-red-100 border-dashed">{{ skill }}</span>
              </div>
              <p class="text-xs text-slate-400 mt-3 italic">
                <i class="fa-solid fa-lightbulb text-yellow-400 mr-1"></i> Tip: Add these to your "Skills" section or weave them into bullet points.
              </p>
            </div>
          </div>
        </div>

        <!-- Formatting & Parseability -->
        <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-lg transition-shadow border border-slate-100 animate-slide-up" style="animation-delay: 0.7s;">
          <h3 class="font-bold text-lg text-slate-900 mb-6">Formatting Health</h3>

          <div class="space-y-4">
            <div 
              v-for="check in formattingChecks" 
              :key="check.name"
              :class="check.status === 'fail' ? 'bg-red-50/50' : ''"
              class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition-colors"
            >
              <div class="flex items-center gap-4">
                <div 
                  :class="{
                    'bg-green-100 text-green-600': check.status === 'pass',
                    'bg-red-100 text-red-600': check.status === 'fail',
                    'bg-yellow-100 text-yellow-600': check.status === 'warn'
                  }"
                  class="w-10 h-10 rounded-full flex items-center justify-center"
                >
                  <i :class="`fa-${check.icon.includes('regular') ? 'regular' : 'solid'} ${check.icon}`"></i>
                </div>
                <div>
                  <h4 class="font-bold text-slate-800 text-sm">{{ check.name }}</h4>
                  <p class="text-xs text-slate-500">{{ check.detail }}</p>
                </div>
              </div>
              <span 
                :class="{
                  'text-green-600': check.status === 'pass',
                  'text-red-600': check.status === 'fail',
                  'text-yellow-600': check.status === 'warn'
                }"
                class="font-bold text-sm"
              >
                <i 
                  :class="{
                    'fa-check': check.status === 'pass',
                    'fa-xmark': check.status === 'fail',
                    'fa-triangle-exclamation': check.status === 'warn'
                  }"
                  class="fa-solid mr-1"
                ></i>
                {{ check.status === 'pass' ? 'Pass' : check.status === 'fail' ? 'Fail' : 'Warn' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Impact -->
      <div class="bg-slate-900 rounded-2xl p-8 text-white shadow-xl animate-slide-up" style="animation-delay: 0.8s;">
        <div class="flex flex-col md:flex-row gap-8 items-center">
          <div class="flex-1">
            <h3 class="text-xl font-bold mb-2 flex items-center gap-2">
              <i class="fa-solid fa-wand-magic-sparkles text-indigo-400"></i> Content Impact Analysis
            </h3>
            <p class="text-slate-400 text-sm mb-6">
              We analyzed your bullet points for strength, clarity, and measurable results.
            </p>

            <div class="space-y-4">
              <div>
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-slate-300">Action Verbs Usage</span>
                  <span class="font-bold text-green-400">Strong (85%)</span>
                </div>
                <div class="w-full bg-slate-700 rounded-full h-2">
                  <div class="bg-green-500 h-2 rounded-full w-[85%]"></div>
                </div>
              </div>
              <div>
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-slate-300">Quantifiable Results (Numbers/%)</span>
                  <span class="font-bold text-red-400">Weak (20%)</span>
                </div>
                <div class="w-full bg-slate-700 rounded-full h-2">
                  <div class="bg-red-500 h-2 rounded-full w-[20%]"></div>
                </div>
                <p class="text-xs text-slate-500 mt-1">Try adding metrics like "Increased revenue by 20%"</p>
              </div>
            </div>
          </div>

          <div class="md:w-1/3 bg-slate-800 p-6 rounded-xl border border-slate-700">
            <h4 class="font-bold text-sm text-slate-300 mb-4 uppercase tracking-wide">Recruiter Simulation</h4>
            <div class="space-y-3">
              <div class="flex justify-between text-sm border-b border-slate-700 pb-2">
                <span class="text-slate-400">Word Count</span>
                <span class="font-mono">642 (Good)</span>
              </div>
              <div class="flex justify-between text-sm border-b border-slate-700 pb-2">
                <span class="text-slate-400">Avg Bullet Length</span>
                <span class="font-mono">14 words</span>
              </div>
              <div class="flex justify-between text-sm pb-2">
                <span class="text-slate-400">Reading Level</span>
                <span class="font-mono">Grade 10</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Fix Actions -->
      <div class="mt-8 flex justify-center animate-slide-up" style="animation-delay: 0.9s;">
        <router-link to="/builder" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-full shadow-lg hover:bg-indigo-700 hover:shadow-indigo-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 transform hover:-translate-y-1">
          <i class="fa-solid fa-wrench mr-2"></i> Fix These Issues in Resume Builder
        </router-link>
      </div>

    </section>

  </div>
</template>

<style scoped>
@keyframes scan {
  0% { top: 0%; }
  50% { top: 100%; }
  100% { top: 0%; }
}

.animate-scan-line {
  animation: scan 2s linear infinite;
}
</style>
