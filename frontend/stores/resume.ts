import { defineStore } from 'pinia'
import type { ResumeData } from '~/types/resume'

interface ExperienceEntry {
    id: number
    position: string
    company: string
    location: string
    startDate: string
    endDate: string
    current: boolean
    responsibilities: string[]
}

interface EducationEntry {
    id: number
    degree: string
    institution: string
    year: string
    percentage: string
}

interface SkillsData {
    backend: string[]
    frontend: string[]
    devops: string[]
    other: string[]
}

interface FormData {
    fullName: string
    title: string
    email: string
    phone: string
    location: string
    linkedin: string
    github: string
    portfolio: string
    summary: string
    skills: SkillsData
    experience: ExperienceEntry[]
    education: EducationEntry[]
    achievements: string[]
}

interface ATSAnalysisData {
    atsScore: number
    atsAnalysisCompleted: boolean
    scoreGrade: string
    experienceLevel: string
    keywordMatchPercentage: number
    wordCount: number
    criticalIssuesCount: number
    matchedKeywords: any[]
    missingKeywords: any[]
    formattingChecks: any[]
    criticalIssues: any[]
    contentAnalysis: {
        action_verbs_percentage: number
        quantifiable_results_percentage: number
        avg_bullet_length: number
        reading_level: string
    }
}

export const useResumeStore = defineStore('resume', {
    state: () => ({
        // Form Data
        formData: {
            fullName: '',
            title: '',
            email: '',
            phone: '',
            location: '',
            linkedin: '',
            github: '',
            portfolio: '',
            summary: '',
            skills: {
                backend: [] as string[],
                frontend: [] as string[],
                devops: [] as string[],
                other: [] as string[]
            },
            experience: [
                {
                    id: 1,
                    position: '',
                    company: '',
                    location: '',
                    startDate: '',
                    endDate: '',
                    current: false,
                    responsibilities: ['']
                }
            ],
            education: [
                {
                    id: 1,
                    degree: '',
                    institution: '',
                    year: '',
                    percentage: ''
                }
            ],
            achievements: ['']
        } as FormData,

        // Loading States
        isProcessing: false,
        uploadError: '',
        successMessage: '',

        // Input Modes
        resumeInputMode: 'upload',
        jobInputMode: 'paste',
        resumeText: '',
        jobDescription: '',

        // AI Settings
        selectedTone: 'Professional',
        aiResults: {
            atsOptimization: null as any,
            improvedBullets: [] as string[],
            skillGapAnalysis: null as any
        },

        // ATS Analysis Results
        atsScore: 0,
        atsAnalysisCompleted: false,
        scoreGrade: 'Not Analyzed',
        experienceLevel: 'Not Detected',
        keywordMatchPercentage: 0,
        wordCount: 0,
        criticalIssuesCount: 0,
        matchedKeywords: [] as any[],
        missingKeywords: [] as any[],
        formattingChecks: [] as any[],
        criticalIssues: [] as any[],
        contentAnalysis: {
            action_verbs_percentage: 0,
            quantifiable_results_percentage: 0,
            avg_bullet_length: 0,
            reading_level: 'Unknown'
        }
    }),

    getters: {
        // Transform form data to ResumeData interface for templates
        resumeDataFormatted: (state): ResumeData => ({
            basics: {
                fullName: state.formData.fullName || '',
                title: state.formData.title || '',
                email: state.formData.email || '',
                phone: state.formData.phone || '',
                location: state.formData.location || '',
                linkedin: state.formData.linkedin,
                github: state.formData.github,
                portfolio: state.formData.portfolio,
                summary: state.formData.summary
            },
            experience: state.formData.experience || [],
            education: state.formData.education || [],
            skills: state.formData.skills || {},
            achievements: state.formData.achievements?.filter((a: string) => a) || []
        }),

        hasSkills: (state) => {
            return Object.values(state.formData.skills).some(arr => arr.length > 0)
        },

        hasExperience: (state) => {
            return state.formData.experience.length > 0 && state.formData.experience[0]?.position
        },

        // ATS score circle offset for SVG
        scoreCircleDashoffset: (state) => {
            const circumference = 264
            return circumference - (state.atsScore / 100) * circumference
        }
    },

    actions: {
        // Load dummy data
        async loadDummyData() {
            try {
                this.isProcessing = true

                // Add a delay to show the loading state
                await new Promise(resolve => setTimeout(resolve, 1500))

                const dummyData = await import('~/data/dummydata.json').then(m => m.default || m)

                // Populate personal information
                this.formData.fullName = dummyData.name || ''
                this.formData.title = dummyData.title || ''
                this.formData.email = dummyData.contact?.email || ''
                this.formData.phone = dummyData.contact?.phone || ''
                this.formData.location = dummyData.contact?.location || ''
                this.formData.linkedin = dummyData.contact?.linkedin || ''
                this.formData.github = ''
                this.formData.portfolio = dummyData.contact?.website || ''
                this.formData.summary = dummyData.summary || ''

                // Populate skills
                if (dummyData.skills) {
                    const technicalSkills = dummyData.skills.technical || []
                    this.formData.skills = {
                        backend: technicalSkills.filter((s: string) =>
                            ['Node.js', 'Python', 'PostgreSQL', 'MongoDB', 'GraphQL', 'REST APIs'].includes(s)
                        ),
                        frontend: technicalSkills.filter((s: string) =>
                            ['JavaScript', 'TypeScript', 'React'].includes(s)
                        ),
                        devops: technicalSkills.filter((s: string) =>
                            ['AWS', 'Docker', 'Kubernetes'].includes(s)
                        ),
                        other: dummyData.skills.soft || []
                    }
                }

                // Populate experience
                if (dummyData.experience && Array.isArray(dummyData.experience)) {
                    this.formData.experience = dummyData.experience.map((exp: any, index: number) => ({
                        id: index + 1,
                        position: exp.position || '',
                        company: exp.company || '',
                        location: exp.location || '',
                        startDate: exp.duration?.split(' - ')[0] || '',
                        endDate: exp.duration?.split(' - ')[1] || '',
                        current: exp.duration?.includes('Present') || false,
                        responsibilities: exp.achievements || ['']
                    }))
                }

                // Populate education
                if (dummyData.education && Array.isArray(dummyData.education)) {
                    this.formData.education = dummyData.education.map((edu: any, index: number) => ({
                        id: index + 1,
                        degree: edu.degree || '',
                        institution: edu.institution || '',
                        year: edu.duration || '',
                        percentage: edu.gpa || ''
                    }))
                }

                // Populate achievements
                const achievements: string[] = []
                if (dummyData.certifications && Array.isArray(dummyData.certifications)) {
                    dummyData.certifications.forEach((cert: any) => {
                        achievements.push(`${cert.name} - ${cert.issuer} (${cert.date})`)
                    })
                }
                if (dummyData.projects && Array.isArray(dummyData.projects)) {
                    dummyData.projects.forEach((project: any) => {
                        achievements.push(`${project.name}: ${project.description}`)
                    })
                }
                this.formData.achievements = achievements.length > 0 ? achievements : ['']

            } catch (error) {
                console.error('Failed to load dummy data:', error)
                this.uploadError = 'Failed to load sample data'
            } finally {
                this.isProcessing = false
            }
        },

        // Update basic info
        updateBasicInfo(field: string, value: string) {
            (this.formData as any)[field] = value
        },

        // Add experience entry
        addExperience() {
            const maxId = Math.max(...this.formData.experience.map(e => e.id), 0)
            this.formData.experience.push({
                id: maxId + 1,
                position: '',
                company: '',
                location: '',
                startDate: '',
                endDate: '',
                current: false,
                responsibilities: ['']
            })
        },

        // Remove experience entry
        removeExperience(id: number) {
            this.formData.experience = this.formData.experience.filter(e => e.id !== id)
        },

        // Add education entry
        addEducation() {
            const maxId = Math.max(...this.formData.education.map(e => e.id), 0)
            this.formData.education.push({
                id: maxId + 1,
                degree: '',
                institution: '',
                year: '',
                percentage: ''
            })
        },

        // Remove education entry
        removeEducation(id: number) {
            this.formData.education = this.formData.education.filter(e => e.id !== id)
        },

        // Update skills
        updateSkills(category: keyof SkillsData, skills: string[]) {
            this.formData.skills[category] = skills
        },

        // Update achievements
        updateAchievements(achievements: string[]) {
            this.formData.achievements = achievements
        },

        // Set ATS analysis results
        setATSAnalysis(analysis: Partial<ATSAnalysisData>) {
            if (analysis.atsScore !== undefined) this.atsScore = analysis.atsScore
            if (analysis.atsAnalysisCompleted !== undefined) this.atsAnalysisCompleted = analysis.atsAnalysisCompleted
            if (analysis.scoreGrade !== undefined) this.scoreGrade = analysis.scoreGrade
            if (analysis.experienceLevel !== undefined) this.experienceLevel = analysis.experienceLevel
            if (analysis.keywordMatchPercentage !== undefined) this.keywordMatchPercentage = analysis.keywordMatchPercentage
            if (analysis.wordCount !== undefined) this.wordCount = analysis.wordCount
            if (analysis.criticalIssuesCount !== undefined) this.criticalIssuesCount = analysis.criticalIssuesCount
            if (analysis.matchedKeywords !== undefined) this.matchedKeywords = analysis.matchedKeywords
            if (analysis.missingKeywords !== undefined) this.missingKeywords = analysis.missingKeywords
            if (analysis.formattingChecks !== undefined) this.formattingChecks = analysis.formattingChecks
            if (analysis.criticalIssues !== undefined) this.criticalIssues = analysis.criticalIssues
            if (analysis.contentAnalysis !== undefined) this.contentAnalysis = analysis.contentAnalysis
        },

        // Set processing state
        setProcessing(value: boolean) {
            this.isProcessing = value
        },

        // Set error message
        setError(message: string) {
            this.uploadError = message
        },

        // Set success message
        setSuccess(message: string) {
            this.successMessage = message
            setTimeout(() => {
                this.successMessage = ''
            }, 2000)
        },

        // Reset form
        resetForm() {
            this.formData = {
                fullName: '',
                title: '',
                email: '',
                phone: '',
                location: '',
                linkedin: '',
                github: '',
                portfolio: '',
                summary: '',
                skills: {
                    backend: [],
                    frontend: [],
                    devops: [],
                    other: []
                },
                experience: [{
                    id: 1,
                    position: '',
                    company: '',
                    location: '',
                    startDate: '',
                    endDate: '',
                    current: false,
                    responsibilities: ['']
                }],
                education: [{
                    id: 1,
                    degree: '',
                    institution: '',
                    year: '',
                    percentage: ''
                }],
                achievements: ['']
            }
            this.atsScore = 0
            this.atsAnalysisCompleted = false
            this.scoreGrade = 'Not Analyzed'
        }
    }
})
