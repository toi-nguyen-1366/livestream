FROM php:8.2-fpm

WORKDIR /var/www

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    ffmpeg \
    libmagickwand-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libmcrypt-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    graphviz \
    zip \
    vim \
    unzip \
    curl \
    git \
    gnupg \
    exiftool \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install \
        dom \
        fileinfo \
        mbstring \
        xml \
        exif \
        intl \
        pdo_mysql \
        mysqli \
        zip \
        pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js + Yarn
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - && \
    apt-get install -y nodejs yarn

# Add user for Laravel
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

USER www

EXPOSE 9000
CMD ["php-fpm"]
