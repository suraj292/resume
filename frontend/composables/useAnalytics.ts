
/**
 * Analytics Composable
 * 
 * A unified interface for tracking user events and page views.
 * Currently defaults to a console logger for development/privacy,
 * but can be easily extended to support Google Analytics, PostHog, etc.
 */

type AnalyticsDriver = 'console' | 'ga4' | 'posthog'

interface AnalyticsOptions {
    driver?: AnalyticsDriver
    debug?: boolean
}

export const useAnalytics = (options: AnalyticsOptions = {}) => {
    const config = useRuntimeConfig()
    const driver = options.driver || 'console'
    const isDebug = options.debug || process.dev

    /**
     * Track a specific user action
     * @param eventName Name of the event (e.g., 'template_selected')
     * @param properties Additional data associated with the event
     */
    const trackEvent = (eventName: string, properties: Record<string, any> = {}) => {
        const payload = {
            eventName,
            properties,
            timestamp: new Date().toISOString(),
            url: typeof window !== 'undefined' ? window.location.href : ''
        }

        if (driver === 'console' || isDebug) {
            console.groupCollapsed(`[Analytics] Event: ${eventName}`)
            console.log('Properties:', properties)
            console.log('Timestamp:', payload.timestamp)
            console.groupEnd()
        }

        // Future: Implement GA4 / PostHog calls here
        // if (driver === 'ga4') { ... }
    }

    /**
     * Track a page view
     * @param path The path being viewed
     */
    const trackPageView = (path: string) => {
        if (driver === 'console' || isDebug) {
            console.log(`[Analytics] Page View: ${path}`)
        }
    }

    return {
        trackEvent,
        trackPageView
    }
}
