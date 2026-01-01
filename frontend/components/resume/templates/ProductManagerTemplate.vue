<script setup lang="ts">
import type { TemplateProps } from '~/types/resume'

const props = defineProps<TemplateProps>()

const allSkills = computed(() => {
  const skills: string[] = []
  Object.values(props.data.skills).forEach(skillArray => {
    if (skillArray) skills.push(...skillArray)
  })
  return skills.slice(0, 8) // Top 8 skills only
})
</script>

<template>
  <article class="resume-template product-manager" :style="{ '--primary-color': theme.primaryColor }">
    <!-- Minimalist Header with Line (only on page 1) -->
    <header v-if="data.basics.fullName" class="pm-header">
      <div class="header-line"></div>
      <h1 class="name">{{ data.basics.fullName }}</h1>
      <h2 class="title">{{ data.basics.title }}</h2>
      
      <div class="contact-row">
        <span v-if="data.basics.email">{{ data.basics.email }}</span>
        <span v-if="data.basics.phone" class="separator">•</span>
        <span v-if="data.basics.phone">{{ data.basics.phone }}</span>
        <span v-if="data.basics.location" class="separator">•</span>
        <span v-if="data.basics.location">{{ data.basics.location }}</span>
      </div>
    </header>

    <!-- Summary with Quote Style -->
    <section v-if="data.basics.summary" class="quote-section">
      <div class="quote-mark">"</div>
      <p class="quote-text">{{ data.basics.summary }}</p>
    </section>

    <!-- Core Competencies - Horizontal Pills -->
    <section v-if="allSkills.length > 0" class="competencies">
      <h3 class="minimal-title">Core Competencies</h3>
      <div class="competency-pills">
        <span v-for="skill in allSkills" :key="skill" class="pill">{{ skill }}</span>
      </div>
    </section>

    <!-- Experience - Clean Timeline -->
    <section v-if="data.experience.length > 0" class="experience">
      <h3 class="minimal-title">Professional Experience</h3>
      
      <div v-for="exp in data.experience" :key="exp.id" class="timeline-item">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
          <div class="exp-meta">
            <h4 class="position">{{ exp.position }}</h4>
            <span class="dates">{{ exp.startDate }} — {{ exp.current ? 'Present' : exp.endDate }}</span>
          </div>
          <p class="company">{{ exp.company }}</p>
          <ul v-if="exp.responsibilities.length > 0" class="achievements">
            <li v-for="(resp, idx) in exp.responsibilities.filter(r => r)" :key="idx">{{ resp }}</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Education - Minimalist -->
    <section v-if="data.education.length > 0" class="education">
      <h3 class="minimal-title">Education</h3>
      <div class="edu-grid">
        <div v-for="edu in data.education" :key="edu.id" class="edu-card">
          <h4>{{ edu.degree }}</h4>
          <p>{{ edu.institution }}</p>
          <span class="edu-year">{{ edu.year }}</span>
        </div>
      </div>
    </section>

    <!-- Achievements -->
    <section v-if="data.achievements && data.achievements.length > 0" class="achievements-section">
      <h3 class="minimal-title">Key Achievements</h3>
      <ul class="achievements-list">
        <li v-for="(achievement, idx) in data.achievements" :key="idx">{{ achievement }}</li>
      </ul>
    </section>
  </article>
</template>

<style scoped>
.resume-template {
  --primary: var(--primary-color, #6366f1);
  
  font-family: var(--resume-font-family, 'Inter', system-ui, sans-serif);
  font-size: calc(11pt * var(--resume-typography-scale, 1));
  line-height: 1.6;
  color: #1e293b;
  background: white;
  padding: 4rem;
}

/* Minimalist Header */
.pm-header {
  position: relative;
  margin-bottom: 2.5rem;
  text-align: left;
}

.header-line {
  width: 4rem;
  height: 4px;
  background: var(--primary);
  margin-bottom: 1.5rem;
}

.name {
  font-size: 2.5rem;
  font-weight: 300;
  color: #0f172a;
  margin: 0;
  letter-spacing: -0.02em;
}

.title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--primary);
  margin: 0.5rem 0 1rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.contact-row {
  font-size: 0.875rem;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.separator {
  color: #cbd5e1;
}

/* Quote Section */
.quote-section {
  position: relative;
  margin: 2.5rem 0;
  padding: 1.5rem 2rem;
  border-left: 4px solid var(--primary);
  background: #f8fafc;
}

.quote-mark {
  position: absolute;
  top: -0.5rem;
  left: 1rem;
  font-size: 3rem;
  color: var(--primary);
  opacity: 0.3;
  font-family: Georgia, serif;
}

.quote-text {
  font-size: 0.938rem;
  color: #475569;
  line-height: 1.7;
  margin: 0;
  font-style: italic;
}

/* Competencies */
.competencies {
  margin-bottom: 2.5rem;
}

.minimal-title {
  font-size: 0.875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #0f172a;
  margin: 0 0 1.25rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.competency-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.pill {
  padding: 0.5rem 1.25rem;
  background: white;
  border: 1.5px solid var(--primary);
  color: var(--primary);
  border-radius: 2rem;
  font-size: 0.813rem;
  font-weight: 600;
}

/* Timeline Experience */
.experience {
  margin-bottom: 2.5rem;
}

.timeline-item {
  position: relative;
  padding-left: 2rem;
  margin-bottom: 2rem;
  break-inside: avoid;
}

.timeline-dot {
  position: absolute;
  left: 0;
  top: 0.5rem;
  width: 0.75rem;
  height: 0.75rem;
  background: var(--primary);
  border-radius: 50%;
  border: 3px solid white;
  box-shadow: 0 0 0 2px var(--primary);
}

.timeline-item::before {
  content: '';
  position: absolute;
  left: 0.313rem;
  top: 1.5rem;
  bottom: -1rem;
  width: 2px;
  background: #e2e8f0;
}

.timeline-item:last-child::before {
  display: none;
}

.timeline-content {
  padding-bottom: 1rem;
}

.exp-meta {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 0.25rem;
  gap: 1rem;
}

.position {
  font-size: 1.125rem;
  font-weight: 600;
  color: #0f172a;
  margin: 0;
}

.dates {
  font-size: 0.75rem;
  color: #94a3b8;
  font-weight: 500;
  white-space: nowrap;
}

.company {
  font-size: 0.938rem;
  color: #64748b;
  margin: 0 0 0.75rem;
}

.achievements {
  margin: 0;
  padding-left: 1.25rem;
  list-style: none;
}

.achievements li {
  font-size: 0.875rem;
  color: #475569;
  margin-bottom: 0.5rem;
  position: relative;
}

.achievements li::before {
  content: '▸';
  position: absolute;
  left: -1.25rem;
  color: var(--primary);
}

/* Education */
.education {
  margin-bottom: 2rem;
}

.edu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.edu-card {
  padding: 1.25rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  background: #f8fafc;
  break-inside: avoid;
}

.edu-card h4 {
  font-size: 0.938rem;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 0.25rem;
}

.edu-card p {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0 0 0.5rem;
}

.edu-year {
  font-size: 0.75rem;
  color: var(--primary);
  font-weight: 600;
}

/* Achievements */
.achievements-section {
  margin-bottom: 2rem;
}

.achievements-list {
  margin: 0;
  padding-left: 1.25rem;
  list-style: none;
}

.achievements-list li {
  font-size: 0.875rem;
  color: #475569;
  margin-bottom: 0.5rem;
  position: relative;
}

.achievements-list li::before {
  content: '▸';
  position: absolute;
  left: -1.25rem;
  color: var(--primary);
}

@media print {
  .resume-template {
    padding: 2rem;
  }
  
  .timeline-item,
  .edu-card {
    page-break-inside: avoid;
  }
}
</style>
