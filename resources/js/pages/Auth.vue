<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()

const activeTab = ref('login')
const showLoginPassword = ref(false)
const showSignupPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')
const validationErrors = ref({})

// Signup Form Data
const signupForm = ref({
  name: '',
  email: '',
  password: '',
  agreed: false
})

// Login Form Data
const loginForm = ref({
  email: '',
  password: '',
  remember: false
})

const passwordStrength = computed(() => {
  const p = signupForm.value.password
  let s = 0
  if (p.length > 5) s++
  if (p.length > 8) s++
  if (/[A-Z]/.test(p)) s++
  if (/[0-9]/.test(p)) s++
  return s
})

const strengthBars = computed(() => {
  const s = passwordStrength.value
  const colors = ['bg-red-400', 'bg-yellow-400', 'bg-blue-400', 'bg-green-500']
  const activeColor = colors[Math.max(0, s - 1)]
  
  return [1, 2, 3, 4].map(i => ({
    active: i <= s,
    class: i <= s ? activeColor : 'bg-slate-200'
  }))
})

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''
  validationErrors.value = {}
  
  const result = await loginUser(loginForm.value)
  
  if (result.success) {
    window.location.href = '/builder'
  } else {
    errorMessage.value = result.error
    validationErrors.value = result.errors || {}
  }
  
  isLoading.value = false
}

const handleSignup = async () => {
  isLoading.value = true
  errorMessage.value = ''
  validationErrors.value = {}
  
  const result = await registerUser(signupForm.value)
  
  if (result.success) {
    window.location.href = '/builder'
  } else {
    errorMessage.value = result.error
    validationErrors.value = result.errors || {}
  }
  
  isLoading.value = false
}

const handleSocialLogin = (provider) => {
  authStore.socialLogin(provider)
}
</script>

<template>
  <div class="flex items-center justify-center p-4 pt-32 min-h-screen bg-slate-50 relative overflow-hidden">
    
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-10 left-10 w-72 h-72 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute bottom-10 right-10 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob" style="animation-delay: 2s"></div>
    </div>

    <!-- Main Card -->
    <main class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row relative z-10 animate-slide-up">
        
        <!-- LEFT: Branding Section -->
        <div class="md:w-5/12 bg-gradient-to-br from-indigo-600 to-violet-700 p-8 md:p-12 text-white flex flex-col justify-between relative overflow-hidden">
            <!-- Decorative overlay -->
            <div class="absolute inset-0 bg-white/5 backdrop-blur-[1px]"></div>
            <div class="absolute top-[-50px] right-[-50px] w-40 h-40 bg-white/10 rounded-full blur-2xl animate-float"></div>
            <div class="absolute bottom-[-20px] left-[-20px] w-60 h-60 bg-indigo-500/30 rounded-full blur-2xl animate-float" style="animation-delay: 2s;"></div>

            <!-- Content -->
            <div class="relative z-10">
                <router-link to="/" class="flex items-center gap-2 mb-8 group w-max">
                    <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-sm border border-white/20 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <span class="font-bold text-lg tracking-tight">Resume<span class="text-indigo-200">AI</span></span>
                </router-link>
                
                <h1 class="text-3xl font-display font-bold mb-4 leading-tight">Build Smarter Resumes with AI</h1>
                <p class="text-indigo-100 text-sm leading-relaxed mb-6">Create ATS-friendly resumes in minutes and land 3x more interviews with our intelligent optimization engine.</p>
                
                <!-- Mini Testimonial -->
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/10 mt-auto">
                    <div class="flex text-yellow-400 text-xs mb-2">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-xs italic text-indigo-50 mb-2">"This tool completely transformed my job search. I got hired at my dream company within 2 weeks!"</p>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-indigo-400 flex items-center justify-center text-[10px] font-bold">JD</div>
                        <span class="text-xs font-bold">John D., Software Engineer</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Auth Forms -->
        <div class="md:w-7/12 p-8 md:p-12 bg-white flex flex-col justify-center">
            
            <!-- Tab Switcher -->
            <div class="flex bg-slate-100 p-1.5 rounded-xl mb-8 w-full max-w-xs mx-auto">
                <button @click="activeTab = 'login'" :class="activeTab === 'login' ? 'bg-white shadow-sm text-slate-900 font-bold' : 'text-slate-500 font-medium hover:text-slate-700'" class="flex-1 py-2.5 rounded-lg text-sm transition-all duration-200">
                    Login
                </button>
                <button @click="activeTab = 'signup'" :class="activeTab === 'signup' ? 'bg-white shadow-sm text-slate-900 font-bold' : 'text-slate-500 font-medium hover:text-slate-700'" class="flex-1 py-2.5 rounded-lg text-sm transition-all duration-200">
                    Sign Up
                </button>
            </div>

            <!-- LOGIN FORM -->
            <transition name="fade" mode="out-in">
            <div v-if="activeTab === 'login'" key="login" class="max-w-xs mx-auto w-full">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-slate-900">Welcome Back</h2>
                    <p class="text-sm text-slate-500">Enter your details to access your account</p>
                </div>

                <form @submit.prevent="handleLogin" class="space-y-4">
                    <div class="input-group relative group">
                        <i class="fa-regular fa-envelope input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                        <input v-model="loginForm.email" type="email" placeholder="Email Address" class="form-input w-full pl-10 pr-4 py-3.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-sans bg-slate-50 focus:bg-white placeholder:text-slate-400 text-sm font-medium text-slate-800" required>
                    </div>
                    
                    <div class="input-group relative group">
                        <i class="fa-solid fa-lock input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                        <input v-model="loginForm.password" :type="showLoginPassword ? 'text' : 'password'" placeholder="Password" class="form-input w-full pl-10 pr-10 py-3.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-sans bg-slate-50 focus:bg-white placeholder:text-slate-400 text-sm font-medium text-slate-800" required>
                        <button type="button" @click="showLoginPassword = !showLoginPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 text-xs transition-colors p-1">
                            <i :class="showLoginPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900 transition-colors">
                            <input v-model="loginForm.remember" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all">
                            Remember me
                        </label>
                        <a href="#" class="text-indigo-600 font-bold hover:underline">Forgot Password?</a>
                    </div>

                    <button type="submit" :disabled="isLoading" class="w-full py-3.5 rounded-xl bg-slate-900 text-white font-bold shadow-lg hover:bg-slate-800 hover:shadow-xl hover:shadow-slate-900/20 transition-all flex items-center justify-center gap-2 group disabled:opacity-75 disabled:cursor-not-allowed">
                        <span v-if="!isLoading">Login</span>
                        <div v-else class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Logging in...
                        </div>
                        <i v-if="!isLoading" class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>
            </div>

            <!-- SIGN UP FORM -->
            <div v-else key="signup" class="max-w-xs mx-auto w-full">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-slate-900">Create Account</h2>
                    <p class="text-sm text-slate-500">Get started with your free resume builder</p>
                </div>

                <form @submit.prevent="handleSignup" class="space-y-4">
                    <div class="input-group relative group">
                        <i class="fa-regular fa-user input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                        <input v-model="signupForm.name" type="text" placeholder="Full Name" class="form-input w-full pl-10 pr-4 py-3.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-sans bg-slate-50 focus:bg-white placeholder:text-slate-400 text-sm font-medium text-slate-800" required>
                    </div>

                    <div class="input-group relative group">
                        <i class="fa-regular fa-envelope input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                        <input v-model="signupForm.email" type="email" placeholder="Email Address" class="form-input w-full pl-10 pr-4 py-3.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-sans bg-slate-50 focus:bg-white placeholder:text-slate-400 text-sm font-medium text-slate-800" required>
                    </div>
                    
                    <div class="input-group relative group">
                        <i class="fa-solid fa-lock input-icon absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                        <input v-model="signupForm.password" :type="showSignupPassword ? 'text' : 'password'" placeholder="Password" class="form-input w-full pl-10 pr-10 py-3.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-sans bg-slate-50 focus:bg-white placeholder:text-slate-400 text-sm font-medium text-slate-800" required>
                        <button type="button" @click="showSignupPassword = !showSignupPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 text-xs transition-colors p-1">
                            <i :class="showSignupPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                        </button>
                    </div>

                    <!-- Password Strength -->
                    <div class="flex gap-1 h-1 mt-1 transition-opacity" :class="signupForm.password ? 'opacity-100' : 'opacity-0'" id="strength-bar">
                        <div v-for="(bar, index) in strengthBars" :key="index" :class="['flex-1 rounded-full transition-colors duration-300', bar.class]"></div>
                    </div>

                    <label class="flex items-start gap-2 cursor-pointer text-xs text-slate-600 mt-2 hover:text-slate-900 transition-colors">
                        <input v-model="signupForm.agreed" type="checkbox" required class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mt-0.5 transition-all">
                        <span>I agree to the <a href="#" class="text-indigo-600 hover:underline font-bold">Terms of Service</a> & <a href="#" class="text-indigo-600 hover:underline font-bold">Privacy Policy</a></span>
                    </label>

                    <button type="submit" :disabled="isLoading" class="w-full py-3.5 rounded-xl bg-indigo-600 text-white font-bold shadow-lg hover:bg-indigo-500 hover:shadow-xl hover:shadow-indigo-500/30 transition-all flex items-center justify-center gap-2 group disabled:opacity-75 disabled:cursor-not-allowed">
                        <span v-if="!isLoading">Create Account</span>
                        <div v-else class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Creating...
                        </div>
                        <i v-if="!isLoading" class="fa-solid fa-user-plus group-hover:scale-110 transition-transform"></i>
                    </button>
                </form>
            </div>
            </transition>

            <!-- Social Login -->
            <div class="max-w-xs mx-auto w-full mt-8">
                <div class="relative flex items-center justify-center mb-6">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                    <span class="relative bg-white px-3 text-xs text-slate-500 font-medium">Or continue with</span>
                </div>

                <div class="flex gap-3 justify-center">
                    <button @click="handleSocialLogin('google')" class="social-btn w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-white hover:border-red-500 hover:bg-red-500 transition-all shadow-sm hover:shadow-red-500/30">
                        <i class="fa-brands fa-google text-lg"></i>
                    </button>
                    <button @click="handleSocialLogin('linkedin')" class="social-btn w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-white hover:border-blue-600 hover:bg-blue-600 transition-all shadow-sm hover:shadow-blue-600/30">
                        <i class="fa-brands fa-linkedin-in text-lg"></i>
                    </button>
                    <button @click="handleSocialLogin('github')" class="social-btn w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-white hover:border-slate-800 hover:bg-slate-800 transition-all shadow-sm hover:shadow-slate-800/30">
                        <i class="fa-brands fa-github text-lg"></i>
                    </button>
                </div>

                <div class="mt-8 text-center flex items-center justify-center gap-2 text-[10px] text-slate-400 uppercase tracking-widest font-bold">
                    <i class="fa-solid fa-lock text-green-500"></i> Secure SSL Encrypted
                </div>
            </div>

            <!-- Footer Links (Mobile Only) -->
            <div class="md:hidden mt-8 text-center text-xs text-slate-400 flex justify-center gap-4">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <a href="#">Help</a>
            </div>

        </div>
    </main>
  </div>
</template>

<style scoped>
.social-btn {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.social-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
