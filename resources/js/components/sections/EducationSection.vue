<template>
  <section :id="sectionId" :class="sectionClasses">
    <div :class="containerClasses">
      <!-- Section Header -->
      <div :class="headerClasses">
        <h2 :class="titleClasses">
          <component 
            :is="iconComponent" 
            v-if="showIcon" 
            :class="iconClasses"
          />
          {{ title }}
        </h2>
        <p v-if="subtitle" :class="subtitleClasses">
          {{ subtitle }}
        </p>
      </div>

      <!-- Education Items -->
      <div :class="itemsContainerClasses">
        <div
          v-for="(item, index) in educationItems"
          :key="index"
          :class="itemClasses"
        >
          <!-- Template: Default -->
          <template v-if="template === 'default'">
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <h3 :class="degreeClasses">{{ item.degree }}</h3>
                <p :class="institutionClasses">{{ item.institution }}</p>
                <p v-if="item.location" :class="locationClasses">
                  {{ item.location }}
                </p>
              </div>
              <div :class="dateContainerClasses">
                <span :class="dateClasses">{{ item.period }}</span>
                <span v-if="item.gpa" :class="gpaClasses">
                  GPA: {{ item.gpa }}
                </span>
              </div>
            </div>
            <ul v-if="item.highlights && item.highlights.length" :class="highlightsClasses">
              <li v-for="(highlight, hIndex) in item.highlights" :key="hIndex">
                {{ highlight }}
              </li>
            </ul>
          </template>

          <!-- Template: Card -->
          <template v-else-if="template === 'card'">
            <div :class="cardClasses">
              <div class="flex items-start gap-4">
                <div v-if="item.logo" :class="logoContainerClasses">
                  <img :src="item.logo" :alt="item.institution" class="w-full h-full object-contain" />
                </div>
                <div class="flex-1">
                  <div class="flex justify-between items-start">
                    <div>
                      <h3 :class="degreeClasses">{{ item.degree }}</h3>
                      <p :class="institutionClasses">{{ item.institution }}</p>
                    </div>
                    <span :class="dateClasses">{{ item.period }}</span>
                  </div>
                  <div class="flex flex-wrap gap-2 mt-2">
                    <span v-if="item.location" :class="badgeClasses">
                      {{ item.location }}
                    </span>
                    <span v-if="item.gpa" :class="badgeClasses">
                      GPA: {{ item.gpa }}
                    </span>
                  </div>
                  <ul v-if="item.highlights && item.highlights.length" :class="highlightsClasses">
                    <li v-for="(highlight, hIndex) in item.highlights" :key="hIndex">
                      {{ highlight }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </template>

          <!-- Template: Minimal -->
          <template v-else-if="template === 'minimal'">
            <div class="flex justify-between items-baseline">
              <div>
                <h3 :class="degreeClasses">{{ item.degree }}</h3>
                <p :class="institutionClasses">{{ item.institution }}</p>
              </div>
              <span :class="dateClasses">{{ item.period }}</span>
            </div>
          </template>

          <!-- Template: Timeline -->
          <template v-else-if="template === 'timeline'">
            <div class="flex gap-4">
              <div :class="timelineLineClasses">
                <div :class="timelineDotClasses"></div>
                <div v-if="index < educationItems.length - 1" :class="timelineConnectorClasses"></div>
              </div>
              <div class="flex-1 pb-8">
                <span :class="dateClasses">{{ item.period }}</span>
                <h3 :class="degreeClasses">{{ item.degree }}</h3>
                <p :class="institutionClasses">{{ item.institution }}</p>
                <p v-if="item.location" :class="locationClasses">
                  {{ item.location }}
                </p>
                <span v-if="item.gpa" :class="gpaClasses">
                  GPA: {{ item.gpa }}
                </span>
                <ul v-if="item.highlights && item.highlights.length" :class="highlightsClasses">
                  <li v-for="(highlight, hIndex) in item.highlights" :key="hIndex">
                    {{ highlight }}
                  </li>
                </ul>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'EducationSection',
  
  props: {
    // Section Configuration
    sectionId: {
      type: String,
      default: 'education'
    },
    title: {
      type: String,
      default: 'Education'
    },
    subtitle: {
      type: String,
      default: ''
    },
    
    // Education Data
    educationItems: {
      type: Array,
      required: true,
      validator: (items) => {
        return items.every(item => 
          item.degree && 
          item.institution && 
          item.period
        );
      }
    },
    
    // Template Selection
    template: {
      type: String,
      default: 'default',
      validator: (value) => {
        return ['default', 'card', 'minimal', 'timeline'].includes(value);
      }
    },
    
    // Icon Configuration
    showIcon: {
      type: Boolean,
      default: false
    },
    iconComponent: {
      type: [String, Object],
      default: 'span'
    },
    
    // Styling Props
    sectionClass: {
      type: String,
      default: 'py-16 bg-white'
    },
    containerClass: {
      type: String,
      default: 'max-w-6xl mx-auto px-4 sm:px-6 lg:px-8'
    },
    headerClass: {
      type: String,
      default: 'mb-12 text-center'
    },
    titleClass: {
      type: String,
      default: 'text-3xl md:text-4xl font-bold text-gray-900 mb-4 flex items-center justify-center gap-3'
    },
    subtitleClass: {
      type: String,
      default: 'text-lg text-gray-600 max-w-2xl mx-auto'
    },
    iconClass: {
      type: String,
      default: 'w-8 h-8 text-blue-600'
    },
    itemsContainerClass: {
      type: String,
      default: 'space-y-8'
    },
    itemClass: {
      type: String,
      default: 'relative'
    },
    degreeClass: {
      type: String,
      default: 'text-xl font-semibold text-gray-900 mb-1'
    },
    institutionClass: {
      type: String,
      default: 'text-lg text-gray-700 mb-1'
    },
    locationClass: {
      type: String,
      default: 'text-sm text-gray-500'
    },
    dateContainerClass: {
      type: String,
      default: 'text-right flex flex-col gap-1'
    },
    dateClass: {
      type: String,
      default: 'text-sm font-medium text-gray-600'
    },
    gpaClass: {
      type: String,
      default: 'text-sm text-gray-500'
    },
    highlightsClass: {
      type: String,
      default: 'mt-3 space-y-1 list-disc list-inside text-gray-600'
    },
    
    // Card Template Specific
    cardClass: {
      type: String,
      default: 'bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow duration-300'
    },
    logoContainerClass: {
      type: String,
      default: 'w-16 h-16 flex-shrink-0 bg-white rounded-lg p-2 shadow-sm'
    },
    badgeClass: {
      type: String,
      default: 'inline-block px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full'
    },
    
    // Timeline Template Specific
    timelineLineClass: {
      type: String,
      default: 'relative flex flex-col items-center'
    },
    timelineDotClass: {
      type: String,
      default: 'w-4 h-4 bg-blue-600 rounded-full border-4 border-white shadow-md z-10'
    },
    timelineConnectorClass: {
      type: String,
      default: 'w-0.5 h-full bg-gray-300 absolute top-4'
    }
  },
  
  computed: {
    sectionClasses() {
      return this.sectionClass;
    },
    containerClasses() {
      return this.containerClass;
    },
    headerClasses() {
      return this.headerClass;
    },
    titleClasses() {
      return this.titleClass;
    },
    subtitleClasses() {
      return this.subtitleClass;
    },
    iconClasses() {
      return this.iconClass;
    },
    itemsContainerClasses() {
      return this.itemsContainerClass;
    },
    itemClasses() {
      return this.itemClass;
    },
    degreeClasses() {
      return this.degreeClass;
    },
    institutionClasses() {
      return this.institutionClass;
    },
    locationClasses() {
      return this.locationClass;
    },
    dateContainerClasses() {
      return this.dateContainerClass;
    },
    dateClasses() {
      return this.dateClass;
    },
    gpaClasses() {
      return this.gpaClass;
    },
    highlightsClasses() {
      return this.highlightsClass;
    },
    cardClasses() {
      return this.cardClass;
    },
    logoContainerClasses() {
      return this.logoContainerClass;
    },
    badgeClasses() {
      return this.badgeClass;
    },
    timelineLineClasses() {
      return this.timelineLineClass;
    },
    timelineDotClasses() {
      return this.timelineDotClass;
    },
    timelineConnectorClasses() {
      return this.timelineConnectorClass;
    }
  }
};
</script>

<style scoped>
/* Additional custom styles can be added here if needed */
</style>
