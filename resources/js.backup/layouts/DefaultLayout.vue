<script setup>
import Navbar from '../components/common/Navbar.vue'
import Footer from '../components/common/Footer.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <Navbar />
    <main class="flex-grow">
      <slot />
    </main>
    <Footer />
  </div>
</template>
