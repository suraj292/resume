
<script setup lang="ts">
import type { TemplateMetadata } from '~/types/resume'

const props = defineProps<{
    templates: TemplateMetadata[]
}>()

const selectedTemplateId = defineModel<string>({ required: true })
const hoveredTemplate = ref<TemplateMetadata | null>(null)

// Pagination
const currentPage = ref(1)
const pageSize = 6
const totalPages = computed(() => Math.ceil(props.templates.length / pageSize))

const paginatedTemplates = computed(() => {
    const start = (currentPage.value - 1) * pageSize
    const end = start + pageSize
    return props.templates.slice(start, end)
})

const itemsPerPageMessage = computed(() => {
    const start = (currentPage.value - 1) * pageSize + 1
    const end = Math.min(currentPage.value * pageSize, props.templates.length)
    return `Showing ${start}-${end} of ${props.templates.length} templates`
})

const selectTemplate = (id: string) => {
    selectedTemplateId.value = id
}

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++
    }
}

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--
    }
}
</script>

<template>
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:gap-6">
        <div v-for="template in paginatedTemplates" :key="template.id" 
            @click="selectTemplate(template.id)"
            @mouseenter="hoveredTemplate = template"
            @mouseleave="hoveredTemplate = null"
            :class="['template-card group relative bg-white border-2 p-2 rounded-2xl cursor-pointer hover:border-indigo-200 hover:shadow-lg transition-all', selectedTemplateId === template.id ? 'template-card-active border-indigo-600' : 'border-slate-100']">

            <!-- Dynamic Template Thumbnail -->
            <BuilderTemplateThumbnail :thumbnail="template.thumbnail" />

            <div class="px-1 sm:px-2 pb-1 sm:pb-2">
                <h3 class="font-bold text-xs sm:text-sm text-slate-800 truncate">{{ template.name }}</h3>
                <p class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5 truncate">{{ template.type }}</p>
                <div class="flex flex-wrap gap-1 mt-1">
                    <span v-for="tag in template.tags.slice(0, 2)" :key="tag"
                        class="text-[8px] sm:text-[9px] px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded">{{ tag }}</span>
                </div>
            </div>

            <div v-if="selectedTemplateId === template.id"
                class="absolute top-2 sm:top-4 right-2 sm:right-4 w-5 h-5 sm:w-6 sm:h-6 bg-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-check text-white text-[10px] sm:text-xs"></i>
            </div>
        </div>



        <!-- Pagination Controls -->
        <div v-if="totalPages > 1" class="col-span-2 flex flex-col items-center gap-2 mt-4 pt-4 border-t border-slate-100">
            <p class="text-[10px] text-slate-400 font-medium">{{ itemsPerPageMessage }}</p>
            <div class="flex items-center gap-3">
                <button 
                    @click="prevPage"
                    :disabled="currentPage === 1"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-indigo-300 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    title="Previous Page"
                >
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                
                <span class="text-xs font-bold text-slate-700">Page {{ currentPage }}</span>
                
                <button 
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-indigo-300 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    title="Next Page"
                >
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Hover Preview Portal -->
    <ClientOnly>
        <Teleport to="body">
            <div v-if="hoveredTemplate"
                class="fixed bottom-6 right-6 z-50 w-64 bg-white rounded-2xl shadow-2xl border-4 border-slate-900/10 p-2 hidden xl:block pointer-events-none transform transition-all duration-300 origin-bottom-right"
                style="animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                <div class="bg-slate-50 rounded-xl overflow-hidden">
                    <BuilderTemplateThumbnail :thumbnail="hoveredTemplate.thumbnail" />
                </div>
                <div class="mt-3 px-2 pb-1 text-center">
                    <p class="text-xs font-bold text-slate-900">{{ hoveredTemplate.name }}</p>
                    <p class="text-[10px] text-slate-500">{{ hoveredTemplate.type }}</p>
                </div>
            </div>
        </Teleport>
    </ClientOnly>

</template>

<style>
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
