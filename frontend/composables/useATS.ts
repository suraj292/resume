import { computed, type Ref } from 'vue'
import type { ResumeData } from '~/types/resume'

export const useATS = (formData: Ref<ResumeData>, jobDescription: Ref<string>) => {

    // Keywords that are commonly looked for
    const commonKeywords = [
        'communication', 'leadership', 'project management', 'teamwork',
        'problem solving', 'python', 'javascript', 'typescript', 'react', 'vue',
        'node', 'java', 'c++', 'sql', 'aws', 'docker', 'kubernetes', 'git',
        'agile', 'scrum', 'html', 'css', 'rest api'
    ]

    // Helper to count words
    const countWords = (str: string) => {
        return str ? str.trim().split(/\s+/).length : 0
    }

    // Helper to extract keywords from text
    const extractKeywords = (text: string): string[] => {
        if (!text) return []
        const lowerText = text.toLowerCase()
        // Simple extraction based on common tech terms + any words > 4 chars in JD
        // In a real app, we'd use a better NLP or a predefined dictionary
        const words = lowerText.match(/\b\w{4,}\b/g) || []

        // Filter to keep only words that are likely significant (e.g., in commonKeywords or appear frequently)
        // For now, let's stick to matching against our common list PLUS words from the JD
        return [...new Set(words)]
    }

    const analysis = computed(() => {
        let score = 0
        const checks: { label: string; passed: boolean; weight: number; feedback: string }[] = []

        const data = formData.value

        // 1. Contact Info (15%)
        const hasName = !!data.basics?.fullName
        const hasEmail = !!data.basics?.email
        const hasPhone = !!data.basics?.phone
        const hasLink = !!(data.basics?.linkedin || data.basics?.portfolio || data.basics?.github)

        if (hasName) score += 5
        else checks.push({ label: 'Name', passed: false, weight: 5, feedback: 'Add your full name' })

        if (hasEmail) score += 5
        else checks.push({ label: 'Email', passed: false, weight: 5, feedback: 'Add your email address' })

        if (hasPhone) score += 2.5
        else checks.push({ label: 'Phone', passed: false, weight: 2.5, feedback: 'Add a phone number' })

        if (hasLink) score += 2.5
        else checks.push({ label: 'Links', passed: false, weight: 2.5, feedback: 'Add a LinkedIn, GitHub, or Portfolio link' })

        // 2. Summary (10%)
        const summaryLen = data.basics?.summary ? data.basics.summary.length : 0
        if (summaryLen > 50) score += 10
        else if (summaryLen > 0) {
            score += 5
            checks.push({ label: 'Summary Length', passed: false, weight: 5, feedback: 'Summary is too short (< 50 chars)' })
        } else {
            checks.push({ label: 'Summary', passed: false, weight: 10, feedback: 'Add a professional summary' })
        }

        // 3. Experience (25%)
        const expCount = data.experience?.filter(e => e.position && e.company).length || 0
        const hasDetailedExp = data.experience?.some(e => e.responsibilities?.some(r => r.length > 20)) || false

        if (expCount >= 1) score += 15
        else checks.push({ label: 'Experience', passed: false, weight: 15, feedback: 'Add at least one work experience' })

        if (hasDetailedExp) score += 10
        else if (expCount >= 1) checks.push({ label: 'Experience Details', passed: false, weight: 10, feedback: 'Add detailed bullet points to experience' })

        // 4. Skills (20%)
        const allSkills = [
            ...(data.skills?.backend || []),
            ...(data.skills?.frontend || []),
            ...(data.skills?.devops || []),
            ...(data.skills?.other || [])
        ]
        const skillCount = allSkills.length
        if (skillCount >= 5) score += 20
        else if (skillCount > 0) {
            score += 10
            checks.push({ label: 'Skill Count', passed: false, weight: 10, feedback: 'Add more skills (aim for 5+)' })
        } else {
            checks.push({ label: 'Skills', passed: false, weight: 20, feedback: 'List your key technical skills' })
        }

        // 5. Education (10%)
        const eduCount = data.education?.filter(e => e.degree && e.institution).length || 0
        if (eduCount >= 1) score += 10
        else checks.push({ label: 'Education', passed: false, weight: 10, feedback: 'Add your educational background' })

        // 6. Keywords / JD Match (20%)
        // If JD is present, match against it. If not, match against common tech keywords.
        let keywordScore = 0
        const resumeText = JSON.stringify(data).toLowerCase()

        const targetKeywords = jobDescription.value
            ? extractKeywords(jobDescription.value).filter(k => k.length > 3) // Filter short words
            : commonKeywords

        // Find intersection
        const matched = targetKeywords.filter(k => resumeText.includes(k.toLowerCase()))
        const matchRate = targetKeywords.length > 0 ? matched.length / targetKeywords.length : 0

        // Cap keyword score at 20
        // If no JD, we are lenient. If JD, strict.
        const keywordFactor = jobDescription.value ? 20 : 20
        const calculatedKeywordScore = Math.min(20, Math.ceil(matchRate * keywordFactor))
        // Bonus for having ANY keywords if JD is empty to act as a baseline
        if (!jobDescription.value && allSkills.length > 5) {
            score += 20
        } else {
            score += calculatedKeywordScore
        }

        if (calculatedKeywordScore < 10 && jobDescription.value) {
            checks.push({ label: 'Keywords', passed: false, weight: 10, feedback: 'Low keyword match with Job Description' })
        }

        return {
            score: Math.min(100, Math.round(score)),
            checks: checks.filter(c => !c.passed),
            missingKeywords: jobDescription.value ? targetKeywords.filter(k => !resumeText.includes(k.toLowerCase())).slice(0, 5) : []
        }
    })

    return {
        realTimeScore: computed(() => analysis.value.score),
        realTimeSuggestions: computed(() => analysis.value.checks),
        missingKeywords: computed(() => analysis.value.missingKeywords)
    }
}
