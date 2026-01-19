# Production Server API Key Setup

## Issue
The production server at `https://food.hosting.hollyn.online` is missing the `WHATSAPP_SERVICE_API_KEY` environment variable, causing "INTERNAL_API_NOT_CONFIGURED" errors.

## Solution

### Step 1: Add API Key to Production Server

On the production server (via cPanel File Manager or SSH), edit the `.env` file and add:

```env
WHATSAPP_SERVICE_API_KEY=sk_3R2qNWn2KuXFXPNMeuAmj2nk3eybtONbY7VrJqbEujLgHYZd
```

**Important:** This must match the `LARAVEL_API_KEY` in your whatsapp-service `.env` file.

### Step 2: Clear Production Server Cache

After adding the environment variable, run these commands on the production server (via SSH or cPanel Terminal):

```bash
cd /path/to/your/laravel/project
php artisan config:clear
php artisan config:cache
```

### Step 3: Verify Configuration

The whatsapp-service should now be able to authenticate with the Laravel API.

## Current Configuration

- **Laravel API Key (Local):** `sk_3R2qNWn2KuXFXPNMeuAmj2nk3eybtONbY7VrJqbEujLgHYZd`
- **WhatsApp Service API Key:** `sk_3R2qNWn2KuXFXPNMeuAmj2nk3eybtONbY7VrJqbEujLgHYZd`
- **Production URL:** `https://food.hosting.hollyn.online`

## Security Note

For production, consider generating a new, unique API key instead of using the same one as local development.
