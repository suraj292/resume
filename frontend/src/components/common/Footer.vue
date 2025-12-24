<template>
  <footer class="bg-slate-950 text-slate-400 py-12 border-t border-slate-800">
    <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
      <div class="flex items-center gap-2 group cursor-pointer">
        <div class="w-6 h-6 rounded bg-indigo-600 flex items-center justify-center text-white text-xs group-hover:rotate-12 transition-transform">
          <i class="fa-solid fa-layer-group"></i>
        </div>
        <span class="font-bold text-white group-hover:text-indigo-400 transition-colors">ResumeAI</span>
      </div>
      <div class="flex gap-6 text-sm">
        <router-link to="/" class="hover:text-white transition-colors">About</router-link>
        <router-link to="/pricing" class="hover:text-white transition-colors">Pricing</router-link>
        <a href="#" class="hover:text-white transition-colors">Privacy</a>
        <a href="#" class="hover:text-white transition-colors">Terms</a>
      </div>
      <div class="text-sm">
        &copy; 2023 ResumeAI. All rights reserved.
      </div>
    </div>
  </footer>
</template>
