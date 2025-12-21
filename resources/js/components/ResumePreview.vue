<template>
  <div class="bg-white shadow-2xl w-full min-h-[1000px] origin-top transform" :style="{ boxShadow: `0 25px 50px -12px ${accentColor}20` }">
    
    <!-- Modernist Template (Two Column) -->
    <div v-if="templateId === 'modernist'" class="p-8 lg:p-16">
      <div class="flex gap-8">
        <!-- Left Column -->
        <div class="w-1/3 space-y-6">
          <!-- Profile Photo - Only on Page 1 -->
          <div v-if="currentPage === 1" class="w-32 h-32 bg-slate-200 rounded-full mx-auto mb-6"></div>
          
          <!-- Contact - Only on Page 1 -->
          <div v-if="currentPage === 1">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 pb-1 border-b" :style="{ borderColor: accentColor }">Contact</h3>
            <div class="space-y-2 text-xs text-slate-600">
              <p v-if="formData.email"><i class="fa-solid fa-envelope mr-2" :style="{ color: accentColor }"></i>{{ formData.email }}</p>
              <p v-if="formData.phone"><i class="fa-solid fa-phone mr-2" :style="{ color: accentColor }"></i>{{ formData.phone }}</p>
              <p v-if="formData.location"><i class="fa-solid fa-location-dot mr-2" :style="{ color: accentColor }"></i>{{ formData.location }}</p>
              <p v-if="formData.linkedin" class="break-all"><i class="fa-brands fa-linkedin mr-2" :style="{ color: accentColor }"></i>{{ formData.linkedin }}</p>
              <p v-if="formData.github" class="break-all"><i class="fa-brands fa-github mr-2" :style="{ color: accentColor }"></i>{{ formData.github }}</p>
            </div>
          </div>

          <!-- Page Continuation Header - Page 2+ -->
          <div v-if="currentPage > 1" class="mb-6">
            <p class="text-sm font-bold text-slate-700">{{ formData.fullName || 'Your Name' }}</p>
            <p class="text-xs text-slate-400">Page {{ currentPage }}</p>
          </div>

          <!-- Skills - Only on Page 1 -->
          <div v-if="currentPage === 1 && hasSkills">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 pb-1 border-b" :style="{ borderColor: accentColor }">Skills</h3>
            <div class="space-y-3">
              <div v-if="formData.skills.backend.length">
                <p class="text-[9px] font-bold text-slate-400 mb-1">Backend</p>
                <div class="flex flex-wrap gap-1">
                  <span v-for="skill in formData.skills.backend" :key="skill" class="text-[9px] px-2 py-0.5 rounded-full font-semibold" :style="{ backgroundColor: accentColor + '20', color: accentColor }">{{ skill }}</span>
                </div>
              </div>
              <div v-if="formData.skills.frontend.length">
                <p class="text-[9px] font-bold text-slate-400 mb-1">Frontend</p>
                <div class="flex flex-wrap gap-1">
                  <span v-for="skill in formData.skills.frontend" :key="skill" class="text-[9px] px-2 py-0.5 rounded-full font-semibold" :style="{ backgroundColor: accentColor + '20', color: accentColor }">{{ skill }}</span>
                </div>
              </div>
              <div v-if="formData.skills.devops.length">
                <p class="text-[9px] font-bold text-slate-400 mb-1">DevOps</p>
                <div class="flex flex-wrap gap-1">
                  <span v-for="skill in formData.skills.devops" :key="skill" class="text-[9px] px-2 py-0.5 rounded-full font-semibold" :style="{ backgroundColor: accentColor + '20', color: accentColor }">{{ skill }}</span>
                </div>
              </div>
              <div v-if="formData.skills.other.length">
                <p class="text-[9px] font-bold text-slate-400 mb-1">Other</p>
                <div class="flex flex-wrap gap-1">
                  <span v-for="skill in formData.skills.other" :key="skill" class="text-[9px] px-2 py-0.5 rounded-full font-semibold" :style="{ backgroundColor: accentColor + '20', color: accentColor }">{{ skill }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Education - Only on Page 1 -->
          <div v-if="currentPage === 1 && hasEducation">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 pb-1 border-b" :style="{ borderColor: accentColor }">Education</h3>
            <div class="space-y-3">
              <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree">
                <h4 class="font-bold text-[10px] text-slate-800">{{ edu.degree }}</h4>
                <p class="text-[9px] text-slate-500">{{ edu.institution }}</p>
                <p class="text-[9px] text-slate-400">{{ edu.year }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="flex-1 space-y-6">
          <!-- Header - Only on Page 1 -->
          <div v-if="currentPage === 1">
            <h1 
              class="text-4xl font-black text-slate-900 tracking-tight uppercase outline-none"
              :contenteditable="editable"
              @blur="editable && handleEdit('fullName', $event)"
              suppressContentEditableWarning>
              {{ formData.fullName || 'Your Name' }}
            </h1>
            <p 
              class="text-xl font-bold mt-2 outline-none" 
              :style="{ color: accentColor }"
              :contenteditable="editable"
              @blur="editable && handleEdit('title', $event)"
              suppressContentEditableWarning>
              {{ formData.title || 'Your Professional Title' }}
            </p>
          </div>

          <!-- Summary -->
          <div v-if="currentPage === 1 && formData.summary || editable">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-1">Summary</h3>
            <p 
              class="text-xs text-slate-600 leading-relaxed outline-none"
              :contenteditable="editable"
              @blur="editable && handleEdit('summary', $event)"
              suppressContentEditableWarning>
              {{ currentPage === 1 ? (formData.summary || 'Click to add professional summary...') : 'Add content for page ' + currentPage }}
            </p>
          </div>

          <!-- Experience - Page 1: First 3 entries -->
          <div v-if="currentPage === 1 && hasExperience">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-1">Experience</h3>
            <div class="space-y-4">
              <div v-for="(exp, idx) in formData.experience.slice(0, 3)" :key="exp.id" v-show="exp.position || exp.company">
                <div class="flex justify-between items-baseline">
                  <h4 
                    class="font-bold text-slate-800 outline-none"
                    :contenteditable="editable"
                    @blur="editable && handleEdit(`experience.${idx}.position`, $event)"
                    suppressContentEditableWarning>
                    {{ exp.position }}{{ exp.company ? ' at ' + exp.company : '' }}
                  </h4>
                  <span class="text-[10px] font-bold text-slate-400 italic">{{ exp.startDate }}{{ exp.endDate ? ' — ' + exp.endDate : '' }}</span>
                </div>
                <p v-if="exp.location" class="text-[10px] text-slate-400 mt-0.5">{{ exp.location }}</p>
                <ul class="text-xs text-slate-500 mt-2 space-y-1">
                  <li 
                    v-for="(resp, respIdx) in exp.responsibilities" 
                    :key="respIdx" 
                    v-show="resp" 
                    class="leading-relaxed outline-none"
                    :contenteditable="editable"
                    @blur="editable && handleEdit(`experience.${idx}.responsibilities.${respIdx}`, $event)"
                    suppressContentEditableWarning>
                    • {{ resp }}
                  </li>
                </ul>
              </div>
            </div>
            <p v-if="formData.experience.length > 3" class="text-[10px] text-slate-400 italic mt-3 text-center">
              Continued on page 2...
            </p>
          </div>

          <!-- Experience - Page 2+: Remaining entries (4 per page) -->
          <div v-if="currentPage > 1 && getPageExperiences(currentPage).length > 0">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-1">Experience (continued)</h3>
            <div class="space-y-4">
              <div v-for="(exp, idx) in getPageExperiences(currentPage)" :key="exp.id">
                <div class="flex justify-between items-baseline">
                  <h4 class="font-bold text-slate-800 outline-none">
                    {{ exp.position }}{{ exp.company ? ' at ' + exp.company : '' }}
                  </h4>
                  <span class="text-[10px] font-bold text-slate-400 italic">{{ exp.startDate }}{{ exp.endDate ? ' — ' + exp.endDate : '' }}</span>
                </div>
                <p v-if="exp.location" class="text-[10px] text-slate-400 mt-0.5">{{ exp.location }}</p>
                <ul class="text-xs text-slate-500 mt-2 space-y-1">
                  <li v-for="(resp, respIdx) in exp.responsibilities" :key="respIdx" v-show="resp" class="leading-relaxed">
                    • {{ resp }}
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Page 2+ Content Placeholder (if no experiences to show) -->
          <div v-if="currentPage > 1 && getPageExperiences(currentPage).length === 0" class="space-y-6">
            <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-lg">
              <i class="fa-solid fa-file-circle-plus text-4xl text-slate-300 mb-4"></i>
              <h3 class="text-sm font-bold text-slate-600 mb-2">Additional Page {{ currentPage }}</h3>
              <p class="text-xs text-slate-400 max-w-md mx-auto">This page is ready for additional content. You can manually add more experience, projects, or other sections here.</p>
            </div>
          </div>

          <!-- Achievements - Only on Page 1 -->
          <div v-if="currentPage === 1 && hasAchievements">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-1">Achievements</h3>
            <ul class="text-xs text-slate-600 space-y-1">
              <li 
                v-for="(achievement, idx) in formData.achievements" 
                :key="idx" 
                v-show="achievement"
                class="outline-none"
                :contenteditable="editable"
                @blur="editable && handleEdit(`achievements.${idx}`, $event)"
                suppressContentEditableWarning>
                • {{ achievement }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Executive Template (Single Column, Centered) -->
    <div v-else-if="templateId === 'executive'" class="p-8 lg:p-16">
      <div class="text-center mb-8 pb-8 border-b-2 border-slate-900">
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
        <div v-if="formData.summary">
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
            <div v-for="(exp, idx) in formData.experience" :key="exp.id" v-show="exp.position">
              <div class="flex justify-between items-baseline">
                <h4 
                  class="font-bold text-slate-800"
                  :contenteditable="editable"
                  @blur="editable && handleEdit(`experience.${idx}.position`, $event)"
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
                  @blur="editable && handleEdit(`experience.${idx}.responsibilities.${respIdx}`, $event)"
                  suppressContentEditableWarning>
                  • {{ resp }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Creative Template (Sidebar Left) -->
    <div v-else-if="templateId === 'creative'" class="flex h-full min-h-[1000px]">
      <!-- Dark Sidebar -->
      <div class="w-1/3 p-8 text-white" :style="{ backgroundColor: accentColor || '#4f46e5' }">
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
      <div class="flex-1 p-8 lg:p-12">
        <div class="space-y-8">
          <div v-if="formData.summary">
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
              <div v-for="(exp, idx) in formData.experience" :key="exp.id" v-show="exp.position">
                <div class="flex justify-between items-baseline">
                  <h4 
                    class="font-bold text-slate-800"
                    :contenteditable="editable"
                    @blur="editable && handleEdit(`experience.${idx}.position`, $event)"
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
                    @blur="editable && handleEdit(`experience.${idx}.responsibilities.${respIdx}`, $event)"
                    suppressContentEditableWarning>
                    • {{ resp }}
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div v-if="hasEducation">
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

    <!-- Tech Minimal Template -->
    <div v-else-if="templateId === 'tech'" class="p-8 lg:p-16">
      <div class="mb-8 pb-6 border-b border-slate-200">
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

      <div v-if="hasSkills" class="grid grid-cols-3 gap-4 mb-8">
        <div v-for="tech in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.devops, ...formData.skills.other].slice(0, 9)" :key="tech" class="text-center p-3 rounded-lg" :style="{ backgroundColor: accentColor + '10' }">
          <span class="text-xs font-semibold" :style="{ color: accentColor }">{{ tech }}</span>
        </div>
      </div>

      <div class="space-y-6">
        <div v-if="formData.summary">
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
            <div v-for="(exp, idx) in formData.experience" :key="exp.id" v-show="exp.position">
              <div class="flex justify-between items-baseline mb-1">
                <h4 
                  class="font-semibold text-slate-800"
                  :contenteditable="editable"
                  @blur="editable && handleEdit(`experience.${idx}.position`, $event)"
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
                  @blur="editable && handleEdit(`experience.${idx}.responsibilities.${respIdx}`, $event)"
                  suppressContentEditableWarning>
                  • {{ resp }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div v-if="hasEducation">
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

    <!-- Graduate Template (Centered Header) -->
    <div v-else-if="templateId === 'graduate'" class="p-8 lg:p-16">
      <div class="text-center mb-8">
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
        <div v-if="formData.summary">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Summary</h3>
          <p 
            class="text-xs text-slate-600 leading-relaxed"
            :contenteditable="editable"
            @blur="editable && handleEdit('summary', $event)"
            suppressContentEditableWarning>
            {{ formData.summary }}
          </p>
        </div>

        <div v-if="hasEducation">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Education</h3>
          <div class="space-y-3">
            <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree">
              <h4 class="font-bold text-slate-800">{{ edu.degree }}</h4>
              <p class="text-xs text-slate-500">{{ edu.institution }} • {{ edu.year }}</p>
              <p v-if="edu.percentage" class="text-xs text-slate-400">{{ edu.percentage }}</p>
            </div>
          </div>
        </div>

        <div v-if="hasSkills">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Skills</h3>
          <div class="flex flex-wrap gap-2">
            <span v-for="skill in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.devops, ...formData.skills.other]" :key="skill" class="text-xs px-3 py-1 rounded-full font-semibold" :style="{ backgroundColor: accentColor + '20', color: accentColor }">{{ skill }}</span>
          </div>
        </div>

        <div v-if="hasExperience">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Experience</h3>
          <div class="space-y-3">
            <div v-for="(exp, idx) in formData.experience" :key="exp.id" v-show="exp.position">
              <h4 
                class="font-semibold text-slate-800"
                :contenteditable="editable"
                @blur="editable && handleEdit(`experience.${idx}.position`, $event)"
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
                  @blur="editable && handleEdit(`experience.${idx}.responsibilities.${respIdx}`, $event)"
                  suppressContentEditableWarning>
                  • {{ resp }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div v-if="hasAchievements">
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
        <!-- Header spanning full width -->
        <div class="col-span-2 p-6 rounded-sm" :style="{ backgroundColor: accentColor || '#dc2626' }">
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

        <!-- Left Column -->
        <div class="space-y-6">
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
        <div class="space-y-6">
          <div v-if="formData.summary" class="p-6 bg-slate-50 rounded-sm">
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
              <div v-for="(exp, idx) in formData.experience" :key="exp.id" v-show="exp.position">
                <h4 
                  class="font-bold text-slate-800 text-sm"
                  :contenteditable="editable"
                  @blur="editable && handleEdit(`experience.${idx}.position`, $event)"
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
                    @blur="editable && handleEdit(`experience.${idx}.responsibilities.${respIdx}`, $event)"
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

    <!-- Default/Fallback Template -->
    <div v-else class="p-8 lg:p-16">
      <div class="border-b-4 border-slate-900 pb-8 mb-8">
        <h1 
          class="text-4xl font-black text-slate-900 tracking-tight uppercase"
          :contenteditable="editable"
          @blur="editable && handleEdit('fullName', $event)"
          suppressContentEditableWarning>
          {{ formData.fullName || 'Your Name' }}
        </h1>
        <p 
          class="text-lg font-bold mt-1" 
          :style="{ color: accentColor }"
          :contenteditable="editable"
          @blur="editable && handleEdit('title', $event)"
          suppressContentEditableWarning>
          {{ formData.title || 'Your Title' }}
        </p>
        <div class="flex gap-4 mt-4 text-[11px] font-bold text-slate-400">
          <span v-if="formData.email"><i class="fa-solid fa-envelope mr-1.5"></i>{{ formData.email }}</span>
          <span v-if="formData.phone"><i class="fa-solid fa-phone mr-1.5"></i>{{ formData.phone }}</span>
          <span v-if="formData.location"><i class="fa-solid fa-location-dot mr-1.5"></i>{{ formData.location }}</span>
        </div>
      </div>

      <div class="space-y-8">
        <div v-if="formData.summary">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-1">Summary</h3>
          <p 
            class="text-xs text-slate-600 leading-relaxed"
            :contenteditable="editable"
            @blur="editable && handleEdit('summary', $event)"
            suppressContentEditableWarning>
            {{ formData.summary }}
          </p>
        </div>

        <div v-if="hasSkills">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-1">Skills</h3>
          <div class="flex flex-wrap gap-2">
            <span v-for="skill in [...formData.skills.backend, ...formData.skills.frontend, ...formData.skills.devops, ...formData.skills.other]" :key="skill" class="text-xs px-3 py-1 rounded-full font-semibold" :style="{ backgroundColor: accentColor + '20', color: accentColor }">{{ skill }}</span>
          </div>
        </div>

        <div v-if="hasExperience">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-1">Experience</h3>
          <div class="space-y-4">
            <div v-for="(exp, idx) in formData.experience" :key="exp.id" v-show="exp.position">
              <div class="flex justify-between items-baseline">
                <h4 
                  class="font-bold text-slate-800"
                  :contenteditable="editable"
                  @blur="editable && handleEdit(`experience.${idx}.position`, $event)"
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
                  @blur="editable && handleEdit(`experience.${idx}.responsibilities.${respIdx}`, $event)"
                  suppressContentEditableWarning>
                  • {{ resp }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div v-if="hasEducation">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-1">Education</h3>
          <div class="space-y-3">
            <div v-for="edu in formData.education" :key="edu.id" v-show="edu.degree">
              <h4 class="font-bold text-slate-800">{{ edu.degree }}</h4>
              <p class="text-xs text-slate-500">{{ edu.institution }} • {{ edu.year }}</p>
            </div>
          </div>
        </div>

        <div v-if="hasAchievements">
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-1">Achievements</h3>
          <ul class="space-y-1">
            <li v-for="(achievement, idx) in formData.achievements" :key="idx" v-show="achievement" class="text-xs text-slate-600 leading-relaxed">• {{ achievement }}</li>
          </ul>
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

// Get experiences for a specific page
const getPageExperiences = (page) => {
  const allExperiences = props.formData?.experience || []
  
  if (page === 1) {
    return [] // Page 1 shows first 3 in main section
  }
  
  // Page 2 shows experiences 4-7 (indices 3-6)
  // Page 3 shows experiences 8-11 (indices 7-10), etc.
  const startIdx = 3 + ((page - 2) * 4)
  const endIdx = startIdx + 4
  
  return allExperiences.slice(startIdx, endIdx)
}
</script>
