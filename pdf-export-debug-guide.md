# PDF Export Error Debugging Guide

## Problem
Getting "It may be damaged or use a file format that Preview doesn't recognize" error when opening exported PDF.

## Root Cause
This usually means the server is returning an error (JSON/HTML) instead of a valid PDF file.

## Debugging Steps Added

### 1. Frontend Validation ✅
Added checks in `Builder.vue` to:
- Validate response content-type is `application/pdf`
- Log response details to console
- Show meaningful error messages

### 2. Backend Logging ✅  
Added detailed error logging in `ResumeExportController.php` to:
- Log PDF generation errors
- Show stack traces in debug mode
- Track data structure issues

### 3. Test Script ✅
Created `test-pdf.php` to verify PDF generation works

## How to Debug

### Step 1: Check Browser Console
1. Open browser DevTools (F12)
2. Go to Console tab
3. Click "Export PDF"
4. Look for:
   ```
   Exporting with data: { resumeData, template, accentColor, achievementsCount }
   Response content-type: <type>
   ```

### Step 2: Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```
Look for "PDF Export Error" entries

### Step 3: Test PDF Generation Directly
```bash
php test-pdf.php
```
This tests if DomPDF can generate PDFs with sample data

## Common Issues & Fixes

### Issue 1: Empty/Missing Data
**Symptom**: PDF generation fails due to undefined array keys
**Fix**: Make sure all required fields have data:
- `formData.fullName`
- `formData.title` 
- `formData.experience`
- `formData.education`
- `formData.skills`

### Issue 2: Authentication Failed
**Symptom**: 401 Unauthorized error
**Fix**: Make sure user is logged in (`auth:sanctum` middleware required)

### Issue 3: Invalid JSON Response
**Symptom**: Console shows `application/json` instead of `application/pdf`
**Fix**: Check Laravel logs for the actual error message

### Issue 4: DomPDF Font  Issues
**Symptom**: Error about missing fonts
**Fix**: Run `php artisan dompdf:clear-cache`

## Test with Sample Data

Try exporting with this minimal data to isolate the issue:
1. Name: "Test User"
2. Title: "Software Engineer"
3. Email: "test@example.com"
4. At least 1 experience entry
5. At least 1 education entry

If this works, gradually add more data to find what causes the issue.

## Next Steps

1. **Try exporting** - Check console for detailed error logs
2. **Check Laravel logs** - Look at `storage/logs/laravel.log`
3. **Run test script** - Verify PDF generation works: `php test-pdf.php`
4. **Share error details** - Copy console errors and Laravel log errors for further debugging

## Files Modified

- `resources/js/pages/Builder.vue` - Added response validation & logging
- `app/Http/Controllers/Api/ResumeExportController.php` - Added error logging
- `test-pdf.php` - Created test script
