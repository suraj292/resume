
/**
 * Versions Composable
 * 
 * Manages named snapshots (versions) of the resume data.
 * Persists versions in localStorage.
 */

import { ref, onMounted } from 'vue'
import { useToast } from './useToast'
import type { ResumeData } from '~/types/resume'

export interface ResumeVersion {
    id: string
    name: string
    timestamp: Date
    data: any
}

export const useVersions = () => {
    const { success, error: showError } = useToast()
    const versions = ref<ResumeVersion[]>([])

    // Load versions from storage on mount
    onMounted(() => {
        if (process.client) {
            const stored = localStorage.getItem('resume_versions')
            if (stored) {
                try {
                    const parsed = JSON.parse(stored)
                    // Convert string timestamps back to Date objects
                    versions.value = parsed.map((v: any) => ({
                        ...v,
                        timestamp: new Date(v.timestamp)
                    }))
                } catch (e) {
                    console.error('Failed to parse versions', e)
                }
            }
        }
    })

    const persist = () => {
        if (process.client) {
            localStorage.setItem('resume_versions', JSON.stringify(versions.value))
        }
    }

    /**
     * Save a new version
     */
    const saveVersion = (name: string, data: any) => {
        const newVersion: ResumeVersion = {
            id: crypto.randomUUID(),
            name: name || `Version ${versions.value.length + 1}`,
            timestamp: new Date(),
            data: JSON.parse(JSON.stringify(data)) // Deep clone
        }

        versions.value.unshift(newVersion) // Add to top
        persist()
        success(`Version "${newVersion.name}" saved`, 'Saved')
    }

    /**
     * Restore a version
     * Returns the data to be applied
     */
    const restoreVersion = (id: string): any | null => {
        const version = versions.value.find(v => v.id === id)
        if (!version) return null

        success(`Restored "${version.name}"`, 'Restored')
        return JSON.parse(JSON.stringify(version.data))
    }

    /**
     * Delete a version
     */
    const deleteVersion = (id: string) => {
        const index = versions.value.findIndex(v => v.id === id)
        if (index !== -1) {
            versions.value.splice(index, 1)
            persist()
            success('Version deleted', 'Deleted')
        }
    }

    return {
        versions,
        saveVersion,
        restoreVersion,
        deleteVersion
    }
}
