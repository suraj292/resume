import DOMPurify from 'dompurify'

/**
 * Composable for sanitizing HTML content to prevent XSS attacks
 */
export const useSanitize = () => {
    /**
     * Sanitize HTML string
     * @param dirty - Potentially unsafe HTML string
     * @param options - DOMPurify configuration options
     * @returns Sanitized HTML string
     */
    const sanitizeHtml = (dirty: string, options?: any): string => {
        if (!process.client) {
            // On server-side, return empty string or implement server-side sanitization
            return ''
        }

        const result = DOMPurify.sanitize(dirty, {
            ALLOWED_TAGS: ['b', 'i', 'em', 'strong', 'a', 'p', 'br', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'div'],
            ALLOWED_ATTR: ['href', 'target', 'rel', 'class'],
            ALLOW_DATA_ATTR: false,
            ...options
        })

        return String(result)
    }

    /**
     * Sanitize HTML for display in v-html
     * More restrictive than general sanitization
     */
    const sanitizeForDisplay = (dirty: string): string => {
        return sanitizeHtml(dirty, {
            ALLOWED_TAGS: ['b', 'i', 'em', 'strong', 'p', 'br', 'ul', 'ol', 'li'],
            ALLOWED_ATTR: []
        })
    }

    /**
     * Strip all HTML tags, leaving only text
     */
    const stripHtml = (dirty: string): string => {
        if (!process.client) {
            return dirty.replace(/<[^>]*>/g, '')
        }

        return DOMPurify.sanitize(dirty, {
            ALLOWED_TAGS: [],
            KEEP_CONTENT: true
        })
    }

    return {
        sanitizeHtml,
        sanitizeForDisplay,
        stripHtml
    }
}
