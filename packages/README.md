# 📦 Filament Link Nofollow Package

This directory contains a **standalone Laravel package** that adds dofollow/nofollow checkbox functionality to Filament's RichEditor.

## 🎯 What's Inside

A complete, production-ready package that can be:
- Used locally in this project
- Published to GitHub
- Submitted to Packagist
- Installed in any Laravel Filament project

## 📂 Package Location

```
packages/filament-link-nofollow/
```

## ✨ Features

- ✅ Dofollow/Nofollow checkbox in link modal
- ✅ Target selection (same tab/new tab)
- ✅ Automatic rel attribute management
- ✅ Edit existing links with pre-filled values
- ✅ Artisan build command
- ✅ Auto-discovery service provider
- ✅ Complete documentation

## 🚀 Quick Start

The package is already installed and configured in this project!

### Using in this project:

```php
use Filament\Forms\Components\RichEditor;
use Webvio\FilamentLinkNofollow\Plugins\CustomLinkPlugin;

RichEditor::make('content')
    ->plugins([
        CustomLinkPlugin::make(),
    ])
```

### Example Usage:

See `app/Filament/Resources/ManageCompanies/ManageCompanyResource.php` for a working example.

## 📖 Documentation

All documentation is in the package directory:

- **README.md** - Main documentation
- **INSTALLATION.md** - Installation guide for other projects
- **QUICK_REFERENCE.md** - Developer quick reference
- **PACKAGE_SUMMARY.md** - Complete package overview
- **CONTRIBUTING.md** - Contribution guidelines
- **CHANGELOG.md** - Version history

## 🌐 Publishing

To share this package with the world:

### 1. Create GitHub Repository

```bash
cd packages/filament-link-nofollow
git init
git add .
git commit -m "Initial release v1.0.0"
git remote add origin https://github.com/YOUR_USERNAME/filament-link-nofollow.git
git push -u origin main
git tag v1.0.0
git push --tags
```

### 2. Submit to Packagist

1. Visit https://packagist.org/packages/submit
2. Enter your GitHub repository URL
3. Submit the package

### 3. Install in Other Projects

Once published:

```bash
composer require webvio/filament-link-nofollow
php artisan filament-link-nofollow:build
php artisan filament:assets
```

## 🔧 Development

### Build JavaScript Assets

```bash
cd packages/filament-link-nofollow
npm run build
```

### Or use Artisan:

```bash
php artisan filament-link-nofollow:build
```

### After Changes:

```bash
php artisan filament:assets
php artisan optimize:clear
```

## 📦 Package Contents

```
filament-link-nofollow/
├── bin/build.js              # esbuild configuration
├── config/                   # Package configuration
├── resources/
│   ├── js/custom-link.js     # TipTap extension
│   └── dist/                 # Compiled assets
├── src/
│   ├── Console/Commands/     # Artisan commands
│   ├── Plugins/              # CustomLinkPlugin
│   └── ServiceProvider       # Auto-discovery
├── composer.json
├── package.json
└── Documentation files
```

## 🎓 Integration in Other Projects

### Option 1: Via Packagist (recommended after publishing)

```bash
composer require webvio/filament-link-nofollow
```

### Option 2: Local Development

Copy the package folder and add to composer.json:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/filament-link-nofollow"
        }
    ],
    "require": {
        "webvio/filament-link-nofollow": "@dev"
    }
}
```

## 📋 Requirements

- PHP 8.1+
- Laravel 11.0+ or 12.0+
- Filament 4.0+
- Node.js & NPM (for building assets)

## 📄 License

MIT License - Free for commercial and personal use

## 👥 Credits

Created by WebVio for the Laravel Filament community

---

For complete documentation, see the files in `packages/filament-link-nofollow/`
