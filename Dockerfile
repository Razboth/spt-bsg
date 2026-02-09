FROM debian:bullseye-slim

LABEL maintainer="Colin Wilson <colin@wyveo.com>"

ENV DEBIAN_FRONTEND=noninteractive \
    NGINX_VERSION=1.21.6-1~bullseye \
    PHP_CONF=/etc/php/8.1/fpm/php.ini \
    FPM_CONF=/etc/php/8.1/fpm/pool.d/www.conf \
    COMPOSER_VERSION=2.2.7

# 1) Tambahkan repositori NGINX & PHP-SURY secara aman
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
      ca-certificates curl gnupg lsb-release apt-transport-https dirmngr; \
    \
    # Import NGINX signing key via HTTPS ke keyring
    curl -fsSL https://nginx.org/keys/nginx_signing.key \
      | gpg --dearmor -o /usr/share/keyrings/nginx-keyring.gpg; \
    echo "deb [signed-by=/usr/share/keyrings/nginx-keyring.gpg] \
      http://nginx.org/packages/mainline/debian bullseye nginx" \
      > /etc/apt/sources.list.d/nginx.list; \
    \
    # Import PHP-SURY key via HTTPS ke keyring
    curl -fsSL https://packages.sury.org/php/apt.gpg \
      | gpg --dearmor -o /usr/share/keyrings/php-sury-keyring.gpg; \
    echo "deb [signed-by=/usr/share/keyrings/php-sury-keyring.gpg] \
      https://packages.sury.org/php/ $(lsb_release -sc) main" \
      > /etc/apt/sources.list.d/php.list; \
    \
    apt-get update

# 2) Instal build & runtime deps, NGINX, PHP, ekstensi, supervisor, dll.
RUN set -eux; \
    buildDeps="curl gcc make autoconf libc-dev zlib1g-dev pkg-config"; \
    apt-get install -y --no-install-recommends \
      $buildDeps \
      apt-utils nano zip unzip python3-pip python3-setuptools git \
      libmemcached-dev libmemcached11 libmagickwand-dev \
      nginx=${NGINX_VERSION} \
      php8.1-fpm php8.1-cli php8.1-bcmath php8.1-dev \
      php8.1-common php8.1-opcache php8.1-readline php8.1-mbstring \
      php8.1-curl php8.1-gd php8.1-imagick php8.1-mysql \
      php8.1-zip php8.1-pgsql php8.1-intl php8.1-xml php-pear; \
    pecl -d php_suffix=8.1 install -o -f redis memcached; \
    pip3 install wheel supervisor git+https://github.com/coderanger/supervisor-stdout; \
    mkdir -p /run/php; \
    echo "#!/bin/sh\nexit 0" > /usr/sbin/policy-rc.d; \
    rm -rf /etc/nginx/conf.d/default.conf

# 3) Tuning PHP & PHP-FPM tanpa error quoting
RUN set -eux; \
    sed -i \
      -e 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/' \
      -e 's/memory_limit\s*=.*/memory_limit = 256M/' \
      -e 's/upload_max_filesize\s*=.*/upload_max_filesize = 100M/' \
      -e 's/post_max_size\s*=.*/post_max_size = 100M/' \
      -e 's/variables_order = "GPCS"/variables_order = "EGPCS"/' \
      "${PHP_CONF}"; \
    sed -i \
      -e 's/;daemonize\s*= yes/daemonize = no/' \
      /etc/php/8.1/fpm/php-fpm.conf; \
    sed -i \
      -e 's/;catch_workers_output = yes/catch_workers_output = yes/' \
      "${FPM_CONF}"; \
    for opt in max_children start_servers min_spare_servers max_spare_servers max_requests; do \
      if [ "$opt" = max_children ]; then new=4; \
      elif [ "$opt" = start_servers ]; then new=3; \
      elif [ "$opt" = min_spare_servers ]; then new=2; \
      elif [ "$opt" = max_spare_servers ]; then new=4; \
      else new=200; fi; \
      sed -i "s/pm\.${opt} = [0-9]\+/pm.${opt} = ${new}/" "${FPM_CONF}"; \
    done; \
    sed -i 's/www-data/nginx/' "${FPM_CONF}"; \
    sed -i 's/^;clear_env = no$/clear_env = no/' "${FPM_CONF}"

# 4) Aktifkan ekstensi PHP
RUN set -eux; \
    for ext in redis memcached imagick; do \
      echo "extension=${ext}.so" \
        > /etc/php/8.1/mods-available/${ext}.ini; \
      ln -sf /etc/php/8.1/mods-available/${ext}.ini \
        /etc/php/8.1/fpm/conf.d/20-${ext}.ini; \
      ln -sf /etc/php/8.1/mods-available/${ext}.ini \
        /etc/php/8.1/cli/conf.d/20-${ext}.ini; \
    done

# 5) Install Composer & cleanup (re-define buildDeps for purge)
RUN set -eux; \
    buildDeps="curl gcc make autoconf libc-dev zlib1g-dev pkg-config"; \
    curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php; \
    curl -fsSL https://composer.github.io/installer.sig -o /tmp/composer-setup.sig; \
    php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { echo 'Invalid installer'; exit(1); }"; \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin \
      --filename=composer --version=${COMPOSER_VERSION}; \
    rm -f /tmp/composer-setup.*; \
    apt-get purge -y --auto-remove $buildDeps; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# 6) Salin konfigurasi & kode aplikasi
COPY ./.docker/supervisord.conf /etc/supervisord.conf
COPY ./.docker/default.conf     /etc/nginx/conf.d/default.conf
COPY .                         /usr/share/nginx/html
COPY .env.example              .env

COPY ./.docker/start.sh        /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]
