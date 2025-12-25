<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()

const status = ref('verifying') // verifying, success, error
const message = ref('')
const isResending = ref(false)

const verifyEmail = async () => {
  try {
    const { path, signature, expires } = route.query
    
    if (!path || !signature || !expires) {
      status.value = 'error'
      message.value = 'Invalid verification link. Please request a new verification email.'
      return
    }

    // Call the Laravel API verification endpoint
    const apiBase = config.public.apiBase || 'http://127.0.0.1:8000'
    const response = await $fetch(`${apiBase}${path}?signature=${signature}&expires=${expires}`, {
      method: 'GET',
      credentials: 'include'
    })

    status.value = 'success'
    message.value = response.message || 'Your email has been verified successfully!'
    
    // Redirect to login after 3 seconds
    setTimeout(() => {
      navigateTo('/auth')
    }, 3000)
    
  } catch (error) {
    status.value = 'error'
    message.value = error.data?.message || 'Email verification failed. The link may have expired.'
  }
}

const resendVerification = async () => {
  isResending.value = true
  try {
    const apiBase = config.public.apiBase || 'http://127.0.0.1:8000'
    const token = localStorage.getItem('auth_token')
    
    await $fetch(`${apiBase}/api/email/resend`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`
      },
      credentials: 'include'
    })
    
    message.value = 'Verification email sent! Please check your inbox.'
  } catch (error) {
    message.value = error.data?.message || 'Failed to resend verification email.'
  } finally {
    isResending.value = false
  }
}

onMounted(() => {
  verifyEmail()
})
</script>

<template>
  <div class="flex items-center justify-center min-h-screen bg-slate-50 p-4">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
      <div class="absolute top-10 left-10 w-72 h-72 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
      <div class="absolute bottom-10 right-10 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob" style="animation-delay: 2s"></div>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12 max-w-md w-full text-center relative z-10">
      <!-- Verifying State -->
      <div v-if="status === 'verifying'" class="animate-pulse">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-indigo-100 flex items-center justify-center">
          <i class="fa-solid fa-circle-notch fa-spin text-4xl text-indigo-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Verifying Your Email</h1>
        <p class="text-slate-600">Please wait while we verify your email address...</p>
      </div>

      <!-- Success State -->
      <div v-else-if="status === 'success'">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
          <i class="fa-solid fa-check text-4xl text-green-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Email Verified!</h1>
        <p class="text-slate-600 mb-6">{{ message }}</p>
        <p class="text-sm text-slate-500">Redirecting you to login...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="status === 'error'">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-red-100 flex items-center justify-center">
          <i class="fa-solid fa-xmark text-4xl text-red-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Verification Failed</h1>
        <p class="text-slate-600 mb-6">{{ message }}</p>
        
        <div class="space-y-3">
          <button 
            @click="resendVerification" 
            :disabled="isResending"
            class="w-full py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i v-if="isResending" class="fa-solid fa-circle-notch fa-spin mr-2"></i>
            {{ isResending ? 'Sending...' : 'Resend Verification Email' }}
          </button>
          
          <NuxtLink 
            to="/auth" 
            class="block w-full py-3 rounded-xl border-2 border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition-colors"
          >
            Back to Login
          </NuxtLink>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-8 pt-6 border-t border-slate-100">
        <NuxtLink to="/" class="flex items-center justify-center gap-2 text-sm text-slate-500 hover:text-indigo-600 transition-colors">
          <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white text-xs">
            <i class="fa-solid fa-layer-group"></i>
          </div>
          <span class="font-bold">Resume<span class="text-indigo-600">AI</span></span>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes blob {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}
</style>
