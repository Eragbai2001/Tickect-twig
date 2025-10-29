# 🚀 Deployment Instructions for Tickety-Twig

## Quick Start: Push to GitHub

### If you have GitHub CLI installed:
```bash
cd /Users/user/Documents/GitHub/Tickety-Twig
gh repo create tickety-twig --public --source=. --remote=origin --push
```

### Manual method:
1. Create a new repository on GitHub: https://github.com/new
2. Run these commands:

```bash
cd /Users/user/Documents/GitHub/Tickety-Twig

# Add GitHub as remote (replace YOUR_USERNAME)
git remote add origin https://github.com/YOUR_USERNAME/tickety-twig.git

# Push to GitHub
git branch -M main
git push -u origin main
```

---

## Deployment Options

### Option 1: Vercel (Easiest - Free)

Vercel now supports PHP! Perfect for this project.

**Steps:**
1. Go to https://vercel.com
2. Sign in with GitHub
3. Click "Import Project"
4. Select your `tickety-twig` repository
5. Configure:
   - Framework Preset: **Other**
   - Root Directory: `./`
   - Build Command: (leave empty)
   - Output Directory: `public`
6. Add environment variables (if needed)
7. Click "Deploy"

**Vercel Configuration:**
Create `vercel.json` in root:
```json
{
  "version": 2,
  "builds": [
    {
      "src": "public/index.php",
      "use": "@vercel/php"
    }
  ],
  "routes": [
    {
      "src": "/(.*)",
      "dest": "/public/index.php"
    }
  ]
}
```

---

### Option 2: Heroku (Free Tier Available)

**Steps:**
1. Install Heroku CLI: https://devcenter.heroku.com/articles/heroku-cli
2. Run these commands:

```bash
# Login to Heroku
heroku login

# Create app
heroku create tickety-twig

# Add PHP buildpack
heroku buildpacks:set heroku/php

# Push to Heroku
git push heroku main

# Open in browser
heroku open
```

**Heroku Configuration:**
Create `Procfile` in root:
```
web: vendor/bin/heroku-php-apache2 public/
```

---

### Option 3: DigitalOcean App Platform

**Steps:**
1. Go to https://cloud.digitalocean.com/apps
2. Click "Create App"
3. Connect your GitHub account
4. Select `tickety-twig` repository
5. Configure:
   - Name: tickety-twig
   - Type: Web Service
   - Environment: PHP
   - HTTP Port: 8080
   - Run Command: (leave default)
6. Choose plan (Basic - $5/month or free trial)
7. Click "Launch App"

---

### Option 4: InfinityFree (100% Free PHP Hosting)

**Steps:**
1. Go to https://infinityfree.net
2. Create free account
3. Create new website
4. Upload files via FTP or File Manager
5. Your site will be live at: `your-username.infinityfreeapp.com`

**FTP Upload:**
- Host: `ftpupload.net`
- Upload all files to `htdocs` folder

---

### Option 5: Traditional Shared Hosting

Deploy to any PHP hosting provider:

**Popular Options:**
- **Hostinger** - $2.99/month
- **SiteGround** - $3.99/month
- **Bluehost** - $2.95/month
- **Namecheap** - $1.58/month

**Steps:**
1. Sign up for hosting
2. Access cPanel / File Manager
3. Upload all files to `public_html` folder
4. Done! Your site is live

---

## Testing Locally

Before deploying, test locally:

```bash
cd /Users/user/Documents/GitHub/Tickety-Twig
php -S localhost:8000 -t public
```

Open: http://localhost:8000

---

## Environment Variables (Optional)

If you need environment variables for production:

1. Create `.env` file (already in .gitignore):
```env
APP_ENV=production
APP_DEBUG=false
```

2. Add to hosting provider's environment variables

---

## Post-Deployment Checklist

- [ ] Test all pages (Home, Sign In, Dashboard, Tickets)
- [ ] Test create/edit/delete ticket functionality
- [ ] Test search, filter, and sort
- [ ] Test on mobile devices
- [ ] Check browser console for errors
- [ ] Update README with live URL

---

## Custom Domain (Optional)

After deployment, you can add a custom domain:

1. Buy domain from Namecheap, GoDaddy, etc.
2. Update DNS settings:
   - For Vercel: Add CNAME to `cname.vercel-dns.com`
   - For Heroku: Add CNAME to `your-app.herokuapp.com`
   - For others: Follow provider's instructions
3. Add domain in hosting platform settings

---

## Support

If you encounter issues:
1. Check server logs
2. Verify PHP version (8.0+)
3. Check file permissions
4. Ensure all files uploaded correctly

Good luck! 🚀
