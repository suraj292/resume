/**
 * Resume Builder Type Definitions
 * 
 * This file contains all TypeScript interfaces for the resume builder system.
 * All templates must conform to these interfaces for proper type safety.
 */

/**
 * Template-agnostic resume data structure
 * All templates must accept this exact interface as props
 */
export interface ResumeData {
    // Basic Information
    basics: {
        fullName: string
        title: string // Professional title
        email: string
        phone: string
        location: string
        linkedin?: string
        github?: string
        portfolio?: string
        summary?: string
    }

    // Work Experience
    experience: Array<{
        id: number | string
        position: string
        company: string
        location?: string
        startDate: string
        endDate: string | 'Present'
        current: boolean
        responsibilities: string[]
        achievements?: string[]
    }>

    // Education
    education: Array<{
        id: number | string
        degree: string
        institution: string
        location?: string
        year: string
        gpa?: string
        percentage?: string
        honors?: string[]
    }>

    // Skills (categorized)
    skills: {
        technical?: string[]
        backend?: string[]
        frontend?: string[]
        devops?: string[]
        languages?: string[]
        tools?: string[]
        soft?: string[]
        other?: string[]
    }

    // Projects (optional)
    projects?: Array<{
        id: number | string
        name: string
        description: string
        technologies: string[]
        url?: string
        highlights: string[]
    }>

    // Certifications (optional)
    certifications?: Array<{
        id: number | string
        name: string
        issuer: string
        date: string
        url?: string
    }>

    // Achievements/Awards (optional)
    achievements?: string[]

    // Languages (optional)
    languages?: Array<{
        language: string
        proficiency: 'Native' | 'Fluent' | 'Professional' | 'Conversational'
    }>
}

/**
 * Theme configuration interface
 * Controls visual appearance across all templates
 */
export interface ThemeConfig {
    // Primary accent color (hex format)
    primaryColor: string

    // Secondary color (optional)
    secondaryColor?: string

    // Font family
    fontFamily?: 'inter' | 'roboto' | 'outfit' | 'jakarta' | 'system'

    // Spacing density
    spacing?: 'compact' | 'normal' | 'relaxed'

    // Typography scale
    typographyScale?: 'small' | 'medium' | 'large'
}

/**
 * Template metadata (stored in templates.json)
 * Used ONLY for template selection UI and thumbnails
 */
export interface TemplateMetadata {
    id: string
    name: string
    component: string // Vue component name (e.g., 'SoftwareEngineerTemplate')
    type: string
    profession: string
    tags: string[]
    description: string

    // Thumbnail configuration (NOT used for actual resume rendering)
    thumbnail: {
        bg: string
        mainClass: string
        elements: any[] // Kept for backward compatibility with existing thumbnails
    }

    // Layout hints
    layout: {
        style: string
        headerAlign: 'left' | 'center'
        primaryColor: string
    }

    // Default theme for this template
    defaultTheme?: Partial<ThemeConfig>
}

/**
 * Props that ALL template components must accept
 * This ensures consistency across all templates
 */
export interface TemplateProps {
    data: ResumeData
    theme: ThemeConfig
}
