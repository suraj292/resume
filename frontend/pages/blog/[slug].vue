<script setup>
import { ref, onMounted, computed } from 'vue'

const route = useRoute()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// State
const article = ref(null)
const relatedPosts = ref([])
const loading = ref(true)
const error = ref(null)
const comments = ref([])
const newComment = ref('')

// Fetch blog post
const fetchBlog = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await fetch(`${apiBase}/api/blogs/${route.params.slug}`)
    
    if (!response.ok) {
      if (response.status === 404) {
        error.value = 'Blog post not found'
      } else {
        error.value = 'Failed to load blog post'
      }
      return
    }
    
    const data = await response.json()
    article.value = data
    
    // Fetch related posts from same category
    if (data.category_id) {
      await fetchRelatedPosts(data.category_id, data.id)
    }
  } catch (err) {
    console.error('Error fetching blog:', err)
    error.value = 'Failed to load blog post. Please try again later.'
  } finally {
    loading.value = false
  }
}

// Fetch related posts
const fetchRelatedPosts = async (categoryId, excludeId) => {
  try {
    const response = await fetch(`${apiBase}/api/blogs?category=${categoryId}&per_page=3`)
    const data = await response.json()
    
    // Filter out current post
    relatedPosts.value = (data.data || []).filter(post => post.id !== excludeId).slice(0, 3)
  } catch (err) {
    console.error('Error fetching related posts:', err)
  }
}

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

// Get image URL
const getImageUrl = (imagePath) => {
  if (!imagePath) return 'https://picsum.photos/seed/default/1200/600'
  if (imagePath.startsWith('http')) return imagePath
  return `${apiBase}/storage/${imagePath}`
}

// Extract table of contents from HTML content
const tableOfContents = computed(() => {
  if (!article.value?.content) return []
  
  const parser = new DOMParser()
  const doc = parser.parseFromString(article.value.content, 'text/html')
  const headings = doc.querySelectorAll('h2, h3')
  
  return Array.from(headings).map((heading, index) => {
    const id = heading.id || `section-${index + 1}`
    heading.id = id // Ensure ID is set
    return {
      id,
      label: heading.textContent,
      level: heading.tagName.toLowerCase()
    }
  })
})

// Post comment (placeholder - you can implement this later)
const postComment = () => {
  if (newComment.value.trim()) {
    comments.value.push({
      author: 'You',
      time: 'Just now',
      text: newComment.value
    })
    newComment.value = ''
  }
}

// Scroll to section
const scrollToSection = (id) => {
  const element = document.getElementById(id)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

// Initialize
onMounted(async () => {
  await fetchBlog()
  
  // Smooth scroll for TOC
  setTimeout(() => {
    const links = document.querySelectorAll('.toc-link')
    links.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault()
        const id = link.getAttribute('href').substring(1)
        scrollToSection(id)
      })
    })
  }, 500)
})

// Update meta tags
useHead(() => {
  if (!article.value) return {}
  
  return {
    title: article.value.meta_title || article.value.title,
    meta: [
      { name: 'description', content: article.value.meta_description || article.value.excerpt },
      { name: 'keywords', content: article.value.meta_keywords || '' },
      { property: 'og:title', content: article.value.meta_title || article.value.title },
      { property: 'og:description', content: article.value.meta_description || article.value.excerpt },
      { property: 'og:image', content: getImageUrl(article.value.featured_image) },
      { property: 'og:type', content: 'article' },
      { name: 'twitter:card', content: 'summary_large_image' },
      { name: 'twitter:title', content: article.value.meta_title || article.value.title },
      { name: 'twitter:description', content: article.value.meta_description || article.value.excerpt },
      { name: 'twitter:image', content: getImageUrl(article.value.featured_image) }
    ]
  }
})
</script>

<template>
  <div class="flex-grow">

    <!-- Loading State -->
    <div v-if="loading" class="container mx-auto px-6 py-20">
      <div class="max-w-4xl mx-auto animate-pulse">
        <div class="h-8 bg-slate-200 rounded w-3/4 mb-4"></div>
        <div class="h-4 bg-slate-200 rounded w-1/2 mb-8"></div>
        <div class="h-96 bg-slate-200 rounded-3xl mb-8"></div>
        <div class="space-y-3">
          <div class="h-4 bg-slate-200 rounded"></div>
          <div class="h-4 bg-slate-200 rounded"></div>
          <div class="h-4 bg-slate-200 rounded w-5/6"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="container mx-auto px-6 py-20 text-center">
      <i class="fa-regular fa-circle-xmark text-6xl text-red-400 mb-4"></i>
      <h1 class="text-3xl font-bold text-slate-900 mb-4">{{ error }}</h1>
      <p class="text-slate-500 mb-8">The blog post you're looking for doesn't exist or has been removed.</p>
      <NuxtLink to="/blog" class="inline-block px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-colors">
        Back to Blog
      </NuxtLink>
    </div>

    <!-- Blog Content -->
    <template v-else-if="article">
      <!-- BLOG HEADER -->
      <section class="bg-white pt-20 pb-12 border-b border-slate-100">
        <div class="container mx-auto px-6 max-w-5xl animate-fade-in">
          <div class="text-center mb-8">
            <NuxtLink 
              to="/blog" 
              class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold uppercase tracking-wide mb-4 hover:bg-indigo-100 transition-colors"
            >
              {{ article.category?.name || 'Blog' }}
            </NuxtLink>
            <h1 class="text-3xl md:text-5xl font-display font-bold text-slate-900 mb-6 leading-tight">
              {{ article.title }}
            </h1>

            <div class="flex items-center justify-center gap-6 text-sm text-slate-500">
              <div class="flex items-center gap-2">
                <img 
                  :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(article.author || 'Author')}&background=6366f1&color=fff`" 
                  class="w-8 h-8 rounded-full"
                >
                <span class="font-semibold text-slate-700">{{ article.author || 'Anonymous' }}</span>
              </div>
              <span>{{ formatDate(article.published_at) }}</span>
              <span>{{ article.read_time }} min read</span>
              <span v-if="article.views_count" class="flex items-center gap-1">
                <i class="fa-regular fa-eye"></i>
                {{ article.views_count }} views
              </span>
            </div>
          </div>

          <div class="w-full h-[400px] md:h-[500px] rounded-3xl overflow-hidden shadow-xl">
            <img :src="getImageUrl(article.featured_image)" :alt="article.title" class="w-full h-full object-cover">
          </div>
        </div>
      </section>

      <section class="container mx-auto px-6 py-12">
        <div class="grid lg:grid-cols-12 gap-12 max-w-6xl mx-auto">

          <!-- CONTENT AREA -->
          <article class="lg:col-span-8 prose prose-lg prose-indigo max-w-none text-slate-600 animate-fade-in" style="animation-delay: 0.1s">

            <!-- Excerpt -->
            <p v-if="article.excerpt" class="lead text-xl text-slate-500 font-light mb-8">
              {{ article.excerpt }}
            </p>

            <!-- Article Content -->
            <div v-html="article.content"></div>

            <!-- CTA Ad -->
            <div class="my-10 p-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl not-prose">
              <div class="bg-white rounded-lg p-6 flex items-center justify-between gap-4">
                <div>
                  <h4 class="font-bold text-slate-900 text-lg">Ready to build your resume?</h4>
                  <p class="text-sm text-slate-500">Create a professional resume in minutes with our AI-powered builder.</p>
                </div>
                <NuxtLink to="/builder" class="px-6 py-2 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors whitespace-nowrap">
                  Get Started
                </NuxtLink>
              </div>
            </div>

            <!-- AUTHOR BOX -->
            <div v-if="article.author" class="mt-12 not-prose bg-indigo-50 rounded-2xl p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6 border border-indigo-100">
              <img 
                :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(article.author)}&background=6366f1&color=fff&size=128`" 
                class="w-20 h-20 rounded-full border-4 border-white shadow-sm"
              >
              <div class="text-center sm:text-left">
                <h3 class="text-lg font-bold text-slate-900 mb-2">About {{ article.author }}</h3>
                <p class="text-slate-600 text-sm mb-4">Content writer and career expert.</p>
                <div class="flex justify-center sm:justify-start gap-3">
                  <a href="#" class="w-8 h-8 rounded-full bg-white text-blue-600 flex items-center justify-center hover:scale-110 transition-transform shadow-sm">
                    <i class="fa-brands fa-linkedin-in"></i>
                  </a>
                  <a href="#" class="w-8 h-8 rounded-full bg-white text-sky-500 flex items-center justify-center hover:scale-110 transition-transform shadow-sm">
                    <i class="fa-brands fa-twitter"></i>
                  </a>
                </div>
              </div>
            </div>

            <!-- SHARE BUTTONS -->
            <div class="mt-8 pt-8 border-t border-slate-200 flex items-center justify-between not-prose">
              <span class="font-bold text-slate-700">Share this article:</span>
              <div class="flex gap-3">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition-transform hover:-translate-y-1">
                  <i class="fa-brands fa-linkedin mr-2"></i> LinkedIn
                </button>
                <button class="px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-bold hover:bg-sky-600 transition-transform hover:-translate-y-1">
                  <i class="fa-brands fa-twitter mr-2"></i> Twitter
                </button>
              </div>
            </div>

            <!-- COMMENTS -->
            <div class="mt-12 not-prose">
              <h3 class="text-xl font-bold text-slate-900 mb-6">Comments ({{ comments.length }})</h3>

              <div class="space-y-6">
                <!-- Comment List -->
                <div v-for="(comment, index) in comments" :key="index" class="flex gap-4">
                  <div class="w-10 h-10 rounded-full bg-slate-200 flex-shrink-0"></div>
                  <div class="bg-slate-50 p-4 rounded-xl flex-grow">
                    <div class="flex justify-between items-center mb-2">
                      <span class="font-bold text-sm text-slate-900">{{ comment.author }}</span>
                      <span class="text-xs text-slate-400">{{ comment.time }}</span>
                    </div>
                    <p class="text-sm text-slate-600">{{ comment.text }}</p>
                  </div>
                </div>

                <!-- Comment Form -->
                <div class="mt-8">
                  <textarea 
                    v-model="newComment"
                    class="w-full p-4 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none text-sm"
                    rows="3" 
                    placeholder="Leave a comment..."
                  ></textarea>
                  <button 
                    @click="postComment"
                    class="mt-3 px-6 py-2 bg-slate-900 text-white text-sm font-bold rounded-lg hover:bg-slate-800 transition-colors"
                  >
                    Post Comment
                  </button>
                </div>
              </div>
            </div>

          </article>

          <!-- SIDEBAR -->
          <aside class="lg:col-span-4 space-y-8">

            <!-- TABLE OF CONTENTS -->
            <div v-if="tableOfContents.length > 0" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 sticky top-24">
              <h4 class="font-bold text-slate-900 mb-4 uppercase text-xs tracking-wider">Table of Contents</h4>
              <ul class="space-y-3 text-sm border-l-2 border-slate-100 ml-1">
                <li v-for="item in tableOfContents" :key="item.id">
                  <a 
                    :href="`#${item.id}`"
                    class="toc-link block pl-4 text-slate-600 hover:text-indigo-600 hover:border-l-2 hover:border-indigo-600 -ml-[2px] transition-all"
                  >
                    {{ item.label }}
                  </a>
                </li>
              </ul>
            </div>

            <!-- Newsletter -->
            <div class="bg-indigo-600 p-6 rounded-2xl text-white text-center">
              <i class="fa-regular fa-paper-plane text-3xl mb-3 opacity-80"></i>
              <h4 class="font-bold text-lg mb-2">Weekly Career Tips</h4>
              <p class="text-indigo-100 text-xs mb-4">Join 50,000+ subscribers getting resume advice.</p>
              <input type="email" placeholder="Email address" class="w-full px-4 py-2 rounded-lg text-slate-900 text-sm mb-2 focus:outline-none">
              <button class="w-full py-2 bg-slate-900 rounded-lg text-sm font-bold hover:bg-slate-800 transition-colors">Subscribe</button>
            </div>

            <!-- Ad Sidebar -->
            <div class="bg-slate-100 rounded-2xl h-[400px] flex items-center justify-center text-slate-400 border border-slate-200 relative">
              <span class="absolute top-2 right-2 text-[10px] bg-white/50 px-2 py-0.5 rounded text-slate-500">Sponsored</span>
              <div class="text-center">
                <i class="fa-solid fa-ad text-3xl mb-2"></i>
                <p class="text-sm">Vertical Ad Unit</p>
              </div>
            </div>

          </aside>
        </div>
      </section>

      <!-- RELATED POSTS -->
      <section v-if="relatedPosts.length > 0" class="bg-slate-50 py-16 border-t border-slate-200">
        <div class="container mx-auto px-6 max-w-6xl">
          <h3 class="text-2xl font-bold text-slate-900 mb-8">Read Next</h3>
          <div class="grid md:grid-cols-3 gap-8">
            <NuxtLink
              v-for="(post, index) in relatedPosts" 
              :key="index"
              :to="`/blog/${post.slug}`"
              class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group"
            >
              <div class="h-48 overflow-hidden">
                <img :src="getImageUrl(post.featured_image)" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
              </div>
              <div class="p-5">
                <h4 class="font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">
                  {{ post.title }}
                </h4>
                <p class="text-xs text-slate-500">{{ formatDate(post.published_at) }} • {{ post.read_time }} min read</p>
              </div>
            </NuxtLink>
          </div>
        </div>
      </section>
    </template>

  </div>
</template>

<style scoped>
.prose {
  color: #334155;
}

.prose p {
  margin-bottom: 1.5rem;
  line-height: 1.8;
}

.prose h2 {
  margin-top: 2.5rem;
  margin-bottom: 1rem;
  font-size: 1.75rem;
  color: #0f172a;
  font-weight: 700;
}

.prose h3 {
  margin-top: 2rem;
  margin-bottom: 0.75rem;
  font-size: 1.4rem;
  color: #0f172a;
  font-weight: 600;
}

.prose ul {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-bottom: 1.5rem;
}

.prose li {
  margin-bottom: 0.5rem;
}

.prose strong {
  color: #0f172a;
}

.prose blockquote {
  border-left: 4px solid #6366f1;
  padding-left: 1rem;
  margin: 2rem 0;
  font-style: italic;
  color: #475569;
}

.prose img {
  border-radius: 0.5rem;
  margin: 2rem 0;
}

.prose a {
  color: #6366f1;
  text-decoration: underline;
}

.prose a:hover {
  color: #4f46e5;
}
</style>
