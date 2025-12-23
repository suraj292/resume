<template>
  <div v-if="experiences.length > 0" class="experience-section">
    <h3 :class="headingClass" :style="headingStyle">
      <i v-if="showIcon" class="fa-solid fa-briefcase mr-2"></i>
      {{ title }}
    </h3>
    
    <div :class="containerClass">
      <div 
        v-for="(exp, idx) in displayExperiences" 
        :key="exp.id" 
        v-show="exp.position || exp.company"
        :class="itemClass">
        
        <!-- Header -->
        <div :class="headerClass">
          <div>
            <h4 
              :class="positionClass"
              :contenteditable="editable"
              @blur="editable && handleEdit(idx, 'position', $event)"
              suppressContentEditableWarning>
              <span>{{ exp.position }}</span>
              <span v-if="exp.company" :class="companyClass">
                {{ companySeparator }} {{ exp.company }}
              </span>
            </h4>
            <p v-if="exp.location && showLocation" :class="locationClass">
              <i class="fa-solid fa-location-dot text-[8px] mr-1"></i>
              {{ exp.location }}
            </p>
          </div>
          <span :class="dateClass" :style="dateStyle">
            {{ exp.startDate }}{{ exp.startDate && (exp.endDate || exp.current) ? ' - ' : '' }}{{ exp.current ? 'Present' : exp.endDate }}
          </span>
        </div>
        
        <!-- Responsibilities -->
        <ul v-if="exp.responsibilities && exp.responsibilities.length" :class="listClass">
          <li 
            v-for="(resp, respIdx) in exp.responsibilities" 
            :key="respIdx" 
            v-show="resp"
            :class="listItemClass"
            :contenteditable="editable"
            @blur="editable && handleEdit(idx, `responsibilities.${respIdx}`, $event)"
            suppressContentEditableWarning>
            <span :class="bulletClass" :style="bulletStyle">{{ bulletChar }}</span>
            <span>{{ resp }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  experiences: {
    type: Array,
    required: true
  },
  templateId: {
    type: String,
    default: 'business-analyst'
  },
  accentColor: {
    type: String,
    default: '#4f46e5'
  },
  editable: {
    type: Boolean,
    default: false
  },
  currentPage: {
    type: Number,
    default: 1
  },
  itemsPerPage: {
    type: Number,
    default: 3
  },
  isFirstPage: {
    type: Boolean,
    default: true
  },
  title: {
    type: String,
    default: 'Professional Experience'
  },
  showIcon: {
    type: Boolean,
    default: true
  },
  showLocation: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:experience'])

// Template-specific configurations
const templateConfig = computed(() => {
  const configs = {
    'software-engineer': {
      headingClass: 'text-[10px] font-black text-blue-800 uppercase tracking-[0.2em] mb-4 pb-2 border-b-2 border-blue-600',
      containerClass: 'space-y-4',
      itemClass: 'relative pl-4 border-l-2 border-blue-300',
      headerClass: 'flex flex-col sm:flex-row sm:justify-between sm:items-baseline gap-1',
      positionClass: 'font-bold text-slate-800 text-sm',
      companyClass: 'text-blue-600',
      companySeparator: '@',
      dateClass: 'text-[10px] font-mono bg-blue-100 text-blue-700 px-2 py-1 rounded',
      locationClass: 'text-[10px] text-slate-400 mt-1',
      listClass: 'text-[11px] text-slate-600 mt-2 space-y-1',
      listItemClass: 'flex items-start gap-2',
      bulletChar: '▸',
      bulletClass: 'text-blue-600 font-bold'
    },
    'data-scientist': {
      headingClass: 'text-[10px] font-black text-emerald-800 uppercase tracking-wider mb-4 flex items-center gap-2',
      containerClass: 'space-y-5',
      itemClass: 'border-l-3 border-emerald-500 pl-4 hover:bg-emerald-50/50 rounded-r py-2',
      headerClass: 'flex justify-between items-start mb-2',
      positionClass: 'font-bold text-sm text-slate-900',
      companyClass: 'text-emerald-700 font-semibold text-xs block',
      companySeparator: '',
      dateClass: 'text-[9px] bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full font-bold',
      listClass: 'text-[10px] text-slate-600 space-y-1',
      listItemClass: 'flex gap-2',
      bulletChar: '●',
      bulletClass: 'text-emerald-600'
    },
    'devops-engineer': {
      headingClass: 'text-[10px] font-bold text-orange-400 uppercase tracking-wider mb-4 font-mono flex items-center gap-2',
      containerClass: 'space-y-4',
      itemClass: 'bg-slate-800 rounded-lg p-4 border-l-4 border-orange-500',
      headerClass: 'flex justify-between items-start mb-2',
      positionClass: 'font-bold text-sm text-orange-400 font-mono',
      companyClass: 'text-emerald-400 text-xs block',
      companySeparator: '',
      dateClass: 'text-[9px] text-slate-400 font-mono bg-slate-700 px-2 py-1 rounded',
      listClass: 'text-[10px] text-slate-300 space-y-1 font-mono',
      listItemClass: 'flex gap-2',
      bulletChar: '→',
      bulletClass: 'text-orange-500'
    },
    'business-analyst': {
      headingClass: 'text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-5 pb-2 border-b-2',
      containerClass: 'space-y-5',
      itemClass: 'relative pl-4 border-l-2 hover:bg-slate-50/50 rounded-r-lg transition-colors py-1',
      headerClass: 'flex flex-col sm:flex-row sm:justify-between sm:items-baseline gap-1',
      positionClass: 'font-bold text-slate-800 text-sm',
      companyClass: 'font-medium text-slate-500',
      companySeparator: 'at',
      dateClass: 'text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap',
      locationClass: 'text-[10px] text-slate-400 mt-1 flex items-center gap-1',
      listClass: 'text-[11px] text-slate-600 mt-3 space-y-1.5',
      listItemClass: 'leading-relaxed flex items-start gap-2',
      bulletChar: '',
      bulletClass: 'w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0'
    },
    'product-manager': {
      headingClass: 'text-[11px] font-black text-purple-800 uppercase tracking-wider mb-4 flex items-center gap-2',
      containerClass: 'space-y-4',
      itemClass: 'bg-white rounded-xl p-4 border-2 border-purple-100 hover:border-purple-300 transition-colors shadow-sm',
      headerClass: 'flex justify-between items-start mb-2',
      positionClass: 'font-bold text-sm text-purple-800',
      companyClass: 'text-pink-600 font-semibold text-xs block',
      companySeparator: '',
      dateClass: 'text-[9px] bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-bold',
      listClass: 'text-[10px] text-slate-600 space-y-1',
      listItemClass: 'flex gap-2',
      bulletChar: '▪',
      bulletClass: 'text-purple-600'
    }
  }
  
  return configs[props.templateId] || configs['business-analyst']
})

// Computed styles
const headingStyle = computed(() => {
  if (['business-analyst', 'product-manager'].includes(props.templateId)) {
    return { borderColor: props.accentColor }
  }
  return {}
})

const dateStyle = computed(() => {
  if (props.templateId === 'business-analyst') {
    return {
      backgroundColor: props.accentColor + '15',
      color: props.accentColor
    }
  }
  return {}
})

const bulletStyle = computed(() => {
  if (props.templateId === 'business-analyst') {
    return { backgroundColor: props.accentColor }
  }
  return {}
})

// Paginated experiences
const displayExperiences = computed(() => {
  const start = (props.currentPage - 1) * props.itemsPerPage
  const end = start + props.itemsPerPage
  return props.experiences.slice(start, end)
})

// Dynamic classes from template config
const headingClass = computed(() => templateConfig.value.headingClass)
const containerClass = computed(() => templateConfig.value.containerClass)
const itemClass = computed(() => templateConfig.value.itemClass)
const headerClass = computed(() => templateConfig.value.headerClass)
const positionClass = computed(() => templateConfig.value.positionClass)
const companyClass = computed(() => templateConfig.value.companyClass)
const companySeparator = computed(() => templateConfig.value.companySeparator)
const dateClass = computed(() => templateConfig.value.dateClass)
const locationClass = computed(() => templateConfig.value.locationClass)
const listClass = computed(() => templateConfig.value.listClass)
const listItemClass = computed(() => templateConfig.value.listItemClass)
const bulletChar = computed(() => templateConfig.value.bulletChar)
const bulletClass = computed(() => templateConfig.value.bulletClass)

// Handle inline editing
const handleEdit = (idx, field, event) => {
  const globalIdx = (props.currentPage - 1) * props.itemsPerPage + idx
  const value = event.target.innerText.trim()
  
  if (field.includes('responsibilities')) {
    const respIdx = parseInt(field.split('.')[1])
    emit('update:experience', {
      index: globalIdx,
      field: 'responsibilities',
      subIndex: respIdx,
      value
    })
  } else {
    emit('update:experience', {
      index: globalIdx,
      field,
      value
    })
  }
}
</script>

<style scoped>
.border-l-3 {
  border-left-width: 3px;
}
</style>
