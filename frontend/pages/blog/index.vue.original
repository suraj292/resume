<script setup>
import { ref } from 'vue'

const searchQuery = ref('')

const categories = [
  { name: 'Resume Tips', count: 24 },
  { name: 'ATS Secrets', count: 18 },
  { name: 'Career Advice', count: 32 },
  { name: 'Cover Letters', count: 12 }
]

const featuredPost = {
  title: '10 Hidden Keywords That Will Triple Your Interview Chances in 2024',
  excerpt: 'Stop guessing what recruiters want. We analyzed 100,000 job descriptions to find the power words that consistently beat the ATS algorithms.',
  category: 'ATS Strategy',
  author: 'Sarah Jenkins',
  date: 'Oct 24, 2023',
  readTime: '8 min read',
  image: 'https://picsum.photos/seed/resume/1200/600'
}

const blogPosts = [
  {
    id: 1,
    title: 'How to Explain Employment Gaps on Your Resume',
    excerpt: "Don't let a career break hurt your chances. Here are 5 ATS-friendly ways to frame your time off positively.",
    category: 'Guides',
    readTime: '5 min read',
    date: 'Oct 20, 2023',
    image: 'https://picsum.photos/seed/work/600/400'
  },
  {
    id: 2,
    title: 'Best Fonts for Resumes: What Recruiters Actually Read',
    excerpt: 'Is Times New Roman dead? We rank the top 10 fonts for readability and parsing compatibility.',
    category: 'Templates',
    readTime: '4 min read',
    date: 'Oct 18, 2023',
    image: 'https://picsum.photos/seed/office/600/400'
  },
  {
    id: 3,
    title: 'Resume vs CV: Which One Do You Really Need?',
    excerpt: 'The definitive guide to understanding the differences and when to use each format.',
    category: 'Tech',
    readTime: '6 min read',
    date: 'Oct 15, 2023',
    image: 'https://picsum.photos/seed/tech/600/400'
  },
  {
    id: 4,
    title: 'Action Verbs List: 100+ Words to Replace "Responsible For"',
    excerpt: 'Stop using passive language. Energize your bullet points with these powerful action verbs.',
    category: 'Writing',
    readTime: '3 min read',
    date: 'Oct 10, 2023',
    image: 'https://picsum.photos/seed/writing/600/400'
  }
]

const trendingPosts = [
  {
    title: '5 Things You Should Remove From Your Resume Immediately',
    date: 'Oct 22, 2023',
    image: 'https://picsum.photos/seed/trend1/100/100'
  },
  {
    title: 'How to Beat the Applicant Tracking System',
    date: 'Oct 19, 2023',
    image: 'https://picsum.photos/seed/trend2/100/100'
  }
]
</script>

<template>
  <div class="flex-grow">
    
    <!-- BLOG HERO -->
    <section class="bg-white border-b border-slate-100 pt-16 pb-16 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-float"></div>
      <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-float" style="animation-delay: 2s"></div>

      <div class="container mx-auto px-6 text-center relative z-10 animate-fade-in">
        <span class="inline-block py-1 px-3 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-4">The Career Blog</span>
        <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 mb-6">
          Resume Tips, <span class="text-indigo-600">ATS Insights</span> & Career Advice
        </h1>
        <p class="text-slate-500 text-lg max-w-2xl mx-auto">
          Master the art of job hunting with expert guides on building resumes that pass the bots and impress the humans.
        </p>
      </div>
    </section>

    <!-- FEATURED POST -->
    <section class="container mx-auto px-6 py-12">
      <article class="relative group rounded-3xl overflow-hidden shadow-lg animate-slide-up hover:shadow-2xl transition-all duration-300">
        <router-link to="/blog/1" class="block relative h-[400px] md:h-[500px]">
          <img :src="featuredPost.image" alt="Featured Post" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent opacity-90"></div>
          <div class="absolute bottom-0 left-0 p-8 md:p-12 w-full md:w-2/3">
            <span class="inline-block px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded-lg mb-4">{{ featuredPost.category }}</span>
            <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-4 leading-tight group-hover:text-indigo-200 transition-colors">
              {{ featuredPost.title }}
            </h2>
            <p class="text-slate-300 mb-6 line-clamp-2">
              {{ featuredPost.excerpt }}
            </p>
            <div class="flex items-center gap-3 text-white/80 text-sm">
              <img src="https://ui-avatars.com/api/?name=Sarah+J&background=6366f1&color=fff" class="w-8 h-8 rounded-full">
              <span class="font-semibold">{{ featuredPost.author }}</span>
              <span>•</span>
              <span>{{ featuredPost.date }}</span>
              <span>•</span>
              <span>{{ featuredPost.readTime }}</span>
            </div>
          </div>
        </router-link>
      </article>
    </section>

    <section class="container mx-auto px-6 py-8">
      <div class="grid lg:grid-cols-12 gap-12">

        <!-- BLOG GRID -->
        <div class="lg:col-span-8">
          <div class="grid md:grid-cols-2 gap-8 mb-12">

            <!-- Blog Posts -->
            <article 
              v-for="(post, index) in blogPosts" 
              :key="post.id"
              class="blog-card bg-white rounded-2xl overflow-hidden border border-slate-100 flex flex-col h-full animate-slide-up"
              :style="{ animationDelay: `${(index % 2) * 0.1}s` }"
            >
              <router-link :to="`/blog/${post.id}`" class="block h-48 overflow-hidden relative">
                <img :src="post.image" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                <span class="absolute top-4 left-4 bg-white/90 backdrop-blur text-slate-800 text-xs font-bold px-3 py-1 rounded-full">{{ post.category }}</span>
              </router-link>
              <div class="p-6 flex flex-col flex-grow">
                <h3 class="text-xl font-bold text-slate-900 mb-3 hover:text-indigo-600 transition-colors">
                  <router-link :to="`/blog/${post.id}`">{{ post.title }}</router-link>
                </h3>
                <p class="text-slate-500 text-sm mb-4 line-clamp-2 flex-grow">
                  {{ post.excerpt }}
                </p>
                <div class="flex items-center justify-between text-xs text-slate-400 mt-auto pt-4 border-t border-slate-50">
                  <span>{{ post.readTime }}</span>
                  <span>{{ post.date }}</span>
                </div>
              </div>
            </article>

            <!-- Ad Placeholder (after 2 posts) -->
            <div class="md:col-span-2 my-4 animate-fade-in">
              <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 flex flex-col md:flex-row items-center gap-6 relative overflow-hidden">
                <span class="absolute top-0 right-0 bg-slate-200 text-slate-500 text-[10px] px-2 py-0.5 rounded-bl">Sponsored</span>
                <div class="w-full md:w-1/3 h-32 bg-slate-200 rounded-lg flex items-center justify-center text-slate-400">
                  <i class="fa-regular fa-image text-3xl"></i>
                </div>
                <div class="w-full md:w-2/3 text-center md:text-left">
                  <h4 class="font-bold text-slate-800 mb-2">Master Your Interview Skills</h4>
                  <p class="text-sm text-slate-500 mb-4">Join our partner platform for mock interviews with real FAANG engineers.</p>
                  <button class="text-xs font-bold text-indigo-600 uppercase tracking-wide border border-indigo-600 px-4 py-2 rounded hover:bg-indigo-600 hover:text-white transition-colors">Learn More</button>
                </div>
              </div>
            </div>

          </div>

          <!-- PAGINATION -->
          <div class="flex justify-center items-center gap-2 mt-12 animate-fade-in">
            <button class="w-10 h-10 rounded-lg border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all flex items-center justify-center">
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="w-10 h-10 rounded-lg bg-indigo-600 text-white font-bold shadow-md">1</button>
            <button class="w-10 h-10 rounded-lg border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 hover:border-indigo-200 transition-all">2</button>
            <button class="w-10 h-10 rounded-lg border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 hover:border-indigo-200 transition-all">3</button>
            <span class="text-slate-400">...</span>
            <button class="w-10 h-10 rounded-lg border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 hover:border-indigo-200 transition-all">12</button>
            <button class="w-10 h-10 rounded-lg border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all flex items-center justify-center">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>

        <!-- SIDEBAR -->
        <aside class="lg:col-span-4 space-y-8 animate-slide-up" style="animation-delay: 0.2s">

          <!-- Search -->
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-900 mb-4">Search</h4>
            <div class="relative">
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Search articles..."
                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
              >
              <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
          </div>

          <!-- Categories -->
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-900 mb-4">Categories</h4>
            <ul class="space-y-2">
              <li v-for="category in categories" :key="category.name">
                <a href="#" class="flex justify-between items-center text-slate-600 hover:text-indigo-600 transition-colors group">
                  <span class="group-hover:translate-x-1 transition-transform">{{ category.name }}</span>
                  <span class="bg-slate-100 text-xs px-2 py-0.5 rounded-full text-slate-500">{{ category.count }}</span>
                </a>
              </li>
            </ul>
          </div>

          <!-- Ad Placeholder -->
          <div class="bg-slate-100 rounded-2xl h-[300px] flex flex-col items-center justify-center text-slate-400 relative overflow-hidden border border-slate-200">
            <span class="absolute top-2 right-2 text-[10px] bg-white/50 px-2 py-0.5 rounded text-slate-500">Sponsored</span>
            <i class="fa-regular fa-image text-4xl mb-2"></i>
            <span class="text-sm">Ad Space (300x250)</span>
          </div>

          <!-- Trending Posts -->
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-900 mb-4">Trending Now</h4>
            <div class="space-y-4">
              <router-link 
                v-for="(post, index) in trendingPosts" 
                :key="index"
                to="/blog/1" 
                class="flex gap-4 group"
              >
                <div class="w-16 h-16 rounded-lg bg-slate-200 overflow-hidden flex-shrink-0">
                  <img :src="post.image" class="w-full h-full object-cover">
                </div>
                <div>
                  <h5 class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors line-clamp-2">
                    {{ post.title }}
                  </h5>
                  <span class="text-xs text-slate-400 mt-1 block">{{ post.date }}</span>
                </div>
              </router-link>
            </div>
          </div>

        </aside>
      </div>
    </section>

  </div>
</template>

<style scoped>
.blog-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.blog-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
