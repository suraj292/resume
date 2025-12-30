<script setup lang="ts">
import type { TemplateProps } from '~/types/resume'

const props = defineProps<TemplateProps>()
</script>

<template>
  <article class="resume-template marketing" :style="{ '--primary-color': theme.primaryColor }">
    <!-- Bold Header with Impact -->
    <header class="marketing-header">
      <div class="impact-row">
        <span class="impact-label">MARKETING PROFESSIONAL</span>
        <span class="location">{{ data.basics.location }}</span>
      </div>
      <h1 class="name">{{ data.basics.fullName }}</h1>
      <h2 class="title">{{ data.basics.title }}</h2>
      
      <div class="contact-chips">
        <span v-if="data.basics.email" class="chip">
          <i class="fa-solid fa-envelope"></i> {{ data.basics.email }}
        </span>
        <span v-if="data.basics.phone" class="chip">
          <i class="fa-solid fa-phone"></i> {{ data.basics.phone }}
        </span>
        <span v-if="data.basics.linkedin" class="chip">
          <i class="fa-brands fa-linkedin"></i> LinkedIn
        </span>
      </div>
    </header>

    <!-- Summary with Stats Look -->
    <section v-if="data.basics.summary" class="profile-section">
      <h3 class="bold-title">PROFILE</h3>
      <p class="profile-text">{{ data.basics.summary }}</p>
    </section>

    <!-- Experience with Results Focus -->
    <section v-if="data.experience.length > 0" class="experience-section">
      <h3 class="bold-title">PROFESSIONAL EXPERIENCE</h3>
      
      <div v-for="exp in data.experience" :key="exp.id" class="exp-block">
        <div class="exp-header-row">
          <div>
            <h4 class="role">{{ exp.position }}</h4>
            <p class="org">{{ exp.company }} • {{exp.location || 'Remote' }}</p>
          </div>
          <div class="period">{{ exp.startDate }} - {{ exp.current ? 'Present' : exp.endDate }}</div>
        </div>
        
        <div v-if="exp.responsibilities.length > 0" class="results-grid">
          <div v-for="(resp, idx) in exp.responsibilities.filter(r => r)" :key="idx" class="result-item">
            <i class="fa-solid fa-chart-line"></i>
            <span>{{ resp }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Skills as Strength Areas -->
    <section v-if="Object.values(data.skills).some(s => s.length > 0)" class="skills-section">
      <h3 class="bold-title">CORE COMPETENCIES</h3>
      <div class="competency-boxes">
        <template v-for="(skillsArray, category) in data.skills" :key="category">
          <div v-if="skillsArray && skillsArray.length > 0" class="competency-box">
            <h4>{{ category }}</h4>
            <p>{{ skillsArray.join(' • ') }}</p>
          </div>
        </template>
      </div>
    </section>

    <!-- Education Compact -->
    <section v-if="data.education.length > 0" class="education-section">
      <h3 class="bold-title">EDUCATION</h3>
      <div class="edu-row">
        <div v-for="edu in data.education" :key="edu.id" class="edu-box">
          <h4>{{ edu.degree }}</h4>
          <p>{{ edu.institution }}</p>
          <span>{{ edu.year }}</span>
        </div>
      </div>
    </section>
  </article>
</template>

<style scoped>
.resume-template {
  --primary: var(--primary-color, #f59e0b);
  
  font-family: var(--resume-font-family, 'Inter', system-ui, sans-serif);
  font-size: calc(10.5pt * var(--resume-typography-scale, 1));
  line-height: 1.5;
  color: #1e293b;
  background: white;
  padding: 2.5rem 3rem;
}

/* Marketing Header */
.marketing-header {
  margin-bottom: 2.5rem;
  border-bottom: 4px solid var(--primary);
  padding-bottom: 1.5rem;
}

.impact-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.impact-label {
  font-size: 0.688rem;
  font-weight: 900;
  letter-spacing: 0.15em;
  color: var(--primary);
  background: rgba(245, 158, 11, 0.1);
  padding: 0.375rem 1rem;
  border-radius: 0.25rem;
}

.location {
  font-size: 0.813rem;
  color: #64748b;
  font-weight: 600;
}

.name {
  font-size: 2.5rem;
  font-weight: 900;
  color: #0f172a;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: -0.01em;
}

.title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--primary);
  margin: 0.5rem 0 1rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.contact-chips {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.chip {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.813rem;
  color: #475569;
  padding: 0.375rem 0.875rem;
  background: #f1f5f9;
  border-radius: 0.375rem;
}

.chip i {
  color: var(--primary);
}

/* Bold Titles */
.bold-title {
  font-size: 0.938rem;
  font-weight: 900;
  letter-spacing: 0.1em;
  color: #0f172a;
  margin: 0 0 1.25rem;
  text-transform: uppercase;
  position: relative;
  padding-left: 1rem;
}

.bold-title::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: var(--primary);
}

/* Profile Section */
.profile-section {
  margin-bottom: 2.5rem;
  break-inside: avoid;
}

.profile-text {
  font-size: 0.938rem;
  color: #475569;
  line-height: 1.7;
  margin: 0;
}

/* Experience */
.experience-section {
  margin-bottom: 2.5rem;
}

.exp-block {
  margin-bottom: 2rem;
  break-inside: avoid;
}

.exp-header-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
  gap: 1rem;
}

.role {
  font-size: 1.125rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.25rem;
}

.org {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0;
}

.period {
  font-size: 0.813rem;
  color: #94a3b8;
  font-weight: 600;
  text-align: right;
  white-space: nowrap;
}

.results-grid {
  display: grid;
  gap: 0.75rem;
}

.result-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  font-size: 0.875rem;
  color: #475569;
}

.result-item i {
  color: var(--primary);
  margin-top: 0.125rem;
  flex-shrink: 0;
}

/* Skills/Competencies */
.skills-section {
  margin-bottom: 2.5rem;
}

.competency-boxes {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.competency-box {
  background: #f8fafc;
  border-left: 3px solid var(--primary);
  padding: 1rem;
  break-inside: avoid;
}

.competency-box h4 {
  font-size: 0.813rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #0f172a;
  margin: 0 0 0.5rem;
}

.competency-box p {
  font-size: 0.75rem;
  color: #64748b;
  margin: 0;
  line-height: 1.6;
}

/* Education */
.education-section {
  margin-bottom: 2rem;
}

.edu-row {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.edu-box {
  flex: 1;
  min-width: 200px;
  break-inside: avoid;
}

.edu-box h4 {
  font-size: 0.938rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.25rem;
}

.edu-box p {
  font-size: 0.813rem;
  color: #64748b;
  margin: 0 0 0.375rem;
}

.edu-box span {
  font-size: 0.75rem;
  color: var(--primary);
  font-weight: 700;
}

@media print {
  .exp-block,
  .competency-box,
  .edu-box {
    page-break-inside: avoid;
  }
}
</style>
