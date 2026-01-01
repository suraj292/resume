<script setup lang="ts">
import type { TemplateProps } from '~/types/resume'

const props = defineProps<TemplateProps>()

const hasPortfolio = computed(() => 
  props.data.projects && props.data.projects.length > 0
)
</script>

<template>
  <article class="resume-template creative" :style="{ '--primary-color': theme.primaryColor }">
    <!-- Creative Header with Accent (only on page 1) -->
    <header v-if="data.basics.fullName" class="creative-header">
      <div class="accent-block"></div>
      <div class="header-content">
        <h1 class="creative-name">{{ data.basics.fullName }}</h1>
        <h2 class="creative-title">{{ data.basics.title }}</h2>
        
        <div class="creative-contact">
          <span v-if="data.basics.email">
            <i class="fa-solid fa-envelope"></i> {{ data.basics.email }}
          </span>
          <span v-if="data.basics.phone">
            <i class="fa-solid fa-phone"></i> {{ data.basics.phone }}
          </span>
          <span v-if="data.basics.location">
            <i class="fa-solid fa-location-dot"></i> {{ data.basics.location }}
          </span>
          <span v-if="data.basics.portfolio">
            <i class="fa-solid fa-globe"></i> {{ data.basics.portfolio }}
          </span>
        </div>
      </div>
    </header>

    <!-- About Section -->
    <section v-if="data.basics.summary" class="about-section">
      <div class="section-header">
        <span class="section-number">01</span>
        <h3>About Me</h3>
      </div>
      <p class="about-text">{{ data.basics.summary }}</p>
    </section>

    <!-- Portfolio/Projects Grid -->
    <section v-if="hasPortfolio" class="portfolio-section">
      <div class="section-header">
        <span class="section-number">02</span>
        <h3>Featured Projects</h3>
      </div>
      <div class="portfolio-grid">
        <div v-for="project in data.projects" :key="project.id" class="portfolio-card">
          <div class="card-header">
            <h4>{{ project.name }}</h4>
          </div>
          <p class="card-desc">{{ project.description }}</p>
          <div v-if="project.technologies.length > 0" class="card-tags">
            <span v-for="tech in project.technologies.slice(0, 4)" :key="tech">{{ tech }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Experience -->
    <section v-if="data.experience.length > 0" class="experience-section">
      <div class="section-header">
        <span class="section-number">03</span>
        <h3>Experience</h3>
      </div>
      
      <div v-for="exp in data.experience" :key="exp.id" class="exp-card">
        <div class="exp-title-row">
          <h4>{{ exp.position }}</h4>
          <span class="year-badge">{{ exp.startDate.split(' ')[1] || exp.startDate }}</span>
        </div>
        <p class="exp-company">{{ exp.company }}</p>
        <ul v-if="exp.responsibilities.length > 0" class="exp-list">
          <li v-for="(resp, idx) in exp.responsibilities.filter(r => r).slice(0, 3)" :key="idx">{{ resp }}</li>
        </ul>
      </div>
    </section>

    <!-- Skills & Education Side by Side -->
    <div class="bottom-grid">
      <section v-if="Object.values(data.skills).some(s => s.length > 0)" class="skills-box">
        <div class="section-header">
          <span class="section-number">04</span>
          <h3>Skills</h3>
        </div>
        <div class="skills-cloud">
          <template v-for="(skillsArray, category) in data.skills" :key="category">
            <span v-if="skillsArray && skillsArray.length > 0" v-for="skill in skillsArray.slice(0, 12)" :key="skill" class="skill-bubble">{{ skill }}</span>
          </template>
        </div>
      </section>

      <section v-if="data.education.length > 0" class="education-box">
        <div class="section-header">
          <span class="section-number">05</span>
          <h3>Education</h3>
        </div>
        <div v-for="edu in data.education" :key="edu.id" class="edu-item">
          <h4>{{ edu.degree }}</h4>
          <p>{{ edu.institution }} · {{ edu.year }}</p>
        </div>
      </section>
    </div>

    <!-- Achievements -->
    <section v-if="data.achievements && data.achievements.length > 0" class="achievements-section">
      <div class="section-header">
        <span class="section-number">05</span>
        <h3>Achievements</h3>
      </div>
      <ul class="achievements-list">
        <li v-for="(achievement, idx) in data.achievements" :key="idx">{{ achievement }}</li>
      </ul>
    </section>
  </article>
</template>

<style scoped>
.resume-template {
  --primary: var(--primary-color, #ec4899);
  
  font-family: var(--resume-font-family, 'Inter', system-ui, sans-serif);
  font-size: calc(10.5pt * var(--resume-typography-scale, 1));
  line-height: 1.5;
  color: #1e293b;
  background: white;
  padding: 4rem;
}

/* Creative Header */
.creative-header {
  display: flex;
  align-items: stretch;
  margin-bottom: 2rem;
}

.accent-block {
  width: 1rem;
  background: linear-gradient(135deg, var(--primary) 0%, #f472b6 100%);
  margin-right: 1.5rem;
  border-radius: 0.25rem;
}

.header-content {
  flex: 1;
}

.name {
  font-size: 2.75rem;
  font-weight: 900;
  color: #0f172a;
  margin: 0;
  line-height: 1;
  letter-spacing: -0.02em;
}

.title {
  font-size: 1.25rem;
  font-weight: 500;
  color: var(--primary);
  margin: 0.75rem 0 0;
  font-style: italic;
}

/* Icon Contacts */
.icon-contacts {
  display: flex;
  gap: 2rem;
  margin-bottom: 2.5rem;
  flex-wrap: wrap;
}

.icon-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #64748b;
  text-decoration: none;
}

.icon-link i {
  color: var(--primary);
  width: 1.25rem;
}

/* Section Headers */
.section-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.section-number {
  font-size: 1.5rem;
  font-weight: 900;
  color: var(--primary);
  opacity: 0.3;
}

.section-header h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

/* About Section */
.about-section {
  margin-bottom: 2.5rem;
  break-inside: avoid;
}

.about-text {
  font-size: 0.938rem;
  color: #475569;
  line-height: 1.7;
  margin: 0;
  padding-left: 3.5rem;
}

/* Portfolio Grid */
.portfolio-section {
  margin-bottom: 2.5rem;
}

.portfolio-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.25rem;
  padding-left: 3.5rem;
}

.portfolio-card {
  border: 2px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 1.25rem;
  transition: all 0.3s;
  break-inside: avoid;
}

.portfolio-card:hover {
  border-color: var(--primary);
  box-shadow: 0 4px 12px rgba(236, 72, 153, 0.1);
}

.card-header h4 {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.5rem;
}

.card-desc {
  font-size: 0.813rem;
  color: #64748b;
  margin: 0 0 0.75rem;
  line-height: 1.5;
}

.card-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.375rem;
}

.card-tags span {
  font-size: 0.688rem;
  padding: 0.25rem 0.625rem;
  background: #fce7f3;
  color: var(--primary);
  border-radius: 0.25rem;
  font-weight: 600;
}

/* Experience Section */
.experience-section {
  margin-bottom: 2.5rem;
}

.exp-card {
  margin-bottom: 1.5rem;
  padding-left: 3.5rem;
  break-inside: avoid;
}

.exp-title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.375rem;
}

.exp-title-row h4 {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.year-badge {
  font-size: 0.75rem;
  padding: 0.25rem 0.75rem;
  background: var(--primary);
  color: white;
  border-radius: 1rem;
  font-weight: 600;
}

.exp-company {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0 0 0.75rem;
}

.exp-list {
  margin: 0;
  padding-left: 1.25rem;
  list-style: disc;
}

.exp-list li {
  font-size: 0.813rem;
  color: #475569;
  margin-bottom: 0.25rem;
}

/* Bottom Grid */
.bottom-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.skills-box,
.education-box {
  break-inside: avoid;
}

.skills-cloud {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  padding-left: 3.5rem;
}

.skill-bubble {
  font-size: 0.75rem;
  padding: 0.375rem 0.875rem;
  background: white;
  border: 1.5px solid var(--primary);
  color: var(--primary);
  border-radius: 2rem;
  font-weight: 600;
}

.edu-item {
  margin-bottom: 1rem;
  padding-left: 3.5rem;
}

.edu-item h4 {
  font-size: 0.875rem;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 0.25rem;
}

.edu-item p {
  font-size: 0.813rem;
  color: #64748b;
  margin: 0;
}

/* Achievements */
.achievements-section {
  margin-bottom: 2rem;
}

.achievements-list {
  margin: 0;
  padding-left: 3.5rem;
  list-style: disc;
}

.achievements-list li {
  font-size: 0.875rem;
  color: #475569;
  margin-bottom: 0.5rem;
  line-height: 1.6;
}

@media print {
  .portfolio-grid {
    grid-template-columns: 1fr;
  }
  
  .portfolio-card,
  .exp-card,
  .skills-box,
  .education-box {
    page-break-inside: avoid;
  }
}
</style>
