<script setup lang="ts">
import ToastContainer from '~/components/ui/ToastContainer.vue'
</script>

<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
    <ClientOnly>
      <ToastContainer />
    </ClientOnly>
  </div>
</template>
