import { ref } from 'vue'
import { useToast } from './useToast'
import type { ResumeData } from '~/types/resume'

export const useCoverLetter = () => {
    const { success, error: showError } = useToast()
    const coverLetter = ref('')
    const isGenerating = ref(false)

    const generateCoverLetter = async (resumeData: any, jobDescription: string) => {
        if (!jobDescription.trim()) {
            showError('Please provide a job description.', 'Missing Input')
            return
        }

        isGenerating.value = true
        coverLetter.value = ''

        try {
            const response = await $fetch<{ success: boolean; cover_letter: string }>('/api/ai/cover-letter', {
                method: 'POST',
                body: {
                    resumeData,
                    jobDescription
                }
            })

            if (response.success && response.cover_letter) {
                coverLetter.value = response.cover_letter.trim()
                success('Cover letter generated successfully!', 'AI Success')
            } else {
                throw new Error('Failed to generate cover letter')
            }
        } catch (err: any) {
            console.error('Cover Letter Generation Failed:', err)
            const msg = err.response?._data?.message || err.message || 'Failed to generate cover letter'
            showError(msg, 'AI Error')
        } finally {
            isGenerating.value = false
        }
    }

    return {
        coverLetter,
        isGenerating,
        generateCoverLetter
    }
}
