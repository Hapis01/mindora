# Mindora — Deployment Guide

## Server Requirements

| Component | Minimum | Recommended |
|---|---|---|
| CPU | 2 vCPU | 4 vCPU |
| RAM | 2 GB | 4 GB |
| Storage | 20 GB SSD | 50 GB SSD |
| OS | Ubuntu 22.04 LTS | Ubuntu 22.04 LTS |
| PHP | 8.2 | 8.2+ |
| MySQL | 8.0 | 8.0+ |
| Python | 3.11 | 3.11+ |
| Node.js | 20 LTS | 20 LTS |

---

## 1. Server Setup (Ubuntu 22.04)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-openssl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install MySQL 8.0
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Nginx
sudo apt install -y nginx

# Install Python 3.11
sudo apt install -y python3.11 python3.11-venv python3-pip
```

---

## 2. Database Setup

```sql
-- Create production database
CREATE DATABASE Mindora_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create application user (least privilege)
CREATE USER 'Mindora_user'@'localhost' IDENTIFIED BY '<strong_password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER
    ON Mindora_db.* TO 'Mindora_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## 3. Application Deployment

```bash
# Clone repository
sudo git clone <repo-url> /var/www/Mindora
cd /var/www/Mindora

# Set ownership
sudo chown -R www-data:www-data /var/www/Mindora
sudo chmod -R 755 /var/www/Mindora
sudo chmod -R 775 /var/www/Mindora/storage
sudo chmod -R 775 /var/www/Mindora/bootstrap/cache

# Install PHP dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader

# Install and build frontend assets
npm ci
npm run build

# Configure environment
sudo cp .env.example .env
sudo nano .env   # Edit with production values (see below)

# Generate application key
sudo -u www-data php artisan key:generate

# Run migrations
sudo -u www-data php artisan migrate --force

# Seed initial data (roles, admin user, assessments)
sudo -u www-data php artisan db:seed --force

# Optimize application
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan optimize
```

---

## 4. Production .env Configuration

```env
APP_NAME=Mindora
APP_ENV=production
APP_KEY=base64:<generated-key>
APP_DEBUG=false
APP_URL=https://Mindora.yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=Mindora_db
DB_USERNAME=Mindora_user
DB_PASSWORD=<strong-db-password>

# Session (production hardening)
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# Encryption
ENCRYPTION_MASTER_KEY=base64:<32-byte-key>
ENCRYPTION_KEY_VERSION=v1

# ML Service
ML_SERVICE_URL=http://127.0.0.1:8001
ML_SERVICE_TOKEN=<unique-secret-token>

# AI Provider (Google Gemini)
AI_PROVIDER=gemini
GEMINI_API_KEY=<production-api-key>
GEMINI_MODEL=gemini-3.6-flash
GEMINI_PROJECT_ID=<google-cloud-project-id>

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

---

## 5. Nginx Configuration

```nginx
# /etc/nginx/sites-available/Mindora
server {
    listen 80;
    server_name Mindora.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name Mindora.yourdomain.com;

    root /var/www/Mindora/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/Mindora.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/Mindora.yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;

    # Security headers (some may duplicate Laravel middleware - belt and suspenders)
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    server_tokens off;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript
               application/javascript application/x-javascript application/xml;

    # Static assets caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site and reload
sudo ln -s /etc/nginx/sites-available/Mindora /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 6. SSL Certificate (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d Mindora.yourdomain.com
sudo systemctl enable certbot.timer  # Auto-renewal
```

---

## 7. ML Microservice as a systemd Service

```ini
# /etc/systemd/system/Mindora-ml.service
[Unit]
Description=Mindora ML Microservice (FastAPI)
After=network.target

[Service]
Type=exec
User=www-data
WorkingDirectory=/var/www/Mindora/python-ml
Environment=PATH=/var/www/Mindora/python-ml/.venv/bin
ExecStart=/var/www/Mindora/python-ml/.venv/bin/uvicorn \
    app.main:app \
    --host 127.0.0.1 \
    --port 8001 \
    --workers 2
Restart=always
RestartSec=10
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
```

```bash
# Setup Python virtual environment
cd /var/www/Mindora/python-ml
python3.11 -m venv .venv
.venv/bin/pip install -r requirements.txt

# Enable and start service
sudo systemctl daemon-reload
sudo systemctl enable Mindora-ml
sudo systemctl start Mindora-ml
sudo systemctl status Mindora-ml
```

---

## 8. Laravel Queue Worker (for background jobs)

```ini
# /etc/systemd/system/Mindora-worker.service
[Unit]
Description=Mindora Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/Mindora
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable Mindora-worker
sudo systemctl start Mindora-worker
```

---

## 9. Firewall Configuration

```bash
# Allow HTTP, HTTPS, SSH only
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw deny 8001/tcp  # Block ML service from public
sudo ufw enable
```

---

## 10. Post-Deployment Verification Checklist

- [ ] Visit `https://Mindora.yourdomain.com` — HTTPS working
- [ ] Check browser → DevTools → Network → Response Headers include `X-Frame-Options: DENY`
- [ ] Login with admin credentials
- [ ] Complete a test assessment end-to-end
- [ ] Verify ML results appear in assessment results
- [ ] Check `/admin` panel accessible with admin account
- [ ] Check `/admin/audit-logs` shows login events
- [ ] `systemctl status Mindora-ml` shows "active (running)"
- [ ] `php artisan queue:work --once` processes a job successfully
- [ ] Database backup job configured (cron or managed service)
