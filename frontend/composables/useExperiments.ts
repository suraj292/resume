
/**
 * Experiments Composable
 * 
 * Manages A/B testing experiments.
 * Handles variant assignment, persistence in localStorage, and exposure tracking.
 */

import { useAnalytics } from './useAnalytics'

export interface Experiment {
    id: string
    variants: string[]
    weights?: number[] // Optional weights (sum to 1), defaults to equal distribution
}

export const useExperiments = () => {
    const { trackEvent } = useAnalytics()

    /**
     * Get the assigned variant for a given experiment.
     * Assigns a new variant if one hasn't been assigned yet.
     */
    const getVariant = (experiment: Experiment): string => {
        if (!process.client) return experiment.variants[0] || '' // Default to control on server

        const storageKey = `experiment_${experiment.id}`
        let storedVariant = localStorage.getItem(storageKey)

        if (storedVariant && experiment.variants.includes(storedVariant)) {
            // Return existing assignment
            return storedVariant
        }

        // Assign new variant
        const variant = assignVariant(experiment)
        localStorage.setItem(storageKey, variant)

        // Track exposure
        trackEvent('experiment_exposure', {
            experiment_id: experiment.id,
            variant: variant
        })

        if (process.dev) {
            console.log(`[Experiments] Assigned ${experiment.id} -> ${variant}`)
        }

        return variant
    }

    /**
     * Helper to randomly assign a variant based on weights
     */
    const assignVariant = (experiment: Experiment): string => {
        if (!experiment.variants.length) return ''

        const weights = experiment.weights ||
            new Array(experiment.variants.length).fill(1 / experiment.variants.length)

        const random = Math.random()
        let sum = 0

        for (let i = 0; i < experiment.variants.length; i++) {
            sum += weights[i]
            if (random < sum) {
                return experiment.variants[i]!
            }
        }

        return experiment.variants[0]!
    }

    return {
        getVariant
    }
}
