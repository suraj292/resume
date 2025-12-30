
/**
 * Job Tracker Composable
 * 
 * Manages job applications for the tracker board.
 * Persists data to localStorage.
 */

import { ref, onMounted } from 'vue'
import { useToast } from './useToast'

export type JobStatus = 'wishlist' | 'applied' | 'interview' | 'offer' | 'rejected'

export interface JobApplication {
    id: string
    company: string
    position: string
    location?: string
    salary?: string
    status: JobStatus
    dateApplied?: string // ISO string
    notes?: string
    createdAt: number
}

export const useJobTracker = () => {
    const { success, error: showError } = useToast()
    const applications = ref<JobApplication[]>([])

    // Load from storage
    onMounted(() => {
        if (process.client) {
            const stored = localStorage.getItem('job_applications')
            if (stored) {
                try {
                    applications.value = JSON.parse(stored)
                } catch (e) {
                    console.error('Failed to parse job applications', e)
                }
            }
        }
    })

    const persist = () => {
        if (process.client) {
            localStorage.setItem('job_applications', JSON.stringify(applications.value))
        }
    }

    const addApplication = (job: Omit<JobApplication, 'id' | 'createdAt'>) => {
        const newJob: JobApplication = {
            ...job,
            id: crypto.randomUUID(),
            createdAt: Date.now()
        }
        applications.value.unshift(newJob)
        persist()
        success(`Added ${newJob.position} at ${newJob.company}`, 'Added')
    }

    const updateStatus = (id: string, status: JobStatus) => {
        const job = applications.value.find(j => j.id === id)
        if (job) {
            job.status = status
            persist()
            success(`Moved to ${status.charAt(0).toUpperCase() + status.slice(1)}`, 'Updated')
        }
    }

    const deleteApplication = (id: string) => {
        const index = applications.value.findIndex(j => j.id === id)
        if (index !== -1) {
            applications.value.splice(index, 1)
            persist()
            success('Job removed', 'Deleted')
        }
    }

    const updateApplication = (id: string, updates: Partial<JobApplication>) => {
        const job = applications.value.find(j => j.id === id)
        if (job) {
            Object.assign(job, updates)
            persist()
            success('Job updated', 'Saved')
        }
    }

    return {
        applications,
        addApplication,
        updateStatus,
        deleteApplication,
        updateApplication
    }
}
