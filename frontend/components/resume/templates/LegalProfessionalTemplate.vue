<script setup lang="ts">
import type { TemplateProps } from '~/types/resume'

const props = defineProps<TemplateProps>()

// Template-specific computed properties
const allSkills = computed(() => {
  const skills: string[] = []
  Object.values(props.data.skills).forEach(skillArray => {
    if (skillArray) skills.push(...skillArray)
  })
  return skills
})
</script>

<template>
  <article class="resume-template legal" :style="{ '--primary-color': theme.primaryColor }">
    <!-- Header Section - Centered, Formal for Legal -->
    <header class="resume-header">
      <h1 class="name">{{ data.basics.fullName }}</h1>
      <h2 class="title">{{ data.basics.title }}</h2>
      
      <div class="contact-info">
        <span v-if="data.basics.email">{{ data.basics.email }}</span>
        <span v-if="data.basics.phone">{{ data.basics.phone }}</span>
        <span v-if="data.basics.location">{{ data.basics.location }}</span>
        <span v-if="data.basics.linkedin">{{ data.basics.linkedin }}</span>
      </div>
    </header>

    <!-- Professional Summary -->
    <section v-if="data.basics.summary" class="resume-section summary">
      <h3 class="section-title">Professional Summary</h3>
      <p class="summary-text">{{ data.basics.summary }}</p>
    </section>

    <!-- Practice Areas / Core Competencies -->
    <section v-if="allSkills.length > 0" class="resume-section competencies">
      <h3 class="section-title">Areas of Expertise</h3>
      <div class="competency-grid">
        <span v-for="skill in allSkills.slice(0, 12)" :key="skill" class="competency-item">
          {{ skill }}
        </span>
      </div>
    </section>

    <!-- Professional Experience -->
    <section v-if="data.experience.length > 0" class="resume-section experience">
      <h3 class="section-title">Professional Experience</h3>
      
      <div v-for="exp in data.experience" :key="exp.id" class="experience-item">
        <div class="exp-title-row">
          <h4 class="position">{{ exp.position }}</h4>
          <span class="dates">{{ exp.startDate }} - {{ exp.current ? 'Present' : exp.endDate }}</span>
        </div>
        <p class="company">{{ exp.company }}<span v-if="exp.location"> | {{ exp.location }}</span></p>
        
        <ul v-if="exp.responsibilities.length > 0" class="achievements">
          <li v-for="(resp, idx) in exp.responsibilities.filter(r => r)" :key="idx">{{ resp }}</li>
        </ul>
      </div>
    </section>

    <!-- Education -->
    <section v-if="data.education.length > 0" class="resume-section education">
      <h3 class="section-title">Education</h3>
      
      <div v-for="edu in data.education" :key="edu.id" class="education-item">
        <div class="edu-row">
          <h4 class="degree">{{ edu.degree }}</h4>
          <span class="edu-year">{{ edu.year }}</span>
        </div>
        <p class="institution">{{ edu.institution }}</p>
      </div>
    </section>

    <!-- Bar Admissions / Certifications -->
    <section v-if="data.certifications && data.certifications.length > 0" class="resume-section certifications">
      <h3 class="section-title">Bar Admissions & Certifications</h3>
      <ul class="cert-list">
        <li v-for="cert in data.certifications" :key="cert.id">
          <strong>{{ cert.name }}</strong> - {{ cert.issuer }} ({{ cert.date }})
        </li>
      </ul>
    </section>
  </article>
</template>

<style scoped>
.resume-template {
  --primary: var(--primary-color, #1e293b);
  
  font-family: var(--resume-font-family, 'Times New Roman', 'Georgia', serif);
  font-size: calc(11pt * var(--resume-typography-scale, 1));
  line-height: 1.6;
  color: #1e293b;
  background: white;
  padding: 3rem 3.5rem;
  break-inside: avoid-page;
}

/* Header - Centered and Very Formal */
.resume-header {
  text-align: center;
  border-bottom: 4px solid var(--primary);
  padding-bottom: calc(1.5rem * var(--resume-spacing-scale, 1));
  margin-bottom: calc(2.5rem * var(--resume-spacing-scale, 1));
  break-inside: avoid;
}

.name {
  font-size: 2.25rem;
  font-weight: 700;
  color: var(--primary);
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  font-family: 'Times New Roman', serif;
}

.title {
  font-size: 1.125rem;
  font-weight: 400;
  color: #475569;
  margin: 0.75rem 0 0;
}

.contact-info {
  display: flex;
  gap: 1.5rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-top: 1rem;
  font-size: 0.813rem;
  color: #64748b;
  font-weight: 400;
}

.contact-info span {
  position: relative;
}

.contact-info span:not(:last-child)::after {
  content: '•';
  position: absolute;
  right: -0.9rem;
  color: #94a3b8;
}

/* Section Styles */
.resume-section {
  margin-bottom: calc(2.5rem * var(--resume-spacing-scale, 1));
  break-inside: avoid-page;
}

.section-title {
  font-size: 0.938rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--primary);
  border-bottom: 2px solid var(--primary);
  padding-bottom: 0.5rem;
  margin: 0 0 1.5rem;
  font-family: 'Times New Roman', serif;
}

/* Professional Summary */
.summary-text {
  font-size: 0.938rem;
  color: #334155;
  line-height: 1.75;
  margin: 0;
  text-align: justify;
}

/* Core Competencies Grid */
.competency-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.625rem 2rem;
}

.competency-item {
  font-size: 0.875rem;
  color: #475569;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.625rem;
}

.competency-item::before {
  content: '▪';
  color: var(--primary);
  font-size: 1.125rem;
}

/* Experience Items */
.experience-item {
  margin-bottom: 2rem;
  break-inside: avoid;
}

.exp-title-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 0.375rem;
  gap: 1rem;
}

.position {
  font-size: 1.063rem;
  font-weight: 700;
  color: var(--primary);
  margin: 0;
}

.company {
  font-size: 0.938rem;
  color: #64748b;
  margin: 0 0 0.875rem;
  font-weight: 500;
  font-style: italic;
}

.dates {
  font-size: 0.813rem;
  color: #94a3b8;
  font-weight: 500;
  white-space: nowrap;
}

.achievements {
  margin: 0;
  padding-left: 1.75rem;
  list-style: none;
}

.achievements li {
  font-size: 0.875rem;
  color: #475569;
  margin-bottom: 0.625rem;
  position: relative;
  line-height: 1.65;
}

.achievements li::before {
  content: '•';
  position: absolute;
  left: -1.25rem;
  color: var(--primary);
  font-weight: bold;
  font-size: 1.125rem;
}

/* Education Items */
.education-item {
  margin-bottom: 1.25rem;
  break-inside: avoid;
}

.edu-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 0.25rem;
  gap: 1rem;
}

.degree {
  font-size: 0.938rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.institution {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0;
  font-style: italic;
}

.edu-year {
  font-size: 0.813rem;
  color: #94a3b8;
  font-weight: 500;
  white-space: nowrap;
}

/* Certifications */
.cert-list {
  margin: 0;
  padding-left: 0;
  list-style: none;
}

.cert-list li {
  font-size: 0.875rem;
  color: #475569;
  margin-bottom: 0.625rem;
  padding-left: 1.5rem;
  position: relative;
}

.cert-list li::before {
  content: '§';
  position: absolute;
  left: 0;
  color: var(--primary);
  font-weight: bold;
  font-size: 1.125rem;
}

/* Print adjustments */
@media print {
  .resume-section {
    page-break-inside: avoid;
  }
  
  .experience-item,
  .education-item {
    page-break-inside: avoid;
  }
  
  .competency-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
