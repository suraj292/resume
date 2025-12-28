# Builder2 - Gemini API Integration

## Setup Instructions

### 1. Get Your Gemini API Key

1. Visit [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy your API key (starts with `AIza...`)

### 2. Configure Environment Variables

Create or update your `.env` file in the `frontend` directory:

```bash
cp .env.example .env
```

Then edit `.env` and add your Gemini API key:

```bash
NUXT_PUBLIC_API_BASE=http://localhost:8000
NUXT_PUBLIC_GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

### 3. Alternative: Use UI to Set API Key

If you don't want to use environment variables, you can:

1. Navigate to `/builder2`
2. Click the "Set API Key" button in the navbar
3. Enter your API key in the modal
4. The key will be saved in localStorage

## Features

### AI-Powered Resume Enhancement

- **Optimize Text**: Click the "Enhance" button on any experience description to improve it with AI
- **Generate Summary**: Click "Generate with AI" in the Professional Summary section to create a compelling summary based on your experience and skills

### How It Works

The integration uses Google's Gemini 2.0 Flash model to:

1. **Text Optimization**: Rewrites bullet points to be more professional, impactful, and result-oriented
2. **Summary Generation**: Creates a compelling professional summary based on your role, skills, and experience

### API Key Priority

The application checks for API keys in this order:

1. Environment variable (`NUXT_PUBLIC_GEMINI_API_KEY`)
2. localStorage (set via UI modal)

This allows flexibility for both development and production environments.

## Usage

1. Navigate to `http://localhost:5174/builder2`
2. Fill in your resume details in the "Content" tab
3. Use AI features:
   - Click "Generate with AI" for professional summary
   - Click "Enhance" on experience descriptions
4. Select templates and colors
5. Preview your resume in real-time

## Security Notes

- Never commit your `.env` file to version control
- The `.env` file is already in `.gitignore`
- API keys stored in localStorage are only accessible from your browser
- For production, use server-side API calls to keep keys secure
