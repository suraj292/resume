import { defineAsyncComponent, type Component } from 'vue'

/**
 * Template component map with lazy loading
 */
export const useTemplateLoader = () => {
    const loadingComponent = {
        template: '<div class="template-loading">Loading template...</div>'
    }

    const errorComponent = {
        template: '<div class="template-error">Failed to load template</div>'
    }

    /**
     * Lazy load a template component
     * @param templateName - Name of the template component
     * @returns Async component
     */
    const loadTemplate = (templateName: string): Component => {
        return defineAsyncComponent({
            loader: () => import(`~/components/templates/${templateName}.vue`),
            loadingComponent,
            errorComponent,
            delay: 200,
            timeout: 10000
        })
    }

    /**
     * Preload a template component
     * @param templateName - Name of the template to preload
     */
    const preloadTemplate = async (templateName: string): Promise<void> => {
        try {
            await import(`~/components/templates/${templateName}.vue`)
        } catch (error) {
            console.warn(`Failed to preload template: ${templateName}`, error)
        }
    }

    /**
     * Preload multiple templates
     * @param templateNames - Array of template names to preload
     */
    const preloadTemplates = async (templateNames: string[]): Promise<void> => {
        await Promise.all(templateNames.map(name => preloadTemplate(name)))
    }

    return {
        loadTemplate,
        preloadTemplate,
        preloadTemplates
    }
}
