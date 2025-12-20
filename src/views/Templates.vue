<script setup>
import { ref, computed } from 'vue'
import templatesData from '../data/templates.json'
import dummyData from '../data/dummydata.json'

const activeFilter = ref('all')
const isModalOpen = ref(false)
const selectedTemplate = ref(null)

const filters = [
  { id: 'all', label: 'All Templates' },
  { id: 'ats', label: 'ATS Friendly' },
  { id: 'modern', label: 'Modern' },
  { id: 'creative', label: 'Creative' },
  { id: 'executive', label: 'Executive' },
  { id: 'tech', label: 'Tech' },
]

const templates = ref(templatesData)
const resumeData = ref(dummyData)

const openModal = (template) => {
  selectedTemplate.value = template
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  setTimeout(() => {
    selectedTemplate.value = null
  }, 300)
}

const isVisible = (categories) => {
  if (activeFilter.value === 'all') return true
  return categories.split(' ').includes(activeFilter.value)
}

const getTypeTags = (type) => {
    const tags = []
    if(type.includes('ats')) tags.push({ text: 'ATS Optimized', class: 'bg-green-100 text-green-700' })
    if(type.includes('modern')) tags.push({ text: 'Modern', class: 'bg-blue-100 text-blue-700' })
    if(type.includes('creative')) tags.push({ text: 'Creative', class: 'bg-purple-100 text-purple-700' })
    if(type.includes('tech')) tags.push({ text: 'Tech', class: 'bg-indigo-100 text-indigo-700' })
    if(type.includes('executive')) tags.push({ text: 'Professional', class: 'bg-slate-100 text-slate-700' })
    return tags
}
</script>

<template>
  <div class="flex-grow">
        
        <!-- 2. HERO SECTION -->
        <section class="relative pt-20 pb-12 text-center px-6 overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full overflow-hidden -z-10 pointer-events-none">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-float"></div>
                <div class="absolute top-20 right-1/4 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-float" style="animation-delay: 2s"></div>
            </div>

            <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 mb-6 animate-slide-up">
                Professional Templates That <span class="text-indigo-600">Get You Hired</span>
            </h1>
            <p class="text-slate-500 text-lg mb-10 max-w-2xl mx-auto animate-slide-up" style="animation-delay: 0.1s;">
                Choose from modern, ATS-friendly designs crafted for every career level. Optimized for parsing algorithms and human recruiters alike.
            </p>
        </section>

        <!-- 3. FILTER BAR -->
        <section class="sticky top-16 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 py-4 mb-12 animate-fade-in">
            <div class="container mx-auto px-6 overflow-x-auto no-scrollbar">
                <div class="flex flex-wrap gap-2 justify-center">
                    <button 
                        v-for="filter in filters" 
                        :key="filter.id"
                        @click="activeFilter = filter.id"
                        class="px-5 py-2 rounded-full text-sm font-medium transition-all"
                        :class="activeFilter === filter.id ? 'bg-slate-900 text-white transform scale-105' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    >
                        {{ filter.label }}
                    </button>
                </div>
            </div>
        </section>

        <!-- 4. TEMPLATE GRID -->
        <section class="container mx-auto px-6 pb-24">
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-8">
                
                <transition-group name="list">
                <div 
                    v-for="template in templates" 
                    :key="template.id"
                    v-show="isVisible(template.type)" 
                    class="template-card group bg-white rounded-2xl border border-slate-200 overflow-hidden relative"
                >
                    <!-- Thumbnail with Real Content -->
                    <div class="aspect-[3/4] relative p-4 cursor-pointer bg-gradient-to-br from-slate-50 to-slate-100" @click="openModal(template)">
                        <div class="w-full h-full bg-white shadow-md relative overflow-hidden transition-all duration-500 group-hover:scale-[1.02] group-hover:shadow-xl rounded-sm border border-slate-200/50">
                            
                            <!-- Modernist / Two Column Layout -->
                            <div v-if="template.layout.style === 'two-column'" class="flex h-full">
                                <!-- Left Sidebar -->
                                <div class="w-[35%] bg-slate-50 p-3 space-y-2 border-r border-slate-200">
                                    <div class="w-10 h-10 bg-gradient-to-br from-slate-300 to-slate-400 rounded-full mx-auto mb-2 ring-2 ring-white"></div>
                                    <div class="space-y-1">
                                        <div class="font-bold uppercase tracking-wider text-slate-700 text-[2.5px] mb-0.5">Contact</div>
                                        <div class="text-slate-600 text-[2px] leading-tight break-all">{{ resumeData.contact.email.substring(0, 18) }}</div>
                                        <div class="text-slate-600 text-[2px]">{{ resumeData.contact.phone }}</div>
                                        <div class="text-slate-600 text-[2px]">{{ resumeData.contact.location }}</div>
                                    </div>
                                    <div class="space-y-1 pt-1">
                                        <div class="font-bold uppercase tracking-wider text-slate-700 text-[2.5px] mb-0.5">Skills</div>
                                        <div v-for="(skill, i) in resumeData.skills.technical.slice(0, 5)" :key="i" class="text-slate-600 text-[2px] leading-relaxed pl-1 border-l-2" :class="template.layout.primaryColor.replace('bg-', 'border-')">{{ skill }}</div>
                                    </div>
                                </div>
                                <!-- Right Content -->
                                <div class="flex-1 p-3 space-y-2">
                                    <div class="mb-2">
                                        <div class="font-black text-slate-900 text-[5px] leading-tight mb-0.5">{{ resumeData.name }}</div>
                                        <div :class="template.layout.primaryColor" class="text-white inline-block px-1 py-0.5 rounded-sm text-[2.5px] font-semibold">{{ resumeData.title }}</div>
                                    </div>
                                    <div class="h-[1px] bg-gradient-to-r from-slate-300 to-transparent"></div>
                                    <div class="space-y-1.5">
                                        <div class="font-bold uppercase text-slate-800 text-[2.5px] tracking-wide">Experience</div>
                                        <div>
                                            <div class="text-slate-900 font-bold text-[2.5px] mb-0.5">{{ resumeData.experience[0].position }}</div>
                                            <div class="text-slate-600 text-[2px] mb-0.5">{{ resumeData.experience[0].company }} • {{ resumeData.experience[0].duration }}</div>
                                            <div class="space-y-0.5">
                                                <div class="h-[1px] w-full bg-slate-200 rounded"></div>
                                                <div class="h-[1px] w-11/12 bg-slate-200 rounded"></div>
                                                <div class="h-[1px] w-4/5 bg-slate-200 rounded"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Executive / Single Column Layout -->
                            <div v-else-if="template.layout.style === 'single-column'" class="h-full p-3 space-y-2">
                                <div class="text-center pb-2 border-b-2 border-slate-900">
                                    <div class="font-black text-slate-900 text-[5px] tracking-wider mb-0.5" style="letter-spacing: 0.5px;">{{ resumeData.name.toUpperCase() }}</div>
                                    <div class="text-slate-700 text-[2.5px] font-semibold">{{ resumeData.title }}</div>
                                    <div class="text-slate-500 text-[2px] mt-0.5">{{ resumeData.contact.email }} • {{ resumeData.contact.phone }}</div>
                                </div>
                                <div class="space-y-1">
                                    <div class="font-bold uppercase text-slate-800 text-[2.5px] tracking-wide">Professional Summary</div>
                                    <div class="space-y-0.5">
                                        <div class="h-[1px] w-full bg-slate-200 rounded"></div>
                                        <div class="h-[1px] w-full bg-slate-200 rounded"></div>
                                        <div class="h-[1px] w-11/12 bg-slate-200 rounded"></div>
                                        <div class="h-[1px] w-4/5 bg-slate-200 rounded"></div>
                                    </div>
                                </div>
                                <div class="space-y-1 pt-1">
                                    <div class="font-bold uppercase text-slate-800 text-[2.5px] tracking-wide">Experience</div>
                                    <div>
                                        <div class="text-slate-900 font-bold text-[2.5px]">{{ resumeData.experience[0].position }}</div>
                                        <div class="text-slate-600 text-[2px]">{{ resumeData.experience[0].company }}</div>
                                        <div class="space-y-0.5 mt-0.5">
                                            <div class="h-[1px] w-full bg-slate-200 rounded"></div>
                                            <div class="h-[1px] w-11/12 bg-slate-200 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Creative / Sidebar Left Layout -->
                            <div v-else-if="template.layout.style === 'sidebar-left'" class="flex h-full">
                                <!-- Dark Sidebar -->
                                <div class="w-[35%] bg-gradient-to-br from-slate-900 to-slate-800 p-2.5 text-white space-y-2">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-white/30 to-white/10 mx-auto mb-2 ring-2 ring-white/20"></div>
                                    <div class="space-y-1 text-center">
                                        <div class="font-bold text-[2.5px] leading-tight">{{ resumeData.name }}</div>
                                        <div class="text-slate-300 text-[2px]">{{ resumeData.title }}</div>
                                    </div>
                                    <div class="h-[1px] bg-white/20 my-1"></div>
                                    <div class="space-y-1">
                                        <div class="font-semibold uppercase text-[2px] tracking-wide text-white/90">Contact</div>
                                        <div class="text-white/70 text-[1.8px] leading-tight break-all">{{ resumeData.contact.email.substring(0, 18) }}</div>
                                        <div class="text-white/70 text-[1.8px]">{{ resumeData.contact.phone }}</div>
                                    </div>
                                    <div class="space-y-1 pt-1">
                                        <div class="font-semibold uppercase text-[2px] tracking-wide text-white/90">Skills</div>
                                        <div v-for="(skill, i) in resumeData.skills.technical.slice(0, 3)" :key="i" class="text-white/70 text-[1.8px] pl-1 border-l border-white/30">{{ skill }}</div>
                                    </div>
                                </div>
                                <!-- Right Content -->
                                <div class="flex-1 p-3 space-y-2">
                                    <div class="space-y-1">
                                        <div class="font-bold uppercase text-slate-800 text-[2.5px] tracking-wide">About</div>
                                        <div class="space-y-0.5">
                                            <div class="h-[1px] w-full bg-slate-200 rounded"></div>
                                            <div class="h-[1px] w-11/12 bg-slate-200 rounded"></div>
                                            <div class="h-[1px] w-4/5 bg-slate-200 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="font-bold uppercase text-slate-800 text-[2.5px] tracking-wide">Experience</div>
                                        <div class="text-slate-900 font-bold text-[2.5px]">{{ resumeData.experience[0].position }}</div>
                                        <div class="text-slate-600 text-[2px]">{{ resumeData.experience[0].company }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tech Minimal Layout -->
                            <div v-else-if="template.layout.style === 'minimal'" class="h-full p-3 space-y-2">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                                    <div>
                                        <div class="font-black text-slate-900 text-[4px] leading-tight">{{ resumeData.name }}</div>
                                        <div class="text-slate-700 text-[2.5px] font-medium">{{ resumeData.title }}</div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-0.5">
                                    <div v-for="(skill, i) in resumeData.skills.technical.slice(0, 4)" :key="i" :class="template.layout.primaryColor" class="text-white px-1 py-0.5 rounded text-[1.8px] font-medium">{{ skill }}</div>
                                </div>
                                <div class="space-y-1.5 pt-1">
                                    <div class="font-bold uppercase text-slate-800 text-[2.5px] tracking-wide">Recent Experience</div>
                                    <div>
                                        <div class="text-slate-900 font-bold text-[2.5px]">{{ resumeData.experience[0].position }}</div>
                                        <div class="text-slate-600 text-[2px] mb-0.5">{{ resumeData.experience[0].company }}</div>
                                        <div class="space-y-0.5">
                                            <div class="h-[1px] w-full bg-slate-200 rounded"></div>
                                            <div class="h-[1px] w-11/12 bg-slate-200 rounded"></div>
                                            <div class="h-[1px] w-4/5 bg-slate-200 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Centered Header / Graduate Layout -->
                            <div v-else-if="template.layout.style === 'centered-header'" class="h-full p-3 space-y-2">
                                <div class="text-center pb-2">
                                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-200 to-indigo-300 rounded-full mx-auto mb-1 ring-2 ring-indigo-100"></div>
                                    <div class="font-black text-slate-900 text-[4px] mb-0.5">{{ resumeData.name }}</div>
                                    <div class="text-slate-700 text-[2.5px] font-medium">{{ resumeData.title }}</div>
                                    <div class="text-slate-500 text-[2px] mt-0.5">{{ resumeData.contact.email }}</div>
                                </div>
                                <div class="h-[1px] bg-gradient-to-r from-transparent via-slate-300 to-transparent"></div>
                                <div class="space-y-1">
                                    <div class="font-bold uppercase text-slate-800 text-[2.5px] tracking-wide">Education</div>
                                    <div class="text-slate-900 font-semibold text-[2.5px]">{{ resumeData.education[0].degree }}</div>
                                    <div class="text-slate-600 text-[2px]">{{ resumeData.education[0].institution }}</div>
                                    <div class="text-slate-500 text-[1.8px]">{{ resumeData.education[0].duration }}</div>
                                </div>
                                <div class="space-y-1 pt-1">
                                    <div class="font-bold uppercase text-slate-800 text-[2.5px] tracking-wide">Experience</div>
                                    <div class="space-y-0.5">
                                        <div class="h-[1px] w-full bg-slate-200 rounded"></div>
                                        <div class="h-[1px] w-11/12 bg-slate-200 rounded"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Grid Layout -->
                            <div v-else-if="template.layout.style === 'grid'" class="h-full p-3 space-y-2">
                                <div class="text-center pb-2 border-b-2 border-slate-900">
                                    <div class="font-black text-slate-900 text-[5px] tracking-wider">{{ resumeData.name.toUpperCase() }}</div>
                                    <div class="text-slate-700 text-[2px] mt-0.5">{{ resumeData.title }}</div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="space-y-1 p-1.5 bg-slate-50 rounded-sm">
                                        <div class="font-bold text-slate-800 text-[2px] uppercase tracking-wider">Contact</div>
                                        <div class="h-[1px] bg-slate-300 rounded"></div>
                                        <div class="space-y-0.5">
                                            <div class="h-[1px] w-full bg-slate-200 rounded"></div>
                                            <div class="h-[1px] w-4/5 bg-slate-200 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-1 p-1.5 bg-slate-50 rounded-sm">
                                        <div class="font-bold text-slate-800 text-[2px] uppercase tracking-wider">Skills</div>
                                        <div class="h-[1px] bg-slate-300 rounded"></div>
                                        <div class="space-y-0.5">
                                            <div class="h-[1px] w-full bg-slate-200 rounded"></div>
                                            <div class="h-[1px] w-4/5 bg-slate-200 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <!-- Hover Overlay -->
                        <div class="template-overlay absolute inset-0 flex flex-col items-center justify-center gap-3">
                            <button class="template-action px-6 py-2.5 bg-white text-slate-900 rounded-lg font-bold shadow-lg hover:bg-indigo-50 transition-colors w-40">
                                <i class="fa-regular fa-eye mr-2"></i> Preview
                            </button>
                            <router-link to="/builder" class="template-action px-6 py-2.5 bg-indigo-600 text-white rounded-lg font-bold shadow-lg hover:bg-indigo-500 transition-colors w-40 text-center" style="transition-delay: 0.15s">
                                Use Template
                            </router-link>
                        </div>
                    </div>
                    
                    <!-- Details -->
                    <div class="p-5 border-t border-slate-100">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-slate-900 text-lg">{{ template.name }}</h3>
                            <span v-if="template.type.includes('ats')" class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 uppercase">ATS Ready</span>
                            <span v-else-if="template.type.includes('creative')" class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 uppercase">Popular</span>
                        </div>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2">{{ template.description }}</p>
                        <div class="flex gap-2 flex-wrap">
                            <span v-for="tag in template.tags.slice(0, 2)" :key="tag" class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">{{ tag }}</span>
                        </div>
                    </div>
                </div>
                </transition-group>

            </div>
        </section>

        <!-- 5. FEATURES -->
        <section class="bg-slate-50 py-16">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-4 gap-6">
                    <div class="flex items-center gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm animate-slide-up">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm">Fully ATS Compatible</span>
                    </div>
                    <div class="flex items-center gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm animate-slide-up" style="animation-delay: 0.1s">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm">Easy Customization</span>
                    </div>
                    <div class="flex items-center gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm animate-slide-up" style="animation-delay: 0.2s">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <i class="fa-solid fa-file-export"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm">PDF & DOCX Export</span>
                    </div>
                    <div class="flex items-center gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm animate-slide-up" style="animation-delay: 0.3s">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                            <i class="fa-solid fa-font"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm">Pro Typography</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. CTA -->
        <section class="py-24 bg-gradient-to-br from-slate-900 to-indigo-900 text-white text-center px-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgdmlld0JveD0iMCAwIDQwIDQwIj48ZyBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0wIDQwaDQwVjBIMHY0MHptMjAgMjBoMjBWMjBIMjB2MjB6TTAgMjBoMjBWMHgyMHYyMHoiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvZz48L3N2Zz4=')] opacity-20"></div>
            
            <div class="relative z-10">
                <h2 class="text-3xl md:text-5xl font-display font-bold mb-6">Create Your Resume in Minutes</h2>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button class="px-8 py-4 bg-white text-indigo-900 font-bold rounded-xl shadow-lg hover:bg-indigo-50 transition-colors">
                        Start with a Template
                    </button>
                    <router-link to="/builder" class="px-8 py-4 bg-transparent border border-indigo-400 text-white font-bold rounded-xl hover:bg-indigo-800 transition-colors">
                        Build Resume with AI
                    </router-link>
                </div>
            </div>
        </section>

        <!-- PREVIEW MODAL -->
        <transition name="fade">
        <div v-if="isModalOpen && selectedTemplate" class="fixed inset-0 z-50">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="closeModal"></div>
            
            <!-- Modal Content -->
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="bg-white w-full max-w-4xl h-[90vh] rounded-2xl shadow-2xl flex flex-col md:flex-row overflow-hidden relative transform transition-all">
                    <button @click="closeModal" class="absolute top-4 right-4 z-20 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-slate-800 flex items-center justify-center shadow-md transition-all">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <!-- Left: Info -->
                    <div class="w-full md:w-1/3 bg-slate-50 border-r border-slate-200 p-8 flex flex-col overflow-y-auto">
                        <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ selectedTemplate.name }}</h2>
                        <div class="flex gap-2 mb-6 flex-wrap">
                            <span v-for="tag in getTypeTags(selectedTemplate.type || '')" :key="tag.text" class="px-2 py-1 rounded text-xs font-bold" :class="tag.class">{{ tag.text }}</span>
                        </div>
                        
                        <div class="space-y-6 mb-8">
                            <div>
                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Best For</h4>
                                <p class="text-sm text-slate-700">{{ selectedTemplate.tags.join(', ') }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">ATS Score</h4>
                                <div class="flex items-center gap-2">
                                    <div class="w-full bg-slate-200 rounded-full h-2 flex-1">
                                        <div class="bg-green-500 h-2 rounded-full w-[95%]"></div>
                                    </div>
                                    <span class="text-sm font-bold text-green-600">95/100</span>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Includes</h4>
                                <ul class="text-sm text-slate-600 space-y-2">
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500"></i> Resume</li>
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500"></i> Cover Letter</li>
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-indigo-500"></i> References Page</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <router-link to="/builder" class="block w-full py-3 bg-indigo-600 text-white font-bold rounded-xl text-center shadow-lg hover:bg-indigo-500 hover:shadow-indigo-500/25 transition-all">
                                Use This Template
                            </router-link>
                        </div>
                    </div>

                    <!-- Right: Visual Preview with Actual Content -->
                    <div class="w-full md:w-2/3 bg-slate-100 p-8 overflow-y-auto flex justify-center">
                         <!-- A CSS-drawn A4 paper representation with real content -->
                         <div class="w-[210mm] min-h-[297mm] bg-white shadow-xl transform scale-[0.5] md:scale-[0.6] lg:scale-[0.7] origin-top p-12 text-slate-800 relative text-sm leading-relaxed">
                            
                            <!-- Two Column Layout -->
                            <div v-if="selectedTemplate.layout.style === 'two-column'" class="flex gap-8 h-full">
                                <!-- Left Sidebar -->
                                <div class="w-1/3 space-y-6">
                                    <div class="w-24 h-24 bg-slate-300 rounded-full mx-auto"></div>
                                    <div>
                                        <h3 class="font-bold text-xs uppercase text-slate-600 mb-2">Contact</h3>
                                        <p class="text-xs text-slate-600">{{ resumeData.contact.email }}</p>
                                        <p class="text-xs text-slate-600">{{ resumeData.contact.phone }}</p>
                                        <p class="text-xs text-slate-600">{{ resumeData.contact.location }}</p>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xs uppercase text-slate-600 mb-2">Skills</h3>
                                        <div v-for="skill in resumeData.skills.technical" :key="skill" class="text-xs text-slate-600 mb-1">{{ skill }}</div>
                                    </div>
                                </div>
                                <!-- Right Content -->
                                <div class="flex-1">
                                    <div class="mb-6">
                                        <h1 class="text-3xl font-bold text-slate-900">{{ resumeData.name }}</h1>
                                        <div :class="selectedTemplate.layout.primaryColor" class="text-white inline-block px-2 py-1 mt-1 text-sm">{{ resumeData.title }}</div>
                                    </div>
                                    <div class="mb-6">
                                        <h2 class="font-bold text-sm uppercase text-slate-700 mb-2">Professional Summary</h2>
                                        <p class="text-xs text-slate-600">{{ resumeData.summary }}</p>
                                    </div>
                                    <div>
                                        <h2 class="font-bold text-sm uppercase text-slate-700 mb-3">Experience</h2>
                                        <div v-for="exp in resumeData.experience" :key="exp.company" class="mb-4">
                                            <h3 class="font-semibold text-sm text-slate-800">{{ exp.position }}</h3>
                                            <p class="text-xs text-slate-600">{{ exp.company }} • {{ exp.duration }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Single Column Layout -->
                            <div v-else-if="selectedTemplate.layout.style === 'single-column'" class="space-y-6">
                                <div class="text-center border-b-2 border-slate-900 pb-4">
                                    <h1 class="text-4xl font-bold text-slate-900 tracking-wide">{{ resumeData.name.toUpperCase() }}</h1>
                                    <p class="text-lg text-slate-600 mt-1">{{ resumeData.title }}</p>
                                    <p class="text-xs text-slate-500 mt-2">{{ resumeData.contact.email }} • {{ resumeData.contact.phone }} • {{ resumeData.contact.location }}</p>
                                </div>
                                <div>
                                    <h2 class="font-bold text-sm uppercase text-slate-700 mb-2">Professional Summary</h2>
                                    <p class="text-xs text-slate-600">{{ resumeData.summary }}</p>
                                </div>
                                <div>
                                    <h2 class="font-bold text-sm uppercase text-slate-700 mb-3">Experience</h2>
                                    <div v-for="exp in resumeData.experience" :key="exp.company" class="mb-4">
                                        <h3 class="font-semibold text-sm text-slate-800">{{ exp.position }}</h3>
                                        <p class="text-xs text-slate-600">{{ exp.company }} • {{ exp.duration }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar Left Layout -->
                            <div v-else-if="selectedTemplate.layout.style === 'sidebar-left'" class="flex gap-0 h-full">
                                <!-- Dark Sidebar -->
                                <div class="w-1/3 bg-slate-900 text-white p-6 -m-12 mr-0 space-y-6">
                                    <div class="w-20 h-20 bg-white/20 rounded-full mx-auto"></div>
                                    <div class="text-center">
                                        <h1 class="text-xl font-bold">{{ resumeData.name }}</h1>
                                        <p class="text-sm text-slate-300 mt-1">{{ resumeData.title }}</p>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xs uppercase mb-2">Contact</h3>
                                        <p class="text-xs text-white/80 mb-1">{{ resumeData.contact.email }}</p>
                                        <p class="text-xs text-white/80">{{ resumeData.contact.phone }}</p>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xs uppercase mb-2">Skills</h3>
                                        <div v-for="skill in resumeData.skills.technical.slice(0, 5)" :key="skill" class="text-xs text-white/80 mb-1">{{ skill }}</div>
                                    </div>
                                </div>
                                <!-- Right Content -->
                                <div class="flex-1 pl-8">
                                    <div class="mb-6">
                                        <h2 class="font-bold text-sm uppercase text-slate-700 mb-2">About Me</h2>
                                        <p class="text-xs text-slate-600">{{ resumeData.summary.substring(0, 150) }}...</p>
                                    </div>
                                    <div>
                                        <h2 class="font-bold text-sm uppercase text-slate-700 mb-3">Experience</h2>
                                        <div v-for="exp in resumeData.experience" :key="exp.company" class="mb-4">
                                            <h3 class="font-semibold text-sm text-slate-800">{{ exp.position }}</h3>
                                            <p class="text-xs text-slate-600">{{ exp.company }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Other layouts default view -->
                            <div v-else class="space-y-6">
                                <div class="text-center pb-4 border-b border-slate-200">
                                    <h1 class="text-3xl font-bold text-slate-900">{{ resumeData.name }}</h1>
                                    <p class="text-base text-slate-600 mt-1">{{ resumeData.title }}</p>
                                    <p class="text-xs text-slate-500 mt-2">{{ resumeData.contact.email }} • {{ resumeData.contact.phone }}</p>
                                </div>
                                <div>
                                    <h2 class="font-bold text-sm uppercase text-slate-700 mb-2">Professional Summary</h2>
                                    <p class="text-xs text-slate-600">{{ resumeData.summary }}</p>
                                </div>
                                <div>
                                    <h2 class="font-bold text-sm uppercase text-slate-700 mb-3">Experience</h2>
                                    <div v-for="exp in resumeData.experience" :key="exp.company" class="mb-4">
                                        <h3 class="font-semibold text-sm text-slate-800">{{ exp.position }}</h3>
                                        <p class="text-xs text-slate-600">{{ exp.company }} • {{ exp.location }} • {{ exp.duration }}</p>
                                        <ul class="list-disc list-inside text-xs text-slate-600 mt-1 space-y-0.5">
                                            <li v-for="(achievement, i) in exp.achievements.slice(0, 2)" :key="i">{{ achievement }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                         </div>
                    </div>
                </div>
            </div>
        </div>
        </transition>

  </div>
</template>

<style scoped>
.template-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.template-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.template-overlay {
    opacity: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(4px);
    transition: all 0.3s ease;
}
.template-card:hover .template-overlay {
    opacity: 1;
}

.template-action {
    transform: translateY(20px);
    opacity: 0;
    transition: all 0.3s ease 0.1s;
}
.template-card:hover .template-action {
    transform: translateY(0);
    opacity: 1;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
