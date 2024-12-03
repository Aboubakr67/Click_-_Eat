# Étape 1 : Choisir une image de base avec PHP et Apache
FROM php:8.2-apache

# Étape 2 : Installer les extensions nécessaires pour MySQL
RUN docker-php-ext-install pdo_mysql

# Étape 3 : Copier les fichiers du projet dans le conteneur
COPY . /var/www/html

# Étape 4 : Donner les bons droits aux fichiers
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Étape 5 : Exposer le port 80
EXPOSE 80

# Étape 6 : Commande par défaut (Apache démarre automatiquement avec l'image de base)
CMD ["apache2-foreground"]



# Les commandes que j'ai executer
# ----------------- Construire l'image Docker ---------------
#docker build -t mon-projet:1.0 .

# ------------------- Lancer le conteneur ---------------------
#docker run -d -p 8080:80 mon-projet:1.0