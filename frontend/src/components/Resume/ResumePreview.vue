<template>
  <div class="bg-white shadow-2xl w-full min-h-[1000px] origin-top transform border border-slate-100" :style="{ boxShadow: `0 25px 50px -12px ${accentColor}25` }">
    
    <!-- Software Engineer Template (Two-Column, Code-Focused) -->
    <div v-if="templateId === 'software-engineer'" class="p-8 lg:p-12 bg-gradient-to-br from-blue-50/30 to-white">
      <div class="flex gap-10">
        <!-- Left Column - Only show on first page -->
        <div v-if="isFirstPage" class="w-[35%] space-y-8">
          <!-- Header with Code Icon -->
          <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto rounded-lg bg-blue-600 flex items-center justify-center text-white text-2xl font-mono mb-3 shadow-lg">
              { }
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight outline-none"
                :contenteditable="editable"
                @blur="editable && handleEdit('fullName', $event)"
                suppressContentEditableWarning>
              {{ formData.fullName || 'Your Name' }}
            </h1>
            <p class="text-base font-bold mt-1 text-blue-600" 
               :contenteditable="editable"
               @blur="editable && handleEdit('title', $event)"
               suppressContentEditableWarning>
              {{ formData.title || 'Software Engineer' }}
            </p>
          </div>

          <!-- Contact -->
          <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
            <h3 class="text-[10px] font-black text-blue-800 uppercase tracking-[0.2em] mb-3 pb-2 border-b border-blue-300">Contact</h3>
            <div class="space-y-2 text-[11px] text-slate-700">
              <p v-if="formData.email" class="flex items-center gap-2"><i class="fa-solid fa-envelope text-blue-600"></i>{{ formData.email }}</p>
              <p v-if="formData.phone" class="flex items-center gap-2"><i class="fa-solid fa-phone text-blue-600"></i>{{ formData.phone }}</p>
              <p v-if="formData.location" class="flex items-center gap-2"><i class="fa-solid fa-location-dot text-blue-600"></i>{{ formData.location }}</p>
              <p v-if="formData.linkedin" class="flex items-center gap-2 break-all"><i class="fa-brands fa-linkedin text-blue-600"></i><span class="truncate">{{ formData.linkedin }}</span></p>
              <p v-if="formData.github" class="flex items-center gap-2 break-all"><i class="fa-brands fa-github text-blue-600"></i><span class="truncate">{{ formData.github }}</span></p>
            </div>
          </div>

          <!-- Tech Stack Grid -->
          <div v-if="hasSkills">
            <h3 class="text-[10px] font-black text-blue-800 uppercase tracking-[0.2em] mb-3">Tech Stack</h3>
            <div class="grid grid-cols-2 gap-2">
              <div v-for="skill in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.devops, ...formData.skills.other].slice(0, 8)" 
                   :key="skill" 
                   class="h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center text-[9px] font-bold shadow-md hover:shadow-lg transition-shadow">
                {{ skill }}
              </div>
            </div>
          </div>

          <!-- Education -->
          <div v-if="hasEducation" class="bg-slate-50 rounded-xl p-4">
            <h3 class="text-[10px] font-black text-slate-700 uppercase tracking-[0.2em] mb-3">Education</h3>
            <div class="space-y-3">
              <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree">
                <h4 class="font-bold text-[11px] text-slate-800">{{ edu.degree }}</h4>
                <p class="text-[10px] text-slate-600">{{ edu.institution }}</p>
                <p class="text-[9px] text-blue-600 font-semibold">{{ edu.year }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div :class="isFirstPage ? 'flex-[1.2]' : 'w-full'" class="space-y-6">
          <!-- Summary -->
          <div v-if="isFirstPage && formData.summary" class="bg-blue-50 border-l-4 border-blue-600 rounded-r-lg p-4">
            <h3 class="text-[10px] font-black text-blue-800 uppercase tracking-[0.2em] mb-2 flex items-center gap-2">
              <i class="fa-solid fa-code"></i>About
            </h3>
            <p class="text-xs text-slate-700 leading-relaxed" :contenteditable="editable" @blur="editable && handleEdit('summary', $event)" suppressContentEditableWarning>
              {{ formData.summary }}
            </p>
          </div>

          <!-- Experience -->
          <div v-if="hasExperience">
            <h3 class="text-[10px] font-black text-blue-800 uppercase tracking-[0.2em] mb-4 pb-2 border-b-2 border-blue-600">Professional Experience</h3>
            <div class="space-y-4">
              <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position || exp.company" class="relative pl-4 border-l-2 border-blue-300">
                <div class="absolute left-[-5px] top-2 w-2 h-2 rounded-full bg-blue-600"></div>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-baseline gap-1">
                  <h4 class="font-bold text-slate-800 text-sm">{{ exp.position }} <span class="text-blue-600">@ {{ exp.company }}</span></h4>
                  <span class="text-[10px] font-mono bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ exp.startDate }} - {{ exp.current ? 'Present' : exp.endDate }}</span>
                </div>
                <ul class="text-[11px] text-slate-600 mt-2 space-y-1">
                  <li v-for="(resp, respIdx) in exp.responsibilities" :key="respIdx" v-show="resp" class="flex items-start gap-2">
                    <span class="text-blue-600 font-bold">▸</span>
                    <span>{{ resp }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Achievements -->
          <div v-if="isFirstPage && hasAchievements" class="bg-gradient-to-r from-blue-50 to-transparent rounded-lg p-4">
            <h3 class="text-[10px] font-black text-blue-800 uppercase tracking-[0.2em] mb-2">Key Achievements</h3>
            <ul class="text-[11px] text-slate-700 space-y-1">
              <li v-for="(achievement, idx) in formData.achievements" :key="idx" v-show="achievement" class="flex items-start gap-2">
                <span class="text-blue-600">✓</span>
                <span>{{ achievement }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Scientist Template (Analytics-Focused, Metrics) -->
    <div v-else-if="templateId === 'data-scientist'" class="p-8 lg:p-12 bg-gradient-to-br from-emerald-50/30 to-white">
      <!-- Header with Stats Badge -->
      <div v-if="isFirstPage" class="flex items-center gap-4 pb-6 mb-6 border-b-2 border-emerald-600">
        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg">
          <i class="fa-solid fa-chart-line text-2xl"></i>
        </div>
        <div class="flex-1">
          <h1 class="text-3xl font-black text-slate-900" :contenteditable="editable" @blur="editable && handleEdit('fullName', $event)" suppressContentEditableWarning>
            {{ formData.fullName || 'Your Name' }}
          </h1>
          <p class="text-lg font-bold text-emerald-600" :contenteditable="editable" @blur="editable && handleEdit('title', $event)" suppressContentEditableWarning>
            {{ formData.title || 'Data Scientist' }}
          </p>
        </div>
        <div class="flex gap-3 text-[10px]">
          <span v-if="formData.email" class="flex items-center gap-1 text-slate-600"><i class="fa-solid fa-envelope text-emerald-600"></i>{{ formData.email }}</span>
          <span v-if="formData.phone" class="flex items-center gap-1 text-slate-600"><i class="fa-solid fa-phone text-emerald-600"></i>{{ formData.phone }}</span>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-6">
        <!-- Left: Skills Matrix -->
        <div v-if="isFirstPage" class="col-span-1 space-y-6">
          <div v-if="hasSkills" class="bg-emerald-50 rounded-xl p-4 border border-emerald-200">
            <h3 class="text-[10px] font-black text-emerald-800 uppercase tracking-wider mb-3">Technical Skills</h3>
            <div class="space-y-3">
              <div v-if="formData.skills.backend.length">
                <p class="text-[9px] font-bold text-emerald-700 mb-1">Analytics & ML</p>
                <div class="flex flex-wrap gap-1">
                  <span v-for="skill in formData.skills.backend" :key="skill" class="text-[8px] px-2 py-0.5 bg-emerald-600 text-white rounded-full font-semibold">{{ skill }}</span>
                </div>
              </div>
              <div v-if="formData.skills.frontend.length">
                <p class="text-[9px] font-bold text-emerald-700 mb-1">Visualization</p>
                <div class="flex flex-wrap gap-1">
                  <span v-for="skill in formData.skills.frontend" :key="skill" class="text-[8px] px-2 py-0.5 bg-teal-600 text-white rounded-full font-semibold">{{ skill }}</span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="hasEducation" class="bg-slate-50 rounded-xl p-4">
            <h3 class="text-[10px] font-black text-slate-700 uppercase tracking-wider mb-3">Education</h3>
            <div class="space-y-2">
              <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree">
                <h4 class="font-bold text-[10px] text-slate-800">{{ edu.degree }}</h4>
                <p class="text-[9px] text-slate-600">{{ edu.institution }}</p>
                <p class="text-[8px] text-emerald-600">{{ edu.year }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Experience & Achievements -->
        <div class="col-span-2 space-y-6">
          <div v-if="isFirstPage && formData.summary" class="bg-emerald-50 rounded-lg p-4 border-l-4 border-emerald-600">
            <p class="text-xs text-slate-700 leading-relaxed">{{ formData.summary }}</p>
          </div>

          <div v-if="hasExperience">
            <h3 class="text-[10px] font-black text-emerald-800 uppercase tracking-wider mb-4 flex items-center gap-2">
              <i class="fa-solid fa-briefcase"></i>Experience & Projects
            </h3>
            <div class="space-y-5">
              <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position || exp.company" class="border-l-3 border-emerald-500 pl-4 hover:bg-emerald-50/50 rounded-r py-2">
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <h4 class="font-bold text-sm text-slate-900">{{ exp.position }}</h4>
                    <p class="text-xs text-emerald-700 font-semibold">{{ exp.company }}</p>
                  </div>
                  <span class="text-[9px] bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full font-bold">{{ exp.startDate }} - {{ exp.current ? 'Now' : exp.endDate }}</span>
                </div>
                <ul class="text-[10px] text-slate-600 space-y-1">
                  <li v-for="(resp, respIdx) in exp.responsibilities" :key="respIdx" v-show="resp" class="flex gap-2">
                    <span class="text-emerald-600">●</span>
                    <span>{{ resp }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- DevOps Engineer Template (Pipeline/Terminal Style) -->
    <div v-else-if="templateId === 'devops-engineer'" class="p-8 lg:p-12 bg-slate-900 text-slate-100">
      <!-- Terminal Header -->
      <div v-if="isFirstPage" class="bg-slate-800 rounded-lg p-4 mb-6 border border-orange-500/30 shadow-lg font-mono">
        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-slate-700">
          <div class="flex gap-1.5">
            <div class="w-3 h-3 rounded-full bg-red-500"></div>
            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
          </div>
          <span class="text-[10px] text-slate-400">~/devops-resume</span>
        </div>
        <div class="space-y-1 text-sm">
          <p><span class="text-orange-500">$</span> cat <span class="text-blue-400">profile.txt</span></p>
          <h1 class="text-2xl font-bold text-orange-400 ml-4" :contenteditable="editable" @blur="editable && handleEdit('fullName', $event)" suppressContentEditableWarning>
            {{ formData.fullName || 'Your Name' }}
          </h1>
          <p class="text-lg text-emerald-400 ml-4" :contenteditable="editable" @blur="editable && handleEdit('title', $event)" suppressContentEditableWarning>
            {{ formData.title || 'DevOps Engineer' }}
          </p>
          <div class="flex gap-4 text-[10px] text-slate-400 ml-4 mt-2">
            <span v-if="formData.email">📧 {{ formData.email }}</span>
            <span v-if="formData.phone">📱 {{ formData.phone }}</span>
            <span v-if="formData.location">📍 {{ formData.location }}</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-4 gap-6">
        <!-- Sidebar: Tools & Skills -->
        <div v-if="isFirstPage" class="col-span-1 space-y-4">
          <div v-if="hasSkills" class="bg-slate-800 rounded-lg p-4 border border-slate-700">
            <h3 class="text-[9px] font-bold text-orange-400 uppercase tracking-wider mb-3 font-mono">// TOOLS</h3>
            <div class="space-y-2">
              <div v-for="skill in [...formData.skills.backend, ...formData.skills.devops, ...formData.skills.other].slice(0, 10)" :key="skill" 
                   class="text-[9px] py-1 px-2 bg-slate-700 text-emerald-400 rounded font-mono border-l-2 border-orange-500">
                {{ skill }}
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="col-span-3 space-y-6">
          <div v-if="isFirstPage && formData.summary" class="bg-slate-800 border-l-4 border-orange-500 rounded-r p-4">
            <p class="text-xs text-slate-300 leading-relaxed font-mono">{{ formData.summary }}</p>
          </div>

          <div v-if="hasExperience">
            <h3 class="text-[10px] font-bold text-orange-400 uppercase tracking-wider mb-4 font-mono flex items-center gap-2">
              <span class="text-orange-500">$</span> git log --experience
            </h3>
            <div class="space-y-4">
              <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position || exp.company" class="bg-slate-800 rounded-lg p-4 border-l-4 border-orange-500">
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <h4 class="font-bold text-sm text-orange-400 font-mono">{{ exp.position }}</h4>
                    <p class="text-xs text-emerald-400">{{ exp.company }}</p>
                  </div>
                  <span class="text-[9px] text-slate-400 font-mono bg-slate-700 px-2 py-1 rounded">{{ exp.startDate }} → {{ exp.current ? 'present' : exp.endDate }}</span>
                </div>
                <ul class="text-[10px] text-slate-300 space-y-1 font-mono">
                  <li v-for="(resp, respIdx) in exp.responsibilities" :key="respIdx" v-show="resp" class="flex gap-2">
                    <span class="text-orange-500">→</span>
                    <span>{{ resp }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Manager Template (Strategy-Focused, Metrics) -->
    <div v-else-if="templateId === 'product-manager'" class="p-8 lg:p-12 bg-gradient-to-br from-purple-50 to-pink-50">
      <!-- Header with Icon -->
      <div v-if="isFirstPage" class="flex items-center gap-4 mb-6 pb-6 border-b-2 border-purple-600">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center text-white shadow-xl">
          <i class="fa-solid fa-rocket text-3xl"></i>
        </div>
        <div class="flex-1">
          <h1 class="text-3xl font-black text-slate-900" :contenteditable="editable" @blur="editable && handleEdit('fullName', $event)" suppressContentEditableWarning>
            {{ formData.fullName || 'Your Name' }}
          </h1>
          <p class="text-xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent" :contenteditable="editable" @blur="editable && handleEdit('title', $event)" suppressContentEditableWarning>
            {{ formData.title || 'Product Manager' }}
          </p>
        </div>
        <div class="text-right text-[10px] text-slate-600 space-y-1">
          <p v-if="formData.email">📧 {{ formData.email }}</p>
          <p v-if="formData.phone">📱 {{ formData.phone }}</p>
          <p v-if="formData.location">📍 {{ formData.location }}</p>
        </div>
      </div>

      <!-- Metrics Cards -->
      <div v-if="isFirstPage" class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-purple-100 border-2 border-purple-300 rounded-xl p-4 text-center">
          <div class="text-2xl font-black text-purple-700">4+</div>
          <div class="text-[10px] text-purple-600 font-semibold uppercase">Years Exp</div>
        </div>
        <div class="bg-pink-100 border-2 border-pink-300 rounded-xl p-4 text-center">
          <div class="text-2xl font-black text-pink-700">{{ formData.experience.length }}</div>
          <div class="text-[10px] text-pink-600 font-semibold uppercase">Products</div>
        </div>
        <div class="bg-purple-100 border-2 border-purple-300 rounded-xl p-4 text-center">
          <div class="text-2xl font-black text-purple-700">{{ [...formData.skills.backend, ...formData.skills.frontend].length }}</div>
          <div class="text-[10px] text-purple-600 font-semibold uppercase">Skills</div>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-6">
        <!-- Left: Skills & Education -->
        <div class="col-span-1 space-y-4">
          <div v-if="hasSkills" class="bg-white rounded-xl p-4 border-2 border-purple-200 shadow-sm">
            <h3 class="text-[10px] font-black text-purple-800 uppercase tracking-wider mb-3 flex items-center gap-2">
              <i class="fa-solid fa-star"></i>Skills
            </h3>
            <div class="flex flex-wrap gap-2">
              <span v-for="skill in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.other].slice(0, 12)" :key="skill" 
                    class="text-[9px] px-2 py-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-full font-bold">
                {{ skill }}
              </span>
            </div>
          </div>

          <div v-if="hasEducation" class="bg-white rounded-xl p-4 border-2 border-pink-200 shadow-sm">
            <h3 class="text-[10px] font-black text-pink-800 uppercase tracking-wider mb-3">Education</h3>
            <div class="space-y-2">
              <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree">
                <h4 class="font-bold text-[10px] text-slate-800">{{ edu.degree }}</h4>
                <p class="text-[9px] text-slate-600">{{ edu.institution }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Experience -->
        <div class="col-span-2 space-y-6">
          <div v-if="isFirstPage && formData.summary" class="bg-white rounded-xl p-4 border-l-4 border-purple-600 shadow-sm">
            <p class="text-xs text-slate-700 leading-relaxed">{{ formData.summary }}</p>
          </div>

          <div v-if="hasExperience">
            <h3 class="text-[11px] font-black text-purple-800 uppercase tracking-wider mb-4 flex items-center gap-2">
              <i class="fa-solid fa-briefcase"></i>Product Experience
            </h3>
            <div class="space-y-4">
              <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position || exp.company" 
                   class="bg-white rounded-xl p-4 border-2 border-purple-100 hover:border-purple-300 transition-colors shadow-sm">
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <h4 class="font-bold text-sm text-purple-800">{{ exp.position }}</h4>
                    <p class="text-xs text-pink-600 font-semibold">{{ exp.company }}</p>
                  </div>
                  <span class="text-[9px] bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-bold">
                    {{ exp.startDate }} - {{ exp.current ? 'Present' : exp.endDate }}
                  </span>
                </div>
                <ul class="text-[10px] text-slate-600 space-y-1">
                  <li v-for="(resp, respIdx) in exp.responsibilities" :key="respIdx" v-show="resp" class="flex gap-2">
                    <span class="text-purple-600">▪</span>
                    <span>{{ resp }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Business Analyst Template (Clean Data-Driven, Charts) -->
    <div v-else-if="templateId === 'business-analyst'" class="p-8 lg:p-12 bg-gradient-to-br from-cyan-50 to-blue-50">
      <div class="flex gap-10">
        <!-- Left Column - Only show on first page -->
        <div v-if="isFirstPage" class="w-[35%] space-y-8">
          <!-- Contact -->
          <div class="bg-slate-50/80 rounded-xl p-5">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4 pb-2 border-b-2" :style="{ borderColor: accentColor }">Contact</h3>
            <div class="space-y-3 text-[11px] text-slate-700">
              <p v-if="formData.email" class="flex items-center gap-2.5 hover:translate-x-1 transition-transform"><span class="w-6 h-6 rounded-full flex items-center justify-center" :style="{ backgroundColor: accentColor + '20' }"><i class="fa-solid fa-envelope text-[10px]" :style="{ color: accentColor }"></i></span>{{ formData.email }}</p>
              <p v-if="formData.phone" class="flex items-center gap-2.5 hover:translate-x-1 transition-transform"><span class="w-6 h-6 rounded-full flex items-center justify-center" :style="{ backgroundColor: accentColor + '20' }"><i class="fa-solid fa-phone text-[10px]" :style="{ color: accentColor }"></i></span>{{ formData.phone }}</p>
              <p v-if="formData.location" class="flex items-center gap-2.5 hover:translate-x-1 transition-transform"><span class="w-6 h-6 rounded-full flex items-center justify-center" :style="{ backgroundColor: accentColor + '20' }"><i class="fa-solid fa-location-dot text-[10px]" :style="{ color: accentColor }"></i></span>{{ formData.location }}</p>
              <p v-if="formData.linkedin" class="flex items-center gap-2.5 break-all hover:translate-x-1 transition-transform"><span class="w-6 h-6 rounded-full flex items-center justify-center" :style="{ backgroundColor: accentColor + '20' }"><i class="fa-brands fa-linkedin text-[10px]" :style="{ color: accentColor }"></i></span><span class="truncate">{{ formData.linkedin }}</span></p>
              <p v-if="formData.github" class="flex items-center gap-2.5 break-all hover:translate-x-1 transition-transform"><span class="w-6 h-6 rounded-full flex items-center justify-center" :style="{ backgroundColor: accentColor + '20' }"><i class="fa-brands fa-github text-[10px]" :style="{ color: accentColor }"></i></span><span class="truncate">{{ formData.github }}</span></p>
            </div>
          </div>

          <!-- Skills -->
          <div v-if="hasSkills" class="bg-slate-50/80 rounded-xl p-5">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4 pb-2 border-b-2" :style="{ borderColor: accentColor }">Skills</h3>
            <div class="space-y-4">
              <div v-if="formData.skills.backend.length">
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-2">Backend</p>
                <div class="flex flex-wrap gap-1.5">
                  <span v-for="skill in formData.skills.backend" :key="skill" class="text-[9px] px-2.5 py-1 rounded-lg font-semibold shadow-sm" :style="{ backgroundColor: accentColor + '15', color: accentColor, border: `1px solid ${accentColor}30` }">{{ skill }}</span>
                </div>
              </div>
              <div v-if="formData.skills.frontend.length">
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-2">Frontend</p>
                <div class="flex flex-wrap gap-1.5">
                  <span v-for="skill in formData.skills.frontend" :key="skill" class="text-[9px] px-2.5 py-1 rounded-lg font-semibold shadow-sm" :style="{ backgroundColor: accentColor + '15', color: accentColor, border: `1px solid ${accentColor}30` }">{{ skill }}</span>
                </div>
              </div>
              <div v-if="formData.skills.devops.length">
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-2">DevOps</p>
                <div class="flex flex-wrap gap-1.5">
                  <span v-for="skill in formData.skills.devops" :key="skill" class="text-[9px] px-2.5 py-1 rounded-lg font-semibold shadow-sm" :style="{ backgroundColor: accentColor + '15', color: accentColor, border: `1px solid ${accentColor}30` }">{{ skill }}</span>
                </div>
              </div>
              <div v-if="formData.skills.other.length">
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-2">Other</p>
                <div class="flex flex-wrap gap-1.5">
                  <span v-for="skill in formData.skills.other" :key="skill" class="text-[9px] px-2.5 py-1 rounded-lg font-semibold shadow-sm" :style="{ backgroundColor: accentColor + '15', color: accentColor, border: `1px solid ${accentColor}30` }">{{ skill }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Education -->
          <div v-if="hasEducation" class="bg-slate-50/80 rounded-xl p-5">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4 pb-2 border-b-2" :style="{ borderColor: accentColor }">Education</h3>
            <div class="space-y-4">
              <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree" class="relative pl-3 border-l-2" :style="{ borderColor: accentColor + '60' }">
                <h4 class="font-bold text-[11px] text-slate-800">{{ edu.degree }}</h4>
                <p class="text-[10px] text-slate-600 font-medium">{{ edu.institution }}</p>
                <p class="text-[9px] text-slate-400 mt-0.5">{{ edu.year }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div :class="isFirstPage ? 'flex-[1.2]' : 'w-full'" class="space-y-6">
          <!-- Header - Only on first page -->
          <div v-if="isFirstPage" class="mb-2">
            <h1 
              class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight leading-tight outline-none"
              :contenteditable="editable"
              @blur="editable && handleEdit('fullName', $event)"
              suppressContentEditableWarning>
              {{ formData.fullName || 'Your Name' }}
            </h1>
            <p 
              class="text-lg lg:text-xl font-bold mt-1.5 outline-none" 
              :style="{ color: accentColor }"
              :contenteditable="editable"
              @blur="editable && handleEdit('title', $event)"
              suppressContentEditableWarning>
              {{ formData.title || 'Your Professional Title' }}
            </p>
            <div class="h-1 w-20 rounded-full mt-3" :style="{ background: `linear-gradient(to right, ${accentColor}, ${accentColor}50)` }"></div>
          </div>

          <!-- Summary - Only on first page -->
          <div v-if="isFirstPage && (formData.summary || editable)" class="bg-gradient-to-r from-slate-50 to-transparent rounded-lg p-4 border-l-3" :style="{ borderLeftColor: accentColor, borderLeftWidth: '3px' }">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3">Profile Summary</h3>
            <p 
              class="text-xs text-slate-700 leading-relaxed outline-none"
              :contenteditable="editable"
              @blur="editable && handleEdit('summary', $event)"
              suppressContentEditableWarning>
              {{ formData.summary || 'Click to add professional summary...' }}
            </p>
          </div>

          <!-- Experience - Paginated -->
          <div v-if="hasExperience && pageExperiences.length > 0">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-5 pb-2 border-b-2" :style="{ borderColor: accentColor }">
              Professional Experience<span v-if="!isFirstPage" class="text-[9px] font-normal ml-2 text-slate-400">(continued)</span>
            </h3>
            <div class="space-y-5">
              <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position || exp.company" class="relative pl-4 border-l-2 hover:bg-slate-50/50 rounded-r-lg transition-colors py-1" :style="{ borderLeftColor: accentColor + '60' }">
                <div class="absolute left-[-5px] top-2 w-2 h-2 rounded-full" :style="{ backgroundColor: accentColor }"></div>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-baseline gap-1">
                  <h4 
                    class="font-bold text-slate-800 text-sm outline-none"
                    :contenteditable="editable"
                    @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.position`, $event)"
                    suppressContentEditableWarning>
                    {{ exp.position }}<span class="font-medium text-slate-500"> at </span><span :style="{ color: accentColor }">{{ exp.company }}</span>
                  </h4>
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap" :style="{ backgroundColor: accentColor + '15', color: accentColor }">{{ exp.startDate }}{{ exp.current ? ' — Present' : (exp.endDate ? ' — ' + exp.endDate : '') }}</span>
                </div>
                <p v-if="exp.location" class="text-[10px] text-slate-400 mt-1 flex items-center gap-1"><i class="fa-solid fa-location-dot text-[8px]"></i>{{ exp.location }}</p>
                <ul class="text-[11px] text-slate-600 mt-3 space-y-1.5">
                  <li 
                    v-for="(resp, respIdx) in exp.responsibilities" 
                    :key="respIdx" 
                    v-show="resp" 
                    class="leading-relaxed outline-none flex items-start gap-2"
                    :contenteditable="editable"
                    @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.responsibilities.${respIdx}`, $event)"
                    suppressContentEditableWarning>
                    <span class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" :style="{ backgroundColor: accentColor }"></span>
                    <span>{{ resp }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Achievements - Only on first page -->
          <div v-if="isFirstPage && hasAchievements" class="bg-gradient-to-r from-amber-50 to-transparent rounded-lg p-4 border-l-3" style="border-left: 3px solid #d97706;">
            <h3 class="text-[10px] font-black text-amber-700 uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><i class="fa-solid fa-trophy"></i>Key Achievements</h3>
            <ul class="text-[11px] text-slate-700 space-y-2">
              <li 
                v-for="(achievement, idx) in formData.achievements" 
                :key="idx" 
                v-show="achievement"
                class="leading-relaxed outline-none flex items-start gap-2"
                :contenteditable="editable"
                @blur="editable && handleEdit(`achievements.${idx}`, $event)"
                suppressContentEditableWarning>
                <span class="text-amber-500">★</span>
                <span>{{ achievement }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Executive/Traditional Templates (Executive, Legal, Finance) -->
    <div v-else-if="['executive', 'legal-professional', 'finance-professional'].includes(templateId)" class="p-8 lg:p-16">
      <div v-if="isFirstPage" class="text-center mb-8 pb-8 border-b-2 border-slate-900">
        <h1 
          class="text-5xl font-black text-slate-900 tracking-tight uppercase mb-2"
          :contenteditable="editable"
          @blur="editable && handleEdit('fullName', $event)"
          suppressContentEditableWarning>
          {{ formData.fullName || 'Jonathan Doe' }}
        </h1>
        <p 
          class="text-lg text-slate-500 font-semibold"
          :contenteditable="editable"
          @blur="editable && handleEdit('title', $event)"
          suppressContentEditableWarning>
          {{ formData.title || 'Senior Software Engineer' }}
        </p>
        <div class="flex justify-center gap-4 mt-4 text-xs font-bold text-slate-400">
          <span v-if="formData.email">{{ formData.email }}</span>
          <span v-if="formData.phone">{{ formData.phone }}</span>
          <span v-if="formData.location">{{ formData.location }}</span>
        </div>
      </div>

      <div class="space-y-8">
        <!-- Summary - Only on first page -->
        <div v-if="isFirstPage && formData.summary">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-900 pb-1">Summary</h3>
          <p 
            class="text-xs text-slate-600 leading-relaxed"
            :contenteditable="editable"
            @blur="editable && handleEdit('summary', $event)"
            suppressContentEditableWarning>
            {{ formData.summary }}
          </p>
        </div>
        
        <div v-if="hasExperience">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-900 pb-1">Professional Experience</h3>
          <div class="space-y-4">
            <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position">
              <div class="flex justify-between items-baseline">
                <h4 
                  class="font-bold text-slate-800"
                  :contenteditable="editable"
                  @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.position`, $event)"
                  suppressContentEditableWarning>
                  {{ exp.position }} at {{ exp.company }}
                </h4>
                <span class="text-[10px] font-bold text-slate-400 italic">{{ exp.startDate }} — {{ exp.current ? 'PRESENT' : exp.endDate }}</span>
              </div>
              <ul class="mt-2 space-y-1">
                <li 
                  v-for="(resp, respIdx) in exp.responsibilities" 
                  :key="respIdx" 
                  v-show="resp" 
                  class="text-xs text-slate-500 leading-relaxed"
                  :contenteditable="editable"
                  @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.responsibilities.${respIdx}`, $event)"
                  suppressContentEditableWarning>
                  • {{ resp }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Creative Templates (Designer, Marketing, Content Creator) -->
    <div v-else-if="['creative-designer', 'marketing-professional', 'content-creator', 'creative'].includes(templateId)" class="flex h-full min-h-[1000px]">
      <!-- Dark Sidebar - Only on first page -->
      <div v-if="isFirstPage" class="w-1/3 p-8 text-white" :style="{ backgroundColor: accentColor || '#4f46e5' }">
        <div class="w-24 h-24 bg-white/20 rounded-full mx-auto mb-6"></div>
        <h1 
          class="text-2xl font-black text-center mb-2"
          :contenteditable="editable"
          @blur="editable && handleEdit('fullName', $event)"
          suppressContentEditableWarning>
          {{ formData.fullName || 'Jonathan Doe' }}
        </h1>
        <p 
          class="text-sm text-center text-white/80 mb-8"
          :contenteditable="editable"
          @blur="editable && handleEdit('title', $event)"
          suppressContentEditableWarning>
          {{ formData.title || 'Senior Software Engineer' }}
        </p>

        <div class="space-y-6">
          <div>
            <h3 class="text-xs font-black uppercase tracking-widest mb-3 text-white/60">Contact</h3>
            <div class="space-y-2 text-xs">
              <p v-if="formData.email" class="break-all">{{ formData.email }}</p>
              <p v-if="formData.phone">{{ formData.phone }}</p>
              <p v-if="formData.location">{{ formData.location }}</p>
            </div>
          </div>

          <div v-if="hasSkills">
            <h3 class="text-xs font-black uppercase tracking-widest mb-3 text-white/60">Skills</h3>
            <div class="flex flex-wrap gap-2">
              <span v-for="skill in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.devops, ...formData.skills.other].slice(0, 12)" :key="skill" class="text-[10px] px-2 py-1 bg-white/20 rounded-full font-semibold">{{ skill }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div :class="isFirstPage ? 'flex-1' : 'w-full'" class="p-8 lg:p-12">
        <div class="space-y-8">
          <div v-if="isFirstPage && formData.summary">
            <h3 class="text-xs font-black uppercase tracking-widest mb-4 pb-1" :style="{ color: accentColor, borderBottom: `2px solid ${accentColor}` }">About</h3>
            <p 
              class="text-xs text-slate-600 leading-relaxed"
              :contenteditable="editable"
              @blur="editable && handleEdit('summary', $event)"
              suppressContentEditableWarning>
              {{ formData.summary }}
            </p>
          </div>

          <div v-if="hasExperience">
            <h3 class="text-xs font-black uppercase tracking-widest mb-4 pb-1" :style="{ color: accentColor, borderBottom: `2px solid ${accentColor}` }">Experience</h3>
            <div class="space-y-4">
              <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position">
                <div class="flex justify-between items-baseline">
                  <h4 
                    class="font-bold text-slate-800"
                    :contenteditable="editable"
                    @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.position`, $event)"
                    suppressContentEditableWarning>
                    {{ exp.position }} at {{ exp.company }}
                  </h4>
                  <span class="text-[10px] font-bold text-slate-400 italic">{{ exp.startDate }} — {{ exp.current ? 'PRESENT' : exp.endDate }}</span>
                </div>
                <ul class="mt-2 space-y-1">
                  <li 
                    v-for="(resp, respIdx) in exp.responsibilities" 
                    :key="respIdx" 
                    v-show="resp" 
                    class="text-xs text-slate-500 leading-relaxed"
                    :contenteditable="editable"
                    @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.responsibilities.${respIdx}`, $event)"
                    suppressContentEditableWarning>
                    • {{ resp }}
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div v-if="isFirstPage && hasEducation">
            <h3 class="text-xs font-black uppercase tracking-widest mb-4 pb-1" :style="{ color: accentColor, borderBottom: `2px solid ${accentColor}` }">Education</h3>
            <div class="space-y-3">
              <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree">
                <h4 class="font-bold text-slate-800">{{ edu.degree }}</h4>
                <p class="text-xs text-slate-500">{{ edu.institution }} • {{ edu.year }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Healthcare Template (Clean, Trustworthy) -->
    <div v-else-if="templateId === 'healthcare'" class="p-8 lg:p-16">
      <!-- Header - Only on first page -->
      <div v-if="isFirstPage" class="mb-8 pb-6 border-b border-slate-200">
        <h1 
          class="text-3xl font-bold text-slate-900 mb-1"
          :contenteditable="editable"
          @blur="editable && handleEdit('fullName', $event)"
          suppressContentEditableWarning>
          {{ formData.fullName || 'Your Name' }}
        </h1>
        <p 
          class="text-base font-medium mb-3" 
          :style="{ color: accentColor }"
          :contenteditable="editable"
          @blur="editable && handleEdit('title', $event)"
          suppressContentEditableWarning>
          {{ formData.title || 'Your Title' }}
        </p>
        <div class="flex flex-wrap gap-3 text-xs text-slate-600">
          <span v-if="formData.email"><i class="fa-solid fa-envelope mr-1"></i>{{ formData.email }}</span>
          <span v-if="formData.phone"><i class="fa-solid fa-phone mr-1"></i>{{ formData.phone }}</span>
          <span v-if="formData.location"><i class="fa-solid fa-location-dot mr-1"></i>{{ formData.location }}</span>
        </div>
      </div>

      <!-- Skills Grid - Only on first page -->
      <div v-if="isFirstPage && hasSkills" class="grid grid-cols-3 gap-4 mb-8">
        <div v-for="tech in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.devops, ...formData.skills.other].slice(0, 9)" :key="tech" class="text-center p-3 rounded-lg" :style="{ backgroundColor: accentColor + '10' }">
          <span class="text-xs font-semibold" :style="{ color: accentColor }">{{ tech }}</span>
        </div>
      </div>

      <div class="space-y-6">
        <!-- Summary - Only on first page -->
        <div v-if="isFirstPage && formData.summary">
          <h3 class="text-sm font-bold text-slate-800 mb-3">Summary</h3>
          <p 
            class="text-xs text-slate-600 leading-relaxed"
            :contenteditable="editable"
            @blur="editable && handleEdit('summary', $event)"
            suppressContentEditableWarning>
            {{ formData.summary }}
          </p>
        </div>

        <div v-if="hasExperience">
          <h3 class="text-sm font-bold text-slate-800 mb-3">Experience</h3>
          <div class="space-y-4">
            <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position">
              <div class="flex justify-between items-baseline mb-1">
                <h4 
                  class="font-semibold text-slate-800"
                  :contenteditable="editable"
                  @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.position`, $event)"
                  suppressContentEditableWarning>
                  {{ exp.position }} at {{ exp.company }}
                </h4>
                <span class="text-[10px] font-bold text-slate-400">{{ exp.startDate }} — {{ exp.current ? 'PRESENT' : exp.endDate }}</span>
              </div>
              <ul class="space-y-1">
                <li 
                  v-for="(resp, respIdx) in exp.responsibilities" 
                  :key="respIdx" 
                  v-show="resp" 
                  class="text-xs text-slate-500 leading-relaxed"
                  :contenteditable="editable"
                  @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.responsibilities.${respIdx}`, $event)"
                  suppressContentEditableWarning>
                  • {{ resp }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Education - Only on first page -->
        <div v-if="isFirstPage && hasEducation">
          <h3 class="text-sm font-bold text-slate-800 mb-3">Education</h3>
          <div class="space-y-3">
            <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree">
              <h4 class="font-semibold text-slate-800">{{ edu.degree }}</h4>
              <p class="text-xs text-slate-500">{{ edu.institution }} • {{ edu.year }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sales Template (Achievement-Focused) -->
    <div v-else-if="templateId === 'sales-professional'" class="p-8 lg:p-16">
      <!-- Header - Only on first page -->
      <div v-if="isFirstPage" class="text-center mb-8">
        <div class="w-20 h-20 rounded-full mx-auto mb-4" :style="{ backgroundColor: accentColor + '20' }"></div>
        <h1 
          class="text-3xl font-black text-slate-900 mb-2"
          :contenteditable="editable"
          @blur="editable && handleEdit('fullName', $event)"
          suppressContentEditableWarning>
          {{ formData.fullName || 'Your Name' }}
        </h1>
        <p 
          class="text-base font-semibold text-slate-600 mb-4"
          :contenteditable="editable"
          @blur="editable && handleEdit('title', $event)"
          suppressContentEditableWarning>
          {{ formData.title || 'Your Title' }}
        </p>
        <div class="flex justify-center gap-4 text-xs text-slate-500">
          <span v-if="formData.email">{{ formData.email }}</span>
          <span v-if="formData.phone">{{ formData.phone }}</span>
          <span v-if="formData.location">{{ formData.location }}</span>
        </div>
      </div>

      <div class="border-t border-slate-200 pt-6 space-y-6">
        <!-- Summary - Only on first page -->
        <div v-if="isFirstPage && formData.summary">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Summary</h3>
          <p 
            class="text-xs text-slate-600 leading-relaxed"
            :contenteditable="editable"
            @blur="editable && handleEdit('summary', $event)"
            suppressContentEditableWarning>
            {{ formData.summary }}
          </p>
        </div>

        <!-- Education - Only on first page -->
        <div v-if="isFirstPage && hasEducation">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Education</h3>
          <div class="space-y-3">
            <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree">
              <h4 class="font-bold text-slate-800">{{ edu.degree }}</h4>
              <p class="text-xs text-slate-500">{{ edu.institution }} • {{ edu.year }}</p>
              <p v-if="edu.percentage" class="text-xs text-slate-400">{{ edu.percentage }}</p>
            </div>
          </div>
        </div>

        <!-- Skills - Only on first page -->
        <div v-if="isFirstPage && hasSkills">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Skills</h3>
          <div class="flex flex-wrap gap-2">
            <span v-for="skill in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.devops, ...formData.skills.other]" :key="skill" class="text-xs px-3 py-1 rounded-full font-semibold" :style="{ backgroundColor: accentColor + '20', color: accentColor }">{{ skill }}</span>
          </div>
        </div>

        <div v-if="hasExperience">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Experience</h3>
          <div class="space-y-3">
            <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position">
              <h4 
                class="font-semibold text-slate-800"
                :contenteditable="editable"
                @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.position`, $event)"
                suppressContentEditableWarning>
                {{ exp.position }} at {{ exp.company }}
              </h4>
              <p class="text-xs text-slate-400 mb-1">{{ exp.startDate }} — {{ exp.current ? 'Present' : exp.endDate }}</p>
              <ul class="space-y-1">
                <li 
                  v-for="(resp, respIdx) in exp.responsibilities" 
                  :key="respIdx" 
                  v-show="resp" 
                  class="text-xs text-slate-500 leading-relaxed"
                  :contenteditable="editable"
                  @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.responsibilities.${respIdx}`, $event)"
                  suppressContentEditableWarning>
                  • {{ resp }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Achievements - Only on first page -->
        <div v-if="isFirstPage && hasAchievements">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Achievements</h3>
          <ul class="space-y-1">
            <li v-for="(achievement, idx) in formData.achievements" :key="idx" v-show="achievement" class="text-xs text-slate-600 leading-relaxed">• {{ achievement }}</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Swiss Grid Template -->
    <div v-else-if="templateId === 'swiss'" class="p-8 lg:p-16">
      <div class="grid grid-cols-2 gap-8">
        <!-- Header spanning full width - Only on first page -->
        <div v-if="isFirstPage" class="col-span-2 p-6 rounded-sm" :style="{ backgroundColor: accentColor || '#dc2626' }">
          <h1 
            class="text-4xl font-black text-white uppercase"
            :contenteditable="editable"
            @blur="editable && handleEdit('fullName', $event)"
            suppressContentEditableWarning>
            {{ formData.fullName || 'Your Name' }}
          </h1>
          <p 
            class="text-lg text-white/90 font-bold mt-1"
            :contenteditable="editable"
            @blur="editable && handleEdit('title', $event)"
            suppressContentEditableWarning>
            {{ formData.title || 'Your Title' }}
          </p>
        </div>

        <!-- Left Column - Only on first page -->
        <div v-if="isFirstPage" class="space-y-6">
          <div class="p-6 bg-slate-50 rounded-sm">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Contact</h3>
            <div class="space-y-2 text-xs text-slate-600">
              <p v-if="formData.email">{{ formData.email }}</p>
              <p v-if="formData.phone">{{ formData.phone }}</p>
              <p v-if="formData.location">{{ formData.location }}</p>
            </div>
          </div>

          <div v-if="hasSkills" class="p-6 bg-slate-50 rounded-sm">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Skills</h3>
            <div class="space-y-1 text-xs text-slate-600">
              <p v-for="skill in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.devops, ...formData.skills.other].slice(0, 10)" :key="skill">{{ skill }}</p>
            </div>
          </div>

          <div v-if="hasEducation" class="p-6 bg-slate-50 rounded-sm">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Education</h3>
            <div class="space-y-3">
              <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree">
                <h4 class="font-bold text-slate-800 text-xs">{{ edu.degree }}</h4>
                <p class="text-[10px] text-slate-500">{{ edu.institution }}</p>
                <p class="text-[10px] text-slate-400">{{ edu.year }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div :class="isFirstPage ? '' : 'col-span-2'" class="space-y-6">
          <!-- Summary - Only on first page -->
          <div v-if="isFirstPage && formData.summary" class="p-6 bg-slate-50 rounded-sm">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">About</h3>
            <p 
              class="text-xs text-slate-600 leading-relaxed"
              :contenteditable="editable"
              @blur="editable && handleEdit('summary', $event)"
              suppressContentEditableWarning>
              {{ formData.summary }}
            </p>
          </div>

          <div v-if="hasExperience" class="p-6 bg-slate-50 rounded-sm">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Experience</h3>
            <div class="space-y-3">
              <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position">
                <h4 
                  class="font-bold text-slate-800 text-sm"
                  :contenteditable="editable"
                  @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.position`, $event)"
                  suppressContentEditableWarning>
                  {{ exp.position }}
                </h4>
                <p class="text-xs text-slate-500 mb-1">{{ exp.company }} • {{ exp.startDate }} — {{ exp.current ? 'Present' : exp.endDate }}</p>
                <ul class="space-y-1">
                  <li 
                    v-for="(resp, respIdx) in exp.responsibilities.slice(0, 2)" 
                    :key="respIdx" 
                    v-show="resp" 
                    class="text-xs text-slate-500 leading-relaxed"
                    :contenteditable="editable"
                    @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.responsibilities.${respIdx}`, $event)"
                    suppressContentEditableWarning>
                    • {{ resp }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Universal Modern Template (Handles all new professional templates) -->
    <div v-else class="p-8 lg:p-12">
      <div :class="[
        'flex gap-8',
        ['software-engineer', 'data-scientist', 'devops-engineer', 'business-analyst', 'healthcare'].includes(templateId) ? 'flex-col' : 'flex-col'
      ]">
        <!-- Header -->
        <div v-if="isFirstPage" :class="[
          'pb-4 mb-4',
          templateId === 'legal-professional' || templateId === 'executive' ? 'text-center border-b-4 border-slate-900' :
          templateId === 'creative-designer' ? 'flex items-center gap-4' :
          'border-b-2',
        ]" :style="{ borderBottomColor: templateId !== 'legal-professional' && templateId !== 'executive' ? accentColor : undefined }">
          
          <!-- Creative Designer - Avatar + Name -->
          <div v-if="templateId === 'creative-designer'" class="w-20 h-20 rounded-full bg-gradient-to-br shadow-lg flex items-center justify-center text-white text-2xl font-black" :style="{ background: `linear-gradient(135deg, ${accentColor}, ${accentColor}dd)` }">
            {{ (formData.fullName || 'You').charAt(0) }}
          </div>

          <!-- Healthcare - Badge -->
          <div v-if="templateId === 'healthcare'" class="flex items-center gap-3 mb-2">
            <div class="w-12 h-12 rounded-full flex items-center justify-center shadow-lg" :style="{ backgroundColor: accentColor }">
              <i class="fa-solid fa-user-doctor text-white text-xl"></i>
            </div>
          </div>

          <div class="flex-1">
            <h1 
              :class="[
                'font-black tracking-tight outline-none',
                templateId === 'legal-professional' || templateId === 'executive' ? 'text-4xl text-slate-900 uppercase' :
                ['software-engineer', 'data-scientist', 'devops-engineer'].includes(templateId) ? 'text-3xl text-slate-900' :
                'text-3xl lg:text-4xl text-slate-900'
              ]"
              :contenteditable="editable"
              @blur="editable && handleEdit('fullName', $event)"
              suppressContentEditableWarning>
              {{ formData.fullName || 'Your Name' }}
            </h1>
            <p 
              :class="['font-bold mt-2 outline-none', templateId === 'legal-professional' || templateId === 'executive' ? 'text-lg text-slate-500' : 'text-lg lg:text-xl']"
              :style="{ color: accentColor }"
              :contenteditable="editable"
              @blur="editable && handleEdit('title', $event)"
              suppressContentEditableWarning>
              {{ formData.title || 'Your Professional Title' }}
            </p>
            
            <!-- Contact Info -->
            <div :class="['flex flex-wrap gap-3 mt-3 text-xs font-medium text-slate-500', templateId === 'legal-professional' || templateId === 'executive' ? 'justify-center' : '']">
              <span v-if="formData.email" class="flex items-center gap-1"><i class="fa-solid fa-envelope text-[10px]" :style="{ color: accentColor }"></i>{{ formData.email }}</span>
              <span v-if="formData.phone" class="flex items-center gap-1"><i class="fa-solid fa-phone text-[10px]" :style="{ color: accentColor }"></i>{{ formData.phone }}</span>
              <span v-if="formData.location" class="flex items-center gap-1"><i class="fa-solid fa-location-dot text-[10px]" :style="{ color: accentColor }"></i>{{ formData.location }}</span>
            </div>
          </div>
        </div>

        <!-- Skills Grid (for tech templates) -->
        <div v-if="isFirstPage && hasSkills && ['software-engineer', 'data-scientist', 'devops-engineer'].includes(templateId)" class="mb-6">
          <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
            <div v-for="skill in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.devops, ...formData.skills.other].slice(0, 12)" 
                 :key="skill" 
                 class="h-10 rounded-lg flex items-center justify-center text-xs font-semibold border shadow-sm"
                 :style="{ backgroundColor: accentColor + '15', borderColor: accentColor + '30', color: accentColor }">
              {{ skill }}
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-6">
          <!-- Summary -->
          <div v-if="isFirstPage && formData.summary" class="bg-slate-50 rounded-lg p-4 border-l-3" :style="{ borderLeftColor: accentColor, borderLeftWidth: '3px' }">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-3 flex items-center gap-2" :style="{ color: accentColor }">
              <i class="fa-solid fa-user"></i>Professional Summary
            </h3>
            <p 
              class="text-xs text-slate-700 leading-relaxed outline-none"
              :contenteditable="editable"
              @blur="editable && handleEdit('summary', $event)"
              suppressContentEditableWarning>
              {{ formData.summary }}
            </p>
          </div>

          <!-- Skills (for non-tech templates) -->
          <div v-if="isFirstPage && hasSkills && !['software-engineer', 'data-scientist', 'devops-engineer', 'modernist', 'tech', 'creative'].includes(templateId)">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4 pb-2 border-b-2" :style="{ color: accentColor, borderBottomColor: accentColor }">
              <i class="fa-solid fa-code mr-2"></i>Core Competencies
            </h3>
            <div class="flex flex-wrap gap-2">
              <span v-for="skill in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.devops, ...formData.skills.other]" 
                    :key="skill" 
                    class="text-xs px-3 py-1.5 rounded-lg font-semibold shadow-sm"
                    :style="{ backgroundColor: accentColor + '15', color: accentColor, border: `1px solid ${accentColor}30` }">
                {{ skill }}
              </span>
            </div>
          </div>

          <!-- Experience -->
          <div v-if="hasExperience">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-5 pb-2 border-b-2 flex items-center gap-2" :style="{ color: accentColor, borderBottomColor: accentColor }">
              <i class="fa-solid fa-briefcase"></i>Professional Experience
            </h3>
            <div class="space-y-5">
              <div v-for="(exp, idx) in pageExperiences" :key="exp.id" v-show="exp.position || exp.company" 
                   class="relative pl-4 border-l-2 hover:bg-slate-50/50 rounded-r-lg transition-colors py-1" 
                   :style="{ borderLeftColor: accentColor + '60' }">
                <div class="absolute left-[-5px] top-2 w-2 h-2 rounded-full" :style="{ backgroundColor: accentColor }"></div>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-baseline gap-1">
                  <h4 
                    class="font-bold text-slate-800 text-sm outline-none"
                    :contenteditable="editable"
                    @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.position`, $event)"
                    suppressContentEditableWarning>
                    {{ exp.position }}<span class="font-medium text-slate-500"> at </span><span :style="{ color: accentColor }">{{ exp.company }}</span>
                  </h4>
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap" :style="{ backgroundColor: accentColor + '15', color: accentColor }">
                    {{ exp.startDate }}{{ exp.current ? ' — Present' : (exp.endDate ? ' — ' + exp.endDate : '') }}
                  </span>
                </div>
                <p v-if="exp.location" class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                  <i class="fa-solid fa-location-dot text-[8px]"></i>{{ exp.location }}
                </p>
                <ul class="text-[11px] text-slate-600 mt-3 space-y-1.5">
                  <li 
                    v-for="(resp, respIdx) in exp.responsibilities" 
                    :key="respIdx" 
                    v-show="resp" 
                    class="leading-relaxed outline-none flex items-start gap-2"
                    :contenteditable="editable"
                    @blur="editable && handleEdit(`experience.${getGlobalIndex(idx)}.responsibilities.${respIdx}`, $event)"
                    suppressContentEditableWarning>
                    <span class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" :style="{ backgroundColor: accentColor }"></span>
                    <span>{{ resp }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Education -->
          <div v-if="isFirstPage && hasEducation">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4 pb-2 border-b-2 flex items-center gap-2" :style="{ color: accentColor, borderBottomColor: accentColor }">
              <i class="fa-solid fa-graduation-cap"></i>Education
            </h3>
            <div class="space-y-3">
              <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree" class="flex justify-between items-start">
                <div>
                  <h4 class="font-bold text-sm text-slate-800">{{ edu.degree }}</h4>
                  <p class="text-xs text-slate-600 mt-0.5">{{ edu.institution }}</p>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap" :style="{ backgroundColor: accentColor + '15', color: accentColor }">{{ edu.year }}</span>
              </div>
            </div>
          </div>

          <!-- Achievements -->
          <div v-if="isFirstPage && hasAchievements" class="bg-gradient-to-r from-amber-50 to-transparent rounded-lg p-4 border-l-3" style="border-left: 3px solid #d97706;">
            <h3 class="text-[10px] font-black text-amber-700 uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
              <i class="fa-solid fa-trophy"></i>Key Achievements
            </h3>
            <ul class="text-[11px] text-slate-700 space-y-2">
              <li 
                v-for="(achievement, idx) in formData.achievements" 
                :key="idx" 
                v-show="achievement"
                class="leading-relaxed outline-none flex items-start gap-2"
                :contenteditable="editable"
                @blur="editable && handleEdit(`achievements.${idx}`, $event)"
                suppressContentEditableWarning>
                <span class="text-amber-500">★</span>
                <span>{{ achievement }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>

</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  templateId: {
    type: String,
    default: 'modernist'
  },
  formData: {
    type: Object,
    default: () => ({
      fullName: '',
      title: '',
      email: '',
      phone: '',
      location: '',
      linkedin: '',
      github: '',
      summary: '',
      skills: {
        backend: [],
        frontend: [],
        devops: [],
        other: []
      },
      experience: [],
      education: [],
      achievements: []
    })
  },
  accentColor: {
    type: String,
    default: '#4f46e5'
  },
  editable: {
    type: Boolean,
    default: false
  },
  currentPage: {
    type: Number,
    default: 1
  }
})

const emit = defineEmits(['update:formData'])

// Handle contenteditable changes
const handleEdit = (field, event) => {
  const value = event.target.innerText.trim()
  emit('update:formData', field, value)
}

// Computed helpers for conditional rendering
const hasSkills = computed(() => {
  return Object.values(props.formData?.skills || {}).some(arr => arr && arr.length > 0)
})

const hasExperience = computed(() => {
  const exp = props.formData?.experience || []
  return exp.length > 0 && (exp[0].position || exp[0].company)
})

const hasEducation = computed(() => {
  const edu = props.formData?.education || []
  return edu.length > 0 && edu[0].degree
})

const hasAchievements = computed(() => {
  const ach = props.formData?.achievements || []
  return ach.length > 0 && ach[0]
})

// Filter experiences based on current page
const pageExperiences = computed(() => {
  const allExperiences = props.formData?.experience || []
  
  if (props.currentPage === 1) {
    // Page 1: Show first 3-4 experiences (or all if fewer)
    return allExperiences.slice(0, 4)
  } else {
    // Page 2+: Show next batch of experiences (4 per page)
    const startIdx = 4 + ((props.currentPage - 2) * 4)
    const endIdx = startIdx + 4
    return allExperiences.slice(startIdx, endIdx)
  }
})

// Get the global index for an experience based on page
const getGlobalIndex = (localIdx) => {
  if (props.currentPage === 1) {
    return localIdx
  } else {
    return 4 + ((props.currentPage - 2) * 4) + localIdx
  }
}

// Check if we should show header/static content (only on page 1)
const isFirstPage = computed(() => props.currentPage === 1)
</script>
