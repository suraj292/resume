<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import type { ResumeData, ThemeConfig } from '~/types/resume'

const props = withDefaults(defineProps<{
  resumeData: ResumeData
  currentThemeConfig: ThemeConfig
  selectedTemplate: string
  currentTemplateComponent: any
  isTemplateLoading: boolean
  sidebarOpen?: boolean
  showControls?: boolean
  initialScale?: number
}>(), {
  showControls: true,
  initialScale: 0.97
})

const emit = defineEmits<{
  (e: 'update:totalPages', value: number): void
}>()

const previewScale = ref(props.initialScale)

const adjustZoom = (delta: number) => {
  previewScale.value = Math.max(0.3, Math.min(1.5, previewScale.value + delta))
}

// Smart multi-page logic - detect when content exceeds A4 page height
const needsSecondPage = computed(() => {
  if (!props.resumeData) return false
  
  // Estimate content height in pixels
  let estimatedHeight = 0
  
  // Header section: ~150px
  estimatedHeight += 150
  
  // Summary: base 100px + 1px per character (wrapping considered)
  if (props.resumeData.basics?.summary) {
    estimatedHeight += 100 + Math.ceil(props.resumeData.basics.summary.length / 2)
  }
  
  // Skills section: base 100px + 5px per skill
  const skillCount = Object.values(props.resumeData.skills || {})
    .reduce((sum, arr) => sum + arr.length, 0)
  if (skillCount > 0) {
    estimatedHeight += 100 + (skillCount * 5)
  }
  
  // Experience: 150px base per entry + 30px per responsibility
  if (props.resumeData.experience) {
    props.resumeData.experience.forEach(exp => {
      if (exp.position) {
        estimatedHeight += 150
        const respCount = exp.responsibilities?.filter(r => r).length || 0
        estimatedHeight += respCount * 30
      }
    })
  }
  
  // Education: 80px per entry
  const eduCount = props.resumeData.education?.filter(e => e.degree).length || 0
  estimatedHeight += eduCount * 80
  
  // Achievements: base 100px + 30px per achievement
  const achCount = props.resumeData.achievements?.filter(a => a).length || 0
  if (achCount > 0) {
    estimatedHeight += 100 + (achCount * 30)
  }
  
  // Page capacity is ~1000px usable height (A4 page minus padding)
  // Show second page if content exceeds this threshold
  // Show second page if content exceeds this threshold
  return estimatedHeight > 1000
})

watch(needsSecondPage, (newVal) => {
  emit('update:totalPages', newVal ? 2 : 1)
}, { immediate: true })

// Split data for page 1 (when multi-page is needed)
const page1Data = computed<ResumeData>(() => {
  if (!needsSecondPage.value) {
    return props.resumeData
  }
  
  // Page 1: Header, Summary, Skills, First 2 experiences
  return {
    ...props.resumeData,
    experience: props.resumeData.experience?.slice(0, 2) || [],
    education: [], // Move to page 2
    achievements: [] // Move to page 2
  }
})

// Split data for page 2 (when multi-page is needed)
const page2Data = computed<ResumeData | null>(() => {
  if (!needsSecondPage.value) {
    return null
  }
  
  // Page 2: Remaining experience, Education, Achievements (no header/summary duplicate)
  return {
    ...props.resumeData,
    basics: {
      ...props.resumeData.basics,
      fullName: '', // Don't repeat name
      title: '', // Don't repeat title
      summary: '' // Don't repeat summary
    },
    experience: props.resumeData.experience?.slice(2) || [],
    skills: {
        backend: [],
        frontend: [],
        devops: [],
        other: []
    } // Don't repeat skills
  }
})
</script>

<template>
  <div class="space-y-8 pb-10">
    <!-- Page 1 -->
    <div 
      id="resume-preview"
      class="resume-paper bg-white shadow-2xl transition-all duration-300"
      :class="{ 'overflow-hidden': needsSecondPage }"
      :style="{ 
        width: '210mm',
        minHeight: '297mm',
        height: needsSecondPage ? '297mm' : 'auto',
        maxHeight: needsSecondPage ? '297mm' : 'none',
        transform: `scale(${previewScale})`,
        transformOrigin: 'top center'
      }"
    >
      <!-- Loading State -->
      <div v-if="isTemplateLoading" class="p-16">
        <!-- Header Skeleton -->
        <div class="flex items-center gap-6 mb-8 border-b-2 border-slate-100 pb-8">
            <UiSkeletonLoader type="avatar" />
            <div class="flex-1 space-y-2">
            <UiSkeletonLoader type="text" height="2rem" width="60%" />
            <UiSkeletonLoader type="text" width="40%" />
            </div>
        </div>
        
        <!-- Body Skeleton -->
        <div class="space-y-6">
        <UiSkeletonLoader type="text" :count="3" />
        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-4">
            <UiSkeletonLoader type="text" height="1.5rem" width="30%" />
            <UiSkeletonLoader type="text" :count="2" />
            <UiSkeletonLoader type="text" height="1.5rem" width="30%" class-name="mt-4" />
            <UiSkeletonLoader type="text" :count="2" />
            </div>
            <div class="space-y-4">
            <UiSkeletonLoader type="text" height="1.5rem" width="50%" />
            <UiSkeletonLoader type="text" :count="4" />
            </div>
        </div>
        </div>
      </div>
      
      <!-- Dynamic Template Component - Page 1 -->
      <div v-else-if="currentTemplateComponent" class="resume-content p-16">
        <div v-memo="[page1Data, currentThemeConfig, currentTemplateComponent, selectedTemplate]">
        <ClientOnly>
            <Transition name="fade" mode="out-in">
            <component 
                :key="selectedTemplate + '-page1'"
                :is="currentTemplateComponent"
                :data="page1Data"
                :theme="currentThemeConfig"
            />
            </Transition>
        </ClientOnly>
        </div>
      </div>
      
      <!-- Fallback Loading State -->
      <div v-else class="p-16">
          <UiSkeletonLoader type="text" :count="5" />
      </div>
    </div>

    <!-- Page 2 (Conditional) -->
    <div 
      v-if="needsSecondPage && currentTemplateComponent && page2Data"
      class="resume-paper bg-white shadow-2xl transition-all duration-300"
      :style="{ 
        width: '210mm',
        minHeight: '297mm',
        height: 'auto',
        transform: `scale(${previewScale})`,
        transformOrigin: 'top center'
      }"
    >
      <div class="resume-content p-16">
        <!-- Page Indicator -->
        <div class="text-right text-xs text-slate-400 mb-6 font-medium">Page 2</div>
        
        <div v-memo="[page2Data, currentThemeConfig, currentTemplateComponent, selectedTemplate]">
          <ClientOnly>
            <Transition name="fade" mode="out-in">
              <component 
                :key="selectedTemplate + '-page2'"
                :is="currentTemplateComponent"
                :data="page2Data"
                :theme="currentThemeConfig"
              />
            </Transition>
          </ClientOnly>
        </div>
      </div>
    </div>

    <!-- Preview Controls -->
    <div v-if="showControls" class="preview-controls flex items-center justify-center gap-4 mt-6">
      <button 
        @click="adjustZoom(-0.1)" 
        class="zoom-btn w-10 h-10 flex items-center justify-center bg-white border-2 border-slate-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 transition-all"
        title="Zoom Out"
      >
        <i class="fa-solid fa-minus text-slate-600"></i>
      </button>
      <span class="zoom-level text-sm font-bold text-slate-600 min-w-[4rem] text-center">
        {{ Math.round(previewScale * 100) }}%
      </span>
      <button 
        @click="adjustZoom(0.1)" 
        class="zoom-btn w-10 h-10 flex items-center justify-center bg-white border-2 border-slate-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 transition-all"
        title="Zoom In"
      >
        <i class="fa-solid fa-plus text-slate-600"></i>
      </button>
    </div>
  </div>
</template>

<style scoped>
.resume-paper {
  transition: height 0.3s ease, min-height 0.3s ease;
}

/* Fade transition for template switching */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
