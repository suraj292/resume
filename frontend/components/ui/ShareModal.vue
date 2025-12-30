<script setup lang="ts">
import { useSocialShare } from '~/composables/useSocialShare'

const props = defineProps<{
  isOpen: boolean
  shareText: string
  shareUrl: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const { generateSocialLinks, copyLink } = useSocialShare()

const links = computed(() => generateSocialLinks({
  text: props.shareText,
  url: props.shareUrl
}))

const handleCopy = () => {
  copyLink(props.shareUrl)
}
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div @click="$emit('close')" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal -->
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 transform transition-all scale-100">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-slate-900">Share Resume</h3>
        <button @click="$emit('close')" class="text-slate-400 hover:text-slate-600 transition-colors">
          <i class="fa-solid fa-xmark text-xl"></i>
        </button>
      </div>

      <div class="grid grid-cols-4 gap-4 mb-6">
        <a :href="links.linkedin" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center gap-2 group">
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-600 transition-colors">
            <i class="fa-brands fa-linkedin-in text-xl text-blue-600 group-hover:text-white transition-colors"></i>
          </div>
          <span class="text-xs font-medium text-slate-600">LinkedIn</span>
        </a>

        <a :href="links.twitter" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center gap-2 group">
          <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center group-hover:bg-sky-500 transition-colors">
            <i class="fa-brands fa-twitter text-xl text-sky-500 group-hover:text-white transition-colors"></i>
          </div>
          <span class="text-xs font-medium text-slate-600">Twitter</span>
        </a>

        <a :href="links.whatsapp" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center gap-2 group">
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-500 transition-colors">
            <i class="fa-brands fa-whatsapp text-xl text-green-500 group-hover:text-white transition-colors"></i>
          </div>
          <span class="text-xs font-medium text-slate-600">WhatsApp</span>
        </a>

        <a :href="links.email" class="flex flex-col items-center gap-2 group">
          <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-500 transition-colors">
            <i class="fa-regular fa-envelope text-xl text-orange-500 group-hover:text-white transition-colors"></i>
          </div>
          <span class="text-xs font-medium text-slate-600">Email</span>
        </a>
      </div>

      <!-- Copy Link -->
      <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-2 pr-2.5">
        <div class="flex-1 px-2 py-1 text-sm text-slate-500 truncate select-all">{{ shareUrl }}</div>
        <button @click="handleCopy" class="bg-white border border-slate-200 hover:border-indigo-300 text-slate-700 hover:text-indigo-600 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
          Copy
        </button>
      </div>
    </div>
  </div>
</template>
