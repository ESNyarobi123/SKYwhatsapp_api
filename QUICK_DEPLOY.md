# Quick Deployment Guide - cPanel

## Hatua za Haraka (5 dakika)

### 1. Build Assets Locally

```bash
npm install
npm run build
```

Hii itaunda `public/build/` folder.

### 2. Upload kwenye cPanel

**Via cPanel File Manager:**

1. **Upload `public/build/` folder** → `public_html/build/`
2. **Upload files zote za Laravel** (app, config, routes, etc.)
3. **Upload `public/.htaccess`** → `public_html/.htaccess`
4. **Upload `public/index.php`** → `public_html/index.php`

### 3. Configure

1. **Create `.env` file** kwenye root ya Laravel (nje ya public_html)
2. **Set permissions:**
   - `storage/` → 755
   - `bootstrap/cache/` → 755

3. **Via SSH au cPanel Terminal, run:**
```bash
cd /home/username/public_html
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
```

### 4. Test

Visit `https://yourdomain.com` na verify assets zina-load.

## ⚠️ Muhimu

- **Lazima** u-build assets kabla ya ku-upload (`npm run build`)
- **Lazima** `public/build/` folder iwe kwenye `public_html/build/`
- **Lazima** `.env` file iwe configured correctly

## ❌ Kama Assets Hazina-load

1. Rebuild: `npm run build`
2. Upload `public/build/` folder tena
3. Clear cache: `php artisan view:clear`
4. Check browser console kwa errors
