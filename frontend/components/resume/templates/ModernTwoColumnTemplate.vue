<script setup lang="ts">
import type { TemplateProps } from '~/types/resume'

const props = defineProps<TemplateProps>()

// Flatten all skills into a single array
const allSkills = computed(() => {
  const skills: string[] = []
  Object.values(props.data.skills).forEach(skillArray => {
    if (skillArray) skills.push(...skillArray)
  })
  return skills
})
</script>

<template>
  <article class="resume-template modern-two-column" :style="{ '--primary-color': theme.primaryColor }">
    <!-- Header with Avatar and Name (only on page 1) -->
    <header v-if="data.basics.fullName" class="resume-header">
      <div class="avatar-circle">
        <i class="fa-solid fa-user"></i>
      </div>
      <div class="header-content">
        <h1 class="name">{{ data.basics.fullName || 'FIRST NAME LAST NAME' }}</h1>
        <div class="contact-row">
          <span v-if="data.basics.phone" class="contact-item">
            <i class="fa-solid fa-phone"></i> {{ data.basics.phone }}
          </span>
          <span v-if="data.basics.email" class="contact-item">
            <i class="fa-solid fa-envelope"></i> {{ data.basics.email }}
          </span>
          <span v-if="data.basics.location" class="contact-item">
            <i class="fa-solid fa-location-dot"></i> {{ data.basics.location }}
          </span>
        </div>
      </div>
    </header>

    <!-- Summary Section -->
    <section v-if="data.basics.summary" class="summary-section">
      <h2 class="section-title">SUMMARY</h2>
      <p class="summary-text">{{ data.basics.summary }}</p>
    </section>

    <!-- Two Column Layout -->
    <div class="two-column-layout">
      <!-- Left Column: Experience -->
      <div class="left-column">
        <section v-if="data.experience.length > 0" class="experience-section">
          <h2 class="section-title">EXPERIENCE</h2>
          
          <div v-for="exp in data.experience" :key="exp.id" class="experience-item">
            <h3 class="job-title">{{ exp.position }} <span class="company-name">{{ exp.company }}</span></h3>
            <p class="job-subtitle">{{ exp.company }} – {{ exp.location || 'Remote' }}</p>
            <ul v-if="exp.responsibilities.length > 0" class="responsibilities-list">
              <li v-for="(resp, idx) in exp.responsibilities.filter(r => r)" :key="idx">
                {{ resp }}
              </li>
            </ul>
          </div>
        </section>
      </div>

      <!-- Right Column: Education & Skills -->
      <div class="right-column">
        <!-- Education -->
        <section v-if="data.education.length > 0" class="education-section">
          <h2 class="section-title">EDUCATION</h2>
          
          <div v-for="edu in data.education" :key="edu.id" class="education-item">
            <h3 class="degree-title">{{ edu.degree }}</h3>
            <p class="institution-name">{{ edu.institution }}</p>
            <p v-if="edu.year" class="education-year">{{ edu.year }}</p>
          </div>
        </section>

        <!-- Skills -->
        <section v-if="allSkills.length > 0" class="skills-section">
          <h2 class="section-title">SKILLS</h2>
          
          <div class="skills-grid">
            <span v-for="skill in allSkills" :key="skill" class="skill-item">
              {{ skill }}
            </span>
          </div>
        </section>

        <!-- Achievements -->
        <section v-if="data.achievements && data.achievements.length > 0" class="achievements-section">
          <h2 class="section-title">ACHIEVEMENTS</h2>
          
          <ul class="achievements-list">
            <li v-for="(achievement, idx) in data.achievements" :key="idx">
              {{ achievement }}
            </li>
          </ul>
        </section>
      </div>
    </div>
  </article>
</template>

<style scoped>
.resume-template {
  --primary: var(--primary-color, #000000);
  
  font-family: var(--resume-font-family, 'Arial', sans-serif);
  font-size: calc(10pt * var(--resume-typography-scale, 1));
  line-height: 1.5;
  color: #000000;
  background: white;
  padding: 4rem;
  break-inside: avoid-page;
  max-width: 850px;
  margin: 0 auto;
}

/* Header Section */
.resume-header {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
}

.avatar-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #d1d5db;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  color: #9ca3af;
}

.header-content {
  flex: 1;
}

.name {
  font-size: 2.5rem;
  font-weight: 900;
  color: #000000;
  margin: 0 0 0.5rem 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  line-height: 1.1;
}

.contact-row {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  font-size: 0.75rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  color: #000000;
}

.contact-item i {
  font-size: 0.688rem;
}

/* Summary Section */
.summary-section {
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
}

.section-title {
  font-size: 0.938rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #000000;
  margin: 0 0 0.75rem 0;
  padding-bottom: 0.375rem;
  border-bottom: 2px solid #000000;
}

.summary-text {
  font-size: 0.813rem;
  color: #000000;
  line-height: 1.6;
  margin: 0;
  text-align: justify;
}

/* Two Column Layout */
.two-column-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2.5rem;
}

/* Left Column - Experience */
.left-column {
  border-right: 1px solid #e5e7eb;
  padding-right: 2.5rem;
}

.experience-section {
  margin-bottom: 0;
}

.experience-item {
  margin-bottom: 1.75rem;
  break-inside: avoid;
}

.job-title {
  font-size: 0.875rem;
  font-weight: 700;
  color: #000000;
  margin: 0 0 0.25rem 0;
  line-height: 1.3;
}

.company-name {
  font-weight: 400;
  text-transform: uppercase;
}

.job-subtitle {
  font-size: 0.75rem;
  color: #000000;
  margin: 0 0 0.625rem 0;
  font-style: italic;
}

.responsibilities-list {
  margin: 0;
  padding-left: 1.25rem;
  list-style: disc;
}

.responsibilities-list li {
  font-size: 0.75rem;
  color: #000000;
  margin-bottom: 0.375rem;
  line-height: 1.5;
}

/* Right Column - Education & Skills */
.right-column {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.education-section {
  margin-bottom: 0;
}

.education-item {
  margin-bottom: 1.25rem;
  break-inside: avoid;
}

.degree-title {
  font-size: 0.875rem;
  font-weight: 700;
  color: #000000;
  margin: 0 0 0.25rem 0;
}

.institution-name {
  font-size: 0.75rem;
  color: #000000;
  margin: 0 0 0.125rem 0;
}

.education-year {
  font-size: 0.688rem;
  color: #000000;
  margin: 0;
}

/* Skills Section */
.skills-section {
  margin-bottom: 0;
}

.skills-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.5rem 1.5rem;
}

.skill-item {
  font-size: 0.75rem;
  color: #000000;
  line-height: 1.6;
}

/* Achievements Section */
.achievements-section {
  margin-bottom: 0;
}

.achievements-list {
  margin: 0;
  padding-left: 1.25rem;
  list-style: disc;
}

.achievements-list li {
  font-size: 0.75rem;
  color: #000000;
  margin-bottom: 0.5rem;
  line-height: 1.6;
}

/* Print adjustments */
@media print {
  .resume-template {
    padding: 1.5rem 2rem;
  }

  .two-column-layout {
    page-break-inside: avoid;
  }
  
  .experience-item,
  .education-item {
    page-break-inside: avoid;
  }
}
</style>
