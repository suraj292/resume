<script setup>
const route = useRoute()
const config = useRuntimeConfig()

// Fetch blog post with SSR for SEO
const { data: post, pending } = await useFetch(`/api/blog/${route.params.id}`, {
  baseURL: config.public.apiBase
})

useHead({
  title: computed(() => post.value?.title || 'Blog Post'),
  meta: [
    { name: 'description', content: computed(() => post.value?.excerpt || 'Read our latest blog post') }
  ]
})
</script>

<template>
  <div class="flex-grow">
    <!-- Loading State -->
    <div v-if="pending" class="container mx-auto px-6 py-20 text-center">
      <i class="fa-solid fa-circle-notch fa-spin text-4xl text-indigo-600"></i>
      <p class="text-slate-600 mt-4">Loading article...</p>
    </div>

    <!-- Blog Post Content -->
    <article v-else-if="post" class="container mx-auto px-6 py-20">
      <div class="max-w-3xl mx-auto">
        <!-- Back Button -->
        <NuxtLink to="/blog" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 mb-8">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Back to Blog</span>
        </NuxtLink>

        <!-- Post Header -->
        <h1 class="text-4xl font-bold text-slate-900 mb-4">{{ post.title }}</h1>
        <div class="flex items-center gap-4 text-slate-600 mb-8">
          <div class="flex items-center gap-2">
            <i class="fa-regular fa-calendar"></i>
            <span>{{ post.published_at || 'Recent' }}</span>
          </div>
          <div class="flex items-center gap-2">
            <i class="fa-regular fa-clock"></i>
            <span>5 min read</span>
          </div>
        </div>

        <!-- Featured Image -->
        <div class="aspect-video bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl mb-8 flex items-center justify-center">
          <i class="fa-solid fa-newspaper text-6xl text-indigo-600"></i>
        </div>

        <!-- Post Content -->
        <div class="prose prose-lg max-w-none" v-html="post.content || post.excerpt"></div>
      </div>
    </article>

    <!-- Error State -->
    <div v-else class="container mx-auto px-6 py-20 text-center">
      <i class="fa-solid fa-exclamation-circle text-6xl text-red-500 mb-4"></i>
      <h2 class="text-2xl font-bold text-slate-900 mb-2">Post Not Found</h2>
      <p class="text-slate-600 mb-8">The blog post you're looking for doesn't exist.</p>
      <NuxtLink to="/blog" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition-colors inline-block">
        Back to Blog
      </NuxtLink>
    </div>
  </div>
</template>
