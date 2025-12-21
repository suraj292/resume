<template>
  <div class="bg-white shadow-2xl w-full min-h-[1000px] origin-top transform" :style="{ boxShadow: `0 25px 50px -12px ${accentColor}20` }">
    
    <!-- Modernist Template (Two Column) -->
    <div v-if="templateId === 'modernist'" class="p-8 lg:p-16">
      <div class="flex gap-8">
        <!-- Left Column -->
        <div class="w-1/3 space-y-6">
          <div class="w-32 h-32 bg-slate-200 rounded-full mx-auto mb-6"></div>
          
          <!-- Contact -->
          <div>
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 pb-1 border-b" :style="{ borderColor: accentColor }">Contact</h3>
            <div class="space-y-2 text-xs text-slate-600">
              <p v-if="formData.email"><i class="fa-solid fa-envelope mr-2" :style="{ color: accentColor }"></i>{{ formData.email }}</p>
              <p v-if="formData.phone"><i class="fa-solid fa-phone mr-2" :style="{ color: accentColor }"></i>{{ formData.phone }}</p>
              <p v-if="formData.location"><i class="fa-solid fa-location-dot mr-2" :style="{ color: accentColor }"></i>{{ formData.location }}</p>
              <p v-if="formData.linkedin" class="break-all"><i class="fa-brands fa-linkedin mr-2" :style="{ color: accentColor }"></i>{{ formData.linkedin }}</p>
              <p v-if="formData.github" class="break-all"><i class="fa-brands fa-github mr-2" :style="{ color: accentColor }"></i>{{ formData.github }}</p>
            </div>
          </div>

          <!-- Skills -->
          <div v-if="hasSkills">
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

          <!-- Education -->
          <div v-if="hasEducation">
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
          <!-- Header -->
          <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight uppercase">{{ formData.fullName || 'Your Name' }}</h1>
            <p class="text-xl font-bold mt-2" :style="{ color: accentColor }">{{ formData.title || 'Your Professional Title' }}</p>
          </div>

          <!-- Summary -->
          <div v-if="formData.summary">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-1">Summary</h3>
            <p class="text-xs text-slate-600 leading-relaxed">{{ formData.summary }}</p>
          </div>

          <!-- Experience -->
          <div v-if="hasExperience">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-1">Experience</h3>
            <div class="space-y-4">
              <div v-for="exp in formData.experience" :key="exp.id" v-show="exp.position || exp.company">
                <div class="flex justify-between items-baseline">
                  <h4 class="font-bold text-slate-800">{{ exp.position }}{{ exp.company ? ' at ' + exp.company : '' }}</h4>
                  <span class="text-[10px] font-bold text-slate-400 italic">{{ exp.startDate }}{{ exp.endDate ? ' — ' + exp.endDate : '' }}</span>
                </div>
                <p v-if="exp.location" class="text-[10px] text-slate-400 mt-0.5">{{ exp.location }}</p>
                <ul class="text-xs text-slate-500 mt-2 space-y-1">
                  <li v-for="(resp, idx) in exp.responsibilities" :key="idx" v-show="resp" class="leading-relaxed">• {{ resp }}</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Achievements -->
          <div v-if="hasAchievements">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-1">Achievements</h3>
            <ul class="text-xs text-slate-600 space-y-1">
              <li v-for="(achievement, idx) in formData.achievements" :key="idx" v-show="achievement">• {{ achievement }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Executive Template (Single Column, Centered) -->
    <div v-else-if="templateId === 'executive'" class="p-8 lg:p-16">
      <div class="text-center mb-8 pb-8 border-b-2 border-slate-900">
        <h1 class="text-5xl font-black text-slate-900 tracking-tight uppercase mb-2">{{ formData.fullName || 'Jonathan Doe' }}</h1>
        <p class="text-lg text-slate-500 font-semibold">{{ formData.title || 'Senior Software Engineer' }}</p>
        <div class="flex justify-center gap-4 mt-4 text-xs font-bold text-slate-400">
          <span v-if="formData.email">{{ formData.email }}</span>
          <span v-if="formData.phone">{{ formData.phone }}</span>
          <span v-if="formData.location">{{ formData.location }}</span>
        </div>
      </div>

      <div class="space-y-8">
        <div>
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-900 pb-1">Professional Experience</h3>
          <div class="space-y-4">
            <div>
              <div class="flex justify-between items-baseline">
                <h4 class="font-bold text-slate-800">Principal Engineer at TechCorp</h4>
                <span class="text-[10px] font-bold text-slate-400 italic">2021 — PRESENT</span>
              </div>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">Led the migration of legacy infrastructure to a modern microservices architecture, improving system uptime by 40%.</p>
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
        <h1 class="text-2xl font-black text-center mb-2">{{ formData.fullName || 'Jonathan Doe' }}</h1>
        <p class="text-sm text-center text-white/80 mb-8">{{ formData.title || 'Senior Software Engineer' }}</p>

        <div class="space-y-6">
          <div>
            <h3 class="text-xs font-black uppercase tracking-widest mb-3 text-white/60">Contact</h3>
            <div class="space-y-2 text-xs">
              <p v-if="formData.email" class="break-all">{{ formData.email }}</p>
              <p v-if="formData.phone">{{ formData.phone }}</p>
              <p v-if="formData.location">{{ formData.location }}</p>
            </div>
          </div>

          <div>
            <h3 class="text-xs font-black uppercase tracking-widest mb-3 text-white/60">Skills</h3>
            <div class="flex flex-wrap gap-2">
              <span v-for="skill in ['JavaScript', 'Vue.js', 'Design']" :key="skill" class="text-[10px] px-2 py-1 bg-white/20 rounded-full font-semibold">{{ skill }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 p-8 lg:p-12">
        <div class="space-y-8">
          <div>
            <h3 class="text-xs font-black uppercase tracking-widest mb-4 pb-1" :style="{ color: accentColor, borderBottom: `2px solid ${accentColor}` }">Experience</h3>
            <div class="space-y-4">
              <div>
                <div class="flex justify-between items-baseline">
                  <h4 class="font-bold text-slate-800">Principal Engineer at TechCorp</h4>
                  <span class="text-[10px] font-bold text-slate-400 italic">2021 — PRESENT</span>
                </div>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Led the migration of legacy infrastructure to a modern microservices architecture.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tech Minimal Template -->
    <div v-else-if="templateId === 'tech'" class="p-8 lg:p-16">
      <div class="mb-8 pb-6 border-b border-slate-200">
        <h1 class="text-3xl font-bold text-slate-900 mb-1">{{ formData.fullName || 'Jonathan Doe' }}</h1>
        <p class="text-base font-medium mb-3" :style="{ color: accentColor }">{{ formData.title || 'Senior Software Engineer' }}</p>
        <div class="flex flex-wrap gap-3 text-xs text-slate-600">
          <span v-if="formData.email"><i class="fa-solid fa-envelope mr-1"></i>{{ formData.email }}</span>
          <span v-if="formData.phone"><i class="fa-solid fa-phone mr-1"></i>{{ formData.phone }}</span>
          <span v-if="formData.location"><i class="fa-solid fa-location-dot mr-1"></i>{{ formData.location }}</span>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-4 mb-8">
        <div v-for="tech in ['JavaScript', 'Python', 'Docker', 'AWS', 'React', 'Node.js']" :key="tech" class="text-center p-3 rounded-lg" :style="{ backgroundColor: accentColor + '10' }">
          <span class="text-xs font-semibold" :style="{ color: accentColor }">{{ tech }}</span>
        </div>
      </div>

      <div class="space-y-6">
        <div>
          <h3 class="text-sm font-bold text-slate-800 mb-3">Experience</h3>
          <div class="space-y-4">
            <div>
              <div class="flex justify-between items-baseline mb-1">
                <h4 class="font-semibold text-slate-800">Principal Engineer at TechCorp</h4>
                <span class="text-[10px] font-bold text-slate-400">2021 — PRESENT</span>
              </div>
              <p class="text-xs text-slate-500 leading-relaxed">Led the migration of legacy infrastructure to a modern microservices architecture.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Graduate Template (Centered Header) -->
    <div v-else-if="templateId === 'graduate'" class="p-8 lg:p-16">
      <div class="text-center mb-8">
        <div class="w-20 h-20 rounded-full mx-auto mb-4" :style="{ backgroundColor: accentColor + '20' }"></div>
        <h1 class="text-3xl font-black text-slate-900 mb-2">{{ formData.fullName || 'Jonathan Doe' }}</h1>
        <p class="text-base font-semibold text-slate-600 mb-4">{{ formData.title || 'Recent Graduate' }}</p>
        <div class="flex justify-center gap-4 text-xs text-slate-500">
          <span v-if="formData.email">{{ formData.email }}</span>
          <span v-if="formData.phone">{{ formData.phone }}</span>
        </div>
      </div>

      <div class="border-t border-slate-200 pt-6 space-y-6">
        <div>
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Education</h3>
          <div>
            <h4 class="font-bold text-slate-800">Bachelor of Science in Computer Science</h4>
            <p class="text-xs text-slate-500">University Name • 2020 - 2024</p>
          </div>
        </div>

        <div>
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Experience</h3>
          <div class="space-y-3">
            <div>
              <h4 class="font-semibold text-slate-800">Software Engineering Intern</h4>
              <p class="text-xs text-slate-500 mt-1">Developed features for web applications using modern frameworks.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Swiss Grid Template -->
    <div v-else-if="templateId === 'swiss'" class="p-8 lg:p-16">
      <div class="grid grid-cols-2 gap-8">
        <!-- Header spanning full width -->
        <div class="col-span-2 p-6 rounded-sm" :style="{ backgroundColor: accentColor || '#dc2626' }">
          <h1 class="text-4xl font-black text-white uppercase">{{ formData.fullName || 'Jonathan Doe' }}</h1>
          <p class="text-lg text-white/90 font-bold mt-1">{{ formData.title || 'Creative Professional' }}</p>
        </div>

        <!-- Left Column -->
        <div class="space-y-6">
          <div class="p-6 bg-slate-50 rounded-sm">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Contact</h3>
            <div class="space-y-2 text-xs text-slate-600">
              <p v-if="formData.email">{{ formData.email }}</p>
              <p v-if="formData.phone">{{ formData.phone }}</p>
            </div>
          </div>

          <div class="p-6 bg-slate-50 rounded-sm">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Skills</h3>
            <div class="space-y-1 text-xs text-slate-600">
              <p>Design Systems</p>
              <p>Typography</p>
              <p>Grid Layouts</p>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
          <div class="p-6 bg-slate-50 rounded-sm">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Experience</h3>
            <div class="space-y-3">
              <div>
                <h4 class="font-bold text-slate-800 text-sm">Senior Designer</h4>
                <p class="text-xs text-slate-500 mt-1">Created bold, grid-based layouts for major brands.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Default/Fallback Template -->
    <div v-else class="p-8 lg:p-16">
      <div class="border-b-4 border-slate-900 pb-8 mb-8">
        <h1 class="text-4xl font-black text-slate-900 tracking-tight uppercase">{{ formData.fullName || 'Jonathan Doe' }}</h1>
        <p class="text-lg font-bold mt-1" :style="{ color: accentColor }">{{ formData.title || 'Senior Software Engineer' }}</p>
        <div class="flex gap-4 mt-4 text-[11px] font-bold text-slate-400">
          <span v-if="formData.email"><i class="fa-solid fa-envelope mr-1.5"></i>{{ formData.email }}</span>
          <span v-if="formData.phone"><i class="fa-solid fa-phone mr-1.5"></i>{{ formData.phone }}</span>
        </div>
      </div>

      <div class="space-y-8">
        <div>
          <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-1">Experience</h3>
          <div class="space-y-4">
            <div>
              <div class="flex justify-between items-baseline">
                <h4 class="font-bold text-slate-800">Principal Engineer at TechCorp</h4>
                <span class="text-[10px] font-bold text-slate-400 italic">2021 — PRESENT</span>
              </div>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">Led the migration of legacy infrastructure to a modern microservices architecture, improving system uptime by 40%.</p>
            </div>
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
  }
})

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
</script>
