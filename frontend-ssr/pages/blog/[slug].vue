<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const article = ref({
  title: '10 Hidden Keywords That Will Triple Your Interview Chances in 2024',
  category: 'ATS Strategy',
  author: {
    name: 'Sarah Jenkins',
    bio: 'Sarah is a former HR Director at a Fortune 500 company turned Career Coach. She helps professionals navigate the modern hiring landscape.',
    avatar: 'https://ui-avatars.com/api/?name=Sarah+J&background=6366f1&color=fff&size=128'
  },
  date: 'Oct 24, 2023',
  readTime: '8 min read',
  image: 'https://picsum.photos/seed/resume/1200/600',
  content: `
    <p class="lead text-xl text-slate-500 font-light mb-8">
      Most job seekers don't realize that their resume isn't read by a human first. It's read by a bot. And that bot is looking for very specific signals.
    </p>

    <h2 id="section-1">Why Keywords Matter</h2>
    <p>
      Applicant Tracking Systems (ATS) are designed to filter out unqualified candidates. They do this by scanning for keywords found in the job description. If your resume lacks these specific terms, you might be rejected instantly, regardless of your actual qualifications.
    </p>
    <p>
      However, not all keywords are created equal. Some are "hard skills" (like Python, SQL, or Figma), while others are "soft skills" or action verbs that demonstrate impact.
    </p>

    <h2 id="section-2">The Top 5 Action Verbs</h2>
    <p>Stop using "Responsible for" or "Managed". These words are passive. Instead, use words that imply result and ownership:</p>
    <ul>
      <li><strong>Spearheaded:</strong> Shows leadership and initiative.</li>
      <li><strong>Orchestrated:</strong> Implies handling complexity.</li>
      <li><strong>Accelerated:</strong> Demonstrates efficiency and speed.</li>
      <li><strong>Optimized:</strong> Shows you improved a process.</li>
      <li><strong>Revitalized:</strong> Great for turning around failing projects.</li>
    </ul>

    <blockquote>
      "The best resumes don't just list duties; they tell a story of impact using data and strong verbs."
    </blockquote>

    <h2 id="section-3">Hard Skills You Might Be Missing</h2>
    <p>
      Depending on your industry, there are "hidden" technical keywords that recruiters search for but often forget to list explicitly in the requirements.
    </p>
    <p>
      For example, in project management, terms like <strong>"Stakeholder Management"</strong>, <strong>"Risk Mitigation"</strong>, and <strong>"Agile Methodologies"</strong> are gold dust.
    </p>

    <h2 id="section-4">How to Integrate Them Naturally</h2>
    <p>
      Don't just keyword stuff. A section at the bottom listing 50 words looks suspicious to both bots and humans. Instead, weave them into your bullet points.
    </p>
    <p>
      <em>Bad:</em> "Used Python."<br>
      <em>Good:</em> "Optimized data processing scripts using <strong>Python</strong>, reducing runtime by 40%."
    </p>

    <h2 id="section-5">Conclusion</h2>
    <p>
      Optimizing for ATS doesn't mean writing like a robot. It means ensuring the language you use to describe your human achievements aligns with the language the machine is programmed to value.
    </p>
  `
})

const tableOfContents = [
  { id: 'section-1', label: 'Why Keywords Matter' },
  { id: 'section-2', label: 'Top 5 Action Verbs' },
  { id: 'section-3', label: 'Missing Hard Skills' },
  { id: 'section-4', label: 'Natural Integration' },
  { id: 'section-5', label: 'Conclusion' }
]

const relatedPosts = [
  {
    title: 'Resume Design Trends 2024',
    date: 'Oct 12',
    readTime: '5 min read',
    image: 'https://picsum.photos/seed/rel1/400/300'
  },
  {
    title: 'How to Write a Cover Letter',
    date: 'Sep 28',
    readTime: '7 min read',
    image: 'https://picsum.photos/seed/rel2/400/300'
  },
  {
    title: 'Remote Work Resume Tips',
    date: 'Sep 15',
    readTime: '4 min read',
    image: 'https://picsum.photos/seed/rel3/400/300'
  }
]

const comments = ref([
  {
    author: 'Mark D.',
    time: '2 hours ago',
    text: 'Great tip about the action verbs! I always struggle with that.'
  }
])

const newComment = ref('')

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

const scrollToSection = (id) => {
  const element = document.getElementById(id)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

onMounted(() => {
  // Smooth scroll for TOC
  const links = document.querySelectorAll('.toc-link')
  links.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault()
      const id = link.getAttribute('href').substring(1)
      scrollToSection(id)
    })
  })
})
</script>

<template>
  <div class="flex-grow">

    <!-- BLOG HEADER -->
    <section class="bg-white pt-20 pb-12 border-b border-slate-100">
      <div class="container mx-auto px-6 max-w-5xl animate-fade-in">
        <div class="text-center mb-8">
          <NuxtLink to="/blog" class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold uppercase tracking-wide mb-4 hover:bg-indigo-100 transition-colors">
            {{ article.category }}
          </NuxtLink>
          <h1 class="text-3xl md:text-5xl font-display font-bold text-slate-900 mb-6 leading-tight">
            {{ article.title }}
          </h1>

          <div class="flex items-center justify-center gap-6 text-sm text-slate-500">
            <div class="flex items-center gap-2">
              <img :src="article.author.avatar" class="w-8 h-8 rounded-full">
              <span class="font-semibold text-slate-700">{{ article.author.name }}</span>
            </div>
            <span>{{ article.date }}</span>
            <span>{{ article.readTime }}</span>
          </div>
        </div>

        <div class="w-full h-[400px] md:h-[500px] rounded-3xl overflow-hidden shadow-xl">
          <img :src="article.image" class="w-full h-full object-cover">
        </div>
      </div>
    </section>

    <section class="container mx-auto px-6 py-12">
      <div class="grid lg:grid-cols-12 gap-12 max-w-6xl mx-auto">

        <!-- CONTENT AREA -->
        <article class="lg:col-span-8 prose prose-lg prose-indigo max-w-none text-slate-600 animate-fade-in" style="animation-delay: 0.1s">

          <!-- Inline Ad (Top) -->
          <div class="my-8 bg-slate-50 border border-slate-200 rounded-xl p-6 flex flex-col items-center justify-center text-center relative overflow-hidden not-prose">
            <span class="absolute top-2 right-2 text-[10px] text-slate-400 border border-slate-200 px-1 rounded">Sponsored</span>
            <div class="w-full h-32 bg-slate-200 rounded mb-3 flex items-center justify-center text-slate-400">
              <i class="fa-solid fa-rectangle-ad text-4xl"></i>
            </div>
            <p class="text-sm font-bold text-slate-700">Get 1-on-1 Career Coaching</p>
            <a href="#" class="text-indigo-600 text-xs font-bold hover:underline">Learn More</a>
          </div>

          <!-- Article Content -->
          <div v-html="article.content"></div>

          <!-- CTA Ad -->
          <div class="my-10 p-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl not-prose">
            <div class="bg-white rounded-lg p-6 flex items-center justify-between gap-4">
              <div>
                <h4 class="font-bold text-slate-900 text-lg">Is your resume missing these?</h4>
                <p class="text-sm text-slate-500">Run a free ATS scan now to find out.</p>
              </div>
              <NuxtLink to="/ats-checker" class="px-6 py-2 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors whitespace-nowrap">
                Check Score
              </NuxtLink>
            </div>
          </div>

          <!-- AUTHOR BOX -->
          <div class="mt-12 not-prose bg-indigo-50 rounded-2xl p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6 border border-indigo-100">
            <img :src="article.author.avatar" class="w-20 h-20 rounded-full border-4 border-white shadow-sm">
            <div class="text-center sm:text-left">
              <h3 class="text-lg font-bold text-slate-900 mb-2">About {{ article.author.name }}</h3>
              <p class="text-slate-600 text-sm mb-4">{{ article.author.bio }}</p>
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
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 sticky top-24">
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
    <section class="bg-slate-50 py-16 border-t border-slate-200">
      <div class="container mx-auto px-6 max-w-6xl">
        <h3 class="text-2xl font-bold text-slate-900 mb-8">Read Next</h3>
        <div class="grid md:grid-cols-3 gap-8">
          <div 
            v-for="(post, index) in relatedPosts" 
            :key="index"
            class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group"
          >
            <div class="h-48 overflow-hidden">
              <img :src="post.image" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
            </div>
            <div class="p-5">
              <h4 class="font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">
                {{ post.title }}
              </h4>
              <p class="text-xs text-slate-500">{{ post.date }} • {{ post.readTime }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

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
</style>
