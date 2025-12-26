<script setup lang="ts">
import type { TemplateProps } from '~/types/resume'

const props = defineProps<TemplateProps>()

const technicalSkills = computed(() => {
  const skills: string[] = []
  if (props.data.skills.devops) skills.push(...props.data.skills.devops)
  if (props.data.skills.technical) skills.push(...props.data.skills.technical)
  if (props.data.skills.tools) skills.push(...props.data.skills.tools)
  return skills
})
</script>

<template>
  <article class="resume-template devops" :style="{ '--primary-color': theme.primaryColor }">
    <!-- Two-Column Layout for DevOps -->
    <div class="two-column-layout">
      <!-- Left Sidebar (30%) -->
      <aside class="sidebar">
        <!-- Contact with Icons -->
        <section class="contact-section">
          <div class="contact-item" v-if="data.basics.email">
            <i class="fa-solid fa-envelope"></i>
            <span>{{ data.basics.email }}</span>
          </div>
          <div class="contact-item" v-if="data.basics.phone">
            <i class="fa-solid fa-phone"></i>
            <span>{{ data.basics.phone }}</span>
          </div>
          <div class="contact-item" v-if="data.basics.location">
            <i class="fa-solid fa-location-dot"></i>
            <span>{{ data.basics.location }}</span>
          </div>
          <div class="contact-item" v-if="data.basics.github">
            <i class="fa-brands fa-github"></i>
            <span>{{ data.basics.github }}</span>
          </div>
        </section>

        <!-- Skills in Sidebar -->
        <section v-if="technicalSkills.length > 0" class="sidebar-section">
          <h3 class="sidebar-title">Technical Stack</h3>
          <div class="skill-tags">
            <span v-for="skill in technicalSkills" :key="skill" class="skill-tag">{{ skill }}</span>
          </div>
        </section>

        <!-- Education in Sidebar -->
        <section v-if="data.education.length > 0" class="sidebar-section">
          <h3 class="sidebar-title">Education</h3>
          <div v-for="edu in data.education" :key="edu.id" class="edu-compact">
            <h4>{{ edu.degree }}</h4>
            <p>{{ edu.institution }}</p>
            <p class="year">{{ edu.year }}</p>
          </div>
        </section>

        <!-- Certifications in Sidebar -->
        <section v-if="data.certifications && data.certifications.length > 0" class="sidebar-section">
          <h3 class="sidebar-title">Certifications</h3>
          <ul class="cert-compact">
            <li v-for="cert in data.certifications" :key="cert.id">
              <i class="fa-solid fa-certificate"></i>
              {{ cert.name }}
            </li>
          </ul>
        </section>
      </aside>

      <!-- Right Main Content (70%) -->
      <main class="main-content">
        <!-- Header -->
        <header class="devops-header">
          <h1 class="name">{{ data.basics.fullName }}</h1>
          <h2 class="title">{{ data.basics.title }}</h2>
        </header>

        <!-- Summary -->
        <section v-if="data.basics.summary" class="content-section">
          <h3 class="section-title">
            <i class="fa-solid fa-user"></i>
            Professional Summary
          </h3>
          <p class="summary">{{ data.basics.summary }}</p>
        </section>

        <!-- Experience -->
        <section v-if="data.experience.length > 0" class="content-section">
          <h3 class="section-title">
            <i class="fa-solid fa-briefcase"></i>
            Professional Experience
          </h3>
          
          <div v-for="exp in data.experience" :key="exp.id" class="exp-item">
            <div class="exp-header">
              <div>
                <h4 class="position">{{ exp.position }}</h4>
                <p class="company">{{ exp.company }}</p>
              </div>
              <span class="dates">{{ exp.startDate }} - {{ exp.current ? 'Present' : exp.endDate }}</span>
            </div>
            <ul v-if="exp.responsibilities.length > 0" class="responsibilities">
              <li v-for="(resp, idx) in exp.responsibilities.filter(r => r)" :key="idx">{{ resp }}</li>
            </ul>
          </div>
        </section>

        <!-- Projects -->
        <section v-if="data.projects && data.projects.length > 0" class="content-section">
          <h3 class="section-title">
            <i class="fa-solid fa-diagram-project"></i>
            Key Projects
          </h3>
          
          <div v-for="project in data.projects" :key="project.id" class="project-item">
            <h4 class="project-name">{{ project.name }}</h4>
            <p class="project-desc">{{ project.description }}</p>
            <div v-if="project.technologies.length > 0" class="tech-stack">
              <i class="fa-solid fa-code"></i>
              {{ project.technologies.join(' • ') }}
            </div>
          </div>
        </section>
      </main>
    </div>
  </article>
</template>

<style scoped>
.resume-template {
  --primary: var(--primary-color, #f97316);
  
  font-family: var(--resume-font-family, 'Inter', system-ui, sans-serif);
  font-size: calc(10.5pt * var(--resume-typography-scale, 1));
  line-height: 1.5;
  color: #1e293b;
  background: white;
}

/* Two Column Layout */
.two-column-layout {
  display: grid;
  grid-template-columns: 30% 70%;
  min-height: 100%;
}

/* Left Sidebar */
.sidebar {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: white;
  padding: 2rem 1.5rem;
}

.contact-section {
  margin-bottom: 2rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
  font-size: 0.813rem;
}

.contact-item i {
  color: var(--primary);
  width: 1.25rem;
}

.sidebar-section {
  margin-bottom: 2rem;
  break-inside: avoid;
}

.sidebar-title {
  font-size: 0.875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--primary);
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid rgba(249, 115, 22, 0.3);
}

.skill-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.skill-tag {
  font-size: 0.75rem;
  padding: 0.25rem 0.75rem;
  background: rgba(249, 115, 22, 0.2);
  border: 1px solid rgba(249, 115, 22, 0.4);
  border-radius: 0.25rem;
  color: #fbbf24;
}

.edu-compact {
  margin-bottom: 1.25rem;
}

.edu-compact h4 {
  font-size: 0.813rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.edu-compact p {
  font-size: 0.75rem;
  color: #cbd5e1;
  margin: 0;
}

.edu-compact .year {
  color: var(--primary);
  font-weight: 600;
}

.cert-compact {
  list-style: none;
  padding: 0;
  margin: 0;
}

.cert-compact li {
  font-size: 0.75rem;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.cert-compact i {
  color: var(--primary);
  font-size: 0.625rem;
}

/* Main Content */
.main-content {
  padding: 2rem 2.5rem;
}

.devops-header {
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 3px solid var(--primary);
}

.name {
  font-size: 2.25rem;
  font-weight: 900;
  color: #0f172a;
  margin: 0;
  letter-spacing: -0.01em;
}

.title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--primary);
  margin: 0.5rem 0 0;
}

.content-section {
  margin-bottom: 2rem;
  break-inside: avoid;
}

.section-title {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.section-title i {
  color: var(--primary);
}

.summary {
  font-size: 0.875rem;
  color: #475569;
  line-height: 1.6;
  margin: 0;
}

.exp-item {
  margin-bottom: 1.5rem;
  break-inside: avoid;
}

.exp-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.5rem;
  gap: 1rem;
}

.position {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.25rem;
}

.company {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0;
}

.dates {
  font-size: 0.75rem;
  color: #94a3b8;
  font-weight: 600;
  white-space: nowrap;
}

.responsibilities {
  margin: 0;
  padding-left: 1.25rem;
  list-style: square;
}

.responsibilities li {
  font-size: 0.813rem;
  color: #475569;
  margin-bottom: 0.25rem;
}

.project-item {
  margin-bottom: 1.25rem;
  border-left: 3px solid var(--primary);
  padding-left: 1rem;
  break-inside: avoid;
}

.project-name {
  font-size: 0.938rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.25rem;
}

.project-desc {
  font-size: 0.813rem;
  color: #475569;
  margin: 0 0 0.5rem;
}

.tech-stack {
  font-size: 0.75rem;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.tech-stack i {
  color: var(--primary);
}

@media print {
  .two-column-layout {
    page-break-inside: avoid;
  }
  
  .sidebar,
  .content-section,
  .exp-item,
  .project-item {
    page-break-inside: avoid;
  }
}
</style>
