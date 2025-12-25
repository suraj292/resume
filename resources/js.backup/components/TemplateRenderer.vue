<template>
  <div :class="mainClass" class="w-full h-full flex">
    <div 
      v-for="(element, index) in elements" 
      :key="index"
      :class="element.class"
    >
      <TemplateRenderer 
        v-if="element.children" 
        :elements="element.children"
        mainClass=""
      />
    </div>
  </div>
</template>

<script setup>
defineProps({
  elements: {
    type: Array,
    default: () => []
  },
  mainClass: {
    type: String,
    default: ''
  }
})
</script>
