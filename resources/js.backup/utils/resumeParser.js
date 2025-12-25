// Enhanced resume parsing function based on Suraj's resume
export function parseComprehensiveResume(text) {
    const data = {
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
        experience: [],
        education: [],
        achievements: []
    }

    if (!text) return data

    // Extract basic contact info
    const emailMatch = text.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/)
    if (emailMatch) data.email = emailMatch[0]

    const phoneMatch = text.match(/(?:\+?91[-\s]?)?[6-9]\d{9}|(?:\+?1[-.]?)?(?:\(?\d{3}\)?[-.]?)?\d{3}[-.]?\d{4}/)
    if (phoneMatch) data.phone = phoneMatch[0]

    // Extract LinkedIn
    const linkedinMatch = text.match(/linkedin\.com\/in\/[\w-]+/)
    if (linkedinMatch) data.linkedin = linkedinMatch[0]

    // Extract GitHub
    const githubMatch = text.match(/github\.com\/[\w-]+/)
    if (githubMatch) data.github = githubMatch[0]

    // Extract name (usually first line, all caps or title case)
    const lines = text.split('\n').map(l => l.trim()).filter(l => l)
    if (lines.length > 0) {
        const firstLine = lines[0]
        if (firstLine.length < 50 && /[A-Z]/.test(firstLine)) {
            data.fullName = firstLine
        }
    }

    // Extract title (usually second line with keywords)
    for (let i = 1; i < Math.min(lines.length, 5); i++) {
        if (/(Developer|Engineer|Designer|Manager|Analyst|Architect)/i.test(lines[i]) &&
            !/(PROFILE|SUMMARY|SKILLS|EXPERIENCE|EDUCATION)/i.test(lines[i])) {
            data.title = lines[i]
            break
        }
    }

    // Extract location
    const locationMatch = text.match(/([A-Z][a-z]+(?:abad|pur|garh)?(?:,\s*)?[A-Z][a-z]+)/i)
    if (locationMatch) data.location = locationMatch[1]

    // Extract profiles summary
    const summaryMatch = text.match(/PROFILE\s+SUMMARY\s*\n([\s\S]*?)\n(?:SKILLS|EXPERIENCE|EDUCATION)/i)
    if (summaryMatch) {
        data.summary = summaryMatch[1].trim().replace(/\n+/g, ' ')
    }

    // Extract skills
    const skillsMatch = text.match(/SKILLS\s*\n([\s\S]*?)\n(?:PROFESSIONAL|EXPERIENCE|EDUCATION)/i)
    if (skillsMatch) {
        const skillsText = skillsMatch[1]

        const backendMatch = skillsText.match(/Backend:\s*([^\n]+)/i)
        if (backendMatch) {
            data.skills.backend = backendMatch[1].split(/[,،]/).map(s => s.trim()).filter(s => s)
        }

        const frontendMatch = skillsText.match(/Frontend:\s*([^\n]+)/i)
        if (frontendMatch) {
            data.skills.frontend = frontendMatch[1].split(/[,،]/).map(s => s.trim()).filter(s => s)
        }

        const devopsMatch = skillsText.match(/DevOps:\s*([^\n]+)/i)
        if (devopsMatch) {
            data.skills.devops = devopsMatch[1].split(/[,،]/).map(s => s.trim()).filter(s => s)
        }

        const otherMatch = skillsText.match(/Other:\s*([^\n]+)/i)
        if (otherMatch) {
            data.skills.other = otherMatch[1].split(/[,،]/).map(s => s.trim()).filter(s => s)
        }
    }

    // Extract experience entries
    const expSection = text.match(/PROFESSIONAL\s+EXPERIENCE\s*\n([\s\S]*?)\n(?:EDUCATION|ACHIEVEMENTS|$)/i)
    if (expSection) {
        const expText = expSection[1]
        // Match job entries
        const jobPattern = /([^\n—]+)\s*—\s*([^\n(]+)\(([^)]+)\)/g
        let match
        let id = 1
        while ((match = jobPattern.exec(expText)) !== null) {
            const position = match[1].trim()
            const company = match[2].trim()
            const dates = match[3].trim()

            // Extract responsibilities (bullet points after the job title)
            const responsibilities = []
            const startIndex = match.index + match[0].length
            const nextJobMatch = jobPattern.exec(expText)
            const endIndex = nextJobMatch ? nextJobMatch.index : expText.length
            jobPattern.lastIndex = match.index + match[0].length

            const jobContent = expText.substring(startIndex, endIndex)
            const bulletPoints = jobContent.match(/[•▪-]\s*([^\n]+)/g)
            if (bulletPoints) {
                bulletPoints.forEach(bp => {
                    const clean = bp.replace(/^[•▪-]\s*/, '').trim()
                    if (clean) responsibilities.push(clean)
                })
            }

            data.experience.push({
                id: id++,
                position,
                company,
                location: '',
                startDate: dates.split('–')[0]?.trim() || '',
                endDate: dates.split('–')[1]?.trim() || 'Present',
                current: dates.toLowerCase().includes('present'),
                responsibilities: responsibilities.length ? responsibilities : ['']
            })
        }
    }

    // Extract education
    const eduSection = text.match(/EDUCATION\s*\n([\s\S]*?)\n(?:ACHIEVEMENTS|$)/i)
    if (eduSection) {
        const eduText = eduSection[1]
        const eduPattern = /([^—\n]+)\s*—\s*([^(]+)\(([^)]+)\)/g
        let match
        let id = 1
        while ((match = eduPattern.exec(eduText)) !== null) {
            data.education.push({
                id: id++,
                degree: match[1].trim(),
                institution: match[2].trim(),
                year: match[3].trim(),
                percentage: ''
            })
        }
    }

    // Extract achievements
    const achievementsSection = text.match(/ACHIEVEMENTS\s*\n([\s\S]+?)$/i)
    if (achievementsSection) {
        const achievements = achievementsSection[1].match(/[•▪-]\s*([^\n]+)/g)
        if (achievements) {
            data.achievements = achievements.map(a => a.replace(/^[•▪-]\s*/, '').trim()).filter(a => a)
        }
    }

    return data
}
