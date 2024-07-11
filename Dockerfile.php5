FROM php:5-apache

# Aktiviere das mod_rewrite-Modul
RUN a2enmod rewrite