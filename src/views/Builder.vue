<script setup>
import { ref, onMounted } from 'vue'
import Sortable from 'sortablejs'
import gsap from 'gsap'

const sortableList = ref(null)
const previewContainer = ref(null)

const activeTemplate = ref('modern')
const activeMobileTab = ref('editor') // 'editor' or 'preview'

// Mock Data
const resumeData = ref({
    name: 'Alex Morgan',
    title: 'Senior Product Designer',
    email: 'alex.morgan@example.com',
    phone: '+1 (555) 012-3456',
    location: 'San Francisco, CA',
    linkedin: 'linkedin.com/in/alexm',
    summary: 'Innovative Senior Product Designer with 7+ years of experience in SaaS and Fintech sectors. Proven track record of improving user retention by 40% through data-driven design iterations. Expert in translating complex user needs into intuitive, accessible, and visually compelling digital experiences.',
    experience: [
        {
            role: 'Lead UX Designer',
            company: 'TechFlow Solutions',
            location: 'San Francisco',
            period: '2021 - Present',
            points: [
                'Spearheaded the redesign of the core analytics dashboard, increasing daily active users by 25% within Q1.',
                'Managed a design system used by 40+ developers, reducing UI inconsistency and technical debt by 30%.',
                'Mentored 3 junior designers and facilitated weekly design critiques to foster a culture of excellence.'
            ]
        },
        {
            role: 'Product Designer',
            company: 'Creative Pulse',
            location: 'New York',
            period: '2018 - 2021',
            points: [
                'Conducted extensive user research including 50+ interviews to inform the roadmap for a new e-commerce platform.',
                'Collaborated closely with PMs and Engineers in an Agile environment to deliver high-fidelity prototypes.'
            ]
        }
    ],
    skills: ['Figma', 'Adobe XD', 'Prototyping', 'HTML/CSS', 'User Research', 'Agile Methodology', 'Design Systems']
})

const sections = ref([
    { id: 'personal', title: 'Personal Details', icon: 'fa-regular fa-id-card' },
    { id: 'summary', title: 'Professional Summary', icon: 'fa-solid fa-align-left' },
    { id: 'experience', title: 'Experience', count: '2 Items', icon: 'fa-solid fa-briefcase' },
    { id: 'education', title: 'Education', icon: 'fa-solid fa-graduation-cap' }
])

const switchTemplate = (type) => {
    activeTemplate.value = type
    
    // Flash effect
    gsap.fromTo(previewContainer.value, 
        { opacity: 0.8, scale: 0.99 },
        { opacity: 1, scale: 1, duration: 0.3 }
    )
}

onMounted(() => {
    if (sortableList.value) {
        new Sortable(sortableList.value, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag'
        })
    }
})
</script>

<template>
  <div class="flex flex-col h-screen pt-20 overflow-hidden bg-slate-100">
    
    <!-- MOBILE TABS (Visible only on lg screens and below) -->
    <div class="lg:hidden flex bg-white border-b border-slate-200 px-4 py-2 gap-2 z-30 shrink-0">
        <button 
            @click="activeMobileTab = 'editor'" 
            class="flex-1 py-2 rounded-lg text-sm font-bold transition-all"
            :class="activeMobileTab === 'editor' ? 'bg-indigo-50 text-indigo-600 ring-2 ring-indigo-500/20' : 'text-slate-500 hover:bg-slate-50'"
        >
            <i class="fa-solid fa-pen-to-square mr-2"></i> Editor
        </button>
        <button 
            @click="activeMobileTab = 'preview'" 
            class="flex-1 py-2 rounded-lg text-sm font-bold transition-all"
            :class="activeMobileTab === 'preview' ? 'bg-indigo-50 text-indigo-600 ring-2 ring-indigo-500/20' : 'text-slate-500 hover:bg-slate-50'"
        >
            <i class="fa-regular fa-eye mr-2"></i> Preview
        </button>
    </div>

    <!-- MAIN WORKSPACE (Split View) -->
    <main class="flex-1 flex overflow-hidden relative">
        
        <!-- LEFT PANEL: Editor & Controls -->
        <!-- Logic: On mobile, show only if tab is 'editor', on Desktop always show -->
        <div 
            class="w-full lg:w-5/12 xl:w-4/12 flex flex-col bg-white border-r border-slate-200 z-20 shadow-xl overflow-hidden transition-transform duration-300 absolute inset-0 lg:relative lg:translate-x-0"
            :class="activeMobileTab === 'editor' ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            
            <!-- Editor Scroll Area -->
            <div class="flex-1 overflow-y-auto custom-scroll p-6 lg:p-8 pb-32">
                
                <!-- 2. HERO SECTION -->
                <div class="mb-10 animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-bold mb-4 border border-indigo-100">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                        </span>
                        AI Engine Ready
                    </div>
                    <h1 class="text-3xl font-display font-bold text-slate-900 mb-2">Build your legacy.</h1>
                    <p class="text-slate-500 text-sm leading-relaxed">Upload your existing resume to extract data, or start fresh. Our AI will guide you to an ATS-optimized result.</p>
                </div>

                <!-- 3. RESUME INPUT SECTION -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8 animate-fade-in" style="animation-delay: 0.3s;">
                    <!-- Upload Card -->
                    <button class="group p-5 border border-slate-200 rounded-2xl hover:border-indigo-500 hover:bg-indigo-50/10 transition-all text-left relative overflow-hidden action-button bg-slate-50 hover:shadow-md">
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 group-hover:border-indigo-200 group-hover:text-indigo-600 flex items-center justify-center text-slate-500 mb-4 transition-colors shadow-sm">
                            <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1">Upload Resume</h3>
                        <p class="text-xs text-slate-500">Auto-fill from PDF/DOCX</p>
                    </button>

                    <!-- Start Scratch Card -->
                    <button class="group p-5 border border-slate-200 rounded-2xl hover:border-indigo-500 hover:bg-indigo-50/10 transition-all text-left action-button bg-white hover:shadow-md">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 group-hover:bg-indigo-100 group-hover:text-indigo-600 flex items-center justify-center text-slate-500 mb-4 transition-colors shadow-sm">
                            <i class="fa-solid fa-pen-to-square text-lg"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm mb-1">Start Fresh</h3>
                        <p class="text-xs text-slate-500">Build from scratch</p>
                    </button>
                </div>

                <!-- 4. JOB DESCRIPTION INPUT -->
                <div class="mb-10 bg-gradient-to-br from-slate-50 to-white p-6 rounded-2xl border border-slate-200 animate-fade-in shadow-sm" style="animation-delay: 0.4s;">
                    <div class="flex justify-between items-center mb-4">
                        <label class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <div class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-briefcase"></i>
                            </div>
                            Target Job Description
                        </label>
                        <button class="text-xs text-indigo-600 font-bold hover:underline bg-indigo-50 px-2 py-1 rounded cursor-pointer">
                            <i class="fa-solid fa-plus mr-1"></i> Paste
                        </button>
                    </div>
                    <textarea class="w-full h-32 p-4 text-sm rounded-xl border-slate-200 border bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none resize-none transition-all placeholder:text-slate-400 shadow-inner" placeholder="Paste the job listing here. We'll analyze keywords..."></textarea>
                    <p class="text-xs text-slate-500 mt-3 flex items-center gap-1.5 font-medium">
                        <i class="fa-solid fa-circle-info text-indigo-500"></i> Helps increase match score by ~40%
                    </p>
                </div>

                <!-- 5. AI CONTROLS PANEL -->
                <div class="mb-10 animate-fade-in" style="animation-delay: 0.5s;">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-xs font-extra-bold text-slate-400 uppercase tracking-widest">AI Optimization</h2>
                        <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">BETA</span>
                    </div>
                    
                    <div class="bg-white border border-slate-200 rounded-2xl p-2 shadow-sm">
                        <!-- Tone Selector -->
                        <div class="border-b border-slate-100 p-2">
                            <label class="text-[10px] font-bold text-slate-400 mb-1.5 block uppercase tracking-wider pl-1">Tone</label>
                            <div class="relative">
                                <select class="w-full py-2 pl-3 pr-8 bg-slate-50 hover:bg-slate-100 border-none rounded-lg text-sm font-bold text-slate-700 appearance-none focus:ring-0 cursor-pointer transition-colors">
                                    <option>Professional & Balanced</option>
                                    <option>Confident & Bold</option>
                                    <option>Technical & Precise</option>
                                    <option>Leadership & Executive</option>
                                </select>
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Toggles Grid -->
                        <div class="grid grid-cols-2 gap-2 p-2">
                             <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition-colors">
                                <input type="checkbox" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                <span class="text-xs font-bold text-slate-700">ATS Fix</span>
                             </label>
                             <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition-colors">
                                <input type="checkbox" checked class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                <span class="text-xs font-bold text-slate-700">Keywords</span>
                             </label>
                        </div>

                        <div class="p-2">
                            <button class="w-full py-3.5 rounded-xl bg-gradient-to-r from-slate-900 to-indigo-900 text-white font-bold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 action-button group overflow-hidden relative">
                                <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                                <i class="fa-solid fa-wand-magic-sparkles text-indigo-300"></i> Optimize Resume
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 7. EDITOR LIST -->
                <div class="animate-fade-in" style="animation-delay: 0.6s;">
                     <div class="flex items-center justify-between mb-5">
                        <h2 class="text-xs font-extra-bold text-slate-400 uppercase tracking-widest">Content Sections</h2>
                    </div>
                    
                    <div class="space-y-3">
                         <!-- Drag & Drop Sections List -->
                         <div ref="sortableList" id="sortable-sections" class="space-y-3">
                            <div v-for="section in sections" :key="section.id" class="group bg-white border border-slate-200 rounded-xl p-4 flex items-center justify-between hover:shadow-md hover:border-indigo-300 transition-all cursor-move active:cursor-grabbing">
                                <div class="flex items-center gap-4">
                                    <div class="w-6 flex justify-center text-slate-300 group-hover:text-slate-400 drag-handle">
                                        <i class="fa-solid fa-grip-vertical"></i>
                                    </div>
                                    <div class="flex-1">
                                         <span class="block font-bold text-slate-800 text-sm">{{ section.title }}</span>
                                         <span v-if="section.count" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ section.count }}</span>
                                    </div>
                                </div>
                                <button class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 flex items-center justify-center transition-colors">
                                    <i :class="section.id !== 'personal' ? 'fa-solid fa-pen' : 'fa-solid fa-chevron-right'"></i>
                                </button>
                            </div>
                         </div>

                         <button class="w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 text-sm font-bold hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all flex items-center justify-center gap-2 group">
                             <div class="w-6 h-6 rounded-full bg-slate-200 group-hover:bg-indigo-200 flex items-center justify-center text-xs text-slate-600 group-hover:text-indigo-700 transition-colors">
                                 <i class="fa-solid fa-plus"></i>
                             </div>
                             Add New Section
                         </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Preview & Output -->
        <!-- Logic: On mobile, show only if tab is 'preview', on Desktop always show -->
         <div 
            class="flex-1 bg-slate-100/50 relative flex flex-col h-full overflow-hidden transition-transform duration-300 absolute inset-0 lg:relative lg:translate-x-0 bg-pattern"
            :class="activeMobileTab === 'preview' ? 'translate-x-0 z-30' : 'translate-x-[100%] lg:translate-x-0'"
         >
            
            <!-- 6. TOOLBAR -->
            <div class="h-16 lg:h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 px-4 lg:px-6 flex items-center justify-between shrink-0 sticky top-0 z-40">
                <div class="flex items-center gap-3 overflow-x-auto no-scrollbar mask-gradient py-2">
                    <!-- Template Thumbnails -->
                    <button @click="switchTemplate('modern')" class="w-10 h-14 lg:w-12 lg:h-16 bg-slate-50 rounded border-2 relative shrink-0 transition-all hover:scale-105 active:scale-95 shadow-sm" :class="activeTemplate === 'modern' ? 'border-indigo-600 ring-2 ring-indigo-500/20' : 'border-slate-200 opacity-70 hover:opacity-100'" title="Modern">
                        <div class="absolute inset-0 flex flex-col p-1 gap-0.5 opacity-50">
                            <div class="w-full h-1.5 bg-slate-700"></div><div class="w-full h-full bg-white"></div>
                        </div>
                    </button>
                    <button @click="switchTemplate('professional')" class="w-10 h-14 lg:w-12 lg:h-16 bg-white rounded border relative shrink-0 transition-all hover:scale-105 active:scale-95 shadow-sm" :class="activeTemplate === 'professional' ? 'border-indigo-600 ring-2 ring-indigo-500/20' : 'border-slate-200 opacity-70 hover:opacity-100'" title="Professional">
                        <div class="absolute inset-0 flex flex-col p-1 gap-0.5 opacity-30">
                            <div class="flex gap-0.5"><div class="w-1/3 h-full bg-slate-300"></div><div class="w-2/3 h-full bg-slate-200"></div></div>
                        </div>
                    </button>
                     <button @click="switchTemplate('creative')" class="w-10 h-14 lg:w-12 lg:h-16 bg-white rounded border relative shrink-0 transition-all hover:scale-105 active:scale-95 shadow-sm" :class="activeTemplate === 'creative' ? 'border-indigo-600 ring-2 ring-indigo-500/20' : 'border-slate-200 opacity-70 hover:opacity-100'" title="Creative">
                        <div class="absolute inset-0 flex flex-col p-1 gap-0.5 opacity-30">
                            <div class="w-full h-full bg-slate-100 border-l-2 border-slate-800"></div>
                        </div>
                    </button>
                    <button @click="switchTemplate('technical')" class="w-10 h-14 lg:w-12 lg:h-16 bg-white rounded border relative shrink-0 transition-all hover:scale-105 active:scale-95 shadow-sm" :class="activeTemplate === 'technical' ? 'border-indigo-600 ring-2 ring-indigo-500/20' : 'border-slate-200 opacity-70 hover:opacity-100'" title="Technical">
                        <div class="absolute inset-0 flex flex-col p-1 gap-0.5 opacity-30">
                           <div class="w-full h-1 bg-slate-800 mb-0.5"></div><div class="grid grid-cols-2 gap-0.5 h-full"><div class="bg-slate-200"></div><div class="bg-slate-200"></div></div>
                        </div>
                    </button>
                </div>
                
                <div class="flex items-center gap-2">
                    <button class="w-9 h-9 text-xs rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 flex items-center justify-center transition-all shadow-sm" title="Zoom In">
                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                    </button>
                    <button class="w-9 h-9 text-xs rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 flex items-center justify-center transition-all shadow-sm" title="Fit to Screen">
                        <i class="fa-solid fa-compress"></i>
                    </button>
                </div>
            </div>

            <!-- 7. LIVE RESUME PREVIEW AREA -->
            <div class="flex-1 overflow-auto custom-scroll p-4 lg:p-12 flex justify-center relative">
                
                <!-- A4 PAPER -->
                <div ref="previewContainer" class="a4-paper p-[15mm] md:p-[20mm] text-slate-800 animate-pop shadow-2xl relative z-10 origin-top transform scale-95 lg:scale-100 mb-20">
                    <!-- Standard Template Layout -->
                    <header class="border-b-2 border-slate-900 pb-6 mb-8 flex justify-between items-start group relative hover:bg-blue-50/50 transition-colors rounded p-4 -m-4 cursor-pointer">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold uppercase tracking-tight text-slate-900 mb-2">{{ resumeData.name }}</h1>
                            <p class="text-lg font-medium text-indigo-600 tracking-wide">{{ resumeData.title }}</p>
                        </div>
                        <div class="text-right text-sm text-slate-600 space-y-1">
                            <p>{{ resumeData.email }}</p>
                            <p>{{ resumeData.phone }}</p>
                            <p>{{ resumeData.location }}</p>
                            <p class="text-indigo-600 font-medium">{{ resumeData.linkedin }}</p>
                        </div>
                        
                        <!-- Hover Edit Icon -->
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity bg-white shadow-sm border border-slate-200 p-1.5 rounded text-indigo-600">
                            <i class="fa-solid fa-pencil"></i>
                        </div>
                    </header>

                    <section class="mb-8 group relative hover:bg-blue-50/50 transition-colors rounded p-4 -m-4 cursor-pointer">
                        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-4 border-b border-slate-200 pb-2">Professional Summary</h2>
                        <p class="text-sm leading-relaxed text-slate-700">
                            {{ resumeData.summary }}
                        </p>
                    </section>

                    <section class="mb-8 group relative hover:bg-blue-50/50 transition-colors rounded p-4 -m-4 cursor-pointer">
                        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-4 border-b border-slate-200 pb-2">Experience</h2>
                        
                        <div v-for="(job, index) in resumeData.experience" :key="index" class="mb-5 last:mb-0">
                            <div class="flex justify-between items-baseline mb-1">
                                <h3 class="font-bold text-slate-900 text-base">{{ job.role }}</h3>
                                <span class="text-sm text-slate-500 font-medium">{{ job.period }}</span>
                            </div>
                            <div class="text-sm text-indigo-600 font-medium mb-2">{{ job.company }} • {{ job.location }}</div>
                            <ul class="list-disc list-outside ml-4 text-sm text-slate-700 space-y-1.5">
                                <li v-for="(point, i) in job.points" :key="i">{{ point }}</li>
                            </ul>
                        </div>
                    </section>

                    <section class="group relative hover:bg-blue-50/50 transition-colors rounded p-4 -m-4 cursor-pointer">
                        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-4 border-b border-slate-200 pb-2">Skills</h2>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="skill in resumeData.skills" :key="skill" class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-md border border-slate-200">{{ skill }}</span>
                        </div>
                    </section>
                </div>
                
            </div>

            <!-- 8. FOOTER ACTION BAR (Floating on Mobile) -->
            <div class="absolute bottom-4 left-4 right-4 lg:bottom-6 lg:left-8 lg:right-8 z-50">
                <div class="bg-slate-900 text-white rounded-2xl shadow-xl shadow-slate-900/20 p-3 lg:p-4 flex flex-row items-center justify-between gap-4 backdrop-blur-md bg-opacity-95 border border-slate-700/50">
                    
                    <!-- Left: ATS Score -->
                    <div class="flex items-center gap-3">
                        <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                            <svg class="w-full h-full transform -rotate-90">
                                <circle cx="20" cy="20" r="16" stroke="#334155" stroke-width="3" fill="none" />
                                <circle cx="20" cy="20" r="16" stroke="#4ade80" stroke-width="3" fill="none" stroke-dasharray="100.5" stroke-dashoffset="24" stroke-linecap="round" />
                            </svg>
                            <span class="absolute text-[10px] font-bold text-green-400">76</span>
                        </div>
                        <div class="hidden sm:block">
                            <div class="text-xs font-bold text-white">ATS Score: Good</div>
                            <div class="text-[10px] text-slate-400">Missing 3 key terms</div>
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center gap-2">
                        <button class="px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors text-xs font-bold hidden sm:block">
                            Save Draft
                        </button>
                        <button class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 hover:bg-slate-700 text-white flex items-center justify-center transition-all hover:scale-105 active:scale-95" title="Download Word">
                            <i class="fa-regular fa-file-word"></i>
                        </button>
                        <button class="px-4 py-2 lg:px-6 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs lg:text-sm font-bold shadow-lg shadow-indigo-500/30 transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                            <span class="hidden sm:inline">Download</span> <i class="fa-solid fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
  </div>
</template>

<style scoped>
/* Custom Scrollbar */
.custom-scroll::-webkit-scrollbar { width: 5px; }
.custom-scroll::-webkit-scrollbar-track { background: transparent; }
.custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

.bg-pattern {
    background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
    background-size: 20px 20px;
}

/* A4 Paper Preview */
.a4-paper {
    width: 210mm;
    min-height: 297mm;
    background: white;
    /* Box shadow optimized for A4 look */
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

@media (max-width: 640px) {
    .a4-paper {
        width: 100%;
        min-height: auto;
        aspect-ratio: 210/297;
    }
}

/* Animations using Tailwind utility classes where possible, custom where complex */
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
    opacity: 0;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-pop {
    animation: pop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes pop {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>
