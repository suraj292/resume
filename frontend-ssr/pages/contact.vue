<script setup>
import { ref } from 'vue'

const isLoading = ref(false)
const showSuccess = ref(false)

const form = ref({
  name: '',
  email: '',
  subject: 'General Inquiry',
  message: ''
})

const handleSubmit = () => {
  isLoading.value = true
  
  // Simulate API call
  setTimeout(() => {
    isLoading.value = false
    showSuccess.value = true
    form.value = {
      name: '',
      email: '',
      subject: 'General Inquiry',
      message: ''
    }
  }, 1500)
}

const sendAnother = () => {
    showSuccess.value = false
}
</script>

<template>
  <div class="flex-grow">

        <!-- 2. HERO SECTION -->
        <section class="relative pt-20 pb-24 text-center px-6 overflow-hidden">
            <!-- Background Decoration -->
            <div
                class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full overflow-hidden -z-10 pointer-events-none">
                <div
                    class="absolute top-10 left-1/4 w-96 h-96 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-60 animate-blob">
                </div>
                <div class="absolute top-20 right-1/4 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-60 animate-blob"
                    style="animation-delay: 2s"></div>
            </div>

            <div class="max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 mb-6 animate-slide-up">
                    We’d Love to <span class="text-indigo-600">Hear From You</span>
                </h1>
                <p class="text-slate-500 text-lg mb-0 animate-slide-up" style="animation-delay: 0.1s;">
                    Have questions about our resume builder, ATS checking, or enterprise pricing? Our team is ready to
                    help you land your dream job.
                </p>
            </div>
        </section>

        <!-- 3. CONTACT CARDS & FORM WRAPPER -->
        <section class="container mx-auto px-6 pb-24 -mt-10">

            <!-- Info Cards (Floating above) -->
            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto mb-16 relative z-10 animate-fade-in"
                style="animation-delay: 0.2s;">
                <!-- Email -->
                <div
                    class="info-card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center group cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-indigo-100">
                    <div
                        class="icon-box w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl transition-all duration-300 group-hover:bg-indigo-600 group-hover:text-white group-hover:scale-110">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg mb-1">Email Support</h3>
                    <p class="text-sm text-slate-500 mb-3">Our team typically responds within 24 hours.</p>
                    <a href="mailto:support@resumeai.com"
                        class="text-indigo-600 font-semibold hover:underline">support@resumeai.com</a>
                </div>

                <!-- Phone -->
                <div
                    class="info-card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center group cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-blue-100">
                    <div
                        class="icon-box w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl transition-all duration-300 group-hover:bg-blue-600 group-hover:text-white group-hover:scale-110">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg mb-1">Phone (Sales)</h3>
                    <p class="text-sm text-slate-500 mb-3">Mon-Fri from 9am to 6pm EST.</p>
                    <a href="tel:+15550000000" class="text-blue-600 font-semibold hover:underline">+1 (555) 000-0000</a>
                </div>

                <!-- Location -->
                <div
                    class="info-card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center group cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-purple-100">
                    <div
                        class="icon-box w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl transition-all duration-300 group-hover:bg-purple-600 group-hover:text-white group-hover:scale-110">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg mb-1">Headquarters</h3>
                    <p class="text-sm text-slate-500 mb-3">Visit our office for a coffee and chat.</p>
                    <span class="text-purple-600 font-semibold">San Francisco, CA</span>
                </div>
            </div>

            <!-- 4. CONTACT FORM SECTION -->
            <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden flex flex-col md:flex-row animate-slide-up"
                style="animation-delay: 0.3s;">

                <!-- Left: Visual/Context -->
                <div
                    class="md:w-5/12 bg-slate-900 p-10 text-white relative overflow-hidden flex flex-col justify-between">
                    <!-- Decor -->
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
                    </div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"
                        style="animation-delay: 2s;"></div>

                    <div class="relative z-10">
                        <h3 class="text-2xl font-display font-bold mb-4">Send us a message</h3>
                        <p class="text-slate-300 text-sm leading-relaxed mb-8">
                            Fill out the form and our team will get back to you as soon as possible. We love hearing
                            your feedback!
                        </p>

                        <div class="space-y-4">
                            <div class="flex items-center gap-3 text-sm text-slate-300">
                                <i class="fa-solid fa-check-circle text-green-400"></i>
                                <span>Fast 24h response time</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-300">
                                <i class="fa-solid fa-shield-halved text-green-400"></i>
                                <span>Your data is secure</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-300">
                                <i class="fa-solid fa-user-group text-green-400"></i>
                                <span>Real human support</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 mt-12">
                        <div class="p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-white/10">
                            <p class="text-xs italic text-indigo-100 mb-2">"The support team helped me format my resume
                                perfectly for the job I wanted. Highly recommended!"</p>
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-6 h-6 rounded-full bg-indigo-500 flex items-center justify-center text-[10px] font-bold">
                                    AJ</div>
                                <span class="text-xs font-bold">Alex J.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Form -->
                <div class="md:w-7/12 p-8 md:p-10 relative">
                    <form @submit.prevent="handleSubmit" class="space-y-6">

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Full Name <span
                                        class="text-red-500">*</span></label>
                                <input v-model="form.name" type="text" required
                                    class="form-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                                    placeholder="John Doe">
                            </div>
                            <!-- Email -->
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Email Address
                                    <span class="text-red-500">*</span></label>
                                <input v-model="form.email" type="email" required
                                    class="form-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                                    placeholder="john@example.com">
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Subject</label>
                            <div class="relative">
                                <select v-model="form.subject"
                                    class="form-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 appearance-none cursor-pointer">
                                    <option>General Inquiry</option>
                                    <option>Technical Support</option>
                                    <option>Billing & Pricing</option>
                                    <option>Feature Request</option>
                                    <option>Partnership</option>
                                </select>
                                <div
                                    class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Message <span
                                    class="text-red-500">*</span></label>
                            <textarea v-model="form.message" required rows="4"
                                class="form-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 resize-none"
                                placeholder="How can we help you?"></textarea>
                            <p class="text-xs text-slate-400 text-right">{{ form.message.length }}/500 characters</p>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" :disabled="isLoading"
                            class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed">
                            <span v-if="!isLoading">Send Message</span>
                            <span v-else>Sending...</span>
                            <i v-if="!isLoading" class="fa-regular fa-paper-plane"></i>
                            <i v-else class="fa-solid fa-circle-notch fa-spin"></i>
                        </button>

                    </form>

                    <!-- Success Message Overlay -->
                    <div v-if="showSuccess"
                        class="absolute inset-0 bg-white z-20 flex flex-col items-center justify-center text-center p-8 animate-fade-in">
                        <div
                            class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center text-green-500 text-3xl mb-6 animate-bounce">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-2">Message Sent!</h3>
                        <p class="text-slate-500 mb-8 max-w-sm">Thank you for reaching out. We've received your message
                            and will get back to you shortly.</p>
                        <button @click="sendAnother"
                            class="px-6 py-2 border border-slate-200 rounded-lg text-slate-600 font-bold hover:bg-slate-50 transition-colors">
                            Send Another
                        </button>
                    </div>

                </div>
            </div>
        </section>

        <!-- 6. FAQ SHORTCUT -->
        <section class="bg-slate-50 py-16 border-t border-slate-200">
            <div class="container mx-auto px-6 text-center">
                <div class="inline-block p-4 bg-white rounded-2xl shadow-sm border border-slate-100 animate-slide-up">
                    <span class="text-slate-500 font-medium mr-4">Looking for quick answers?</span>
                    <div class="inline-flex gap-2 mt-2 sm:mt-0">
                        <a href="#"
                            class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-bold hover:bg-indigo-100 transition-colors">Visit
                            Help Center</a>
                        <a href="#"
                            class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">Read
                            FAQs</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. CTA -->
        <section
            class="py-24 bg-gradient-to-br from-slate-900 to-indigo-900 text-white text-center px-6 relative overflow-hidden">
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgdmlld0JveD0iMCAwIDQwIDQwIj48ZyBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0wIDQwaDQwVjBIMHY0MHptMjAgMjBoMjBWMjBIMjB2MjB6TTAgMjBoMjBWMHgyMHYyMHoiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvZz48L3N2Zz4=')] opacity-20">
            </div>

            <div class="relative z-10">
                <h2 class="text-3xl md:text-5xl font-display font-bold mb-6">Ready to Build Your Resume with AI?</h2>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <NuxtLink to="/builder"
                        class="px-8 py-4 bg-white text-indigo-900 font-bold rounded-xl shadow-lg hover:bg-indigo-50 transition-colors">
                        Build Resume Free
                    </NuxtLink>
                    <NuxtLink to="/ats-checker"
                        class="px-8 py-4 bg-transparent border border-indigo-400 text-white font-bold rounded-xl hover:bg-indigo-800 transition-colors">
                        Check ATS Score
                    </NuxtLink>
                </div>
            </div>
        </section>

    </div>
</template>

<style scoped>
/* Input Styles */
.form-input {
    transition: all 0.3s ease;
}

.form-input:focus {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
}

/* Animations */
.animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
}

.animate-slide-up {
    animation: slideUp 0.6s ease-out forwards;
}

.animate-blob {
    animation: blob 7s infinite;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
</style>
