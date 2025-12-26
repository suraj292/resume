/**
 * Resume Theme Composable
 * 
 * Manages theme configuration and applies CSS variables for dynamic theming
 */

import type { ThemeConfig } from '~/types/resume'

export const useResumeTheme = () => {
    const defaultTheme: ThemeConfig = {
        primaryColor: '#4f46e5',
        secondaryColor: '#6366f1',
        fontFamily: 'inter',
        spacing: 'normal',
        typographyScale: 'medium'
    }

    const currentTheme = ref<ThemeConfig>({ ...defaultTheme })

    /**
     * Font family mapping to actual CSS font stacks
     */
    const fontFamilyMap: Record<string, string> = {
        inter: "'Inter', system-ui, -apple-system, sans-serif",
        roboto: "'Roboto', system-ui, -apple-system, sans-serif",
        outfit: "'Outfit', system-ui, -apple-system, sans-serif",
        jakarta: "'Plus Jakarta Sans', system-ui, -apple-system, sans-serif",
        system: "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
    }

    /**
     * Spacing scale multipliers
     */
    const spacingScaleMap: Record<string, string> = {
        compact: '0.875',
        normal: '1',
        relaxed: '1.25'
    }

    /**
     * Typography scale multipliers  
     */
    const typographyScaleMap: Record<string, string> = {
        small: '0.9',
        medium: '1',
        large: '1.1'
    }

    /**
     * Apply theme as CSS variables to a target element
     * 
     * @param theme - Theme configuration to apply
     * @param targetElement - DOM element to apply theme to (defaults to document root)
     */
    const applyTheme = (theme: ThemeConfig, targetElement?: HTMLElement) => {
        const element = targetElement || (process.client ? document.documentElement : null)

        if (!element) return

        // Apply primary color
        element.style.setProperty('--resume-primary-color', theme.primaryColor)

        // Apply secondary color if provided
        if (theme.secondaryColor) {
            element.style.setProperty('--resume-secondary-color', theme.secondaryColor)
        }

        // Apply font family
        if (theme.fontFamily) {
            const fontStack = fontFamilyMap[theme.fontFamily]
            if (fontStack) {
                element.style.setProperty('--resume-font-family', fontStack)
            }
        }

        // Apply spacing scale
        if (theme.spacing) {
            const spacingValue = spacingScaleMap[theme.spacing]
            if (spacingValue) {
                element.style.setProperty('--resume-spacing-scale', spacingValue)
            }
        }

        // Apply typography scale
        if (theme.typographyScale) {
            const typographyValue = typographyScaleMap[theme.typographyScale]
            if (typographyValue) {
                element.style.setProperty('--resume-typography-scale', typographyValue)
            }
        }
    }

    /**
     * Update current theme and apply changes
     * 
     * @param theme - Partial theme configuration to merge with current theme
     */
    const updateTheme = (theme: Partial<ThemeConfig>) => {
        currentTheme.value = { ...currentTheme.value, ...theme }
        applyTheme(currentTheme.value)
    }

    /**
     * Reset theme to default values
     */
    const resetTheme = () => {
        currentTheme.value = { ...defaultTheme }
        applyTheme(currentTheme.value)
    }

    /**
     * Get lighter/darker variant of a color
     * Useful for generating color palettes
     * 
     * @param hex - Hex color code
     * @param percent - Percentage to lighten (positive) or darken (negative)
     * @returns Modified hex color
     */
    const adjustColorBrightness = (hex: string, percent: number): string => {
        // Remove # if present
        hex = hex.replace('#', '')

        // Convert to RGB
        let r = parseInt(hex.substring(0, 2), 16)
        let g = parseInt(hex.substring(2, 4), 16)
        let b = parseInt(hex.substring(4, 6), 16)

        // Adjust brightness
        r = Math.max(0, Math.min(255, r + (r * percent / 100)))
        g = Math.max(0, Math.min(255, g + (g * percent / 100)))
        b = Math.max(0, Math.min(255, b + (b * percent / 100)))

        // Convert back to hex
        const toHex = (n: number) => {
            const hex = Math.round(n).toString(16)
            return hex.length === 1 ? '0' + hex : hex
        }

        return `#${toHex(r)}${toHex(g)}${toHex(b)}`
    }

    // Apply default theme on mount (client-side only)
    if (process.client) {
        onMounted(() => {
            applyTheme(currentTheme.value)
        })
    }

    return {
        currentTheme: readonly(currentTheme),
        applyTheme,
        updateTheme,
        resetTheme,
        adjustColorBrightness
    }
}
