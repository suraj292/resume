<script setup>
import { ref, computed } from 'vue'

const activeTab = ref('upload')
const isAnalyzing = ref(false)
const showResults = ref(false)
const score = ref(0)
const fileName = ref('')

const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        fileName.value = file.name
    }
}

const startAnalysis = () => {
    isAnalyzing.value = true
    window.scrollTo({ top: 0, behavior: 'smooth' })
    
    // Simulate Analysis
    setTimeout(() => {
        isAnalyzing.value = false
        showResults.value = true
        animateScore(76)
    }, 3000)
}

const animateScore = (target) => {
    let current = 0
    const interval = setInterval(() => {
        if (current >= target) {
            clearInterval(interval)
            score.value = target
        } else {
            current++
            score.value = current
        }
    }, 20)
}

const scoreCircleDashoffset = computed(() => {
    // 2 * PI * 42 (radius) approx 264
    const circumference = 264
    return circumference - (score.value / 100) * circumference
})

const resetScan = () => {
    showResults.value = false
    score.value = 0
    fileName.value = ''
    activeTab.value = 'upload'
}
</script>

<template>
  <div class="flex-grow">
    
        <!-- 2. HERO SECTION -->
        <section v-if="!showResults && !isAnalyzing" class="pt-16 pb-12 text-center px-6 max-w-4xl mx-auto animate-slide-up">
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

        <!-- 3. RESUME INPUT SECTION -->
        <section v-if="!showResults && !isAnalyzing" class="px-6 pb-20 transition-all duration-500 animate-slide-up" style="animation-delay: 0.2s;">
            <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden transform hover:scale-[1.01] transition-transform duration-500">
                
                <!-- Tabs Header -->
                <div class="flex border-b border-slate-100 bg-slate-50/50">
                    <button class="flex-1 py-4 text-sm transition-colors flex items-center justify-center gap-2" 
                        :class="activeTab === 'upload' ? 'font-bold text-indigo-600 border-b-2 border-indigo-600 bg-white' : 'font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                        @click="activeTab = 'upload'">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload Resume
                    </button>
                    <button class="flex-1 py-4 text-sm transition-colors flex items-center justify-center gap-2" 
                        :class="activeTab === 'paste' ? 'font-bold text-indigo-600 border-b-2 border-indigo-600 bg-white' : 'font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                        @click="activeTab = 'paste'">
                        <i class="fa-solid fa-paste"></i> Paste Text
                    </button>
                </div>

                <div class="p-8">
                    <!-- Tab A: Upload -->
                    <div v-if="activeTab === 'upload'">
                        <div class="border-2 border-dashed border-slate-300 rounded-xl h-64 flex flex-col items-center justify-center bg-slate-50/50 hover:bg-indigo-50/20 hover:border-indigo-400 transition-all cursor-pointer group relative">
                            <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleFileUpload">
                            
                            <!-- Default State -->
                            <div v-if="!fileName" class="text-center group-hover:-translate-y-1 transition-transform duration-300">
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
                                <h3 class="text-lg font-bold text-slate-800">{{ fileName }}</h3>
                                <p class="text-green-600 text-sm mt-1 font-medium">Ready for analysis</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab B: Paste -->
                    <div v-else>
                        <textarea class="w-full h-64 p-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none resize-none text-sm leading-relaxed" placeholder="Copy and paste your resume content here..."></textarea>
                    </div>

                    <!-- 4. ANALYZE BUTTON -->
                    <div class="mt-8">
                        <button @click="startAnalysis" class="w-full py-4 rounded-xl bg-gradient-to-r from-slate-900 to-slate-800 hover:from-slate-800 hover:to-slate-700 text-white font-bold text-lg shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-3 relative overflow-hidden group">
                            <span class="relative z-10 group-hover:hidden">Analyze Resume</span>
                            <span class="relative z-10 hidden group-hover:inline">Start Free Scan <i class="fa-solid fa-arrow-right ml-1"></i></span>
                            <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- LOADING STATE (Overlay) -->
        <div v-if="isAnalyzing" class="fixed inset-0 bg-white/95 backdrop-blur-sm z-50 flex flex-col items-center justify-center">
            <div class="relative w-64 h-80 border-2 border-slate-200 rounded-lg bg-white shadow-xl overflow-hidden mb-8">
                <!-- Mock Resume Lines -->
                <div class="p-6 space-y-4">
                    <div class="w-20 h-20 bg-slate-100 rounded-full mb-4"></div>
                    <div class="w-3/4 h-4 bg-slate-200 rounded"></div>
                    <div class="w-1/2 h-4 bg-slate-200 rounded"></div>
                    <div class="space-y-2 pt-4">
                        <div class="w-full h-2 bg-slate-100 rounded"></div>
                        <div class="w-full h-2 bg-slate-100 rounded"></div>
                        <div class="w-full h-2 bg-slate-100 rounded"></div>
                    </div>
                </div>
                <!-- Scanning Line -->
                <div class="absolute top-0 left-0 w-full h-1 bg-indigo-500 shadow-[0_0_15px_rgba(99,102,241,0.5)] animate-scan-line"></div>
            </div>

            <h2 class="text-2xl font-display font-bold text-slate-900 mb-2">Scanning Your Resume...</h2>
            <div class="text-slate-500 text-sm font-medium flex flex-col items-center gap-1 h-6 overflow-hidden">
                <span class="animate-pulse">Analyzing keyword density</span>
            </div>
        </div>

        <!-- 5-10. RESULTS DASHBOARD -->
        <section v-if="showResults" class="container mx-auto px-6 pb-24 pt-12">
            
            <div class="flex items-center justify-between mb-8 animate-slide-up">
                <h2 class="text-2xl font-bold text-slate-900">Analysis Report</h2>
                <button @click="resetScan" class="text-sm font-medium text-slate-500 hover:text-slate-800 flex items-center gap-2 hover:rotate-180 transition-transform duration-500">
                    <i class="fa-solid fa-rotate-right"></i> New Scan
                </button>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                
                <!-- Left Column: Score & Key Stats -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- 5. ATS SCORE -->
                    <div class="bg-white rounded-2xl p-8 card-shadow text-center border border-slate-100 animate-slide-up" style="animation-delay: 0.1s;">
                        <h3 class="text-slate-500 font-bold text-xs uppercase tracking-wider mb-6">Overall ATS Score</h3>
                        
                        <div class="relative w-48 h-48 mx-auto mb-6">
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <circle class="text-slate-100" stroke-width="8" stroke="currentColor" fill="transparent" r="42" cx="50" cy="50" />
                                <circle class="progress-ring__circle text-indigo-600 transition-all duration-1000 ease-out" 
                                    stroke-width="8" stroke-linecap="round" stroke="currentColor" fill="transparent" r="42" cx="50" cy="50" stroke-dasharray="264" 
                                    :stroke-dashoffset="scoreCircleDashoffset" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-5xl font-display font-bold text-slate-900">{{ score }}</span>
                                <span class="text-sm font-medium text-slate-400">/100</span>
                            </div>
                        </div>

                        <div class="inline-block px-4 py-1 rounded-full bg-yellow-100 text-yellow-700 font-bold text-sm mb-4">
                            Average Match
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Your resume is readable but misses key technical terms found in similar job descriptions.
                        </p>
                    </div>

                    <!-- 9. READABILITY -->
                    <div class="bg-white rounded-2xl p-6 card-shadow border border-slate-100 animate-slide-up" style="animation-delay: 0.2s;">
                        <h3 class="text-slate-800 font-bold text-lg mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-glasses text-indigo-500"></i> Readability
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-slate-600">Flesch-Kincaid Score</span>
                                    <span class="font-bold text-slate-900">65 (Standard)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full w-[65%] animate-progress"></div>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                                <i class="fa-solid fa-ruler-combined text-slate-400 mt-1"></i>
                                <div>
                                    <span class="block text-sm font-bold text-slate-800">Length: 650 words</span>
                                    <span class="text-xs text-slate-500">Ideal range (400-800 words) for concise impact.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Detailed Breakdown -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- 6. KEYWORD MATCH -->
                    <div class="bg-white rounded-2xl p-8 card-shadow border border-slate-100 animate-slide-up" style="animation-delay: 0.3s;">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-tags text-indigo-500"></i> Keyword Analysis
                            </h3>
                            <span class="text-indigo-600 font-bold text-lg">68% Match</span>
                        </div>

                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Found -->
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase mb-3">Found Keywords</h4>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-md border border-green-100 flex items-center gap-1 hover:scale-105 transition-transform cursor-default"><i class="fa-solid fa-check"></i> Project Management</span>
                                    <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-md border border-green-100 flex items-center gap-1 hover:scale-105 transition-transform cursor-default"><i class="fa-solid fa-check"></i> SQL</span>
                                    <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-md border border-green-100 flex items-center gap-1 hover:scale-105 transition-transform cursor-default"><i class="fa-solid fa-check"></i> Team Leadership</span>
                                    <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-md border border-green-100 flex items-center gap-1 hover:scale-105 transition-transform cursor-default"><i class="fa-solid fa-check"></i> Agile</span>
                                </div>
                            </div>

                            <!-- Missing -->
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase mb-3">Missing Critical Keywords</h4>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2.5 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-md border border-red-100 flex items-center gap-1 hover:scale-105 transition-transform cursor-default"><i class="fa-solid fa-xmark"></i> Python</span>
                                    <span class="px-2.5 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-md border border-red-100 flex items-center gap-1 hover:scale-105 transition-transform cursor-default"><i class="fa-solid fa-xmark"></i> Data Analysis</span>
                                    <span class="px-2.5 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-md border border-red-100 flex items-center gap-1 hover:scale-105 transition-transform cursor-default"><i class="fa-solid fa-xmark"></i> AWS</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-3 italic">Tip: Try incorporating these skills into your "Experience" section.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- 7. FORMATTING CHECK -->
                        <div class="bg-white rounded-2xl p-6 card-shadow border border-slate-100 animate-slide-up" style="animation-delay: 0.4s;">
                            <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-file-lines text-indigo-500"></i> Formatting
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-center justify-between text-sm">
                                    <span class="text-slate-600">File Type (PDF)</span>
                                    <span class="text-green-600 font-bold flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> Pass</span>
                                </li>
                                <li class="flex items-center justify-between text-sm">
                                    <span class="text-slate-600">Section Headings</span>
                                    <span class="text-green-600 font-bold flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> Clear</span>
                                </li>
                                <li class="flex items-center justify-between text-sm">
                                    <span class="text-slate-600">Date Formats</span>
                                    <span class="text-yellow-600 font-bold flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> Inconsistent</span>
                                </li>
                                <li class="flex items-center justify-between text-sm">
                                    <span class="text-slate-600">Bullet Points</span>
                                    <span class="text-green-600 font-bold flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> Good</span>
                                </li>
                            </ul>
                        </div>

                        <!-- 8. CONTENT QUALITY -->
                        <div class="bg-white rounded-2xl p-6 card-shadow border border-slate-100 animate-slide-up" style="animation-delay: 0.5s;">
                            <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-pen-nib text-indigo-500"></i> Quality
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-xs font-bold text-slate-500 mb-1">
                                        <span>Action Verbs</span>
                                        <span>Strong</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                                        <div class="bg-indigo-500 h-1.5 rounded-full w-[80%] animate-progress"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-xs font-bold text-slate-500 mb-1">
                                        <span>Quantified Results</span>
                                        <span class="text-red-500">Weak</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                                        <div class="bg-red-400 h-1.5 rounded-full w-[30%] animate-progress"></div>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1">Add more numbers (%, $)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 10. CRITICAL ISSUES -->
                    <div class="bg-red-50 border border-red-100 rounded-2xl p-6 animate-slide-up" style="animation-delay: 0.6s;">
                        <h3 class="font-bold text-red-700 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> 2 Critical Issues Found
                        </h3>
                        <div class="space-y-3">
                            <div class="bg-white p-3 rounded-lg border border-red-100 shadow-sm flex items-start gap-3">
                                <i class="fa-solid fa-image text-red-400 mt-1"></i>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Image Detected</h4>
                                    <p class="text-xs text-slate-600">ATS cannot read text inside images. Remove the headshot or header graphic.</p>
                                </div>
                            </div>
                            <div class="bg-white p-3 rounded-lg border border-red-100 shadow-sm flex items-start gap-3">
                                <i class="fa-solid fa-table text-red-400 mt-1"></i>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Complex Tables</h4>
                                    <p class="text-xs text-slate-600">Nested tables in 'Education' may confuse the parser. Use standard columns.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 11-12. FOOTER ACTIONS & RECOMMENDATIONS -->
            <div class="mt-8 bg-slate-900 rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between gap-6 text-white shadow-2xl animate-slide-up" style="animation-delay: 0.7s;">
                <div class="flex-1">
                    <h3 class="font-bold text-xl mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-indigo-400"></i> AI Recommendation
                    </h3>
                    <p class="text-slate-300 text-sm">
                        "Your resume is strong on leadership but lacks hard technical skills for this role. Add the missing keywords and quantify your sales achievements to boost your score to 85+."
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-bold text-sm shadow-lg hover:shadow-indigo-500/30 transition-all hover:scale-105 active:scale-95">
                        Optimize with AI
                    </button>
                    <button class="px-5 py-3 rounded-xl bg-slate-800 border border-slate-700 hover:bg-slate-700 font-medium text-sm transition-all hover:scale-105 active:scale-95">
                        Download Report
                    </button>
                </div>
            </div>

        </section>
  </div>
</template>

<style scoped>
.card-shadow {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.card-shadow:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.01);
    transform: translateY(-2px);
}

/* SVG Circle Progress */
.progress-ring__circle {
    transition: stroke-dashoffset 0.1s linear; /* Smooth fill during animation */
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
}

.animate-scan-line {
    animation: scan 2s linear infinite;
}

@keyframes scan {
    0% { top: 0%; }
    50% { top: 100%; }
    100% { top: 0%; }
}

.animate-pulse-slow {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
