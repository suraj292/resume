<script setup>
import { ref } from 'vue'

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

const openModal = (name, type) => {
  selectedTemplate.value = { name, type }
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
                <div class="flex gap-3 md:justify-center min-w-max">
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
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Template 1: The Modernist (Modern/ATS) -->
                <transition-group name="list">
                <div v-show="isVisible('modern ats')" key="modernist" class="template-card group bg-white rounded-2xl border border-slate-200 overflow-hidden relative">
                    <!-- Thumbnail (CSS Drawn) -->
                    <div class="aspect-[3/4] bg-slate-50 relative p-6 cursor-pointer" @click="openModal('The Modernist', 'modern ats')">
                        <div class="w-full h-full bg-white shadow-sm flex flex-col p-4 gap-3 relative overflow-hidden transition-transform duration-500 group-hover:scale-105">
                            <div class="flex gap-3">
                                <div class="w-16 h-16 bg-slate-200 rounded-full"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-4 w-3/4 bg-slate-800 rounded"></div>
                                    <div class="h-3 w-1/2 bg-indigo-500 rounded"></div>
                                </div>
                            </div>
                            <div class="h-px bg-slate-200 my-2"></div>
                            <div class="flex gap-4 h-full">
                                <div class="w-1/3 space-y-2">
                                    <div class="h-3 w-full bg-slate-200 rounded"></div>
                                    <div class="h-3 w-full bg-slate-200 rounded"></div>
                                    <div class="h-3 w-2/3 bg-slate-200 rounded"></div>
                                </div>
                                <div class="w-2/3 space-y-3">
                                    <div class="h-3 w-full bg-slate-100 rounded"></div>
                                    <div class="h-3 w-full bg-slate-100 rounded"></div>
                                    <div class="h-3 w-3/4 bg-slate-100 rounded"></div>
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
                            <h3 class="font-bold text-slate-900 text-lg">The Modernist</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 uppercase">ATS Ready</span>
                        </div>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2">Clean, double-column layout perfect for showcasing skills and experience side-by-side.</p>
                        <div class="flex gap-2">
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Modern</span>
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">2 Column</span>
                        </div>
                    </div>
                </div>

                <!-- Template 2: Executive Suite (Executive/ATS) -->
                <div v-show="isVisible('executive ats')" key="executive" class="template-card group bg-white rounded-2xl border border-slate-200 overflow-hidden relative">
                    <div class="aspect-[3/4] bg-slate-50 relative p-6 cursor-pointer" @click="openModal('Executive Suite', 'executive ats')">
                        <div class="w-full h-full bg-white shadow-sm flex flex-col p-6 gap-3 relative overflow-hidden transition-transform duration-500 group-hover:scale-105">
                            <div class="text-center space-y-2 mb-4">
                                <div class="h-5 w-1/2 bg-slate-900 mx-auto rounded"></div>
                                <div class="h-3 w-1/3 bg-slate-400 mx-auto rounded"></div>
                            </div>
                            <div class="h-px bg-slate-900 mb-2"></div>
                            <div class="space-y-3">
                                <div class="h-3 w-full bg-slate-200 rounded"></div>
                                <div class="h-3 w-full bg-slate-200 rounded"></div>
                                <div class="h-3 w-5/6 bg-slate-200 rounded"></div>
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
                    <div class="p-5 border-t border-slate-100">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-slate-900 text-lg">Executive Suite</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 uppercase">ATS Ready</span>
                        </div>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2">Traditional, text-focused layout ideal for C-level and management roles.</p>
                        <div class="flex gap-2">
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Professional</span>
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Single Column</span>
                        </div>
                    </div>
                </div>

                <!-- Template 3: Creative Spark (Creative) -->
                <div v-show="isVisible('creative')" key="creative" class="template-card group bg-white rounded-2xl border border-slate-200 overflow-hidden relative">
                    <div class="aspect-[3/4] bg-slate-50 relative p-6 cursor-pointer" @click="openModal('Creative Spark', 'creative')">
                        <div class="w-full h-full bg-white shadow-sm flex relative overflow-hidden transition-transform duration-500 group-hover:scale-105">
                            <div class="w-1/3 bg-slate-900 h-full p-2 flex flex-col gap-2">
                                <div class="w-10 h-10 rounded-full bg-white/20 mb-4"></div>
                                <div class="h-2 w-full bg-white/20 rounded"></div>
                                <div class="h-2 w-full bg-white/20 rounded"></div>
                            </div>
                            <div class="w-2/3 p-4 flex flex-col gap-3">
                                <div class="h-6 w-3/4 bg-slate-900 rounded"></div>
                                <div class="h-2 w-full bg-slate-200 rounded"></div>
                                <div class="h-2 w-full bg-slate-200 rounded"></div>
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
                    <div class="p-5 border-t border-slate-100">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-slate-900 text-lg">Creative Spark</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 uppercase">New</span>
                        </div>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2">Bold sidebar design for designers, marketers, and creative professionals.</p>
                        <div class="flex gap-2">
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Creative</span>
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Visual</span>
                        </div>
                    </div>
                </div>

                <!-- Template 4: Tech Minimal (Tech/ATS) -->
                <div v-show="isVisible('tech ats')" key="tech" class="template-card group bg-white rounded-2xl border border-slate-200 overflow-hidden relative">
                    <div class="aspect-[3/4] bg-slate-50 relative p-6 cursor-pointer" @click="openModal('Tech Minimal', 'tech ats')">
                        <div class="w-full h-full bg-white shadow-sm flex flex-col p-4 gap-2 relative overflow-hidden transition-transform duration-500 group-hover:scale-105">
                            <div class="h-6 w-full bg-slate-100 rounded mb-2"></div>
                            <div class="grid grid-cols-3 gap-2 mb-2">
                                <div class="h-2 bg-indigo-100 rounded"></div>
                                <div class="h-2 bg-indigo-100 rounded"></div>
                                <div class="h-2 bg-indigo-100 rounded"></div>
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="h-2 w-full bg-slate-100 rounded"></div>
                                <div class="h-2 w-full bg-slate-100 rounded"></div>
                                <div class="h-2 w-full bg-slate-100 rounded"></div>
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
                    <div class="p-5 border-t border-slate-100">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-slate-900 text-lg">Tech Minimal</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 uppercase">ATS Ready</span>
                        </div>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2">Optimized for developers with a focus on skills, languages, and project links.</p>
                        <div class="flex gap-2">
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Tech</span>
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Minimal</span>
                        </div>
                    </div>
                </div>

                <!-- Template 5: The Graduate (Modern) -->
                <div v-show="isVisible('modern')" key="graduate" class="template-card group bg-white rounded-2xl border border-slate-200 overflow-hidden relative">
                    <div class="aspect-[3/4] bg-slate-50 relative p-6 cursor-pointer" @click="openModal('The Graduate', 'modern')">
                        <div class="w-full h-full bg-white shadow-sm flex flex-col p-5 gap-3 relative overflow-hidden transition-transform duration-500 group-hover:scale-105">
                            <div class="h-12 w-12 bg-indigo-50 rounded-full mx-auto"></div>
                            <div class="h-4 w-1/2 bg-slate-800 mx-auto rounded"></div>
                            <div class="h-px bg-slate-100 my-1"></div>
                            <div class="space-y-2">
                                <div class="h-2 w-full bg-slate-100 rounded"></div>
                                <div class="h-2 w-full bg-slate-100 rounded"></div>
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
                    <div class="p-5 border-t border-slate-100">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-slate-900 text-lg">The Graduate</h3>
                        </div>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2">Clean structure emphasizing education and internships for entry-level applicants.</p>
                        <div class="flex gap-2">
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Entry Level</span>
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Clean</span>
                        </div>
                    </div>
                </div>

                <!-- Template 6: Swiss Grid (Creative) -->
                <div v-show="isVisible('creative')" key="swiss" class="template-card group bg-white rounded-2xl border border-slate-200 overflow-hidden relative">
                    <div class="aspect-[3/4] bg-slate-50 relative p-6 cursor-pointer" @click="openModal('Swiss Grid', 'creative')">
                        <div class="w-full h-full bg-white shadow-sm grid grid-cols-2 p-4 gap-4 relative overflow-hidden transition-transform duration-500 group-hover:scale-105">
                            <div class="col-span-2 h-8 bg-slate-900 rounded-sm"></div>
                            <div class="h-full bg-slate-100 rounded-sm"></div>
                            <div class="h-full bg-slate-100 rounded-sm"></div>
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
                    <div class="p-5 border-t border-slate-100">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-slate-900 text-lg">Swiss Grid</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 uppercase">Popular</span>
                        </div>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2">A bold, grid-based layout that organizes complex information elegantly.</p>
                        <div class="flex gap-2">
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Grid</span>
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Bold</span>
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
        <div v-if="isModalOpen" class="fixed inset-0 z-50">
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
                        <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ selectedTemplate?.name }}</h2>
                        <div class="flex gap-2 mb-6 flex-wrap">
                            <span v-for="tag in getTypeTags(selectedTemplate?.type || '')" :key="tag.text" class="px-2 py-1 rounded text-xs font-bold" :class="tag.class">{{ tag.text }}</span>
                        </div>
                        
                        <div class="space-y-6 mb-8">
                            <div>
                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Best For</h4>
                                <p class="text-sm text-slate-700">General Roles, Entry Level, Students</p>
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

                    <!-- Right: Visual Preview (Placeholder) -->
                    <div class="w-full md:w-2/3 bg-slate-100 p-8 overflow-y-auto flex justify-center">
                         <!-- A CSS-drawn A4 paper representation -->
                         <div class="w-[210mm] min-h-[297mm] bg-white shadow-xl transform scale-[0.5] md:scale-[0.6] lg:scale-[0.7] origin-top p-12 text-slate-800">
                            <!-- Content dynamically adjusted based on template type? For now, static layout simulation -->
                            <div class="border-b-2 border-slate-900 pb-8 mb-8">
                                <h1 class="text-5xl font-bold mb-2">Alex Morgan</h1>
                                <p class="text-2xl text-slate-600">Product Manager</p>
                            </div>
                            <div class="space-y-8">
                                <div class="h-4 w-full bg-slate-100 rounded"></div>
                                <div class="h-4 w-full bg-slate-100 rounded"></div>
                                <div class="h-4 w-3/4 bg-slate-100 rounded"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-8 mt-12">
                                 <div class="h-64 bg-slate-50 rounded"></div>
                                 <div class="h-64 bg-slate-50 rounded"></div>
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
