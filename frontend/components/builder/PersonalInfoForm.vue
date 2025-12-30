<script setup lang="ts">
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { personalInfoSchema } from '~/utils/schemas'

// Props definition
const modelValue = defineModel<{
  fullName: string
  title: string
  email: string
  phone: string
  location: string
  linkedin: string
  github: string
  portfolio: string
  summary: string
}>({ required: true })

// Setup form validation
const { errors, defineField } = useForm({
  validationSchema: toTypedSchema(personalInfoSchema),
  initialValues: modelValue.value
})

// Define fields with validation
const [fullName, fullNameProps] = defineField('fullName')
const [title, titleProps] = defineField('title')
const [email, emailProps] = defineField('email')
const [phone, phoneProps] = defineField('phone')
const [location, locationProps] = defineField('location')
const [linkedin, linkedinProps] = defineField('linkedin')
const [github, githubProps] = defineField('github')
const [portfolio, portfolioProps] = defineField('portfolio')
const [summary, summaryProps] = defineField('summary')

// Sync form updates back to parent model
// We watch individual fields or the whole form. 
// Since defineField gives us reactive refs that update the form state, 
// we can watch those refs and update modelValue.
watch([fullName, title, email, phone, location, linkedin, github, portfolio, summary], () => {
  modelValue.value = {
    fullName: fullName.value || '',
    title: title.value || '',
    email: email.value || '',
    phone: phone.value || '',
    location: location.value || '',
    linkedin: linkedin.value || '',
    github: github.value || '',
    portfolio: portfolio.value || '',
    summary: summary.value || ''
  }
})

// Also watch for external changes (if parent updates modelValue)
watch(() => modelValue.value, (newVal) => {
  if (newVal.fullName !== fullName.value) fullName.value = newVal.fullName
  if (newVal.title !== title.value) title.value = newVal.title
  if (newVal.email !== email.value) email.value = newVal.email
  if (newVal.phone !== phone.value) phone.value = newVal.phone
  if (newVal.location !== location.value) location.value = newVal.location
  if (newVal.linkedin !== linkedin.value) linkedin.value = newVal.linkedin
  if (newVal.github !== github.value) github.value = newVal.github
  if (newVal.portfolio !== portfolio.value) portfolio.value = newVal.portfolio
  if (newVal.summary !== summary.value) summary.value = newVal.summary
}, { deep: true })
</script>

<template>
  <div class="space-y-4">
    <!-- Full Name -->
    <div
      class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm"
      :class="{ 'border-rose-300 ring-1 ring-rose-100': errors.fullName }">
      <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
        <i class="fa-solid fa-grip-vertical"></i>
      </div>
      <div class="flex-1">
        <div class="flex justify-between">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Full Name</label>
          <span v-if="errors.fullName" class="text-[10px] font-bold text-rose-500">{{ errors.fullName }}</span>
        </div>
        <input v-model="fullName" v-bind="fullNameProps" type="text" placeholder="e.g. John Doe" autocomplete="name"
          class="w-full bg-transparent font-bold text-slate-800 outline-none text-base">
      </div>
    </div>

    <!-- Professional Title -->
    <div
      class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm"
      :class="{ 'border-rose-300 ring-1 ring-rose-100': errors.title }">
      <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
        <i class="fa-solid fa-grip-vertical"></i>
      </div>
      <div class="flex-1">
        <div class="flex justify-between">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Professional
            Title</label>
          <span v-if="errors.title" class="text-[10px] font-bold text-rose-500">{{ errors.title }}</span>
        </div>
        <input v-model="title" v-bind="titleProps" type="text" placeholder="e.g. Software Engineer" autocomplete="organization-title"
          class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
      </div>
    </div>

    <!-- Email -->
    <div
      class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm"
      :class="{ 'border-rose-300 ring-1 ring-rose-100': errors.email }">
      <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
        <i class="fa-solid fa-grip-vertical"></i>
      </div>
      <div class="flex-1">
        <div class="flex justify-between">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Email Address</label>
          <span v-if="errors.email" class="text-[10px] font-bold text-rose-500">{{ errors.email }}</span>
        </div>
        <input v-model="email" v-bind="emailProps" type="email" placeholder="john@example.com" autocomplete="email"
          class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
      </div>
    </div>

    <!-- Phone -->
    <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm"
      :class="{ 'border-rose-300 ring-1 ring-rose-100': errors.phone }">
      <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
        <i class="fa-solid fa-grip-vertical"></i>
      </div>
      <div class="flex-1">
        <div class="flex justify-between">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Phone Number</label>
          <span v-if="errors.phone" class="text-[10px] font-bold text-rose-500">{{ errors.phone }}</span>
        </div>
        <input v-model="phone" v-bind="phoneProps" type="tel" placeholder="+1 (555) 000-0000" autocomplete="tel"
          class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
      </div>
    </div>

    <!-- Location -->
    <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
      <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
        <i class="fa-solid fa-grip-vertical"></i>
      </div>
      <div class="flex-1">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Location / Address</label>
        <input v-model="location" v-bind="locationProps" type="text" placeholder="New York, NY" autocomplete="address-level2"
          class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
      </div>
    </div>

    <!-- LinkedIn -->
    <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm"
      :class="{ 'border-rose-300 ring-1 ring-rose-100': errors.linkedin }">
      <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
        <i class="fa-solid fa-grip-vertical"></i>
      </div>
      <div class="flex-1">
        <div class="flex justify-between">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">LinkedIn Profile</label>
          <span v-if="errors.linkedin" class="text-[10px] font-bold text-rose-500">{{ errors.linkedin }}</span>
        </div>
        <input v-model="linkedin" v-bind="linkedinProps" type="url" placeholder="https://linkedin.com/in/yourprofile" autocomplete="url"
          class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
      </div>
    </div>

    <!-- GitHub -->
    <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm"
      :class="{ 'border-rose-300 ring-1 ring-rose-100': errors.github }">
      <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
        <i class="fa-solid fa-grip-vertical"></i>
      </div>
      <div class="flex-1">
        <div class="flex justify-between">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">GitHub Profile</label>
          <span v-if="errors.github" class="text-[10px] font-bold text-rose-500">{{ errors.github }}</span>
        </div>
        <input v-model="github" v-bind="githubProps" type="url" placeholder="https://github.com/yourusername" autocomplete="url"
          class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
      </div>
    </div>

    <!-- Portfolio -->
    <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm"
      :class="{ 'border-rose-300 ring-1 ring-rose-100': errors.portfolio }">
      <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
        <i class="fa-solid fa-grip-vertical"></i>
      </div>
      <div class="flex-1">
        <div class="flex justify-between">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Portfolio Website</label>
          <span v-if="errors.portfolio" class="text-[10px] font-bold text-rose-500">{{ errors.portfolio }}</span>
        </div>
        <input v-model="portfolio" v-bind="portfolioProps" type="url" placeholder="https://yourwebsite.com" autocomplete="url"
          class="w-full bg-transparent font-semibold text-slate-700 outline-none text-sm">
      </div>
    </div>

    <!-- Summary -->
    <div class="group relative bg-white border border-slate-200 p-4 rounded-2xl hover:border-indigo-300 transition-colors flex items-start gap-4 shadow-sm">
      <div class="drag-handle mt-1.5 text-slate-300 hover:text-indigo-400 transition-colors cursor-grab">
        <i class="fa-solid fa-grip-vertical"></i>
      </div>
      <div class="flex-1">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Professional
          Summary</label>
        <textarea v-model="summary" v-bind="summaryProps" rows="4"
          placeholder="Brief overview of your professional background..."
          class="w-full bg-transparent font-normal text-slate-700 outline-none text-sm resize-none"></textarea>
      </div>
    </div>
  </div>
</template>
