import { z } from 'zod'

// Shared schemas
export const optionalString = z.string().optional().or(z.literal(''))
export const optionalUrl = z.string().url().optional().or(z.literal(''))

// Personal Info Schema
export const personalInfoSchema = z.object({
    fullName: z.string().min(2, 'Name must be at least 2 characters').max(50, 'Name must be under 50 characters'),
    title: z.string().min(2, 'Job title is required').max(100),
    email: z.string().email('Invalid email address'),
    phone: z.string().min(10, 'Phone number must be valid').optional().or(z.literal('')),
    location: optionalString,
    linkedin: optionalUrl,
    github: optionalUrl,
    portfolio: optionalUrl,
    summary: optionalString
})

export type PersonalInfoValues = z.infer<typeof personalInfoSchema>
