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
              :key="previewKey"
              :templateId="selectedTemplate" 
              :formData="formData" 
              :accentColor="currentAccentColor"
              :editable="true"
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

// State
const activeTab = ref('upload')
const sidebarOpen = ref(false)
const previewModalOpen = ref(false)
const lastSaved = ref('2m ago')
const personalFieldsContainer = ref(null)

// Form Data - Comprehensive Resume Structure
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
    let match
    let id = 1
    const experiences = []

    while ((match = jobPattern.exec(expText)) !== null) {
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

      // Extract responsibilities
      const responsibilities = []
      const startIndex = match.index + match[0].length
      const restOfText = expText.substring(startIndex)
      const nextJobIndex = restOfText.search(/[^\n]+\s*[—–-]\s*[^\n]+\s*\([^)]+\)/)
      const jobContent = nextJobIndex > 0 ? restOfText.substring(0, nextJobIndex) : restOfText.substring(0, 500)
      
      const bulletPoints = jobContent.match(/[•▪●⇨➢➤→✓-]\s*([^\n]+)/g)
      if (bulletPoints) {
        bulletPoints.forEach(bp => {
          const clean = bp.replace(/^[•▪●⇨➢➤→✓-]\s*/, '').trim()
          if (clean && clean.length > 10) responsibilities.push(clean)
        })
      }

      experiences.push({
        id: id++,
        position,
        company,
        location,
        startDate,
        endDate,
        current: endDate.toLowerCase().includes('present'),
        responsibilities: responsibilities.length ? responsibilities : ['']
      })
    }

    if (experiences.length > 0) {
      formData.value.experience = experiences
      console.log('💼 Found experience entries:', experiences.length)
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

// Helper Functions for Dynamic Fields
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
  console.log('➕ Added new experience entry')
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
