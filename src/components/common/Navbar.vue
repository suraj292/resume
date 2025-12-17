<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const isMenuOpen = ref(false)
const isScrolled = ref(false)

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <nav
    class="fixed w-full z-50 top-0 transition-all duration-300"
    :class="[
      isScrolled ? 'bg-white/90 backdrop-blur-md shadow-md border-b border-slate-200/60' : 'bg-transparent'
    ]"
  >
    <div class="container mx-auto px-6 h-20 flex items-center justify-between">
      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-2.5 group">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white text-lg shadow-lg group-hover:scale-105 transition-transform duration-300">
          <i class="fa-solid fa-layer-group"></i>
        </div>
        <span class="text-xl font-display font-bold text-slate-900 tracking-tight">Resume<span class="text-indigo-600">AI</span></span>
      </router-link>

      <!-- Desktop Links -->
      <div class="hidden md:flex items-center gap-8">
        <router-link to="/" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" active-class="text-indigo-600">Home</router-link>
        <router-link to="/builder" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" active-class="text-indigo-600">Resume Builder</router-link>
        <router-link to="/ats-checker" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" active-class="text-indigo-600">ATS Checker</router-link>
        <router-link to="/templates" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" active-class="text-indigo-600">Templates</router-link>
        <router-link to="/pricing" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" active-class="text-indigo-600">Pricing</router-link>
        <router-link to="/contact" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors" active-class="text-indigo-600">Contact</router-link>
      </div>

      <!-- CTA -->
      <div class="hidden md:flex items-center gap-4">
        <router-link to="/auth" class="text-sm font-medium text-slate-600 hover:text-slate-900">Log In</router-link>
        <router-link to="/builder" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-bold shadow-lg hover:bg-slate-800 hover:shadow-xl transition-all transform hover:-translate-y-0.5">
          Build Resume Free
        </router-link>
      </div>

      <!-- Mobile Menu Button -->
      <button @click="toggleMenu" class="md:hidden text-slate-600 text-xl focus:outline-none">
        <i class="fa-solid" :class="isMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div v-if="isMenuOpen" class="md:hidden bg-white border-t border-slate-100 px-6 py-4 shadow-lg absolute w-full left-0 top-20 flex flex-col gap-4">
      <router-link to="/" class="text-sm font-medium text-slate-600 hover:text-indigo-600" @click="isMenuOpen = false">Home</router-link>
      <router-link to="/builder" class="text-sm font-medium text-slate-600 hover:text-indigo-600" @click="isMenuOpen = false">Resume Builder</router-link>
      <router-link to="/ats-checker" class="text-sm font-medium text-slate-600 hover:text-indigo-600" @click="isMenuOpen = false">ATS Checker</router-link>
      <router-link to="/templates" class="text-sm font-medium text-slate-600 hover:text-indigo-600" @click="isMenuOpen = false">Templates</router-link>
      <router-link to="/pricing" class="text-sm font-medium text-slate-600 hover:text-indigo-600" @click="isMenuOpen = false">Pricing</router-link>
      <router-link to="/contact" class="text-sm font-medium text-slate-600 hover:text-indigo-600" @click="isMenuOpen = false">Contact</router-link>
      <div class="h-px bg-slate-100 my-2"></div>
      <router-link to="/auth" class="text-sm font-medium text-slate-600 hover:text-slate-900" @click="isMenuOpen = false">Log In</router-link>
      <router-link to="/builder" class="px-5 py-3 rounded-xl bg-slate-900 text-white text-sm font-bold text-center shadow-lg" @click="isMenuOpen = false">
        Build Resume Free
      </router-link>
    </div>
  </nav>
</template>
