import { defineStore } from 'pinia'
import { shallowRef } from 'vue'
import type { TemplateMetadata, ThemeConfig } from '~/types/resume'
import { useResumeTemplate } from '~/composables/useResumeTemplate'
import { useResumeTheme } from '~/composables/useResumeTheme'

interface ColorPalette {
    id: string
    name: string
    category: string
    hex: string
}

export const useTemplateStore = defineStore('template', {
    state: () => ({
        // Templates
        templates: [] as TemplateMetadata[],
        selectedTemplateId: 'software-engineer',
        isTemplateLoading: false,
        currentTemplateComponent: shallowRef(null) as any,

        // Colors
        selectedColorId: 'indigo',
        customColor: '#6366f1',
        colorPalettes: [
            { id: 'indigo', name: 'Royal Indigo', category: 'Default Corporate', hex: '#4f46e5' },
            { id: 'emerald', name: 'Growth Emerald', category: 'Finance & Healthcare', hex: '#059669' },
            { id: 'rose', name: 'Passion Rose', category: 'Creative & NGO', hex: '#e11d48' },
            { id: 'slate', name: 'Classic Slate', category: 'Modern Minimalist', hex: '#334155' },
            { id: 'amber', name: 'Solar Amber', category: 'High Energy & Sales', hex: '#d97706' },
            { id: 'violet', name: 'Deep Violet', category: 'Luxury & Visionary', hex: '#7c3aed' }
        ] as ColorPalette[],

        // Preview
        previewScale: 0.97,
        currentPage: 1,
        totalPages: 1,
        actualContentHeight: 0
    }),

    getters: {
        // Get current template metadata
        currentTemplate: (state): TemplateMetadata | undefined => {
            return state.templates.find(t => t.id === state.selectedTemplateId)
        },

        // Get current accent color (custom or from palette)
        currentAccentColor: (state): string => {
            if (state.customColor) return state.customColor
            const palette = state.colorPalettes.find(p => p.id === state.selectedColorId)
            return palette ? palette.hex : '#4f46e5'
        },

        // Get current theme configuration
        currentThemeConfig(): ThemeConfig {
            return {
                primaryColor: this.currentAccentColor,
                fontFamily: 'inter',
                spacing: 'normal',
                typographyScale: 'medium'
            }
        },

        // Group templates by category
        templatesByCategory: (state) => {
            const categories: Record<string, TemplateMetadata[]> = {}
            state.templates.forEach(template => {
                const category = template.type || 'Other'
                if (!categories[category]) {
                    categories[category] = []
                }
                categories[category].push(template)
            })
            return categories
        },

        // Template header class (dynamic based on template)
        templateHeaderClass(): string {
            const config = this.currentTemplate
            if (!config) return 'border-b-4 border-slate-900 pb-8 mb-8'

            switch (config.id) {
                case 'executive':
                    return 'text-center border-b-2 border-slate-900 pb-8 mb-8'
                case 'creative-designer':
                case 'marketing-professional':
                    return 'border-b-2 pb-6 mb-6'
                case 'data-scientist':
                    return 'border-b border-slate-300 pb-6 mb-6'
                default:
                    return 'border-b-4 border-slate-900 pb-8 mb-8'
            }
        },

        // Template name class (dynamic based on template)
        templateNameClass(): string {
            const config = this.currentTemplate
            if (!config) return 'text-3xl font-black text-slate-900 tracking-tight uppercase'

            switch (config.id) {
                case 'executive':
                    return 'text-4xl font-black text-slate-900 tracking-tight uppercase'
                case 'creative-designer':
                case 'content-creator':
                    return 'text-3xl font-bold text-slate-900'
                default:
                    return 'text-3xl font-black text-slate-900 tracking-tight uppercase'
            }
        },

        // Template primary color
        templatePrimaryColor(): string {
            const config = this.currentTemplate
            return config?.layout?.primaryColor || this.currentAccentColor
        }
    },

    actions: {
        // Load templates from JSON
        async loadTemplates() {
            try {
                const templatesData = await import('~/data/templates.json').then(m => m.default || m) as TemplateMetadata[]
                this.templates = templatesData

                // Set default template if not already set
                if (!this.selectedTemplateId && templatesData.length > 0) {
                    this.selectedTemplateId = templatesData[0]?.id || 'software-engineer'
                }

                // Load the initial template component
                await this.loadTemplateComponent()
            } catch (error) {
                console.error('Failed to load templates:', error)
            }
        },

        // Load template component dynamically
        async loadTemplateComponent() {
            try {
                this.isTemplateLoading = true
                this.currentTemplateComponent = null

                await nextTick() // Ensure cleanup

                const { getTemplateComponent } = useResumeTemplate()
                this.currentTemplateComponent = await getTemplateComponent(
                    this.selectedTemplateId,
                    this.templates
                )
            } catch (error) {
                console.error('Template loading failed:', error)
                throw error
            } finally {
                this.isTemplateLoading = false
            }
        },

        // Select a template
        async selectTemplate(templateId: string) {
            console.log('Selecting template:', templateId)
            this.selectedTemplateId = templateId

            try {
                await this.loadTemplateComponent()

                const template = this.templates.find(t => t.id === templateId)
                if (template) {
                    console.log('Template found:', template)
                    // Success message will be handled in the component
                    return template
                }
            } catch (error) {
                console.error('Failed to select template:', error)
                throw error
            }
        },

        // Set custom color
        setCustomColor(color: string) {
            this.customColor = color
            this.selectedColorId = '' // Clear palette selection
            this.updateTheme()
        },

        // Select color from palette
        selectPaletteColor(colorId: string) {
            this.selectedColorId = colorId
            this.customColor = '' // Clear custom color
            this.updateTheme()
        },

        // Update theme (applies to composable)
        updateTheme() {
            const { updateTheme } = useResumeTheme()
            updateTheme(this.currentThemeConfig)
        },

        // Adjust preview zoom
        adjustZoom(delta: number) {
            this.previewScale = Math.max(0.3, Math.min(1.5, this.previewScale + delta))
        },

        // Set actual content height (for page splitting)
        setContentHeight(height: number) {
            this.actualContentHeight = height
        },

        // Set current page
        setCurrentPage(page: number) {
            this.currentPage = page
        },

        // Set total pages
        setTotalPages(pages: number) {
            this.totalPages = pages
        }
    }
})
