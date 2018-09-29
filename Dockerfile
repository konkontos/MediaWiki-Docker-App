FROM mediawiki

LABEL maintainer="kkon@handmadeapps.tech"

# Build args and env.

ARG USE_SSL

WORKDIR /root/

# Install utils

RUN apt-get update
RUN apt-get --assume-yes install zip
RUN apt-get --assume-yes install git
RUN apt-get --assume-yes install vim
RUN apt-get --assume-yes install iputils-ping
RUN apt-get autoclean

COPY php.ini /usr/local/etc/php/

# Enable Apache modules

RUN a2enmod rewrite
RUN a2enmod ssl

# Copy default Apache conf.

WORKDIR /var/www/html

COPY ./apache_restart.sh /usr/local/bin/apache_restart
RUN chmod o-rx /usr/local/bin/apache_restart
RUN chmod ug+rx /usr/local/bin/apache_restart

WORKDIR /var/www/html/extensions

RUN git clone https://github.com/Rican7/MediaWiki-MarkdownExtraParser.git
RUN git clone https://github.com/wikimedia/mediawiki-extensions-Nuke.git
RUN mv MediaWiki-MarkdownExtraParser MarkdownExtraParser
RUN mv mediawiki-extensions-Nuke Nuke
RUN chown -R www-data:www-data MarkdownExtraParser
RUN chown -R www-data:www-data Nuke

COPY --chown=www-data:www-data ./markdown.php ./MarkdownExtraParser

WORKDIR /var/www/html

COPY --chown=root:root ./apache_base_ssl_$USE_SSL.conf /etc/apache2/sites-enabled/000-default.conf
