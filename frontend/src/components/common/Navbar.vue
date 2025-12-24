<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const isMenuOpen = ref(false)
const isScrolled = ref(false)
const showUserDropdown = ref(false)

const isAuthenticated = computed(() => authStore.isAuthenticated)
const currentUser = computed(() => authStore.currentUser)

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

const handleLogout = async () => {
  await authStore.logout()
  showUserDropdown.value = false
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  // User data is loaded after successful login, no need to fetch here
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
      </div>

      <!-- Desktop CTA - Guest User -->
      <div v-if="!isAuthenticated" class="hidden md:flex items-center gap-4">
        <router-link to="/auth" class="text-sm font-medium text-slate-600 hover:text-slate-900">Log In</router-link>
        <router-link to="/auth" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-bold shadow-lg hover:bg-slate-800 hover:shadow-xl transition-all transform hover:-translate-y-0.5">
          Get Started Free
        </router-link>
      </div>

      <!-- Desktop CTA - Logged In User -->
      <div v-else class="hidden md:flex items-center gap-4">
        <router-link to="/builder" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-colors">
          <i class="fa-solid fa-plus mr-1.5"></i> New Resume
        </router-link>
        
        <!-- User Dropdown -->
        <div class="relative" @mouseenter="showUserDropdown = true" @mouseleave="showUserDropdown = false">
          <button class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-100 transition-colors">
            <div class="text-right hidden lg:block">
              <p class="text-xs font-bold text-slate-900">{{ currentUser?.name || 'User' }}</p>
              <p class="text-[10px] text-slate-500">{{ currentUser?.email || '' }}</p>
            </div>
            <img 
              :src="currentUser?.avatar || 'https://ui-avatars.com/api/?name=User&background=6366f1&color=fff&size=128'" 
              class="w-9 h-9 rounded-full border-2 border-white shadow-sm"
              :alt="currentUser?.name"
            >
          </button>

          <!-- Dropdown Menu -->
          <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
          >
            <div v-show="showUserDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-2">
              <router-link to="/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fa-regular fa-user w-4"></i>
                <span>My Profile</span>
              </router-link>
              <router-link to="/builder" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fa-solid fa-file-lines w-4"></i>
                <span>My Resumes</span>
              </router-link>
              <router-link to="/pricing" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fa-solid fa-crown w-4"></i>
                <span>Upgrade Plan</span>
              </router-link>
              <div class="h-px bg-slate-100 my-2"></div>
              <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                <i class="fa-solid fa-right-from-bracket w-4"></i>
                <span>Logout</span>
              </button>
            </div>
          </Transition>
        </div>
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
      
      <div class="h-px bg-slate-100 my-2"></div>
      
      <!-- Mobile - Guest User -->
      <template v-if="!isAuthenticated">
        <router-link to="/auth" class="text-sm font-medium text-slate-600 hover:text-slate-900" @click="isMenuOpen = false">Log In</router-link>
        <router-link to="/auth" class="px-5 py-3 rounded-xl bg-slate-900 text-white text-sm font-bold text-center shadow-lg" @click="isMenuOpen = false">
          Get Started Free
        </router-link>
      </template>

      <!-- Mobile - Logged In User -->
      <template v-else>
        <div class="flex items-center gap-3 py-2 border-b border-slate-100">
          <img 
            :src="currentUser?.avatar || 'https://ui-avatars.com/api/?name=User&background=6366f1&color=fff&size=128'" 
            class="w-10 h-10 rounded-full"
          >
          <div>
            <p class="text-sm font-bold text-slate-900">{{ currentUser?.name || 'User' }}</p>
            <p class="text-xs text-slate-500">{{ currentUser?.email || '' }}</p>
          </div>
        </div>
        <router-link to="/profile" class="text-sm font-medium text-slate-600 hover:text-indigo-600" @click="isMenuOpen = false">
          <i class="fa-regular fa-user mr-2"></i> My Profile
        </router-link>
        <router-link to="/builder" class="text-sm font-medium text-slate-600 hover:text-indigo-600" @click="isMenuOpen = false">
          <i class="fa-solid fa-file-lines mr-2"></i> My Resumes
        </router-link>
        <button @click="handleLogout(); isMenuOpen = false" class="text-left text-sm font-medium text-red-600 hover:text-red-700">
          <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
        </button>
      </template>
    </div>
  </nav>
</template>
