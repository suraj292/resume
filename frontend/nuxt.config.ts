// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },

  devServer: {
    host: '127.0.0.1',
    port: 5174
  },

  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@vite-pwa/nuxt',
    '@vueuse/nuxt'
  ],

  pwa: {
    strategies: 'injectManifest',
    srcDir: '.',
    filename: 'service-worker.ts',
    registerType: 'autoUpdate',
    manifest: {
      name: 'Resume Builder AI',
      short_name: 'ResumeAI',
      theme_color: '#4f46e5',
      icons: [
        {
          src: 'pwa-192x192.png',
          sizes: '192x192',
          type: 'image/png'
        },
        {
          src: 'pwa-512x512.png',
          sizes: '512x512',
          type: 'image/png'
        }
      ]
    },
    workbox: {
      navigateFallback: '/',
      globPatterns: ['**/*.{js,css,html,png,svg,ico}']
    },
    client: {
      installPrompt: true,
    },
    devOptions: {
      enabled: true,
      type: 'module'
    }
  },

  tailwindcss: {
    cssPath: '~/assets/css/main.css',
    configPath: 'tailwind.config.js',
    exposeConfig: false,
    viewer: true,
  },

  css: [
    '~/assets/css/print.css'
  ],

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000'
      // Gemini API key removed for security - all AI operations should go through backend API
    }
  },

  app: {
    head: {
      title: 'Resume Builder - AI-Powered Resume Creation',
      htmlAttrs: { lang: 'en' },
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Build professional, ATS-optimized resumes in minutes with AI. Free online resume builder with modern templates and PDF export.' },
        { name: 'theme-color', content: '#4f46e5' },
        // Open Graph
        { property: 'og:type', content: 'website' },
        { property: 'og:title', content: 'Resume Builder - AI-Powered' },
        { property: 'og:description', content: 'Build professional, ATS-optimized resumes in minutes with AI.' },
        { property: 'og:image', content: '/og-image.png' }, // Setup placeholder if not exists
        // Twitter
        { name: 'twitter:card', content: 'summary_large_image' },
        { name: 'twitter:title', content: 'Resume Builder - AI-Powered' },
        { name: 'twitter:description', content: 'Build professional, ATS-optimized resumes in minutes with AI.' }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' },
        { rel: 'stylesheet', href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' }
      ]
    }
  },

  nitro: {
    compressPublicAssets: true,
    routeRules: {
      '/api/**': { proxy: `${process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000'}/api/v1/**` },
      '/api/webhooks/**': { proxy: `${process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000'}/api/webhooks/**` }
    }
  },

  ssr: true,

  experimental: {
    payloadExtraction: false
  },

  compatibilityDate: '2024-12-24'
})
