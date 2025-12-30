<script setup>
import { ref, onMounted, computed } from 'vue'

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// State
const searchQuery = ref('')
const selectedCategory = ref(null)
const loading = ref(true)
const error = ref(null)

// Data
const featuredPost = ref(null)
const blogPosts = ref([])
const trendingPosts = ref([])
const categories = ref([])
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 12,
  total: 0
})

// Fetch featured post
const fetchFeaturedPost = async () => {
  try {
    const response = await fetch(`${apiBase}/api/blogs/featured`)
    const data = await response.json()
    if (data && data.length > 0) {
      featuredPost.value = data[0]
    }
  } catch (err) {
    console.error('Error fetching featured post:', err)
  }
}

// Fetch blog posts
const fetchBlogs = async (page = 1) => {
  loading.value = true
  error.value = null
  
  try {
    let url = `${apiBase}/api/blogs?page=${page}&per_page=12`
    
    if (searchQuery.value) {
      url += `&search=${encodeURIComponent(searchQuery.value)}`
    }
    
    if (selectedCategory.value) {
      url += `&category=${encodeURIComponent(selectedCategory.value)}`
    }
    
    const response = await fetch(url)
    const data = await response.json()
    
    blogPosts.value = data.data || []
    pagination.value = {
      current_page: data.current_page,
      last_page: data.last_page,
      per_page: data.per_page,
      total: data.total
    }
  } catch (err) {
    console.error('Error fetching blogs:', err)
    error.value = 'Failed to load blog posts. Please try again later.'
  } finally {
    loading.value = false
  }
}

// Fetch trending posts
const fetchTrendingPosts = async () => {
  try {
    const response = await fetch(`${apiBase}/api/blogs/trending`)
    const data = await response.json()
    trendingPosts.value = data || []
  } catch (err) {
    console.error('Error fetching trending posts:', err)
  }
}

// Fetch categories
const fetchCategories = async () => {
  try {
    const response = await fetch(`${apiBase}/api/blog-categories`)
    const data = await response.json()
    categories.value = data || []
  } catch (err) {
    console.error('Error fetching categories:', err)
  }
}

// Handle search
const handleSearch = () => {
  fetchBlogs(1)
}

// Handle category filter
const filterByCategory = (categorySlug) => {
  selectedCategory.value = categorySlug === selectedCategory.value ? null : categorySlug
  fetchBlogs(1)
}

// Handle pagination
const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchBlogs(page)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

// Get image URL
const getImageUrl = (imagePath) => {
  if (!imagePath) return 'https://picsum.photos/seed/default/600/400'
  if (imagePath.startsWith('http')) return imagePath
  return `${apiBase}/storage/${imagePath}`
}

// Computed
const hasNextPage = computed(() => pagination.value.current_page < pagination.value.last_page)
const hasPrevPage = computed(() => pagination.value.current_page > 1)

// Initialize
onMounted(async () => {
  await Promise.all([
    fetchFeaturedPost(),
    fetchBlogs(),
    fetchTrendingPosts(),
    fetchCategories()
  ])
})
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
    <section v-if="featuredPost" class="container mx-auto px-6 py-12">
      <article class="relative group rounded-3xl overflow-hidden shadow-lg animate-slide-up hover:shadow-2xl transition-all duration-300">
        <NuxtLink :to="`/blog/${featuredPost.slug}`" class="block relative h-[400px] md:h-[500px]">
          <img :src="getImageUrl(featuredPost.featured_image)" :alt="featuredPost.title" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent opacity-90"></div>
          <div class="absolute bottom-0 left-0 p-8 md:p-12 w-full md:w-2/3">
            <span class="inline-block px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded-lg mb-4">{{ featuredPost.category?.name || 'Featured' }}</span>
            <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-4 leading-tight group-hover:text-indigo-200 transition-colors">
              {{ featuredPost.title }}
            </h2>
            <p class="text-slate-300 mb-6 line-clamp-2">
              {{ featuredPost.excerpt }}
            </p>
            <div class="flex items-center gap-3 text-white/80 text-sm">
              <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(featuredPost.author || 'Author')}&background=6366f1&color=fff`" class="w-8 h-8 rounded-full">
              <span class="font-semibold">{{ featuredPost.author }}</span>
              <span>•</span>
              <span>{{ formatDate(featuredPost.published_at) }}</span>
              <span>•</span>
              <span>{{ featuredPost.read_time }} min read</span>
            </div>
          </div>
        </NuxtLink>
      </article>
    </section>

    <section class="container mx-auto px-6 py-8">
      <div class="grid lg:grid-cols-12 gap-12">

        <!-- BLOG GRID -->
        <div class="lg:col-span-8">
          <!-- Error Message -->
          <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ error }}
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="grid md:grid-cols-2 gap-8 mb-12">
            <div v-for="i in 4" :key="i" class="bg-white rounded-2xl overflow-hidden border border-slate-100 animate-pulse">
              <div class="h-48 bg-slate-200"></div>
              <div class="p-6">
                <div class="h-4 bg-slate-200 rounded mb-3"></div>
                <div class="h-4 bg-slate-200 rounded w-3/4 mb-4"></div>
                <div class="h-3 bg-slate-200 rounded w-1/2"></div>
              </div>
            </div>
          </div>

          <!-- Blog Posts -->
          <div v-else class="grid md:grid-cols-2 gap-8 mb-12">
            <article 
              v-for="(post, index) in blogPosts" 
              :key="post.id"
              class="blog-card bg-white rounded-2xl overflow-hidden border border-slate-100 flex flex-col h-full animate-slide-up"
              :style="{ animationDelay: `${(index % 2) * 0.1}s` }"
            >
              <NuxtLink :to="`/blog/${post.slug}`" class="block h-48 overflow-hidden relative">
                <img :src="getImageUrl(post.featured_image)" :alt="post.title" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                <span class="absolute top-4 left-4 bg-white/90 backdrop-blur text-slate-800 text-xs font-bold px-3 py-1 rounded-full">{{ post.category?.name || 'Uncategorized' }}</span>
              </NuxtLink>
              <div class="p-6 flex flex-col flex-grow">
                <h3 class="text-xl font-bold text-slate-900 mb-3 hover:text-indigo-600 transition-colors">
                  <NuxtLink :to="`/blog/${post.slug}`">{{ post.title }}</NuxtLink>
                </h3>
                <p class="text-slate-500 text-sm mb-4 line-clamp-2 flex-grow">
                  {{ post.excerpt }}
                </p>
                <div class="flex items-center justify-between text-xs text-slate-400 mt-auto pt-4 border-t border-slate-50">
                  <span>{{ post.read_time }} min read</span>
                  <span>{{ formatDate(post.published_at) }}</span>
                </div>
              </div>
            </article>

            <!-- No Results -->
            <div v-if="!loading && blogPosts.length === 0" class="md:col-span-2 text-center py-12">
              <i class="fa-regular fa-folder-open text-4xl text-slate-300 mb-4"></i>
              <p class="text-slate-500">No blog posts found. Try adjusting your search or filters.</p>
            </div>

            <!-- Ad Placeholder (after 2 posts) -->
            <div v-if="blogPosts.length >= 2" class="md:col-span-2 my-4 animate-fade-in">
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
          <div v-if="!loading && blogPosts.length > 0" class="flex justify-center items-center gap-2 mt-12 animate-fade-in">
            <button 
              @click="goToPage(pagination.current_page - 1)"
              :disabled="!hasPrevPage"
              class="w-10 h-10 rounded-lg border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            
            <template v-for="page in pagination.last_page" :key="page">
              <button 
                v-if="page === 1 || page === pagination.last_page || Math.abs(page - pagination.current_page) <= 1"
                @click="goToPage(page)"
                :class="[
                  'w-10 h-10 rounded-lg font-bold transition-all',
                  page === pagination.current_page 
                    ? 'bg-indigo-600 text-white shadow-md' 
                    : 'border border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-indigo-200'
                ]"
              >
                {{ page }}
              </button>
              <span v-else-if="Math.abs(page - pagination.current_page) === 2" class="text-slate-400">...</span>
            </template>
            
            <button 
              @click="goToPage(pagination.current_page + 1)"
              :disabled="!hasNextPage"
              class="w-10 h-10 rounded-lg border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
            >
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
                @keyup.enter="handleSearch"
                type="text" 
                placeholder="Search articles..."
                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
              >
              <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
            <button 
              @click="handleSearch"
              class="w-full mt-3 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-colors"
            >
              Search
            </button>
          </div>

          <!-- Categories -->
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-900 mb-4">Categories</h4>
            <ul class="space-y-2">
              <li v-for="category in categories" :key="category.id">
                <button 
                  @click="filterByCategory(category.slug)"
                  :class="[
                    'flex justify-between items-center w-full text-left transition-colors group',
                    selectedCategory === category.slug ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600'
                  ]"
                >
                  <span class="group-hover:translate-x-1 transition-transform">{{ category.name }}</span>
                  <span class="bg-slate-100 text-xs px-2 py-0.5 rounded-full text-slate-500">{{ category.blogs_count || 0 }}</span>
                </button>
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
          <div v-if="trendingPosts.length > 0" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h4 class="font-bold text-slate-900 mb-4">Trending Now</h4>
            <div class="space-y-4">
              <NuxtLink 
                v-for="(post, index) in trendingPosts" 
                :key="index"
                :to="`/blog/${post.slug}`" 
                class="flex gap-4 group"
              >
                <div class="w-16 h-16 rounded-lg bg-slate-200 overflow-hidden flex-shrink-0">
                  <img :src="getImageUrl(post.featured_image)" :alt="post.title" class="w-full h-full object-cover">
                </div>
                <div>
                  <h5 class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors line-clamp-2">
                    {{ post.title }}
                  </h5>
                  <span class="text-xs text-slate-400 mt-1 block">{{ formatDate(post.published_at) }}</span>
                </div>
              </NuxtLink>
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
  line-clamp: 2;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
