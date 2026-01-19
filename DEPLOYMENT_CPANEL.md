# Mwongozo wa Ku-Deploy Laravel Project kwenye cPanel

## Hatua za Ku-Deploy kwenye cPanel

### 1. Build Assets Kabla ya Upload

**Muhimu:** Lazima u-build assets kabla ya ku-upload kwenye cPanel.

```bash
# 1. Install dependencies
npm install

# 2. Build production assets
npm run build
```

Hii itaunda folder `public/build/` na files zote za CSS na JS zilizo-compiled.

### 2. Upload Files kwenye cPanel

#### A. Upload Structure

Kwenye cPanel, upload files kama ifuatavyo:

```
public_html/
├── .htaccess (kutoka public/.htaccess)
├── index.php (kutoka public/index.php)
├── build/ (folder nzima kutoka public/build/)
│   ├── assets/
│   └── manifest.json
├── logo.png
├── favicon.ico
└── (files zingine za public/)

SKYwhatsapp_api/ (folder ya mizizi ya Laravel)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── composer.json
├── package.json
└── (files zingine)
```

**Muhimu:**
- Folder `public` ya Laravel inakuwa `public_html` kwenye cPanel
- Files za Laravel zinaweza kuwa nje ya `public_html` (kwa usalama)

#### B. File Permissions

Baada ya upload, set permissions:

```bash
# Via cPanel File Manager au SSH:
chmod 755 storage bootstrap/cache
chmod 644 .env
```

Au kwenye cPanel File Manager:
- `storage/` → 755
- `bootstrap/cache/` → 755
- `.env` → 644

### 3. Configure .env File

Kwenye cPanel, edit `.env` file:

```env
APP_NAME="SKY WhatsApp API"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# WhatsApp Service (kama unatumia Node.js service)
WHATSAPP_SERVICE_URL=http://localhost:3000
WHATSAPP_SERVICE_API_KEY=your-secret-key-here
```

**Muhimu:**
- `APP_DEBUG=false` kwa production
- `APP_URL` lazima iwe sawa na domain yako
- Generate `APP_KEY` kwa: `php artisan key:generate`

### 4. Setup Database

Kwenye cPanel:

1. Fungua **MySQL Databases**
2. Create database na user
3. Grant permissions
4. Run migrations:

```bash
php artisan migrate --force
```

### 5. Configure Document Root

Kwenye cPanel:

1. Fungua **File Manager**
2. Hover juu ya `public_html`
3. Click **Document Root** icon
4. Set kama document root

**Au** kama unatumia subdomain/folder tofauti:

1. Fungua **Subdomains** au **Addon Domains**
2. Set document root kuwa: `/home/username/public_html/public`

### 6. Storage Link

Run command hii kwa SSH au via cPanel Terminal:

```bash
php artisan storage:link
```

Hii ita-link `storage/app/public` na `public/storage`.

### 7. Optimize Laravel kwa Production

Run commands hizi:

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache kwa production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Verify Build Files

Hakikisha folder `public/build/` ina files:

```
public/build/
├── assets/
│   ├── app-[hash].js
│   └── app-[hash].css
└── manifest.json
```

Kama files hazipo, build tena:

```bash
npm run build
```

### 9. Test Application

1. Visit `https://yourdomain.com`
2. Check browser console kwa errors
3. Verify CSS na JS zina-load

## Troubleshooting

### Problem: CSS/JS hazina-load

**Solution:**
1. Hakikisha `public/build/` folder ipo na ina files
2. Check `public/build/manifest.json` ipo
3. Rebuild assets: `npm run build`
4. Clear Laravel cache: `php artisan view:clear`

### Problem: 500 Internal Server Error

**Solution:**
1. Check `.env` file ipo na configured
2. Check file permissions (storage, bootstrap/cache)
3. Check Laravel logs: `storage/logs/laravel.log`
4. Enable debug temporarily: `APP_DEBUG=true` (kwa testing tu)

### Problem: Assets zina-load lakini styles hazifanyi kazi

**Solution:**
1. Rebuild assets: `npm run build`
2. Clear browser cache
3. Check `manifest.json` ina correct paths
4. Verify `@vite()` directive ipo kwenye Blade templates

### Problem: Routes hazifanyi kazi

**Solution:**
1. Check `.htaccess` file ipo kwenye `public_html/`
2. Verify `mod_rewrite` enabled kwenye cPanel
3. Run: `php artisan route:cache`

## Quick Deployment Checklist

- [ ] Build assets: `npm run build`
- [ ] Upload `public/build/` folder
- [ ] Upload files zote za Laravel
- [ ] Configure `.env` file
- [ ] Set file permissions (storage, bootstrap/cache)
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Create storage link: `php artisan storage:link`
- [ ] Optimize: `php artisan config:cache`
- [ ] Test application
- [ ] Verify assets zina-load

## Notes

1. **Node.js Service:** Kama unatumia Node.js service kwa WhatsApp, lazima u-host tofauti (VPS, cloud server, etc.) kwa sababu cPanel haitaki Node.js applications.

2. **Queue Workers:** Kwa production, setup queue worker kwa Cron Job au Process Manager.

3. **SSL Certificate:** Hakikisha SSL certificate ipo kwa HTTPS.

4. **Backup:** Fanya backup ya database na files kabla ya ku-deploy.

## Alternative: Build kwenye Server

Kama cPanel yako ina Node.js support (via Terminal):

```bash
# SSH kwenye server
cd /home/username/public_html
npm install
npm run build
```

Lakini kwa kawaida, ni rahisi ku-build locally na ku-upload `public/build/` folder.
