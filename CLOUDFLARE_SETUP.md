# Cloudflare DNS Setup for Multi-Tenant Architecture

This guide explains how to configure Cloudflare DNS for your multi-tenant realtor SaaS platform.

## Overview

Your multi-tenant architecture requires two types of DNS configurations:

1. **Wildcard Subdomain** - For all tenant subdomains (`*.myrealtorsites.com`)
2. **Custom Domains** - For tenant-specific custom domains (`www.johndoe.com`)

---

## Prerequisites

- Domain registered and added to Cloudflare
- Cloudflare nameservers active
- Server with public IP address running Traefik

---

## Part 1: Base Domain Setup

### 1.1 Add Your Domain to Cloudflare

1. Go to [Cloudflare Dashboard](https://dash.cloudflare.com/)
2. Click **Add Site**
3. Enter your domain: `myrealtorsites.com`
4. Select a plan (Free plan works fine)
5. Update nameservers at your domain registrar to Cloudflare's nameservers

### 1.2 DNS Records for Base Domain

Add the following DNS records in Cloudflare:

#### A Record (Root Domain)
```
Type: A
Name: @
Content: YOUR_SERVER_IP
Proxy: ☑️ Proxied (Orange Cloud)
TTL: Auto
```

#### A Record (WWW)
```
Type: A
Name: www
Content: YOUR_SERVER_IP
Proxy: ☑️ Proxied (Orange Cloud)
TTL: Auto
```

#### A Record (Wildcard Subdomain) - **CRITICAL for Multi-Tenant**
```
Type: A
Name: *
Content: YOUR_SERVER_IP
Proxy: ⚠️ DNS Only (Grey Cloud) - See note below
TTL: Auto
```

**⚠️ IMPORTANT - Wildcard Subdomain Proxy Setting:**

Cloudflare's proxy (orange cloud) has limitations with wildcard subdomains and Let's Encrypt:

**Option A: DNS Only (Recommended for Let's Encrypt)**
- Set wildcard to **DNS Only** (grey cloud)
- Traefik handles SSL directly with Let's Encrypt
- ✅ Works with HTTP challenge
- ✅ Certificates issued directly to your server
- ❌ Loses Cloudflare WAF/CDN features on subdomains

**Option B: Proxied (Advanced)**
- Set wildcard to **Proxied** (orange cloud)
- Requires Cloudflare API for DNS challenge
- ✅ Gets Cloudflare WAF/CDN features
- ✅ Cloudflare handles SSL
- ❌ Requires additional configuration (see below)

### 1.3 SSL/TLS Settings

Go to **SSL/TLS** in Cloudflare dashboard:

**For Option A (DNS Only):**
- SSL/TLS encryption mode: **Full (strict)** or **Full**
- This ensures end-to-end encryption even without Cloudflare proxy

**For Option B (Proxied):**
- SSL/TLS encryption mode: **Full (strict)**
- Edge Certificates: Enable **Always Use HTTPS**

---

## Part 2: Cloudflare with Let's Encrypt (DNS Only)

If you chose **Option A (DNS Only)** for wildcard subdomain:

### Configuration

1. **Traefik Configuration** - Already set up in `traefik/traefik.yml`:
   ```yaml
   certificateResolvers:
     letsencrypt:
       acme:
         email: admin@myrealtorsites.com
         storage: /letsencrypt/acme.json
         httpChallenge:
           entryPoint: web
   ```

2. **Run traefik:sync** to generate dynamic config:
   ```bash
   php artisan traefik:sync
   ```

3. **Deploy** and Let's Encrypt will automatically issue certificates

### Benefits
- ✅ Simple setup
- ✅ Works with HTTP challenge
- ✅ Free SSL certificates
- ✅ Automatic renewal

### Limitations
- ❌ No Cloudflare CDN for subdomains
- ❌ No Cloudflare WAF protection for subdomains
- ✅ Root domain can still be proxied separately

---

## Part 3: Cloudflare with DNS Challenge (Proxied) - Advanced

If you want Cloudflare proxy (orange cloud) on wildcard subdomain:

### 3.1 Get Cloudflare API Token

1. Go to Cloudflare Dashboard → **My Profile** → **API Tokens**
2. Click **Create Token**
3. Use template: **Edit zone DNS**
4. Configure:
   - Permissions: `Zone / DNS / Edit`
   - Zone Resources: `Include / Specific zone / myrealtorsites.com`
5. Create token and **save it securely**

### 3.2 Update Traefik Configuration

Edit `traefik/traefik.yml`:

```yaml
certificateResolvers:
  letsencrypt:
    acme:
      email: admin@myrealtorsites.com
      storage: /letsencrypt/acme.json
      # Use DNS challenge instead of HTTP
      dnsChallenge:
        provider: cloudflare
        delayBeforeCheck: 30
        resolvers:
          - "1.1.1.1:53"
          - "8.8.8.8:53"
```

### 3.3 Update Docker Compose

Add Cloudflare credentials to `docker-compose-production.yml`:

```yaml
  traefik:
    image: traefik:v2.10
    environment:
      - CF_API_EMAIL=your-email@example.com
      - CF_API_KEY=your_cloudflare_api_token
    # ... rest of config
```

### 3.4 Set Wildcard to Proxied

In Cloudflare DNS:
```
Type: A
Name: *
Content: YOUR_SERVER_IP
Proxy: ☑️ Proxied (Orange Cloud)
TTL: Auto
```

### Benefits
- ✅ Cloudflare CDN for all subdomains
- ✅ Cloudflare WAF protection
- ✅ DDoS protection
- ✅ Analytics

### Limitations
- ⚠️ More complex setup
- ⚠️ Requires API token management
- ⚠️ Slower certificate issuance (DNS propagation)

---

## Part 4: Custom Domain Setup (Tenant Domains)

When tenants add custom domains like `www.johndoe.com`:

### 4.1 Tenant DNS Configuration

Instruct tenants to add a CNAME record in **their** DNS provider:

```
Type: CNAME
Name: www.johndoe.com
Target: johndoe.myrealtorsites.com
TTL: Auto
```

Or use an A record if CNAME on root is not supported:
```
Type: A
Name: www.johndoe.com
Content: YOUR_SERVER_IP
TTL: Auto
```

### 4.2 Verify Custom Domain

In your application (via Filament or custom controller):

```php
// After tenant adds custom domain
$site->update([
    'custom_domain' => 'www.johndoe.com',
    'custom_domain_verified' => false,
]);

// Verify DNS (manual or automated with dns_get_record)
if ($this->verifyDnsRecord($site->custom_domain, $site->subdomain)) {
    $site->update(['custom_domain_verified' => true]);

    // Sync Traefik config
    Artisan::call('traefik:sync');
}
```

### 4.3 Traefik Auto-Configuration

Once verified, `traefik:sync` adds the custom domain to Traefik:

```yaml
rule: Host(`www.johndoe.com`) || HostRegexp(`{subdomain:[a-z0-9-]+}.myrealtorsites.com`)
```

Let's Encrypt automatically issues a certificate for the custom domain.

---

## Part 5: Recommended Production Setup

For the best balance of features and simplicity:

### Base Domain (myrealtorsites.com)
- **Root (@)**: Proxied ☑️ (Orange) - Marketing site with CDN
- **WWW**: Proxied ☑️ (Orange) - Marketing site with CDN
- **Wildcard (*)**: DNS Only ☐ (Grey) - Tenant subdomains with Let's Encrypt

### Custom Domains
- Each tenant domain: Let's Encrypt certificate via HTTP challenge
- Tenants can optionally use Cloudflare proxy on their own domains

### Cloudflare Settings
- SSL/TLS Mode: **Full (strict)**
- Always Use HTTPS: **Enabled**
- Automatic HTTPS Rewrites: **Enabled**
- Minimum TLS Version: **1.2**

---

## Part 6: Testing

### Test Wildcard Subdomain

```bash
# Check DNS resolution
dig test.myrealtorsites.com

# Test HTTPS (after traefik:sync)
curl -I https://test.myrealtorsites.com
```

### Test Custom Domain

```bash
# Check CNAME
dig www.johndoe.com

# Verify points to subdomain
dig www.johndoe.com CNAME
# Should return: johndoe.myrealtorsites.com

# Test HTTPS
curl -I https://www.johndoe.com
```

---

## Part 7: Troubleshooting

### Issue: Wildcard subdomain not working

**Cause:** DNS not propagated or wildcard record missing

**Solution:**
```bash
# Check DNS
dig *.myrealtorsites.com

# Verify Cloudflare DNS
# Should show A record for * pointing to your server IP
```

### Issue: Let's Encrypt certificates failing

**Cause:** Cloudflare proxy blocking HTTP challenge

**Solutions:**
1. Change wildcard DNS record to **DNS Only** (grey cloud)
2. OR switch to DNS challenge (see Part 3)

### Issue: Custom domain certificate not issued

**Cause:** DNS not pointing correctly or not verified in database

**Solution:**
```bash
# Verify DNS
dig www.johndoe.com

# Check database
php artisan tinker
>>> Site::where('custom_domain', 'www.johndoe.com')->first()->custom_domain_verified
# Should be: true

# Force Traefik sync
php artisan traefik:sync

# Check Traefik logs
docker logs traefik
```

### Issue: "Too many certificates" error

**Cause:** Let's Encrypt rate limit (50 certificates per week per domain)

**Solution:**
- Use Let's Encrypt staging for testing
- In `traefik/traefik.yml`, uncomment:
  ```yaml
  caServer: https://acme-staging-v02.api.letsencrypt.org/directory
  ```

---

## Part 8: Automation

### Scheduled Traefik Sync

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Sync Traefik config every 15 minutes
    $schedule->command('traefik:sync')->everyFifteenMinutes();
}
```

### Event-Based Sync

Create an observer for Site model:

```php
// app/Observers/SiteObserver.php
class SiteObserver
{
    public function updated(Site $site): void
    {
        // If custom domain was verified, sync Traefik
        if ($site->wasChanged('custom_domain_verified') && $site->custom_domain_verified) {
            Artisan::call('traefik:sync');
        }
    }
}
```

---

## Summary

✅ **Base domain wildcard**: DNS Only for Let's Encrypt HTTP challenge
✅ **Custom domains**: CNAME to tenant subdomain
✅ **Certificates**: Automatic via Let's Encrypt
✅ **Sync**: `php artisan traefik:sync` generates routing config
✅ **Zero downtime**: Traefik hot-reloads configuration

For most use cases, **DNS Only (grey cloud)** on wildcard with **Let's Encrypt HTTP challenge** is the simplest and most reliable setup.
