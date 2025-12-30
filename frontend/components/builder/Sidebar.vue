<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  activeTab: string
  sidebarOpen: boolean
}>()

const emit = defineEmits<{
  (e: 'update:activeTab', value: string): void
  (e: 'update:sidebarOpen', value: boolean): void
  (e: 'close-mobile-sidebar'): void
}>()

const tabs = ref([
  { id: 'upload', name: 'Import & Job', icon: 'fa-cloud-arrow-up' },
  { id: 'manual', name: 'Manual Info', icon: 'fa-pen-to-square' },
  { id: 'ai', name: 'AI Assistant', icon: 'fa-wand-magic-sparkles' },
  { id: 'templates', name: 'Templates', icon: 'fa-layer-group' },
  { id: 'colors', name: 'Color Palette', icon: 'fa-palette' },
  { id: 'history', name: 'Version History', icon: 'fa-clock-rotate-left' },
  { id: 'cover-letter', name: 'Cover Letter', icon: 'fa-envelope-open-text' },
  { id: 'tracker', name: 'Job Tracker', icon: 'fa-briefcase' }
])

const switchTab = (tabId: string) => {
  emit('update:activeTab', tabId)
  if (process.client && window.innerWidth < 1024) {
    emit('close-mobile-sidebar')
  }
}
</script>

<template>
  <aside 
    id="mobile-sidebar"
    :class="['w-16 bg-white border-r border-slate-200 flex flex-col items-center py-6 gap-6 z-40 fixed left-0 top-28 bottom-0 transition-transform duration-300 lg:relative lg:translate-x-0 lg:top-0', { '-translate-x-full lg:translate-x-0': !sidebarOpen }]">
    <div class="flex flex-col gap-4">
      <div v-for="tab in tabs" :key="tab.id" class="nav-item group relative">
        <button 
          @click="switchTab(tab.id)" 
          :class="['nav-btn w-10 h-10 flex items-center justify-center rounded-xl transition-all', activeTab === tab.id ? 'nav-btn-active bg-indigo-50 text-indigo-600' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600']"
          :title="tab.name"
          :aria-label="tab.name"
          :aria-current="activeTab === tab.id ? 'page' : undefined"
        >
          <i :class="`fa-solid ${tab.icon} text-base`"></i>
        </button>
        
        <!-- Tooltip -->
        <div class="absolute left-14 top-1/2 -translate-y-1/2 bg-slate-800 text-white text-xs font-medium px-2 py-1 rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">
          {{ tab.name }}
          <div class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1 border-4 border-transparent border-r-slate-800"></div>
        </div>
      </div>
    </div>
  </aside>
</template>

<style scoped>
/* Ensure hidden class works for mobile */
.-translate-x-full {
  transform: translateX(-100%);
}
</style>
