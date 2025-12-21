<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
        @click="close"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out delay-100"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div 
            v-if="isOpen"
            class="relative max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden"
            @click.stop
          >
            <!-- Animated Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 opacity-10"></div>
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 animate-pulse"></div>
            
            <!-- Content -->
            <div class="relative p-8 text-center">
              <!-- Animated Icon -->
              <div class="mb-6 relative">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center animate-bounce-slow">
                  <i class="fa-solid fa-lock text-3xl text-indigo-600"></i>
                </div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-20 h-20 bg-indigo-400 rounded-full blur-2xl opacity-20 animate-pulse"></div>
              </div>

              <!-- Title -->
              <h2 class="text-2xl font-black text-slate-900 mb-3 tracking-tight">
                Authentication Required
              </h2>

              <!-- Message -->
              <p class="text-slate-600 mb-6 leading-relaxed">
                Please <span class="font-bold text-indigo-600">sign in</span> or 
                <span class="font-bold text-purple-600">create an account</span> to access this feature.
              </p>

              <!-- Benefits -->
              <div class="mb-6 bg-slate-50 rounded-2xl p-4 text-left">
                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Why Sign Up?</p>
                <ul class="space-y-2">
                  <li class="flex items-start gap-2 text-xs text-slate-600">
                    <i class="fa-solid fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Build professional ATS-optimized resumes</span>
                  </li>
                  <li class="flex items-start gap-2 text-xs text-slate-600">
                    <i class="fa-solid fa-check-circle text-green-500 mt-0.5"></i>
                    <span>AI-powered content generation & optimization</span>
                  </li>
                  <li class="flex items-start gap-2 text-xs text-slate-600">
                    <i class="fa-solid fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Save and manage multiple resume versions</span>
                  </li>
                  <li class="flex items-start gap-2 text-xs text-slate-600">
                    <i class="fa-solid fa-check-circle text-green-500 mt-0.5"></i>
                    <span>Real-time ATS compatibility scoring</span>
                  </li>
                </ul>
              </div>

              <!-- Action Buttons -->
              <div class="grid grid-cols-2 gap-3">
                <a 
                  href="/auth"
                  class="group relative px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all overflow-hidden"
                >
                  <span class="relative z-10 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Sign In
                  </span>
                  <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>
                </a>
                
                <a 
                  href="/auth?tab=register"
                  class="group relative px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all overflow-hidden"
                >
                  <span class="relative z-10 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus"></i>
                    Sign Up
                  </span>
                  <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>
                </a>
              </div>

              <!-- Close Button -->
              <button 
                @click="close"
                class="mt-4 text-xs text-slate-400 hover:text-slate-600 transition-colors underline"
              >
                Maybe later
              </button>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute top-4 right-4 w-32 h-32 bg-gradient-to-br from-indigo-400/20 to-purple-400/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-4 left-4 w-24 h-24 bg-gradient-to-br from-pink-400/20 to-purple-400/20 rounded-full blur-3xl"></div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const isOpen = ref(false)

const emit = defineEmits(['close'])

const close = () => {
  isOpen.value = false
  setTimeout(() => {
    emit('close')
  }, 300)
}

onMounted(() => {
  setTimeout(() => {
    isOpen.value = true
  }, 100)
})
</script>

<style scoped>
@keyframes bounce-slow {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-bounce-slow {
  animation: bounce-slow 2s ease-in-out infinite;
}
</style>
