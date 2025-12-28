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
  <article class="resume-template executive" :style="{ '--primary-color': theme.primaryColor }">
    <!-- Header Section - Centered for Executive -->
    <header class="resume-header">
      <h1 class="name">{{ data.basics.fullName }}</h1>
      <h2 class="title">{{ data.basics.title }}</h2>
      
      <div class="contact-info">
        <span v-if="data.basics.email">{{ data.basics.email }}</span>
        <span v-if="data.basics.phone">{{ data.basics.phone }}</span>
        <span v-if="data.basics.location">{{ data.basics.location }}</span>
      </div>
    </header>

    <!-- Professional Summary - Prominent -->
    <section v-if="data.basics.summary" class="resume-section summary">
      <h3 class="section-title">Executive Summary</h3>
      <p class="summary-text">{{ data.basics.summary }}</p>
    </section>

    <!-- Core Competencies -->
    <section v-if="allSkills.length > 0" class="resume-section competencies">
      <h3 class="section-title">Core Competencies</h3>
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

    <!-- Certifications -->
    <section v-if="data.certifications && data.certifications.length > 0" class="resume-section certifications">
      <h3 class="section-title">Professional Certifications</h3>
      <ul class="cert-list">
        <li v-for="cert in data.certifications" :key="cert.id">
          <strong>{{ cert.name }}</strong> - {{ cert.issuer }} ({{ cert.date }})
        </li>
      </ul>
    </section>

    <!-- Achievements -->
    <section v-if="data.achievements && data.achievements.length > 0" class="resume-section achievements-section">
      <h3 class="section-title">Key Achievements</h3>
      <ul class="achievements-list">
        <li v-for="(achievement, idx) in data.achievements" :key="idx">{{ achievement }}</li>
      </ul>
    </section>
  </article>
</template>

<style scoped>
.resume-template {
  --primary: var(--primary-color, #000000);
  
  font-family: var(--resume-font-family, 'Georgia', serif);
  font-size: calc(11pt * var(--resume-typography-scale, 1));
  line-height: 1.6;
  color: #1e293b;
  background: white;
  break-inside: avoid-page;
}

/* Header - Centered and Bold */
.resume-header {
  text-align: center;
  border-bottom: 2px solid var(--primary);
  padding-bottom: calc(1.5rem * var(--resume-spacing-scale, 1));
  margin-bottom: calc(2rem * var(--resume-spacing-scale, 1));
  break-inside: avoid;
}

.name {
  font-size: 2.5rem;
  font-weight: 900;
  color: var(--primary);
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.title {
  font-size: 1.25rem;
  font-weight: 400;
  color: #475569;
  margin: 0.75rem 0 0;
  font-style: italic;
}

.contact-info {
  display: flex;
  gap: 2rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-top: 1rem;
  font-size: 0.875rem;
  color: #64748b;
  font-weight: 500;
}

/* Section Styles */
.resume-section {
  margin-bottom: calc(2rem * var(--resume-spacing-scale, 1));
  break-inside: avoid-page;
}

.section-title {
  font-size: 1rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--primary);
  border-bottom: 2px solid var(--primary);
  padding-bottom: 0.5rem;
  margin: 0 0 1.25rem;
}

/* Executive Summary */
.summary-text {
  font-size: 1rem;
  color: #334155;
  line-height: 1.7;
  margin: 0;
  text-align: justify;
}

/* Core Competencies Grid */
.competency-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.75rem 1.5rem;
}

.competency-item {
  font-size: 0.875rem;
  color: #475569;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.competency-item::before {
  content: '▪';
  color: var(--primary);
  font-size: 1.25rem;
}

/* Experience Items */
.experience-item {
  margin-bottom: 1.75rem;
  break-inside: avoid;
}

.exp-title-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 0.25rem;
  gap: 1rem;
}

.position {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--primary);
  margin: 0;
}

.company {
  font-size: 0.938rem;
  color: #64748b;
  margin: 0 0 0.75rem;
  font-weight: 600;
}

.dates {
  font-size: 0.875rem;
  color: #94a3b8;
  font-weight: 600;
  white-space: nowrap;
}

.achievements {
  margin: 0;
  padding-left: 1.5rem;
  list-style: none;
}

.achievements li {
  font-size: 0.875rem;
  color: #475569;
  margin-bottom: 0.5rem;
  position: relative;
}

.achievements li::before {
  content: '→';
  position: absolute;
  left: -1.25rem;
  color: var(--primary);
  font-weight: bold;
}

/* Education Items */
.education-item {
  margin-bottom: 1rem;
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
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.institution {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0;
}

.edu-year {
  font-size: 0.875rem;
  color: #94a3b8;
  font-weight: 600;
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
  margin-bottom: 0.5rem;
  padding-left: 1.25rem;
  position: relative;
}

.cert-list li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: var(--primary);
  font-weight: bold;
}

/* Achievements */
.achievements-list {
  margin: 0;
  padding-left: 0;
  list-style: none;
}

.achievements-list li {
  font-size: 0.875rem;
  color: #475569;
  margin-bottom: 0.5rem;
  padding-left: 1.25rem;
  position: relative;
}

.achievements-list li::before {
  content: '★';
  position: absolute;
  left: 0;
  color: var(--primary);
  font-weight: bold;
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
    grid-template-columns: repeat(3, 1fr);
  }
}
</style>
