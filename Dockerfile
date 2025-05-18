FROM dunglas/frankenphp AS frankenruntime

ENV SERVER_NAME=localhost:80 
# ajoutez des extensions supplémentaires ici :
RUN install-php-extensions \
	zip \
    pdo \
    pdo_pgsql \
	gd \
	intl \
	opcache