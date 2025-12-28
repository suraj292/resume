# Gemini API Frontend Integration

## Overview

The resume builder now uses the **Google Generative AI SDK** (`@google/generative-ai`) directly in the frontend for all AI-powered features. This eliminates the need for backend API calls for text extraction and parsing.

## Setup

### 1. Install Dependencies

The required package is already installed:
```bash
npm install @google/generative-ai
```

### 2. Configure Environment Variables

Add your Gemini API key to `.env`:

```bash
NUXT_PUBLIC_API_BASE=http://localhost:8000
NUXT_PUBLIC_GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

Get your API key from: https://aistudio.google.com/app/apikey

### 3. Runtime Configuration

The API key is exposed via `nuxt.config.ts`:

```typescript
runtimeConfig: {
  public: {
    apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000',
    geminiApiKey: process.env.NUXT_PUBLIC_GEMINI_API_KEY || ''
  }
}
```

## Architecture

### Composable: `useGemini.ts`

Located at `/frontend/composables/useGemini.ts`, this composable provides all Gemini AI functionality:

#### Functions

1. **`extractTextFromFile(file: File): Promise<string>`**
   - Extracts text from PDF, DOCX, or TXT files
   - Uses Gemini's multimodal capabilities
   - Converts file to base64 and sends to Gemini API

2. **`parseResumeToStructuredData(text: string): Promise<any>`**
   - Parses extracted text into structured JSON
   - Returns formatted resume data with all fields
   - Handles skills categorization, experience, education, etc.

3. **`enhanceText(text: string, context?: string): Promise<string>`**
   - Improves resume bullet points
   - Makes text more professional and impactful
   - Uses action verbs and result-oriented language

4. **`generateSummary(context): Promise<string>`**
   - Creates professional summary based on role, skills, and experience
   - Context-aware generation
   - Maximum 3 sentences

### API Key Priority

The composable checks for API keys in this order:

1. **Environment Variable** (`NUXT_PUBLIC_GEMINI_API_KEY`)
2. **localStorage** (set via UI modal)
3. **Throws error** if not found

## Usage Examples

### In builder.vue

```typescript
// Extract and parse resume
const handleResumeUpload = async (event: Event) => {
  const file = event.target.files?.[0]
  if (!file) return

  const { extractTextFromFile, parseResumeToStructuredData } = useGemini()
  
  // Extract text
  const text = await extractTextFromFile(file)
  
  // Parse to structured data
  const parsedData = await parseResumeToStructuredData(text)
  
  // Populate form
  populateFormWithParsedData(parsedData)
}
```

### In builder2.vue

```typescript
// Enhance text
const optimizeText = async (index: number) => {
  const { enhanceText } = useGemini()
  const original = resumeData.experience[index].description
  const enhanced = await enhanceText(original)
  resumeData.experience[index].description = enhanced
}

// Generate summary
const generateSummary = async () => {
  const { generateSummary: geminiGenerateSummary } = useGemini()
  const context = {
    role: resumeData.personal.title,
    skills: resumeData.skills,
    experience: resumeData.experience
  }
  const summary = await geminiGenerateSummary(context)
  resumeData.summary = summary
}
```

## Features

### ✅ Text Extraction
- **PDF Support**: Extracts text from PDF files using Gemini's vision capabilities
- **DOCX Support**: Processes Word documents
- **TXT Support**: Direct text file reading

### ✅ AI Parsing
- **Structured Data**: Converts raw text to JSON format
- **Field Detection**: Automatically identifies name, email, phone, etc.
- **Skills Categorization**: Separates skills into backend, frontend, devops, other
- **Experience Parsing**: Extracts job titles, companies, dates, responsibilities

### ✅ Text Enhancement
- **Professional Tone**: Rewrites bullet points with action verbs
- **Result-Oriented**: Focuses on achievements and impact
- **Concise**: Keeps descriptions under 30 words

### ✅ Summary Generation
- **Context-Aware**: Uses role, skills, and experience
- **Compelling**: Highlights key strengths
- **Professional**: 3-sentence format

## Error Handling

All functions include comprehensive error handling:

```typescript
try {
  const { extractTextFromFile } = useGemini()
  const text = await extractTextFromFile(file)
} catch (error) {
  if (error.message.includes('API key not found')) {
    // Show API key modal
    showApiModal.value = true
  } else {
    // Show error toast
    toastr.error(error.message, 'Error')
  }
}
```

## Benefits of Frontend Integration

1. **🚀 Faster**: No backend round-trip for AI operations
2. **💰 Cost-Effective**: Direct API calls, no server processing
3. **🔒 Secure**: API keys can be managed client-side or server-side
4. **📦 Simpler**: Fewer dependencies on backend services
5. **🎯 Flexible**: Easy to add new AI features

## Migration Notes

### Before (Backend API)
```typescript
const data = await $fetch('/api/extract-text?parse=true', {
  method: 'POST',
  body: formData
})
```

### After (Frontend Composable)
```typescript
const { extractTextFromFile, parseResumeToStructuredData } = useGemini()
const text = await extractTextFromFile(file)
const data = await parseResumeToStructuredData(text)
```

## Security Considerations

- ✅ API keys stored in environment variables (not committed to git)
- ✅ localStorage fallback for development
- ✅ Error messages don't expose sensitive data
- ⚠️ For production, consider server-side proxy for API calls
- ⚠️ Implement rate limiting to prevent API abuse

## Testing

Test the integration:

1. Navigate to `/builder` or `/builder2`
2. Upload a resume PDF/DOCX
3. Verify text extraction works
4. Check that fields are populated correctly
5. Test "Enhance" button on experience descriptions
6. Test "Generate with AI" for summary

## Troubleshooting

### "API key not found" error
- Check `.env` file has `NUXT_PUBLIC_GEMINI_API_KEY`
- Restart dev server after adding env variable
- Verify API key starts with `AIza`

### Text extraction fails
- Ensure file is valid PDF/DOCX
- Check file size (should be under 10MB)
- Verify API key has proper permissions

### Parsing returns empty data
- Check console for JSON parsing errors
- Verify resume has standard format
- Try with different resume file

## Future Enhancements

- [ ] Add caching for repeated API calls
- [ ] Implement retry logic for failed requests
- [ ] Add progress indicators for long operations
- [ ] Support for more file formats (RTF, ODT)
- [ ] Batch processing for multiple files
- [ ] ATS score calculation using Gemini
