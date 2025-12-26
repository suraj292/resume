<script setup lang="ts">
import type { TemplateProps } from '~/types/resume'

const props = defineProps<TemplateProps>()

// Template-specific computed properties
const hasProjects = computed(() => 
  props.data.projects && props.data.projects.length > 0
)

const hasCertifications = computed(() =>
  props.data.certifications && props.data.certifications.length > 0
)

const technicalSkills = computed(() => {
  const skills: string[] = []
  if (props.data.skills.technical) skills.push(...props.data.skills.technical)
  if (props.data.skills.backend) skills.push(...props.data.skills.backend)
  if (props.data.skills.frontend) skills.push(...props.data.skills.frontend)
  if (props.data.skills.devops) skills.push(...props.data.skills.devops)
  return skills
})

const otherSkills = computed(() => {
  const skills: string[] = []
  if (props.data.skills.tools) skills.push(...props.data.skills.tools)
  if (props.data.skills.other) skills.push(...props.data.skills.other)
  return skills
})
</script>

<template>
  <article class="resume-template software-engineer" :style="{ '--primary-color': theme.primaryColor }">
    <!-- Header Section -->
    <header class="resume-header">
      <h1 class="name">{{ data.basics.fullName }}</h1>
      <h2 class="title">{{ data.basics.title }}</h2>
      
      <div class="contact-info">
        <span v-if="data.basics.email">
          <i class="fa-solid fa-envelope"></i> {{ data.basics.email }}
        </span>
        <span v-if="data.basics.phone">
          <i class="fa-solid fa-phone"></i> {{ data.basics.phone }}
        </span>
        <span v-if="data.basics.location">
          <i class="fa-solid fa-location-dot"></i> {{ data.basics.location }}
        </span>
        <span v-if="data.basics.github">
          <i class="fa-brands fa-github"></i> {{ data.basics.github }}
        </span>
        <span v-if="data.basics.linkedin">
          <i class="fa-brands fa-linkedin"></i> {{ data.basics.linkedin }}
        </span>
      </div>
    </header>

    <!-- Summary -->
    <section v-if="data.basics.summary" class="resume-section summary">
      <h3 class="section-title">Professional Summary</h3>
      <p class="summary-text">{{ data.basics.summary }}</p>
    </section>

    <!-- Technical Skills with Pill Tags -->
    <section v-if="technicalSkills.length > 0" class="resume-section skills">
      <h3 class="section-title">Technical Skills</h3>
      <div class="skill-pills">
        <span v-for="skill in technicalSkills" :key="skill" class="skill-pill">
          {{ skill }}
        </span>
      </div>
    </section>

    <!-- Experience -->
    <section v-if="data.experience.length > 0" class="resume-section experience">
      <h3 class="section-title">Experience</h3>
      
      <div v-for="exp in data.experience" :key="exp.id" class="experience-item">
        <div class="exp-header">
          <div>
            <h4 class="position">{{ exp.position }}</h4>
            <p class="company">{{ exp.company }}<span v-if="exp.location"> • {{ exp.location }}</span></p>
          </div>
          <div class="dates">{{ exp.startDate }} - {{ exp.current ? 'Present' : exp.endDate }}</div>
        </div>
        
        <ul v-if="exp.responsibilities.length > 0" class="responsibilities">
          <li v-for="(resp, idx) in exp.responsibilities.filter(r => r)" :key="idx">{{ resp }}</li>
        </ul>
      </div>
    </section>

    <!-- Projects (Software Engineer specific) -->
    <section v-if="hasProjects" class="resume-section projects">
      <h3 class="section-title">Projects</h3>
      
      <div v-for="project in data.projects" :key="project.id" class="project-item">
        <div class="project-header">
          <h4 class="project-name">{{ project.name }}</h4>
          <a v-if="project.url" :href="project.url" class="project-url">{{ project.url }}</a>
        </div>
        <p class="project-desc">{{ project.description }}</p>
        <div v-if="project.technologies.length > 0" class="project-tech">
          <span v-for="tech in project.technologies" :key="tech" class="tech-tag">{{ tech }}</span>
        </div>
        <ul v-if="project.highlights && project.highlights.length > 0" class="project-highlights">
          <li v-for="(highlight, idx) in project.highlights" :key="idx">{{ highlight }}</li>
        </ul>
      </div>
    </section>

    <!-- Education -->
    <section v-if="data.education.length > 0" class="resume-section education">
      <h3 class="section-title">Education</h3>
      
      <div v-for="edu in data.education" :key="edu.id" class="education-item">
        <div class="edu-header">
          <div>
            <h4 class="degree">{{ edu.degree }}</h4>
            <p class="institution">{{ edu.institution }}<span v-if="edu.location"> • {{ edu.location }}</span></p>
          </div>
          <div class="edu-year">{{ edu.year }}</div>
        </div>
        <p v-if="edu.gpa || edu.percentage" class="edu-gpa">
          {{ edu.gpa ? `GPA: ${edu.gpa}` : `${edu.percentage}` }}
        </p>
      </div>
    </section>

    <!-- Certifications (if any) -->
    <section v-if="hasCertifications" class="resume-section certifications">
      <h3 class="section-title">Certifications</h3>
      
      <div v-for="cert in data.certifications" :key="cert.id" class="certification-item">
        <h4 class="cert-name">{{ cert.name }}</h4>
        <p class="cert-issuer">{{ cert.issuer }} • {{ cert.date }}</p>
      </div>
    </section>

    <!-- Other Skills (if any) -->
    <section v-if="otherSkills.length > 0" class="resume-section other-skills">
      <h3 class="section-title">Additional Skills</h3>
      <p class="skills-list">{{ otherSkills.join(' • ') }}</p>
    </section>
  </article>
</template>

<style scoped>
.resume-template {
  /* Use CSS variables for theming */
  --primary: var(--primary-color, #4f46e5);
  
  /* Print-friendly styles */
  font-family: var(--resume-font-family, 'Inter', system-ui, -apple-system, sans-serif);
  font-size: calc(11pt * var(--resume-typography-scale, 1));
  line-height: 1.5;
  color: #1e293b;
  background: white;
  
  /* Proper print breaks */
  break-inside: avoid-page;
}

@media print {
  .resume-template {
    /* Disable animations and shadows in print */
    box-shadow: none !important;
    transform: none !important;
  }
}

/* Header Styles */
.resume-header {
  border-bottom: 3px solid var(--primary);
  padding-bottom: calc(1.5rem * var(--resume-spacing-scale, 1));
  margin-bottom: calc(2rem * var(--resume-spacing-scale, 1));
  break-inside: avoid;
}

.name {
  font-size: 2rem;
  font-weight: 800;
  color: #0f172a;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: -0.02em;
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
  margin-top: 0.75rem;
  font-size: 0.875rem;
  color: #64748b;
  font-weight: 600;
}

.contact-info i {
  margin-right: 0.25rem;
}

/* Section Styles */
.resume-section {
  margin-bottom: calc(2rem * var(--resume-spacing-scale, 1));
  break-inside: avoid-page;
}

.section-title {
  font-size: 0.875rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--primary);
  margin: 0 0 1rem;
}

/* Summary */
.summary-text {
  font-size: 0.938rem;
  color: #475569;
  line-height: 1.6;
  margin: 0;
}

/* Skills with Pills */
.skill-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.skill-pill {
  padding: 0.375rem 1rem;
  background: rgba(79, 70, 229, 0.1);
  border: 1px solid rgba(79, 70, 229, 0.2);
  border-radius: 9999px;
  font-size: 0.813rem;
  font-weight: 600;
  color: var(--primary);
}

/* Experience Items */
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
  margin: 0;
}

.company {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0.125rem 0 0;
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
  margin-bottom: 0.25rem;
}

/* Project Items */
.project-item {
  margin-bottom: 1.25rem;
  break-inside: avoid;
}

.project-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 0.25rem;
  gap: 1rem;
}

.project-name {
  font-size: 0.938rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.project-url {
  font-size: 0.75rem;
  color: var(--primary);
  text-decoration: none;
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
  margin-bottom: 0.5rem;
}

.tech-tag {
  font-size: 0.75rem;
  padding: 0.25rem 0.75rem;
  background: #f1f5f9;
  color: #475569;
  border-radius: 0.375rem;
  font-weight: 600;
}

.project-highlights {
  margin: 0;
  padding-left: 1.25rem;
  list-style: disc;
}

.project-highlights li {
  font-size: 0.813rem;
  color: #64748b;
  margin-bottom: 0.125rem;
}

/* Education Items */
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
  margin: 0;
}

.institution {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0.125rem 0 0;
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

/* Certification Items */
.certification-item {
  margin-bottom: 0.75rem;
  break-inside: avoid;
}

.cert-name {
  font-size: 0.875rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 0.125rem;
}

.cert-issuer {
  font-size: 0.813rem;
  color: #64748b;
  margin: 0;
}

/* Other Skills */
.skills-list {
  font-size: 0.875rem;
  color: #475569;
  margin: 0;
}

/* Print-specific adjustments */
@media print {
  .resume-section {
    page-break-inside: avoid;
  }
  
  .experience-item,
  .project-item,
  .education-item,
  .certification-item {
    page-break-inside: avoid;
  }
  
  .project-url {
    color: inherit;
  }
}
</style>
