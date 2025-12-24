<script setup>
import Navbar from './components/common/Navbar.vue'
import Footer from './components/common/Footer.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <Navbar />
    <main class="flex-grow pt-20">
      <RouterView />
    </main>
    <Footer />
  </div>
</template>

<style scoped>
/* App-level styles */
</style>
