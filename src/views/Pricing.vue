<script setup>
import { ref } from 'vue'

const isYearly = ref(false)

const toggleBilling = () => {
    isYearly.value = !isYearly.value
}
</script>

<template>
  <div class="flex-grow">
        
        <!-- 2. HERO PRICING INTRO -->
        <section class="pt-20 pb-16 text-center px-6">
            <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 mb-6 animate-fade-in-up">
                Simple, transparent <span class="text-indigo-600">pricing</span>
            </h1>
            <p class="text-slate-500 text-lg mb-10 max-w-xl mx-auto animate-fade-in-up" style="animation-delay: 0.1s;">
                Choose the plan that fits your career goals. No hidden fees, cancel anytime.
            </p>

            <!-- Toggle Switch -->
            <div class="flex items-center justify-center gap-4 mb-16 animate-fade-in-up" style="animation-delay: 0.2s;">
                <span class="text-sm font-medium text-slate-600" id="monthly-label">Monthly</span>
                
                <div class="relative inline-block w-14 align-middle select-none transition duration-200 ease-in">
                    <input type="checkbox" name="toggle" id="billing-toggle" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 border-slate-200 appearance-none cursor-pointer transition-all duration-300 left-0" :checked="isYearly" @change="toggleBilling"/>
                    <label for="billing-toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-slate-200 cursor-pointer transition-colors duration-300"></label>
                </div>
                
                <span class="text-sm font-medium text-slate-900">Yearly</span>
                <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full border border-green-200 -ml-2 animate-bounce">Save 20%</span>
            </div>

            <!-- 3. PRICING CARDS -->
            <div class="container mx-auto max-w-6xl px-4">
                <div class="grid md:grid-cols-3 gap-8">
                    
                    <!-- Free Plan -->
                    <div class="bg-white rounded-2xl p-8 border border-slate-200 pricing-card flex flex-col animate-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="mb-4 text-left">
                            <h3 class="text-xl font-bold text-slate-900">Free</h3>
                            <p class="text-sm text-slate-500 mt-1">For getting started</p>
                        </div>
                        <div class="mb-6 text-left">
                            <span class="text-4xl font-bold text-slate-900">₹0</span>
                            <span class="text-slate-400">/forever</span>
                        </div>
                        <router-link to="/builder" class="block w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-center rounded-xl transition-colors mb-8 transform hover:scale-105 active:scale-95">
                            Start Free
                        </router-link>
                        <ul class="space-y-4 text-sm text-slate-600 flex-grow text-left">
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> 1 Resume</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> Limited Templates</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> Basic ATS Score</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> Resume Upload</li>
                            <li class="flex items-center gap-3 text-slate-400"><i class="fa-solid fa-xmark"></i> AI Optimization</li>
                            <li class="flex items-center gap-3 text-slate-400"><i class="fa-solid fa-xmark"></i> PDF Download</li>
                        </ul>
                    </div>

                    <!-- Pro Plan (Popular) -->
                    <div class="bg-slate-900 rounded-2xl p-8 border-2 border-indigo-500 pricing-card flex flex-col relative transform md:-translate-y-4 shadow-2xl animate-fade-in-up" style="animation-delay: 0.4s;">
                        <div class="absolute top-0 right-0 bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-bl-xl rounded-tr-lg animate-pulse">MOST POPULAR</div>
                        <div class="mb-4 text-left">
                            <h3 class="text-xl font-bold text-white">Pro</h3>
                            <p class="text-sm text-indigo-200 mt-1">Best for job seekers</p>
                        </div>
                        <div class="mb-6 text-left">
                            <div class="price-container">
                                <transition name="fade" mode="out-in">
                                  <span :key="isYearly" class="text-4xl font-bold text-white price-text">{{ isYearly ? '₹4,999' : '₹499' }}</span>
                                </transition>
                                <span class="text-slate-400 period-text">{{ isYearly ? '/year' : '/month' }}</span>
                            </div>
                        </div>
                        <a href="#" class="block w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-center rounded-xl transition-colors mb-8 shadow-lg shadow-indigo-500/25 transform hover:scale-105 active:scale-95">
                            Upgrade to Pro
                        </a>
                        <ul class="space-y-4 text-sm text-slate-300 flex-grow text-left">
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-400"></i> Unlimited Resumes</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-400"></i> All Premium Templates</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-400"></i> AI Resume Optimization</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-400"></i> ATS Keyword Matching</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-400"></i> PDF & DOCX Downloads</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-400"></i> Priority Support</li>
                        </ul>
                    </div>

                    <!-- Premium Plan -->
                    <div class="bg-white rounded-2xl p-8 border border-slate-200 pricing-card flex flex-col animate-fade-in-up" style="animation-delay: 0.5s;">
                        <div class="mb-4 text-left">
                            <h3 class="text-xl font-bold text-slate-900">Career+</h3>
                            <p class="text-sm text-slate-500 mt-1">For serious professionals</p>
                        </div>
                        <div class="mb-6 text-left">
                            <div class="price-container">
                                <transition name="fade" mode="out-in">
                                  <span :key="isYearly" class="text-4xl font-bold text-slate-900 price-text">{{ isYearly ? '₹9,999' : '₹999' }}</span>
                                </transition>
                                <span class="text-slate-400 period-text">{{ isYearly ? '/year' : '/month' }}</span>
                            </div>
                        </div>
                        <a href="#" class="block w-full py-3 px-4 bg-white border-2 border-slate-900 hover:bg-slate-50 text-slate-900 font-bold text-center rounded-xl transition-colors mb-8 transform hover:scale-105 active:scale-95">
                            Go Premium
                        </a>
                        <ul class="space-y-4 text-sm text-slate-600 flex-grow text-left">
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-600"></i> <strong>Everything in Pro</strong></li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-600"></i> Advanced ATS Analysis</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-600"></i> Job Description Matcher</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-600"></i> Cover Letter Generator</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-600"></i> Personal Branding Themes</li>
                            <li class="flex items-center gap-3"><i class="fa-solid fa-check text-indigo-600"></i> Early Access Features</li>
                        </ul>
                    </div>

                </div>
            </div>
        </section>

        <!-- 4. FEATURE COMPARISON TABLE -->
        <section class="py-16 bg-slate-50 border-t border-slate-200">
            <div class="container mx-auto px-6 max-w-4xl">
                <h2 class="text-3xl font-display font-bold text-center text-slate-900 mb-12">Detailed Comparison</h2>
                
                <div class="overflow-x-auto bg-white rounded-2xl shadow-sm border border-slate-200 animate-fade-in-up" style="animation-delay: 0.6s;">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="p-4 pl-8 font-semibold text-slate-600">Features</th>
                                <th class="p-4 text-center font-bold text-slate-700">Free</th>
                                <th class="p-4 text-center font-bold text-indigo-600 bg-indigo-50/50">Pro</th>
                                <th class="p-4 text-center font-bold text-slate-900">Career+</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <!-- Row 1 -->
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                <td class="p-4 pl-8 text-slate-700">Resumes</td>
                                <td class="p-4 text-center text-slate-500">1</td>
                                <td class="p-4 text-center font-bold text-slate-900 bg-indigo-50/20">Unlimited</td>
                                <td class="p-4 text-center font-bold text-slate-900">Unlimited</td>
                            </tr>
                            <!-- Row 2 -->
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                <td class="p-4 pl-8 text-slate-700">AI Rewrites</td>
                                <td class="p-4 text-center text-slate-300"><i class="fa-solid fa-minus"></i></td>
                                <td class="p-4 text-center text-green-500 bg-indigo-50/20"><i class="fa-solid fa-check"></i></td>
                                <td class="p-4 text-center text-green-500"><i class="fa-solid fa-check"></i></td>
                            </tr>
                            <!-- Row 3 -->
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                <td class="p-4 pl-8 text-slate-700">Cover Letter Gen</td>
                                <td class="p-4 text-center text-slate-300"><i class="fa-solid fa-minus"></i></td>
                                <td class="p-4 text-center text-slate-300 bg-indigo-50/20"><i class="fa-solid fa-minus"></i></td>
                                <td class="p-4 text-center text-green-500"><i class="fa-solid fa-check"></i></td>
                            </tr>
                             <!-- Row 4 -->
                             <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 pl-8 text-slate-700">Export Formats</td>
                                <td class="p-4 text-center text-slate-500">TXT</td>
                                <td class="p-4 text-center text-slate-900 bg-indigo-50/20">PDF, DOCX</td>
                                <td class="p-4 text-center text-slate-900">PDF, DOCX</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 5. TRUST & VALUE -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-6 max-w-6xl text-center">
                <h2 class="text-3xl font-display font-bold text-slate-900 mb-12">Trusted by 50,000+ Professionals</h2>
                
                <div class="grid md:grid-cols-3 gap-8 text-left mb-16">
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:shadow-lg transition-shadow duration-300 animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="text-yellow-400 mb-4 text-sm"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <p class="text-slate-700 mb-4 italic">"I was getting rejected by ATS constantly. One scan with the Pro plan, I fixed my keywords, and got an interview the next week."</p>
                        <div class="font-bold text-slate-900 text-sm">- Priya M., Product Manager</div>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:shadow-lg transition-shadow duration-300 animate-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="text-yellow-400 mb-4 text-sm"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <p class="text-slate-700 mb-4 italic">"Worth every rupee. The Career+ plan wrote my cover letter in seconds. It saved me hours of stress."</p>
                        <div class="font-bold text-slate-900 text-sm">- Rahul S., Software Engineer</div>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:shadow-lg transition-shadow duration-300 animate-fade-in-up" style="animation-delay: 0.4s;">
                        <div class="text-yellow-400 mb-4 text-sm"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i></div>
                        <p class="text-slate-700 mb-4 italic">"Simple interface, great templates. The most honest resume builder I've found online."</p>
                        <div class="font-bold text-slate-900 text-sm">- Sarah J., Marketing Lead</div>
                    </div>
                </div>

                <div class="flex flex-wrap justify-center gap-12 text-center animate-fade-in-up" style="animation-delay: 0.5s;">
                    <div class="hover:scale-110 transition-transform duration-300">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">90%</div>
                        <div class="text-slate-500 font-medium">ATS Pass Rate</div>
                    </div>
                    <div class="hover:scale-110 transition-transform duration-300">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">50k+</div>
                        <div class="text-slate-500 font-medium">Resumes Built</div>
                    </div>
                    <div class="hover:scale-110 transition-transform duration-300">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">3x</div>
                        <div class="text-slate-500 font-medium">More Interviews</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. FAQ SECTION -->
        <section class="py-16 bg-slate-50">
            <div class="container mx-auto px-6 max-w-3xl">
                <h2 class="text-3xl font-display font-bold text-center text-slate-900 mb-10">Frequently Asked Questions</h2>

                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden faq-item hover:shadow-sm transition-shadow">
                        <button class="w-full flex justify-between items-center p-5 text-left font-bold text-slate-800 hover:bg-slate-50 transition-colors" onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('i').classList.toggle('rotate-180')">
                            Is the Free plan really free?
                            <i class="fa-solid fa-chevron-down text-slate-400 faq-icon transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden bg-slate-50 px-5 text-slate-600 text-sm p-4">
                            Yes! You can build one resume, use our basic templates, and download it as a TXT file completely for free. No credit card required.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden faq-item hover:shadow-sm transition-shadow">
                        <button class="w-full flex justify-between items-center p-5 text-left font-bold text-slate-800 hover:bg-slate-50 transition-colors" onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('i').classList.toggle('rotate-180')">
                            Can I cancel my subscription anytime?
                             <i class="fa-solid fa-chevron-down text-slate-400 faq-icon transition-transform duration-300"></i>
                        </button>
                         <div class="faq-content hidden bg-slate-50 px-5 text-slate-600 text-sm p-4">
                            Absolutely. You can cancel your subscription from your account settings at any time. You will retain access to premium features until the end of your billing cycle.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden faq-item hover:shadow-sm transition-shadow">
                        <button class="w-full flex justify-between items-center p-5 text-left font-bold text-slate-800 hover:bg-slate-50 transition-colors" onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('i').classList.toggle('rotate-180')">
                            Are the resumes ATS-friendly?
                             <i class="fa-solid fa-chevron-down text-slate-400 faq-icon transition-transform duration-300"></i>
                        </button>
                         <div class="faq-content hidden bg-slate-50 px-5 text-slate-600 text-sm p-4">
                            Yes. All our templates are designed with ATS readability in mind, ensuring columns, fonts, and structures are parsed correctly by applicant tracking systems.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. FINAL CTA -->
        <section class="py-20 bg-gradient-to-br from-indigo-700 to-indigo-900 text-white text-center px-6">
            <h2 class="text-3xl md:text-4xl font-display font-bold mb-6 animate-fade-in-up">Land More Interviews Today</h2>
            <p class="text-indigo-200 text-lg mb-10 max-w-2xl mx-auto animate-fade-in-up" style="animation-delay: 0.1s;">Stop guessing and start applying with confidence. Join thousands of job seekers getting hired faster.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.2s;">
                <router-link to="/builder" class="px-8 py-4 bg-white text-indigo-900 font-bold rounded-xl shadow-lg hover:bg-indigo-50 transition-colors transform hover:-translate-y-1">
                    Build Resume Free
                </router-link>
                <router-link to="/ats-checker" class="px-8 py-4 bg-transparent border border-indigo-400 text-white font-bold rounded-xl hover:bg-indigo-800 transition-colors transform hover:-translate-y-1">
                    Check ATS Score
                </router-link>
            </div>
        </section>
  </div>
</template>

<style scoped>
.toggle-checkbox:checked {
    right: 0;
    border-color: #4f46e5;
}
.toggle-checkbox:checked + .toggle-label {
    background-color: #4f46e5;
}

.pricing-card {
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
}
.pricing-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 30px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
