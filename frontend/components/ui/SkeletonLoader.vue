<script setup lang="ts">
interface Props {
  type?: 'text' | 'avatar' | 'image' | 'button' | 'card'
  width?: string
  height?: string
  count?: number
  className?: string
}

withDefaults(defineProps<Props>(), {
  type: 'text',
  width: '100%',
  height: 'auto',
  count: 1,
  className: ''
})
</script>

<template>
  <div class="animate-pulse space-y-3" :class="className">
    <template v-for="n in count" :key="n">
      
      <!-- Text Line -->
      <div v-if="type === 'text'" 
        class="bg-slate-200 rounded" 
        :style="{ width, height: height === 'auto' ? '1rem' : height }">
      </div>

      <!-- Avatar Circle -->
      <div v-if="type === 'avatar'" 
        class="bg-slate-200 rounded-full flex-shrink-0" 
        :style="{ width: width === '100%' ? '3rem' : width, height: height === 'auto' ? '3rem' : height }">
      </div>

      <!-- Image/Card Placeholder -->
      <div v-if="type === 'image' || type === 'card'" 
        class="bg-slate-200 rounded-lg" 
        :style="{ width, height: height === 'auto' ? '12rem' : height }">
      </div>

      <!-- Button Placeholder -->
      <div v-if="type === 'button'" 
        class="bg-slate-200 rounded-md" 
        :style="{ width: width === '100%' ? '8rem' : width, height: height === 'auto' ? '2.5rem' : height }">
      </div>

    </template>
  </div>
</template>
