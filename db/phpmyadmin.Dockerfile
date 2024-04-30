FROM phpmyadmin/phpmyadmin
COPY phpmyadmin-servername.conf /etc/apache2/conf-available/phpmyadmin-servername.conf
RUN a2enconf phpmyadmin-servername.conf