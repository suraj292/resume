/**
 * Resume Template Composable
 * 
 * Handles dynamic loading and management of resume template components
 */

import type { TemplateMetadata } from '~/types/resume'

export const useResumeTemplate = () => {
    /**
     * Dynamic template component loader
     * Maps template.component to actual Vue component with lazy loading
     * 
     * @param componentName - Name of the Vue component (e.g., 'SoftwareEngineerTemplate')
     * @returns Async component that can be rendered with <component :is="...">
     */
    const loadTemplateComponent = (componentName: string) => {
        return defineAsyncComponent({
            loader: () =>
                import(`@/components/resume/templates/${componentName}.vue`)
                    .catch((error) => {
                        console.error(`Failed to load template component: ${componentName}`, error)
                        // Fallback to default template on error
                        return import('@/components/resume/templates/SoftwareEngineerTemplate.vue')
                    }),
            // Loading component (optional)
            loadingComponent: defineComponent({
                template: '<div class="p-16 text-center text-slate-400">Loading template...</div>'
            }),
            // Error component (optional)
            errorComponent: defineComponent({
                template: '<div class="p-16 text-center text-red-600">Failed to load template. Using default.</div>'
            }),
            delay: 200, // Delay before showing loading component
            timeout: 10000 // Timeout for loading
        })
    }

    /**
     * Get template component by template ID
     * 
     * @param templateId - The ID of the template (e.g., 'software-engineer')
     * @param templates - Array of template metadata from templates.json
     * @returns Promise resolving to the template component
     * @throws Error if template is not found
     */
    const getTemplateComponent = async (templateId: string, templates: TemplateMetadata[]) => {
        const template = templates.find(t => t.id === templateId)

        if (!template) {
            console.error(`Template not found: ${templateId}`)
            throw new Error(`Template not found: ${templateId}`)
        }

        if (!template.component) {
            console.error(`Template ${templateId} has no component field`)
            throw new Error(`Template ${templateId} is missing component field`)
        }

        return loadTemplateComponent(template.component)
    }

    /**
     * Validate if a template component exists
     * 
     * @param componentName - Name of the component to check
     * @returns Promise<boolean> indicating if component exists
     */
    const templateExists = async (componentName: string): Promise<boolean> => {
        try {
            await import(`@/components/resume/templates/${componentName}.vue`)
            return true
        } catch {
            return false
        }
    }

    return {
        loadTemplateComponent,
        getTemplateComponent,
        templateExists
    }
}
