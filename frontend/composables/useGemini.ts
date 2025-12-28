import { GoogleGenerativeAI } from '@google/generative-ai'

export const useGemini = () => {
    const config = useRuntimeConfig()

    const getApiKey = (): string => {
        // Try environment variable first
        let apiKey = config.public.geminiApiKey

        // Fall back to localStorage
        if (!apiKey && process.client) {
            apiKey = localStorage.getItem('gemini_api_key') || ''
        }

        if (!apiKey) {
            throw new Error('Gemini API key not found. Please set NUXT_PUBLIC_GEMINI_API_KEY in .env or add it via the UI.')
        }

        return apiKey
    }

    const extractTextFromFile = async (file: File): Promise<string> => {
        const apiKey = getApiKey()
        const genAI = new GoogleGenerativeAI(apiKey)
        const model = genAI.getGenerativeModel({ model: 'gemini-2.0-flash-exp' })

        // Convert file to base64
        const base64Data = await fileToBase64(file)
        const mimeType = file.type

        const prompt = `Extract all text content from this document. Return only the raw text without any formatting or markdown.`

        const result = await model.generateContent([
            {
                inlineData: {
                    data: base64Data,
                    mimeType: mimeType
                }
            },
            prompt
        ])

        const response = await result.response
        return response.text()
    }

    const parseResumeToStructuredData = async (text: string): Promise<any> => {
        const apiKey = getApiKey()
        const genAI = new GoogleGenerativeAI(apiKey)
        const model = genAI.getGenerativeModel({ model: 'gemini-2.0-flash-exp' })

        const prompt = `Parse the following resume text into structured JSON format. Extract:
- fullName (string)
- title (string - professional title/role)
- email (string)
- phone (string)
- location (string)
- linkedin (string - URL if available)
- github (string - URL if available)
- portfolio (string - URL if available)
- summary (string - professional summary)
- skills (object with arrays: backend, frontend, devops, other)
- experience (array of objects with: position, company, location, startDate, endDate, current (boolean), responsibilities (array of strings))
- education (array of objects with: degree, institution, year, percentage)
- achievements (array of strings)

Return ONLY valid JSON, no markdown formatting or code blocks.

Resume text:
${text}`

        const result = await model.generateContent(prompt)
        const response = await result.response
        const jsonText = response.text().replace(/```json\n?/g, '').replace(/```\n?/g, '').trim()

        return JSON.parse(jsonText)
    }

    const enhanceText = async (text: string, context?: string): Promise<string> => {
        const apiKey = getApiKey()
        const genAI = new GoogleGenerativeAI(apiKey)
        const model = genAI.getGenerativeModel({ model: 'gemini-2.0-flash-exp' })

        const prompt = context
            ? `${context}\n\nText to enhance: "${text}"`
            : `Rewrite the following resume bullet point to be more professional, impactful, and result-oriented using action verbs. Keep it concise (under 30 words). Text: "${text}"`

        const result = await model.generateContent(prompt)
        const response = await result.response
        return response.text().trim()
    }

    const generateSummary = async (context: { role: string; skills: string[]; experience: any[] }): Promise<string> => {
        const apiKey = getApiKey()
        const genAI = new GoogleGenerativeAI(apiKey)
        const model = genAI.getGenerativeModel({ model: 'gemini-2.0-flash-exp' })

        const contextText = `
      Role: ${context.role}
      Skills: ${context.skills.join(', ')}
      Experience: ${context.experience.map(e => `${e.position} at ${e.company}`).join(', ')}
    `

        const prompt = `Write a professional resume summary (max 3 sentences) for a ${context.role} based on this context: ${contextText}. Highlight key strengths and make it compelling.`

        const result = await model.generateContent(prompt)
        const response = await result.response
        return response.text().trim()
    }

    return {
        extractTextFromFile,
        parseResumeToStructuredData,
        enhanceText,
        generateSummary
    }
}

// Helper function to convert file to base64
const fileToBase64 = (file: File): Promise<string> => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader()
        reader.onload = () => {
            if (reader.result && typeof reader.result === 'string') {
                const base64 = reader.result.split(',')[1]
                if (base64) {
                    resolve(base64)
                } else {
                    reject(new Error('Invalid file format: could not extract base64 data'))
                }
            } else {
                reject(new Error('Failed to read file'))
            }
        }
        reader.onerror = reject
        reader.readAsDataURL(file)
    })
}
