<script setup lang="ts">
import type { TemplateProps } from '~/types/resume'

const props = defineProps<TemplateProps>()

// Data science specific skills
const technicalSkills = computed(() => {
  const skills: string[] = []
  if (props.data.skills.technical) skills.push(...props.data.skills.technical)
  if (props.data.skills.tools) skills.push(...props.data.skills.tools)
  return skills
})

const programmingLanguages = computed(() => 
  props.data.skills.languages || []
)

const hasProjects = computed(() =>
  props.data.projects && props.data.projects.length > 0
)
</script>

<template>
  <article class="resume-template data-scientist" :style="{ '--primary-color': theme.primaryColor }">
    <!-- Header with Stats Badge -->
    <header class="resume-header">
      <div class="header-content">
        <div class="name-block">
          <h1 class="name">{{ data.basics.fullName }}</h1>
          <h2 class="title">{{ data.basics.title }}</h2>
        </div>
        
        <div class="stats-badge">
          <i class="fa-solid fa-chart-line"></i>
        </div>
      </div>
      
      <div class="contact-info">
        <span v-if="data.basics.email">
          <i class="fa-solid fa-envelope"></i> {{ data.basics.email }}
        </span>
        <span v-if="data.basics.phone">
          <i class="fa-solid fa-phone"></i> {{ data.basics.phone }}
        </span>
        <span v-if="data.basics.location">
          <i class="fa-solid fa-map-marker-alt"></i> {{ data.basics.location }}
        </span>
        <span v-if="data.basics.github">
          <i class="fa-brands fa-github"></i> {{ data.basics.github }}
        </span>
      </div>
    </header>

    <!-- Summary -->
    <section v-if="data.basics.summary" class="resume-section summary">
      <h3 class="section-title">Professional Summary</h3>
      <p class="summary-text">{{ data.basics.summary }}</p>
    </section>

    <!-- Technical Skills in Columns -->
    <section v-if="technicalSkills.length > 0 || programmingLanguages.length > 0" class="resume-section skills">
      <h3 class="section-title">Technical Expertise</h3>
      <div class="skills-grid">
        <div v-if="programmingLanguages.length > 0" class="skill-category">
          <h4 class="skill-category-title">Languages</h4>
          <p class="skill-list">{{ programmingLanguages.join(', ') }}</p>
        </div>
        <div v-if="technicalSkills.length > 0" class="skill-category">
          <h4 class="skill-category-title">Tools & Technologies</h4>
          <p class="skill-list">{{ technicalSkills.join(', ') }}</p>
        </div>
      </div>
    </section>

    <!-- Experience with Metrics Focus -->
    <section v-if="data.experience.length > 0" class="resume-section experience">
      <h3 class="section-title">Professional Experience</h3>
      
      <div v-for="exp in data.experience" :key="exp.id" class="experience-item">
        <div class="exp-header">
          <div>
            <h4 class="position">{{ exp.position }}</h4>
            <p class="company">{{ exp.company }}<span v-if="exp.location"> • {{ exp.location }}</span></p>
          </div>
          <div class="dates">{{ exp.startDate }} - {{ exp.current ? 'Present' : exp.endDate }}</div>
        </div>
        
        <ul v-if="exp.responsibilities.length > 0" class="responsibilities">
          <li v-for="(resp, idx) in exp.responsibilities.filter(r => r)" :key="idx">
            <span class="metric-highlight">{{ resp }}</span>
          </li>
        </ul>
      </div>
    </section>

    <!-- Projects -->
    <section v-if="hasProjects" class="resume-section projects">
      <h3 class="section-title">Key Projects</h3>
      
      <div v-for="project in data.projects" :key="project.id" class="project-item">
        <h4 class="project-name">{{ project.name }}</h4>
        <p class="project-desc">{{ project.description }}</p>
        <div v-if="project.technologies.length > 0" class="project-tech">
          <span v-for="tech in project.technologies" :key="tech" class="tech-badge">{{ tech }}</span>
        </div>
      </div>
    </section>

    <!-- Education -->
    <section v-if="data.education.length > 0" class="resume-section education">
      <h3 class="section-title">Education</h3>
      
      <div v-for="edu in data.education" :key="edu.id" class="education-item">
        <div class="edu-header">
          <div>
            <h4 class="degree">{{ edu.degree }}</h4>
            <p class="institution">{{ edu.institution }}</p>
          </div>
          <div class="edu-year">{{ edu.year }}</div>
        </div>
        <p v-if="edu.gpa" class="edu-gpa">GPA: {{ edu.gpa }}</p>
      </div>
    </section>
  </article>
</template>

<style scoped>
.resume-template {
  --primary: var(--primary-color, #059669);
  
  font-family: var(--resume-font-family, 'Inter', system-ui, sans-serif);
  font-size: calc(11pt * var(--resume-typography-scale, 1));
  line-height: 1.5;
  color: #1e293b;
  background: white;
  padding: 4rem;
  break-inside: avoid-page;
}

/* Header with Stats Badge */
.resume-header {
  border-bottom: 1px solid #cbd5e1;
  padding-bottom: calc(1.25rem * var(--resume-spacing-scale, 1));
  margin-bottom: calc(1.75rem * var(--resume-spacing-scale, 1));
  break-inside: avoid;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.stats-badge {
  width: 3.5rem;
  height: 3.5rem;
  background: linear-gradient(135deg, var(--primary) 0%, #10b981 100%);
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.name {
  font-size: 1.875rem;
  font-weight: 800;
  color: #0f172a;
  margin: 0;
  letter-spacing: -0.01em;
}

.title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--primary);
  margin: 0.25rem 0 0;
}

.contact-info {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  font-size: 0.813rem;
  color: #64748b;
  font-weight: 500;
}

.contact-info i {
  margin-right: 0.25rem;
}

/* Section Styles */
.resume-section {
  margin-bottom: calc(1.75rem * var(--resume-spacing-scale, 1));
  break-inside: avoid-page;
}

.section-title {
  font-size: 0.875rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--primary);
  margin: 0 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid var(--primary);
}

/* Summary */
.summary-text {
  font-size: 0.938rem;
  color: #475569;
  line-height: 1.6;
  margin: 0;
}

/* Skills Grid */
.skills-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.skill-category {
  background: #f8fafc;
  padding: 0.875rem;
  border-radius: 0.5rem;
  border-left: 3px solid var(--primary);
}

.skill-category-title {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--primary);
  margin: 0 0 0.5rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.skill-list {
  font-size: 0.813rem;
  color: #475569;
  margin: 0;
  line-height: 1.6;
}

/* Experience */
.experience-item {
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
  margin: 0 0 0.125rem;
}

.company {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0;
}

.dates {
  font-size: 0.813rem;
  color: #94a3b8;
  font-weight: 600;
  white-space: nowrap;
}

.responsibilities {
  margin: 0;
  padding-left: 1.25rem;
  list-style: disc;
}

.responsibilities li {
  font-size: 0.875rem;
  color: #475569;
  margin-bottom: 0.375rem;
}

.metric-highlight {
  /* Metrics in bold if they contain numbers */
  font-weight: inherit;
}

/* Projects */
.project-item {
  margin-bottom: 1.25rem;
  break-inside: avoid;
}

.project-name {
  font-size: 0.938rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.25rem;
}

.project-desc {
  font-size: 0.875rem;
  color: #475569;
  margin: 0 0 0.5rem;
}

.project-tech {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.tech-badge {
  font-size: 0.75rem;
  padding: 0.25rem 0.625rem;
  background: rgba(5, 150, 105, 0.1);
  color: var(--primary);
  border: 1px solid rgba(5, 150, 105, 0.2);
  border-radius: 0.25rem;
  font-weight: 600;
}

/* Education */
.education-item {
  margin-bottom: 1rem;
  break-inside: avoid;
}

.edu-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.25rem;
  gap: 1rem;
}

.degree {
  font-size: 0.938rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.125rem;
}

.institution {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0;
}

.edu-year {
  font-size: 0.813rem;
  color: #94a3b8;
  font-weight: 600;
  white-space: nowrap;
}

.edu-gpa {
  font-size: 0.813rem;
  color: #64748b;
  margin: 0.25rem 0 0;
}

/* Print adjustments */
@media print {
  .resume-section {
    page-break-inside: avoid;
  }
  
  .experience-item,
  .project-item,
  .education-item {
    page-break-inside: avoid;
  }
  
  .stats-badge {
    print-color-adjust: exact;
    -webkit-print-color-adjust: exact;
  }
}
</style>
