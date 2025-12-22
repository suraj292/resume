<template>
  <!-- Auth Required Modal -->
  <!-- <AuthRequiredModal v-if="showAuthModal" @close="showAuthModal = false" /> -->
  
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

            <!-- Tab: Manual Content - Comprehensive Resume Builder -->
            <div v-show="activeTab === 'manual'" class="tab-content space-y-6">
              <header class="mb-6">
                <h2 class="text-xl font-display font-bold text-slate-800">Resume Content</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium">Build your complete resume section by section</p>
              </header>

              <!-- Personal Information -->
              <section class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                  <i class="fa-solid fa-user text-indigo-600"></i>
                  Personal Information
                </h3>
                <div class="space-y-4">
                  <div v-for="field in personalFields" :key="field.id" class="group">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ field.label }}</label>
                    <input v-model="formData[field.id]" :type="field.type" :placeholder="field.placeholder" 
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                  </div>
                </div>
              </section>

              <!-- Professional Summary -->
              <section class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                  <i class="fa-solid fa-file-lines text-indigo-600"></i>
                  Professional Summary
                </h3>
                <textarea v-model="formData.summary" rows="4" 
                          class="w-full px-4 py-3 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none resize-none transition-all" 
                          placeholder="e.g. Senior Full-Stack Developer with 4+ years of experience building scalable applications..."></textarea>
                <p class="text-[10px] text-slate-400 mt-2">{{ formData.summary.length }} characters</p>
              </section>

              <!-- Skills -->
              <section class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                  <i class="fa-solid fa-code text-indigo-600"></i>
                  Skills
                </h3>
                <div class="space-y-5">
                  <div v-for="category in ['backend', 'frontend', 'devops', 'other']" :key="category">
                    <div class="flex items-center justify-between mb-2">
                      <label class="text-xs font-bold text-slate-600 capitalize">{{ category }}</label>
                      <button @click="addSkill(category)" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-plus text-[10px]"></i> Add
                      </button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                      <span v-for="(skill, idx) in formData.skills[category]" :key="idx"
                            class="group inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">
                        {{ skill }}
                        <button @click="removeSkill(category, idx)" class="opacity-0 group-hover:opacity-100 transition-opacity">
                          <i class="fa-solid fa-times text-[10px] hover:text-red-600"></i>
                        </button>
                      </span>
                      <span v-if="formData.skills[category].length === 0" class="text-xs text-slate-400 italic">No {{ category }} skills added yet</span>
                    </div>
                  </div>
                </div>
              </section>

              <!-- Work Experience -->
              <section class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-briefcase text-indigo-600"></i>
                    Work Experience
                  </h3>
                  <button @click="addExperience" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-plus text-[10px]"></i> Add Position
                  </button>
                </div>
                <div class="space-y-4">
                  <div v-for="(exp, idx) in formData.experience" :key="exp.id" class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="flex items-start justify-between mb-3">
                      <span class="text-xs font-bold text-slate-400">Position {{ idx + 1 }}</span>
                      <button @click="removeExperience(idx)" v-if="formData.experience.length > 1" 
                              class="text-xs text-red-600 hover:text-red-700 font-semibold">
                        <i class="fa-solid fa-trash text-[10px]"></i> Remove
                      </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                      <input v-model="exp.position" placeholder="Position Title" class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                      <input v-model="exp.company" placeholder="Company Name" class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                      <input v-model="exp.startDate" placeholder="Start Date (e.g. Jun 2023)" class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                      <input v-model="exp.endDate" placeholder="End Date / Present" class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                      <input v-model="exp.location" placeholder="Location (optional)" class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 md:col-span-2">
                    </div>
                    <div>
                      <label class="text-xs font-bold text-slate-600 mb-2 block">Responsibilities</label>
                      <div class="space-y-2">
                        <div v-for="(resp, respIdx) in exp.responsibilities" :key="respIdx" class="flex gap-2">
                          <textarea v-model="exp.responsibilities[respIdx]" rows="2" 
                                    class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 resize-none" 
                                    placeholder="• Describe your responsibility or achievement..."></textarea>
                          <button @click="removeResponsibility(idx, respIdx)" v-if="exp.responsibilities.length > 1"
                                  class="text-red-600 hover:text-red-700 px-2">
                            <i class="fa-solid fa-times"></i>
                          </button>
                        </div>
                        <button @click="addResponsibility(idx)" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">
                          <i class="fa-solid fa-plus text-[10px]"></i> Add Responsibility
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </section>

              <!-- Education -->
              <section class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
                    Education
                  </h3>
                  <button @click="addEducation" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-plus text-[10px]"></i> Add Education
                  </button>
                </div>
                <div class="space-y-4">
                  <div v-for="(edu, idx) in formData.education" :key="edu.id" class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="flex items-start justify-between mb-3">
                      <span class="text-xs font-bold text-slate-400">Education {{ idx + 1 }}</span>
                      <button @click="removeEducation(idx)" v-if="formData.education.length > 1"
                              class="text-xs text-red-600 hover:text-red-700 font-semibold">
                        <i class="fa-solid fa-trash text-[10px]"></i> Remove
                      </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                      <input v-model="edu.degree" placeholder="Degree (e.g. BCA, B.Tech)" class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 md:col-span-2">
                      <input v-model="edu.institution" placeholder="Institution Name" class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                      <input v-model="edu.year" placeholder="Year (e.g. 2019-2022)" class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                      <input v-model="edu.percentage" placeholder="Percentage / GPA (optional)" class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 md:col-span-2">
                    </div>
                  </div>
                </div>
              </section>

              <!-- Achievements -->
              <section class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-trophy text-indigo-600"></i>
                    Achievements & Certifications
                  </h3>
                  <button @click="addAchievement" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-plus text-[10px]"></i> Add Achievement
                  </button>
                </div>
                <div class="space-y-2">
                  <div v-for="(achievement, idx) in formData.achievements" :key="idx" class="flex gap-2">
                    <i class="fa-solid fa-circle text-[6px] text-indigo-600 mt-2"></i>
                    <input v-model="formData.achievements[idx]" 
                           class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500/20" 
                           placeholder="e.g. Reduced MySQL query time by 80%">
                    <button @click="removeAchievement(idx)" v-if="formData.achievements.length > 1"
                            class="text-red-600 hover:text-red-700 px-2">
                      <i class="fa-solid fa-times"></i>
                    </button>
                  </div>
                </div>
              </section>
            </div>

            <!-- Tab: AI Assistant -->
            <div v-show="activeTab === 'ai'" class="tab-content">
              <header class="mb-6">
                <h2 class="text-xl font-display font-bold text-slate-800 flex items-center gap-2">
                  <i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i>
                  AI Resume Engine
                </h2>
                <p class="text-slate-400 text-xs mt-1 font-medium italic">Analyze and optimize your resume for ATS compatibility</p>
              </header>

              <!-- ATS Analysis Button -->
              <div class="mb-6">
                <button 
                  @click="analyzeATS" 
                  :disabled="isProcessing"
                  class="w-full group relative bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 disabled:from-slate-400 disabled:to-slate-500 text-white p-5 rounded-2xl transition-all shadow-lg hover:shadow-xl overflow-hidden text-left"
                >
                  <div class="relative z-10 flex items-center justify-between">
                    <div>
                      <i class="fa-solid fa-chart-line mb-2 block text-indigo-200"></i>
                      <span class="block text-sm font-bold leading-tight">{{ atsAnalysisCompleted ? 'Re-analyze Resume' : 'Analyze Resume for ATS' }}</span>
                      <span class="block text-[10px] text-indigo-100 mt-1">{{ isProcessing ? 'Analyzing...' : 'Get detailed ATS compatibility score' }}</span>
                    </div>
                    <i class="fa-solid fa-arrow-right text-2xl text-indigo-300"></i>
                  </div>
                  <i class="fa-solid fa-sparkles absolute -right-2 -bottom-2 text-6xl text-white/10 group-hover:scale-110 transition-transform"></i>
                </button>
              </div>

              <!-- ATS Analysis Dashboard -->
              <div v-if="atsAnalysisCompleted" class="space-y-6">
                <!-- Score Card -->
                <div class="bg-white rounded-2xl p-6 shadow-md border border-slate-100 text-center">
                  <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 via-yellow-500 to-green-500 rounded-t-2xl"></div>
                  <h3 class="text-slate-500 font-bold text-xs uppercase tracking-wider mb-4">Overall ATS Score</h3>

                  <div class="relative w-40 h-40 mx-auto mb-4">
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
                      <span class="text-5xl font-display font-bold text-slate-900 tracking-tighter">{{ atsScore }}</span>
                      <span class="text-xs font-medium text-slate-400">/100</span>
                    </div>
                  </div>

                  <div 
                    :class="{
                      'bg-green-100 text-green-800': atsScore >= 80,
                      'bg-yellow-100 text-yellow-800': atsScore >= 60 && atsScore < 80,
                      'bg-orange-100 text-orange-800': atsScore >= 40 && atsScore < 60,
                      'bg-red-100 text-red-800': atsScore < 40
                    }"
                    class="inline-block px-3 py-1 rounded-full font-bold text-xs mb-3"
                  >
                    {{ scoreGrade }}
                  </div>
                  <p class="text-xs text-slate-500 leading-relaxed">
                    {{ atsScore >= 80 ? 'Excellent! Your resume is highly ATS-compatible.' : atsScore >= 60 ? 'Good resume, but there\'s room for improvement.' : 'Your resume needs significant improvements to pass ATS screening.' }}
                  </p>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-3 gap-3">
                  <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm text-center">
                    <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm mx-auto mb-2">
                      <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="block text-lg font-bold text-slate-900">{{ matchedKeywords.length }}</span>
                    <span class="text-[10px] text-slate-500 font-medium">Keywords</span>
                  </div>
                  <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm text-center">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm mx-auto mb-2">
                      <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <span class="block text-lg font-bold text-slate-900 text-[10px]">{{ experienceLevel }}</span>
                    <span class="text-[10px] text-slate-500 font-medium">Experience</span>
                  </div>
                  <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm text-center">
                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-sm mx-auto mb-2">
                      <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <span class="block text-lg font-bold text-slate-900">{{ criticalIssuesCount }}</span>
                    <span class="text-[10px] text-slate-500 font-medium">Issues</span>
                  </div>
                </div>

                <!-- Critical Issues -->
                <div v-if="criticalIssues.length > 0" class="bg-red-50 border border-red-100 rounded-xl p-4">
                  <h4 class="font-bold text-red-800 text-xs mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation"></i> Critical Issues to Fix
                  </h4>
                  <ul class="space-y-2">
                    <li v-for="(issue, index) in criticalIssues.slice(0, 3)" :key="index" class="flex items-start gap-2 bg-white p-2 rounded-lg border border-red-100">
                      <i class="fa-solid fa-xmark text-red-500 text-xs mt-0.5"></i>
                      <div>
                        <span class="block text-xs font-bold text-slate-800">{{ issue.title }}</span>
                        <span class="text-[10px] text-slate-600">{{ issue.description }}</span>
                      </div>
                    </li>
                  </ul>
                </div>

                <!-- Skills Gap -->
                <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
                  <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-xs text-slate-900">Skills Gap Analysis</h3>
                    <span class="text-[10px] font-bold bg-slate-100 px-2 py-0.5 rounded text-slate-600">{{ keywordMatchPercentage }}% Match</span>
                  </div>

                  <div class="space-y-3">
                    <div v-if="matchedKeywords.length > 0">
                      <h4 class="text-[10px] font-bold text-green-600 uppercase mb-2 flex items-center gap-1">
                        <i class="fa-solid fa-check"></i> Found ({{ matchedKeywords.length }})
                      </h4>
                      <div class="flex flex-wrap gap-1">
                        <span v-for="skill in matchedKeywords.slice(0, 8)" :key="skill" class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-medium rounded border border-green-100">{{ skill }}</span>
                        <span v-if="matchedKeywords.length > 8" class="px-2 py-0.5 bg-slate-50 text-slate-500 text-[10px] font-medium rounded">+{{ matchedKeywords.length - 8 }} more</span>
                      </div>
                    </div>

                    <div v-if="missingKeywords.length > 0" class="border-t border-slate-100 pt-3">
                      <h4 class="text-[10px] font-bold text-red-500 uppercase mb-2 flex items-center gap-1">
                        <i class="fa-solid fa-xmark"></i> Missing ({{ missingKeywords.length }})
                      </h4>
                      <div class="flex flex-wrap gap-1">
                        <span v-for="skill in missingKeywords.slice(0, 8)" :key="skill" class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-medium rounded border border-red-100 border-dashed">{{ skill }}</span>
                        <span v-if="missingKeywords.length > 8" class="px-2 py-0.5 bg-slate-50 text-slate-500 text-[10px] font-medium rounded">+{{ missingKeywords.length - 8 }} more</span>
                      </div>
                      <p class="text-[10px] text-slate-400 mt-2 italic">
                        <i class="fa-solid fa-lightbulb text-yellow-400 mr-1"></i> Add these keywords to improve your score
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Content Impact -->
                <div class="bg-slate-900 rounded-xl p-4 text-white">
                  <h3 class="text-sm font-bold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-indigo-400"></i> Content Impact
                  </h3>
                  <div class="space-y-3">
                    <div>
                      <div class="flex justify-between text-[10px] mb-1">
                        <span class="text-slate-300">Action Verbs</span>
                        <span 
                          :class="{
                            'text-green-400': contentAnalysis.action_verbs_percentage >= 70,
                            'text-yellow-400': contentAnalysis.action_verbs_percentage >= 50 && contentAnalysis.action_verbs_percentage < 70,
                            'text-red-400': contentAnalysis.action_verbs_percentage < 50
                          }"
                          class="font-bold"
                        >{{ contentAnalysis.action_verbs_percentage }}%</span>
                      </div>
                      <div class="w-full bg-slate-700 rounded-full h-1.5">
                        <div 
                          :class="{
                            'bg-green-500': contentAnalysis.action_verbs_percentage >= 70,
                            'bg-yellow-500': contentAnalysis.action_verbs_percentage >= 50 && contentAnalysis.action_verbs_percentage < 70,
                            'bg-red-500': contentAnalysis.action_verbs_percentage < 50
                          }"
                          :style="{ width: contentAnalysis.action_verbs_percentage + '%' }"
                          class="h-1.5 rounded-full transition-all duration-500"
                        ></div>
                      </div>
                    </div>
                    <div>
                      <div class="flex justify-between text-[10px] mb-1">
                        <span class="text-slate-300">Quantifiable Results</span>
                        <span 
                          :class="{
                            'text-green-400': contentAnalysis.quantifiable_results_percentage >= 50,
                            'text-yellow-400': contentAnalysis.quantifiable_results_percentage >= 30 && contentAnalysis.quantifiable_results_percentage < 50,
                            'text-red-400': contentAnalysis.quantifiable_results_percentage < 30
                          }"
                          class="font-bold"
                        >{{ contentAnalysis.quantifiable_results_percentage }}%</span>
                      </div>
                      <div class="w-full bg-slate-700 rounded-full h-1.5">
                        <div 
                          :class="{
                            'bg-green-500': contentAnalysis.quantifiable_results_percentage >= 50,
                            'bg-yellow-500': contentAnalysis.quantifiable_results_percentage >= 30 && contentAnalysis.quantifiable_results_percentage < 50,
                            'bg-red-500': contentAnalysis.quantifiable_results_percentage < 30
                          }"
                          :style="{ width: contentAnalysis.quantifiable_results_percentage + '%' }"
                          class="h-1.5 rounded-full transition-all duration-500"
                        ></div>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-700">
                      <div class="text-[10px]">
                        <span class="text-slate-400">Words:</span>
                        <span class="font-mono font-bold ml-1">{{ wordCount }}</span>
                      </div>
                      <div class="text-[10px]">
                        <span class="text-slate-400">Reading:</span>
                        <span class="font-mono font-bold ml-1">{{ contentAnalysis.reading_level }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- AI Tools (Always visible) -->
              <div class="mt-6 space-y-4">
                <div class="border-t border-slate-200 pt-6">
                  <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">AI Enhancement Tools</h3>
                  <div class="space-y-3">
                    <button @click="improveBulletPoints" class="w-full group flex items-center justify-between p-4 hover:bg-indigo-50 rounded-xl border border-slate-200 hover:border-indigo-200 transition-all">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                          <i class="fa-solid fa-list-check text-xs"></i>
                        </div>
                        <div class="text-left">
                          <p class="text-xs font-bold text-slate-700">Improve Bullet Points</p>
                          <p class="text-[10px] text-slate-400">Rewrite with action verbs</p>
                        </div>
                      </div>
                      <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-indigo-400 transition-colors"></i>
                    </button>

                    <button @click="analyzeSkillGap" class="w-full group flex items-center justify-between p-4 hover:bg-indigo-50 rounded-xl border border-slate-200 hover:border-indigo-200 transition-all">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                          <i class="fa-solid fa-magnifying-glass-chart text-xs"></i>
                        </div>
                        <div class="text-left">
                          <p class="text-xs font-bold text-slate-700">Skill Gap Analysis</p>
                          <p class="text-[10px] text-slate-400">Compare with job requirements</p>
                        </div>
                      </div>
                      <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-indigo-400 transition-colors"></i>
                    </button>
                  </div>
                </div>

                <!-- Generate Entire Resume Button -->
                <div class="border-t border-slate-200 pt-6">
                  <button 
                    @click="generateResume" 
                    :disabled="isProcessing"
                    class="w-full group relative bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 disabled:from-slate-400 disabled:to-slate-500 text-white p-6 rounded-2xl transition-all shadow-xl hover:shadow-2xl overflow-hidden"
                  >
                    <div class="relative z-10">
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-sparkles text-xl"></i>
                          </div>
                          <div class="text-left">
                            <span class="block text-base font-bold leading-tight">Generate Entire Resume</span>
                            <span class="block text-[10px] text-emerald-100 mt-1">{{ isProcessing ? 'Processing...' : 'AI-powered complete resume generation' }}</span>
                          </div>
                        </div>
                        <i class="fa-solid fa-wand-magic-sparkles text-2xl text-emerald-200 group-hover:rotate-12 transition-transform"></i>
                      </div>
                      <p class="text-[10px] text-emerald-50 leading-relaxed">
                        Let AI create your complete resume based on job description and your context. All sections will be filled automatically.
                      </p>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>
                  </button>
                  <p class="text-[9px] text-slate-400 mt-2 text-center italic">
                    <i class="fa-solid fa-info-circle mr-1"></i> Make sure to add job description in "Import & Job" tab first
                  </p>
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
        <section class="hidden lg:flex flex-[1.5] preview-container flex-col items-start justify-start p-12 overflow-y-auto custom-scrollbar">
          <!-- Page Controls -->
          <div class="w-full max-w-[800px] mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <button 
                @click="currentPage > 1 && currentPage--"
                :disabled="currentPage === 1"
                class="w-8 h-8 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition-all flex items-center justify-center">
                <i class="fa-solid fa-chevron-left text-xs text-slate-600"></i>
              </button>
              
              <div class="px-4 py-2 bg-white border border-slate-200 rounded-lg">
                <span class="text-xs font-bold text-slate-700">Page {{ currentPage }} of {{ totalPages }}</span>
              </div>
              
              <button 
                @click="currentPage < totalPages && currentPage++"
                :disabled="currentPage === totalPages"
                class="w-8 h-8 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition-all flex items-center justify-center">
                <i class="fa-solid fa-chevron-right text-xs text-slate-600"></i>
              </button>
            </div>

            <div class="flex items-center gap-2">
              <button 
                @click="addPage"
                class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus text-[10px]"></i>
                Add Page
              </button>
              
              <button 
                v-if="totalPages > 1"
                @click="removePage"
                class="px-3 py-2 bg-white border border-red-200 hover:bg-red-50 text-red-600 text-xs font-bold rounded-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-trash text-[10px]"></i>
                Remove
              </button>
            </div>
          </div>

          <!-- Preview Content -->
          <div class="w-full max-w-[800px]">
            <ResumePreview 
              :key="previewKey"
              :templateId="selectedTemplate" 
              :formData="formData" 
              :accentColor="currentAccentColor"
              :editable="true"
              :currentPage="currentPage"
              @update:formData="(field, value) => updateFormData(field, value)"
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
              :key="previewKey"
              :templateId="selectedTemplate" 
              :formData="formData" 
              :accentColor="currentAccentColor"
              :editable="true"
              @update:formData="(field, value) => updateFormData(field, value)"
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
import AuthRequiredModal from '../components/AuthRequiredModal.vue'
import { useAuthStore } from '../stores/auth'

// Auth
const authStore = useAuthStore()
const showAuthModal = ref(false)

// State
const activeTab = ref('upload')
const sidebarOpen = ref(false)
const previewModalOpen = ref(false)
const lastSaved = ref('2m ago')
const personalFieldsContainer = ref(null)

// Form Data - Comprehensive Resume Structure (starts empty, populated after upload/paste)
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
  
  // Education
  education: [
    {
      id: 1,
      degree: '',
      institution: '',
      year: '',
      percentage: ''
    }
  ],
  
  // Achievements
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

// Preview key to force re-render when formData changes
const previewKey = computed(() => {
  return JSON.stringify({
    fullName: formData.value.fullName,
    title: formData.value.title,
    email: formData.value.email,
    phone: formData.value.phone,
    location: formData.value.location,
    linkedin: formData.value.linkedin,
    github: formData.value.github,
    summary: formData.value.summary,
    skillsCount: Object.values(formData.value.skills).flat().length,
    experienceCount: formData.value.experience.length,
    educationCount: formData.value.education.length,
    achievementsCount: formData.value.achievements.length,
    template: selectedTemplate.value
  })
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

const selectTemplate = (templateId) =>{
  selectedTemplate.value = templateId
  const template = templatesFromJSON.value.find(t => t.id === templateId)
  console.log('✅ Template selected:', template?.name || templateId)
  
  // Show success message
  successMessage.value = `✓ Applied ${template?.name || 'template'}`
  setTimeout(() => {
    successMessage.value = ''
  }, 2000)
  
  // Close mobile sidebar after selection
  closeMobileSidebarIfNeeded()
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

  console.log('📄 Parsing comprehensive resume content...', { textLength: text.length })

  // Extract basic contact info
  const emailMatch = text.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/)
  if (emailMatch) {
    formData.value.email = emailMatch[0]
    console.log('✉️ Found email:', emailMatch[0])
  }

  const phoneMatch = text.match(/(?:\+?91[-\s]?)?[6-9]\d{9}|(?:\+?1[-.]?)?(?:\(?\d{3}\)?[-.]?)?\d{3}[-.]?\d{4}/)
  if (phoneMatch) {
    formData.value.phone = phoneMatch[0]
    console.log('📞 Found phone:', phoneMatch[0])
  }

  // Extract LinkedIn
  const linkedinMatch = text.match(/linkedin\.com\/in\/([\w-]+)/)
  if (linkedinMatch) {
    formData.value.linkedin = linkedinMatch[0]
    console.log('🔗 Found LinkedIn:', linkedinMatch[0])
  }

  // Extract GitHub
  const githubMatch = text.match(/github\.com\/([\w-]+)/)
  if (githubMatch) {
    formData.value.github = githubMatch[0]
    console.log('🔗 Found GitHub:', githubMatch[0])
  }

  // Extract name (first line, usually all caps or title case)
  const lines = text.split('\n').map(l => l.trim()).filter(l => l)
  if (lines.length > 0) {
    const firstLine = lines[0]
    if (firstLine.length < 50 && /[A-Z]/.test(firstLine)) {
      formData.value.fullName = firstLine
      console.log('👤 Found name:', firstLine)
    }
  }

  // Extract title (second line with keywords)
  for (let i = 1; i < Math.min(lines.length, 5); i++) {
    if (/(Developer|Engineer|Designer|Manager|Analyst|Architect|Specialist)/i.test(lines[i]) && 
        !/(PROFILE|SUMMARY|SKILLS|EXPERIENCE|EDUCATION)/i.test(lines[i])) {
      formData.value.title = lines[i]
      console.log('💼 Found title:', lines[i])
      break
    }
  }

  // Extract location
  const locationMatch = text.match(/([A-Z][a-z]+(?:abad|pur|garh|ganj)?(?:,\s*)?[A-Z][a-z]+)/i)
  if (locationMatch) {
    formData.value.location = locationMatch[1]
    console.log('📍 Found location:', locationMatch[1])
  }

  // Extract Professional Summary
  const summaryMatch = text.match(/(?:PROFILE\s+SUMMARY|SUMMARY|OBJECTIVE|ABOUT)\s*\n([\s\S]*?)\n(?:SKILLS|TECHNICAL|EXPERIENCE|EDUCATION)/i)
  if (summaryMatch) {
    formData.value.summary = summaryMatch[1].trim().replace(/\n+/g, ' ')
    console.log('📝 Found summary:', formData.value.summary.substring(0, 100) + '...')
  }

  // Extract Skills (categorized)
  const skillsMatch = text.match(/SKILLS?\s*\n([\s\S]*?)\n(?:PROFESSIONAL|EXPERIENCE|PROJECTS|EDUCATION)/i)
  if (skillsMatch) {
    const skillsText = skillsMatch[1]
    
    const backendMatch = skillsText.match(/Backend:\s*([^\n]+)/i)
    if (backendMatch) {
      formData.value.skills.backend = backendMatch[1].split(/[,،]/).map(s => s.trim()).filter(s => s)
      console.log('🔧 Found backend skills:', formData.value.skills.backend.length)
    }

    const frontendMatch = skillsText.match(/Frontend:\s*([^\n]+)/i)
    if (frontendMatch) {
      formData.value.skills.frontend = frontendMatch[1].split(/[,،]/).map(s => s.trim()).filter(s => s)
      console.log('🎨 Found frontend skills:', formData.value.skills.frontend.length)
    }

    const devopsMatch = skillsText.match(/DevOps:\s*([^\n]+)/i)
    if (devopsMatch) {
      formData.value.skills.devops = devopsMatch[1].split(/[,،]/).map(s => s.trim()).filter(s => s)
      console.log('⚙️ Found devops skills:', formData.value.skills.devops.length)
    }

    const otherMatch = skillsText.match(/Other:\s*([^\n]+)/i)
    if (otherMatch) {
      formData.value.skills.other = otherMatch[1].split(/[,،]/).map(s => s.trim()).filter(s => s)
      console.log('🔩 Found other skills:', formData.value.skills.other.length)
    }
  }

  // Extract Work Experience
  const expSection = text.match(/(?:PROFESSIONAL\s+)?EXPERIENCE\s*\n([\s\S]*?)\n(?:EDUCATION|PROJECTS|ACHIEVEMENTS|CERTIFICATIONS|$)/i)
  if (expSection) {
    const expText = expSection[1]
    const jobPattern = /([^\n—]+?)\s*[—–-]\s*([^\n(]+?)\s*\(([^)]+)\)/g
    const matches = [...expText.matchAll(jobPattern)]
    const experiences = []

    matches.forEach((match, index) => {
      const position = match[1].trim()
      const company = match[2].trim()
      const dateLocation = match[3].trim()
      
      // Parse dates and location
      const dateParts = dateLocation.split(',')
      let dates = dateParts[0].trim()
      let location = dateParts.length > 1 ? dateParts.slice(1).join(',').trim() : ''
      
      // Extract start and end dates
      const [startDate, endDate] = dates.includes('–') || dates.includes('-') 
        ? dates.split(/[–-]/).map(d => d.trim())
        : [dates, 'Present']

      // Extract responsibilities for this job
      const responsibilities = []
      const startIndex = match.index + match[0].length
      
      // Find next job or end of section
      const nextJobIndex = index + 1 < matches.length 
        ? matches[index + 1].index 
        : expText.length
      const jobContent = expText.substring(startIndex, nextJobIndex)
      
      // Extract bullet points - be more flexible with patterns
      const bulletPoints = jobContent.match(/[•▪●⇨➢➤→✓\-*]\s*([^\n•▪●⇨➢➤→✓\-*]+)/g)
      if (bulletPoints) {
        bulletPoints.forEach(bp => {
          const clean = bp.replace(/^[•▪●⇨➢➤→✓\-*]\s*/, '').trim()
          if (clean && clean.length > 5) {
            responsibilities.push(clean)
          }
        })
      }
      
      // If no bullet points found, try to get a simple text description
      if (responsibilities.length === 0) {
        const lines = jobContent.split('\n').map(l => l.trim()).filter(l => l && l.length > 10)
        if (lines.length > 0) {
          responsibilities.push(lines[0])
        }
      }

      experiences.push({
        id: index + 1,
        position,
        company,
        location,
        startDate,
        endDate,
        current: endDate.toLowerCase().includes('present'),
        responsibilities: responsibilities.length ? responsibilities : ['']
      })
    })

    if (experiences.length > 0) {
      formData.value.experience = experiences
      console.log('💼 Found experience entries:', experiences.length)
      experiences.forEach((exp, idx) => {
        console.log(`   ${idx + 1}. ${exp.position} at ${exp.company} - ${exp.responsibilities.length} responsibilities`)
      })
    }
  }

  // Extract Education
  const eduSection = text.match(/EDUCATION\s*\n([\s\S]*?)\n(?:ACHIEVEMENTS|CERTIFICATIONS|PROJECTS|$)/i)
  if (eduSection) {
    const eduText = eduSection[1]
    const eduPattern = /([^—\n]+?)\s*[—–-]\s*([^(]+?)\s*\(([^)]+)\)/g
    let match
    let id = 1
    const education = []

    while ((match = eduPattern.exec(eduText)) !== null) {
      education.push({
        id: id++,
        degree: match[1].trim(),
        institution: match[2].trim(),
        year: match[3].trim(),
        percentage: ''
      })
    }

    if (education.length > 0) {
      formData.value.education = education
      console.log('🎓 Found education entries:', education.length)
    }
  }

  // Extract Achievements
  const achievementsSection = text.match(/ACHIEVEMENTS?\s*\n([\s\S]*?)(?:\n[A-Z]{3,}|$)/i)
  if (achievementsSection) {
    const achievements = achievementsSection[1].match(/[•▪●⇨➢➤→✓-]\s*([^\n]+)/g)
    if (achievements) {
      formData.value.achievements = achievements
        .map(a => a.replace(/^[•▪●⇨➢➤→✓-]\s*/, '').trim())
        .filter(a => a && a.length > 10)
      console.log('🏆 Found achievements:', formData.value.achievements.length)
    }
  }

  console.log('✅ Comprehensive parsing complete:', {
    name: !!formData.value.fullName,
    title: !!formData.value.title,
    email: !!formData.value.email,
    summary: !!formData.value.summary,
    skills: Object.values(formData.value.skills).some(arr => arr.length > 0),
    experience: formData.value.experience.length,
    education: formData.value.education.length,
    achievements: formData.value.achievements.length
  })
  
  // Show success message
  if (formData.value.email || formData.value.fullName || formData.value.title) {
    const sections = []
    if (formData.value.summary) sections.push('Summary')
    if (Object.values(formData.value.skills).some(arr => arr.length > 0)) sections.push('Skills')
    if (formData.value.experience.length > 0) sections.push(`${formData.value.experience.length} Experience`)
    if (formData.value.education.length > 0) sections.push(`${formData.value.education.length} Education`)
    if (formData.value.achievements.length > 0) sections.push(`${formData.value.achievements.length} Achievements`)
    
    successMessage.value = `✓ Extracted: ${sections.join(', ')}. Check Manual Info tab!`
    setTimeout(() => successMessage.value = '', 5000)
  }
  
  // Auto-calculate pages based on content
  calculateRequiredPages()
}

// Calculate how many pages are needed based on content
const calculateRequiredPages = () => {
  const expCount = formData.value.experience.filter(exp => exp.position && exp.company).length
  const achCount = formData.value.achievements.filter(ach => ach && ach.trim()).length
  
  // Page 1 can hold: 4 experience entries + header + summary + skills + education + achievements
  // Page 2+: 4 experience entries each (no header/static content)
  let pagesNeeded = 1
  
  if (expCount > 4) {
    // Calculate overflow experiences beyond page 1
    const overflow = expCount - 4
    pagesNeeded += Math.ceil(overflow / 4)
  }
  
  // Add pages for achievements if there are many (and no overflow experiences)
  if (achCount > 8 && pagesNeeded === 1) {
    pagesNeeded = 2
  }
  
  if (pagesNeeded > totalPages.value) {
    totalPages.value = pagesNeeded
    console.log(`📄 Auto-created ${pagesNeeded} pages for content distribution`)
    console.log(`   → ${expCount} experiences: Page 1 shows 1-4, Page 2+ shows remaining`)
  }
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

const generateResume = async () => {
  if (isProcessing.value) return
  
  isProcessing.value = true
  uploadError.value = ''
  successMessage.value = ''
  
  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
    
    // Get context from localStorage
    const context = localStorage.getItem('resumeContext')
    const parsed = context ? JSON.parse(context) : {}
    
    const response = await axios.post(`${apiUrl}/api/ai/generate-resume`, {
      resumeContext: parsed.resumeText || resumeText.value || '',
      jobDescription: parsed.jobDescription || jobDescription.value || '',
      tone: selectedTone.value,
      currentData: formData.value
    })
    
    if (response.data.success && response.data.data) {
      const aiData = response.data.data
      
      // Update formData with AI-generated content
      if (aiData.summary) formData.value.summary = aiData.summary
      
      if (aiData.skills) {
        Object.keys(aiData.skills).forEach(category => {
          if (formData.value.skills[category]) {
            formData.value.skills[category] = aiData.skills[category]
          }
        })
      }
      
      if (aiData.experience && Array.isArray(aiData.experience)) {
        formData.value.experience = aiData.experience.map((exp, idx) => ({
          id: Date.now() + idx,
          ...exp
        }))
      }
      
      successMessage.value = '✨ AI generated your resume! Check the Manual Info tab to review and edit.'
      setTimeout(() => {
        successMessage.value = ''
        activeTab.value = 'manual'
      }, 3000)
      
      console.log('✅ AI Resume Generated:', aiData)
    }
  } catch (error) {
    console.error('AI Generation Error:', error)
    uploadError.value = error.response?.data?.message || 'Failed to generate resume with AI'
  } finally {
    isProcessing.value = false
  }
}

const optimizeForATS = async () => {
  if (isProcessing.value) return
  
  isProcessing.value = true
  uploadError.value = ''
  successMessage.value = ''
  
  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
    
    const context = localStorage.getItem('resumeContext')
    const parsed = context ? JSON.parse(context) : {}
    
    const response = await axios.post(`${apiUrl}/api/ai/optimize-ats`, {
      currentResume: formData.value,
      jobDescription: parsed.jobDescription || jobDescription.value || ''
    })
    
    if (response.data.success) {
      const suggestions = response.data.suggestions
      
      // Update ATS score if available
      if (suggestions.atsScore) {
        atsScore.value = suggestions.atsScore
      }
      
      // Show suggestions to user
      let message = `ATS Score: ${suggestions.atsScore || 'N/A'}\n\n`
      
      if (suggestions.missingKeywords && suggestions.missingKeywords.length > 0) {
        message += `Missing Keywords: ${suggestions.missingKeywords.join(', ')}\n\n`
      }
      
      if (suggestions.priorityChanges && suggestions.priorityChanges.length > 0) {
        message += `Priority Changes:\n${suggestions.priorityChanges.slice(0, 3).join('\n')}`
      }
      
      alert(message)
      successMessage.value = '✓ ATS analysis complete! See suggestions above.'
      console.log('📊 ATS Optimization:', suggestions)
    }
  } catch (error) {
    console.error('ATS Optimization Error:', error)
    uploadError.value = error.response?.data?.message || 'Failed to optimize for ATS'
  } finally {
    isProcessing.value = false
  }
}

const improveBulletPoints = async () => {
  if (isProcessing.value) return
  
  // Collect all responsibility bullets from experience
  const allBullets = []
  formData.value.experience.forEach(exp => {
    exp.responsibilities.forEach(resp => {
      if (resp && resp.trim()) allBullets.push(resp)
    })
  })
  
  if (allBullets.length === 0) {
    uploadError.value = 'No bullet points found. Add some experience first!'
    return
  }
  
  isProcessing.value = true
  uploadError.value = ''
  successMessage.value = ''
  
  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
    
    const response = await axios.post(`${apiUrl}/api/ai/improve-bullets`, {
      bulletPoints: allBullets,
      tone: selectedTone.value
    })
    
    if (response.data.success && response.data.improvedBullets) {
      const improved = response.data.improvedBullets
      
      // Update formData with improved bullets
      let bulletIndex = 0
      formData.value.experience.forEach(exp => {
        exp.responsibilities = exp.responsibilities.map(resp => {
          if (resp && resp.trim() && bulletIndex < improved.length) {
            return improved[bulletIndex++]
          }
          return resp
        })
      })
      
      successMessage.value = '✨ Bullet points improved with AI! Check your experience section.'
      console.log('✅ Improved Bullets:', improved)
    }
  } catch (error) {
    console.error('Bullet Points Error:', error)
    uploadError.value = error.response?.data?.message || 'Failed to improve bullet points'
  } finally {
    isProcessing.value = false
  }
}

const analyzeSkillGap = async () => {
  if (isProcessing.value) return
  
  const context = localStorage.getItem('resumeContext')
  const parsed = context ? JSON.parse(context) : {}
  
  if (!parsed.jobDescription && !jobDescription.value) {
    uploadError.value = 'Please provide a job description first (Import & Context tab)'
    return
  }
  
  isProcessing.value = true
  uploadError.value = ''
  successMessage.value = ''
  
  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
    
    const response = await axios.post(`${apiUrl}/api/ai/skill-gap`, {
      currentSkills: formData.value.skills,
      jobDescription: parsed.jobDescription || jobDescription.value
    })
    
    if (response.data.success) {
      const analysis = response.data.analysis
      
      let message = `Skill Match: ${analysis.matchPercentage || 'N/A'}%\n\n`
      
      if (analysis.matchingSkills && analysis.matchingSkills.length > 0) {
        message += `✓ You have: ${analysis.matchingSkills.join(', ')}\n\n`
      }
      
      if (analysis.missingSkills && analysis.missingSkills.length > 0) {
        message += `✗ Missing: ${analysis.missingSkills.join(', ')}\n\n`
      }
      
      if (analysis.suggestions && analysis.suggestions.length > 0) {
        message += `Suggestions:\n${analysis.suggestions.join('\n')}`
      }
      
      alert(message)
      successMessage.value = '✓ Skill gap analysis complete!'
      console.log('📈 Skill Gap Analysis:', analysis)
    }
  } catch (error) {
    console.error('Skill Gap Error:', error)
    uploadError.value = error.response?.data?.message || 'Failed to analyze skill gap'
  } finally {
    isProcessing.value = false
  }
}

const analyzeATS = async () => {
  if (isProcessing.value) return
  
  // Build resume content from formData
  let resumeContent = `${formData.value.fullName}\n${formData.value.title}\n`
  resumeContent += `${formData.value.email} | ${formData.value.phone} | ${formData.value.location}\n\n`
  
  if (formData.value.summary) {
    resumeContent += `SUMMARY\n${formData.value.summary}\n\n`
  }
  
  if (Object.values(formData.value.skills).some(arr => arr.length > 0)) {
    resumeContent += `SKILLS\n`
    if (formData.value.skills.backend.length) resumeContent += `Backend: ${formData.value.skills.backend.join(', ')}\n`
    if (formData.value.skills.frontend.length) resumeContent += `Frontend: ${formData.value.skills.frontend.join(', ')}\n`
    if (formData.value.skills.devops.length) resumeContent += `DevOps: ${formData.value.skills.devops.join(', ')}\n`
    if (formData.value.skills.other.length) resumeContent += `Other: ${formData.value.skills.other.join(', ')}\n`
    resumeContent += `\n`
  }
  
  if (formData.value.experience.length > 0 && formData.value.experience[0].position) {
    resumeContent += `EXPERIENCE\n`
    formData.value.experience.forEach(exp => {
      resumeContent += `${exp.position} - ${exp.company} (${exp.startDate} - ${exp.current ? 'Present' : exp.endDate})\n`
      exp.responsibilities.forEach(resp => {
        if (resp.trim()) resumeContent += `• ${resp}\n`
      })
      resumeContent += `\n`
    })
  }
  
  if (!resumeContent.trim() || resumeContent.length < 100) {
    uploadError.value = 'Please fill in your resume information first (at least name, title, and some experience)'
    return
  }
  
  isProcessing.value = true
  uploadError.value = ''
  successMessage.value = ''
  
  try {
    const formDataObj = new FormData()
    formDataObj.append('input_type', 'paste')
    formDataObj.append('content', resumeContent)
    
    const response = await axios.post('/api/resume-analysis', formDataObj, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    const data = response.data.data
    
    // Update all ATS analysis data
    atsScore.value = data.ats_score
    scoreGrade.value = data.score_grade
    experienceLevel.value = data.experience_level
    keywordMatchPercentage.value = data.keyword_match_percentage
    wordCount.value = data.word_count
    matchedKeywords.value = data.matched_keywords || []
    missingKeywords.value = data.missing_keywords || []
    formattingChecks.value = data.formatting_checks || []
    criticalIssues.value = data.critical_issues || []
    criticalIssuesCount.value = (data.critical_issues || []).length
    
    if (data.content_analysis) {
      contentAnalysis.value = data.content_analysis
    }
    
    atsAnalysisCompleted.value = true
    successMessage.value = `✓ ATS Analysis Complete! Score: ${data.ats_score}/100`
    setTimeout(() => successMessage.value = '', 5000)
    
  } catch (error) {
    console.error('ATS analysis error:', error)
    uploadError.value = error.response?.data?.error || error.response?.data?.message || 'Failed to analyze resume for ATS'
  } finally {
    isProcessing.value = false
  }
}

const removeExperience = (index) => {
  if (formData.value.experience.length > 1) {
    formData.value.experience.splice(index, 1)
    console.log('➖ Removed experience entry', index)
  }
}

const addResponsibility = (expIndex) => {
  formData.value.experience[expIndex].responsibilities.push('')
  console.log('➕ Added new responsibility to experience', expIndex)
}

const removeResponsibility = (expIndex, respIndex) => {
  if (formData.value.experience[expIndex].responsibilities.length > 1) {
    formData.value.experience[expIndex].responsibilities.splice(respIndex, 1)
    console.log('➖ Removed responsibility', respIndex, 'from experience', expIndex)
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
  console.log('➕ Added new education entry')
}

const removeEducation = (index) => {
  if (formData.value.education.length > 1) {
    formData.value.education.splice(index, 1)
    console.log('➖ Removed education entry', index)
  }
}

const addSkill = (category) => {
  const skill = prompt(`Enter new ${category} skill:`)
  if (skill && skill.trim()) {
    formData.value.skills[category].push(skill.trim())
    console.log(`➕ Added ${category} skill:`, skill)
  }
}

const removeSkill = (category, index) => {
  formData.value.skills[category].splice(index, 1)
  console.log(`➖ Removed ${category} skill at index`, index)
}

const addAchievement = () => {
  formData.value.achievements.push('')
  console.log('➕ Added new achievement')
}

const removeAchievement = (index) => {
  if (formData.value.achievements.length > 1) {
    formData.value.achievements.splice(index, 1)
    console.log('➖ Removed achievement', index)
  }
}

// Computed helpers for conditional rendering
const hasSkills = computed(() => {
  return Object.values(formData.value.skills).some(arr => arr.length > 0)
})

const hasExperience = computed(() => {
  return formData.value.experience.length > 0 && formData.value.experience[0].position
})

const hasEducation = computed(() => {
  return formData.value.education.length > 0 && formData.value.education[0].degree
})

const hasAchievements = computed(() => {
  return formData.value.achievements.length > 0 && formData.value.achievements[0]
})

// Update formData from editable preview
const updateFormData = (field, value) => {
  console.log('📝 Preview edit:', field, '=', value)
  
  // Handle nested field updates (e.g., "experience.0.position")
  if (field.includes('.')) {
    const parts = field.split('.')
    let target = formData.value
    
    // Navigate to the parent object
    for (let i = 0; i < parts.length - 1; i++) {
      target = target[parts[i]]
    }
    
    // Set the final value
    target[parts[parts.length - 1]] = value
  } else {
    // Simple field update
    formData.value[field] = value
  }
}

// Page management functions
const addPage = () => {
  totalPages.value++
  currentPage.value = totalPages.value
  console.log(`➕ Added page ${totalPages.value}`)
}

const removePage = () => {
  if (totalPages.value > 1) {
    totalPages.value--
    if (currentPage.value > totalPages.value) {
      currentPage.value = totalPages.value
    }
    console.log(`➖ Removed page, now ${totalPages.value} pages`)
  }
}

const exportPDF = async () => {
  console.log('Exporting PDF...')
  
  try {
    // Prepare resume data from formData
    const resumeData = {
      personal: {
        name: formData.value.fullName,
        email: formData.value.email,
        phone: formData.value.phone,
        location: formData.value.location,
        linkedin: formData.value.linkedin,
        github: formData.value.github,
        portfolio: formData.value.portfolio,
      },
      summary: formData.value.summary,
      experience: formData.value.experience.map(exp => ({
        title: exp.position,
        company: exp.company,
        location: exp.location,
        startDate: exp.startDate,
        endDate: exp.current ? 'Present' : exp.endDate,
        description: exp.responsibilities.join('\n')
      })),
      education: formData.value.education.map(edu => ({
        degree: edu.degree,
        school: edu.institution,
        graduationDate: edu.year,
        gpa: edu.percentage
      })),
      skills: [
        ...formData.value.skills.backend.map(s => ({ name: s, category: 'Backend' })),
        ...formData.value.skills.frontend.map(s => ({ name: s, category: 'Frontend' })),
        ...formData.value.skills.devops.map(s => ({ name: s, category: 'DevOps' })),
        ...formData.value.skills.other.map(s => ({ name: s, category: 'Other' }))
      ],
      certifications: [],
      totalPages: totalPages.value
    }

    // Call backend API
    const response = await fetch('/api/resume/export-pdf', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/pdf',
      },
      credentials: 'include',
      body: JSON.stringify({
        resume_data: resumeData
      })
    })

    if (!response.ok) {
      throw new Error('Export failed')
    }

    // Create blob and download
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    
    // Use user's name for filename or default
    const filename = formData.value.fullName 
      ? `${formData.value.fullName.replace(/[^A-Za-z0-9\-]/g, '_')}_Resume.pdf`
      : 'resume.pdf'
    
    a.download = filename
    document.body.appendChild(a)
    a.click()
    window.URL.revokeObjectURL(url)
    document.body.removeChild(a)

    console.log('PDF exported successfully!')

  } catch (error) {
    console.error('Export error:', error)
    alert('Failed to export PDF. Please try again.')
  }
}

// Initialize drag and drop
onMounted(async () => {
  // Check if redirected from social auth
  const urlParams = new URLSearchParams(window.location.search)
  const isSocialAuth = urlParams.get('social_auth') === 'success'
  
  if (isSocialAuth) {
    console.log('🔐 Social auth redirect detected')
    // Remove the query parameter
    window.history.replaceState({}, document.title, window.location.pathname)
    // Force fetch user
    const userData = await authStore.fetchUser()
    if (userData) {
      console.log('✅ Builder: Social auth successful:', userData.name)
    } else {
      console.log('❌ Builder: Social auth failed, showing modal')
      showAuthModal.value = true
    }
  } else {
    // Give auth store time to initialize and check social login
    await new Promise(resolve => setTimeout(resolve, 100))
    
    // Check authentication on mount
    if (!authStore.isAuthenticated) {
      // If there's a session cookie, try to fetch user (for social login redirects)
      if (authStore.hasSessionCookie()) {
        console.log('🔄 Builder: Fetching user data...')
        // Wait for user fetch to complete before deciding to show modal
        const userData = await authStore.fetchUser()
        if (!userData) {
          // No valid session, show auth modal
          console.log('❌ Builder: No user data, showing auth modal')
          showAuthModal.value = true
        } else {
          console.log('✅ Builder: User authenticated:', userData.name)
        }
        // If userData exists, modal stays hidden (default false)
      } else {
        // No session cookie, show auth modal immediately
        console.log('❌ Builder: No session cookie, showing auth modal')
        showAuthModal.value = true
      }
    } else {
      console.log('✅ Builder: Already authenticated')
    }
  }
  
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
