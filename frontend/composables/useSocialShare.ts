
/**
 * Social Share Composable
 * 
 * Handles native sharing via Web Share API and provides fallback
 * links for major social platforms.
 */

import { useToast } from './useToast'

export interface ShareOptions {
    title?: string
    text?: string
    url?: string
    files?: File[]
}

export const useSocialShare = () => {
    const { success, error: showError } = useToast()
    const isSupported = ref(false)

    onMounted(() => {
        isSupported.value = !!(navigator && navigator.share)
    })

    /**
     * Trigger native share sheet
     */
    const share = async (options: ShareOptions) => {
        if (!isSupported.value) {
            console.warn('Web Share API not supported')
            return false
        }

        try {
            await navigator.share({
                title: options.title || 'Check this out',
                text: options.text,
                url: options.url || window.location.href,
                files: options.files
            })
            success('Shared successfully', 'Success')
            return true
        } catch (err: any) {
            if (err.name !== 'AbortError') {
                showError('Failed to share content', 'Error')
                console.error('Share failed:', err)
            }
            return false
        }
    }

    /**
     * Generate social links for fallback
     */
    const generateSocialLinks = (options: { text: string, url: string }) => {
        const text = encodeURIComponent(options.text)
        const url = encodeURIComponent(options.url)

        return {
            linkedin: `https://www.linkedin.com/feed/?shareActive=true&text=${text}%20${url}`,
            twitter: `https://twitter.com/intent/tweet?text=${text}&url=${url}`,
            whatsapp: `https://wa.me/?text=${text}%20${url}`,
            email: `mailto:?subject=${encodeURIComponent('My Resume')}&body=${text}%20${url}`
        }
    }

    /**
     * Copy link to clipboard
     */
    const copyLink = async (url: string) => {
        try {
            await navigator.clipboard.writeText(url)
            success('Link copied to clipboard', 'Copied')
            return true
        } catch (err) {
            console.error('Copy failed:', err)
            showError('Failed to copy link', 'Error')
            return false
        }
    }

    return {
        isSupported,
        share,
        generateSocialLinks,
        copyLink
    }
}
