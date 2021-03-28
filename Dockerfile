# Digest of chialab/php:7.4-fpm
FROM chialab/php@sha256:793ef5d0f9e1e85acbb51507d7d6b97e7df6e7a105d0458801cc2d5711b3b2c8
# sudo: To set permission to files in entrypoint.sh
RUN apt update && apt install -y \
    sudo \
 && rm -rf /var/lib/apt/lists/*
# @see https://medium.com/@c.harrison/speedy-composer-installs-in-docker-builds-41eea6d0172b
# @see https://qiita.com/keitakn/items/37f0fac49442b72c403e
RUN composer config -g repos.packagist composer https://packagist.jp \
 && composer global require hirak/prestissimo --no-plugins --no-scripts \
 && composer clearcache
# To wait for database in entrypoint.sh
ADD https://raw.githubusercontent.com/vishnubob/wait-for-it/81b1373f17855a4dc21156cfe1694c31d7d1792e/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it
# To set permission of vendor as www-data since web server accesses them.
USER www-data
ENV PATH=/var/www/html/vendor/bin:$PATH
ENV HOME=/var/www/html
COPY composer.json composer.lock /var/www/html/
# Don't specify --no-plugins when run composer install because it blocks to set up wp directory \
RUN composer install --prefer-dist --optimize-autoloader --no-scripts --no-dev \
 && composer clearcache
# To set as VOLUME.
RUN mkdir \
    /var/www/html/web/static \
    /var/www/html/web/app/upgrade \
    /var/www/html/web/app/uploads \
    /var/www/html/web/wp/wp-content/languages
USER root
# Stages minimum required files and directories
# since host directories may incluede some of files and directories which is installed by Composer.
# To run WP-CLI to install latest translation in entrypoint.sh
COPY wp-cli.yml /var/www/html/
COPY config /var/www/html/config
COPY web/index.php web/wp-config.php /var/www/html/web/
COPY web/app/mu-plugins /var/www/html/web/app/mu-plugins
COPY web/app/themes /var/www/html/web/app/themes
COPY web/app/plugins/staticpress2019-s3 /var/www/html/web/app/plugins/staticpress2019-s3
COPY ./entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint
ENTRYPOINT ["entrypoint"]
        # To cache.
VOLUME ["/var/www/html/vendor", \
        "/var/www/html/web/static", \
        # To share static files with web server service.
        "/var/www/html/web/wp", \
        "/var/www/html/web/wp/wp-content/languages", \
        "/var/www/html/web/app/mu-plugins", \
        "/var/www/html/web/app/plugins", \
        "/var/www/html/web/app/themes", \
        "/var/www/html/web/app/upgrade", \
        "/var/www/html/web/app/uploads"]
CMD ["php-fpm"]
