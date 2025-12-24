# Resume Builder - API-First Architecture

A modern resume builder application with **separated frontend and backend**.

## Architecture

```
┌─────────────────────────────────┐
│   Frontend (Node.js + Vue 3)   │
│   Port: 5173                    │
│   Location: /frontend           │
│                                 │
│   - Vue 3 + Vite SPA           │
│   - Tailwind CSS               │
│   - 13 Resume Templates        │
│   - Pinia State Management     │
└─────────────────────────────────┘
              ↓↑ REST API
┌─────────────────────────────────┐
│   Backend (Laravel API)         │
│   Port: 8000                    │
│   Location: / (root)            │
│                                 │
│   - Laravel 11                  │
│   - API Routes Only             │
│   - Gemini AI Integration       │
│   - Puppeteer PDF Export        │
└─────────────────────────────────┘
```

## Project Structure

```
resume-builder/
├── frontend/              ← Standalone Vue.js Frontend
│   ├── src/
│   │   ├── views/         ← Pages (Builder, etc.)
│   │   ├── components/    ← UI Components + Templates
│   │   ├── api/           ← Laravel API Clients
│   │   ├── stores/        ← Pinia Stores
│   │   └── router/        ← Vue Router
│   ├── package.json       ← Frontend dependencies
│   └── vite.config.js     ← Vite configuration
│
├── app/                   ← Laravel Backend (API Only)
│   ├── Http/Controllers/  ← API Controllers
│   ├── Models/            ← Eloquent Models
│   └── Services/          ← Business Logic
├── routes/
│   └── api.php            ← API Routes
├── config/
│   └── cors.php           ← CORS Configuration
└── composer.json          ← Backend dependencies
```

## Running the Application

### Backend (Laravel API)

```bash
# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Start server
php artisan serve  # http://localhost:8000
```

### Frontend (Vue SPA)

```bash
# Navigate to frontend
cd frontend

# Install dependencies
npm install

# Start dev server
npm run dev  # http://localhost:5173
```

## Environment Configuration

### Backend `.env`
```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

# Enable CORS for frontend
SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DOMAIN=localhost
```

### Frontend `.env.development`
```env
VITE_API_URL=http://localhost:8000
```

## API Endpoints

### Resume Management
- `GET /api/resumes` - List all resumes
- `POST /api/resumes` - Create new resume
- `GET /api/resumes/{id}` - Get resume
- `PUT /api/resumes/{id}` - Update resume
- `DELETE /api/resumes/{id}` - Delete resume

### AI Features
- `POST /api/ai/generate` - Generate resume from text
- `POST /api/ai/optimize` - Optimize for ATS
- `POST /api/ai/enhance` - Enhance section content

### Export & Upload
- `POST /api/export/pdf` - Export as PDF
- `POST /api/upload/extract` - Extract text from file

### ATS Checker
- `POST /api/ats-check` - Analyze resume for ATS

## Features

### Frontend Features
- ✅ 13 Professional Resume Templates
- ✅ Live Preview with Instant Updates
- ✅ Template Switching
- ✅ Color Customization
- ✅ Drag-and-Drop File Upload
- ✅ Real-time Form Validation
- ✅ Mobile Responsive Design

### Backend Features
- ✅ RESTful API Architecture
- ✅ Gemini AI Integration
- ✅ PDF Generation (Puppeteer)
- ✅ File Upload & Parsing
- ✅ ATS Score Analysis
- ✅ Content Optimization

## Technology Stack

### Frontend
- **Framework**: Vue 3 (Composition API)
- **Build Tool**: Vite
- **Styling**: Tailwind CSS
- **State**: Pinia
- **Routing**: Vue Router 4
- **HTTP**: Axios

### Backend
- **Framework**: Laravel 11
- **Database**: MySQL/PostgreSQL
- **AI**: Google Gemini API
- **PDF**: Puppeteer
- **Authentication**: Laravel Sanctum (planned)

## Deployment

### Frontend
Deploy to Vercel, Netlify, or any static hosting:

```bash
cd frontend
npm run build
# Upload dist/ folder to hosting
```

Set production API URL:
```env
VITE_API_URL=https://api.yourapp.com
```

### Backend
Deploy to your Laravel hosting (Forge, Vapor, etc.)

Configure CORS for production frontend URL.

## Development Workflow

1. **Backend Development**
   - Implement API endpoints in `routes/api.php`
   - Create controllers in `app/Http/Controllers`
   - Test with Postman or curl

2. **Frontend Development**
   - Update API calls in `frontend/src/api/`
   - Build UI in `frontend/src/views/`
   - Test with dev server

3. **Integration Testing**
   - Ensure CORS is configured
   - Test end-to-end flows
   - Verify API responses

## CORS Configuration

The backend must allow requests from the frontend origin.

**config/cors.php**:
```php
'paths' => ['api/*'],
'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

## License

MIT

## Support

For issues or questions, please open an issue on GitHub.
