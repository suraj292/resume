<script setup>
// No imports needed - components are auto-imported
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <CommonNavbar />
    <main class="flex-grow pt-20">
      <slot />
    </main>
    <CommonFooter />
  </div>
</template>
