ARG PHP_VERSION=8.3

FROM drupal:10-php${PHP_VERSION}-apache

ARG INSTALL_XDEBUG=0
ARG CIVICRM_VERSION=5.81
ARG WEBFORM_VERSION=6.3
ARG WEBFORM_CIVICRM_VERSION=6.2
ARG WEBFORM_VALIDATION_VERSION=2.0

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apt-get update \
    && apt-get install -y libicu-dev unzip git \
    && install-php-extensions gd intl mbstring mysqli pdo pdo_mysql \
    && if [ "$INSTALL_XDEBUG" = "1" ]; then install-php-extensions xdebug; fi \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN composer config extra.enable-patching true \
    && composer config --no-plugins allow-plugins.cweagans/composer-patches true \
    && composer config --no-plugins allow-plugins.civicrm/composer-downloads-plugin true \
    && composer config --no-plugins allow-plugins.civicrm/composer-compile-plugin true \
    && composer config --no-plugins allow-plugins.civicrm/civicrm-asset-plugin true \
    && composer config extra.compile-mode all

RUN composer require \
    civicrm/civicrm-core:$CIVICRM_VERSION \
    civicrm/civicrm-packages:$CIVICRM_VERSION \
    civicrm/civicrm-drupal-8:$CIVICRM_VERSION

RUN composer require --dev \
    drush/drush \
    civicrm/cli-tools

RUN composer require -w drupal/webform:$WEBFORM_CIVICRM_VERSION \
    drupal/webform_civicrm:$WEBFORM_VERSION \
    drupal/webform_validation:$WEBFORM_VALIDATION_VERSION

RUN echo "memory_limit = 1024M" > /usr/local/etc/php/conf.d/memory-limit.ini

RUN mkdir -p /opt/drupal/web/sites/default/files/civicrm/ext \
    && chown -R www-data: /opt/drupal/web/sites/default/files

COPY install-drupal-and-civicrm /usr/local/bin/
RUN chmod +x /usr/local/bin/install-drupal-and-civicrm

COPY install-phpunit /usr/local/bin/
RUN chmod +x /usr/local/bin/install-phpunit

COPY initialize-test-db /usr/local/bin/
RUN chmod +x /usr/local/bin/initialize-test-db

COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER www-data