FROM php:8.2-fpm

# Build arguments for GitHub token
ARG GITHUB_TOKEN
ARG APP_URL
ARG GOOGLE_MAPS_API_KEY

# Set environment variables from build args
ENV APP_URL=${APP_URL}
ENV GOOGLE_MAPS_API_KEY=${GOOGLE_MAPS_API_KEY}

RUN echo "APP_URL is $APP_URL"

# Install dependencies (added nginx to existing list)
RUN apt-get update && apt-get install -y \
    supervisor \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    nginx \
    procps \
    net-tools

# Installing Node.js
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

# installing mariadb client
RUN apt-get install -y mariadb-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions mbstring pdo_mysql zip exif pcntl gd mysqli xdebug sockets redis bcmath intl

# Configure Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure Git to use HTTPS instead of SSH for GitHub
RUN git config --global url."https://github.com/".insteadOf "git@github.com:" && \
    git config --global url."https://github.com/".insteadOf "ssh://git@github.com/" && \
    git config --global url."https://".insteadOf "git://"

# Configure Composer for HTTPS and GitHub token
RUN composer config --global repo.packagist composer https://packagist.org && \
    composer config --global github-protocols https && \
    composer config --global process-timeout 600

# Configure GitHub token if provided
RUN if [ -n "$GITHUB_TOKEN" ]; then \
        echo "Configuring GitHub OAuth token for Composer..." && \
        composer config -g github-oauth.github.com $GITHUB_TOKEN; \
    else \
        echo "No GitHub token provided, proceeding with public repository access only"; \
    fi

# Set working directory
WORKDIR /var/www

# Create supervisor directories and main config
RUN mkdir -p /var/log/supervisor /etc/supervisor/conf.d

# Create main supervisor configuration
COPY <<EOF /etc/supervisor/supervisord.conf
[unix_http_server]
file=/var/run/supervisor.sock
chmod=0700

[supervisord]
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid
childlogdir=/var/log/supervisor
nodaemon=true
user=root

[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

[supervisorctl]
serverurl=unix:///var/run/supervisor.sock

[include]
files = /etc/supervisor/conf.d/*.conf
EOF

# Create program configurations
COPY <<EOF /etc/supervisor/conf.d/nginx.conf
[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
startretries=3
user=root
stdout_logfile=/var/log/supervisor/nginx_stdout.log
stderr_logfile=/var/log/supervisor/nginx_stderr.log
stdout_logfile_maxbytes=0
stderr_logfile_maxbytes=0
EOF

COPY <<EOF /etc/supervisor/conf.d/php-fpm.conf
[program:php-fpm]
command=php-fpm --nodaemonize
autostart=true
autorestart=true
startretries=3
user=root
stdout_logfile=/var/log/supervisor/php_fmp_stdout.log
stderr_logfile=/var/log/supervisor/php_fpm_stderr.log
stdout_logfile_maxbytes=0
stderr_logfile_maxbytes=0
EOF

# Create basic nginx configuration
RUN mkdir -p /etc/nginx/sites-available
COPY <<EOF /etc/nginx/sites-available/default
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    root /var/www/public;
    index index.php index.html index.htm;
    server_name _;

    client_max_body_size 50M;

    location /health {
        access_log off;
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)\$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
        fastcgi_read_timeout 300;
    }

    location /insights {
        try_files $uri $uri/ /insights/index.php?$args;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

# Copy all application code
COPY . .
# Run composer install WITHOUT autoloader generation
RUN composer install --prefer-dist --no-interaction --no-autoloader

# Then generate autoloader manually
RUN composer dumpautoload -o

# Copy package.json files
COPY package*.json ./

# Install Node.js dependencies
RUN npm ci



# Set proper permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache


# Run Laravel optimization
RUN php artisan ziggy:generate \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && composer dumpautoload -o

# Build frontend assets
RUN npm run build

# Clean up
RUN npm prune --production && npm cache clean --force

# Expose port 80 for nginx
EXPOSE 80

# Use supervisor to manage both nginx and php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
