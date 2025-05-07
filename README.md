# P6-Snow-Tricks

Installation du projet:

git clone https://github.com/enzomu/P6-Snow-Tricks.git


composer install

créer la BDD:
php bin/console doctrine:database:create
php bin/console doctrine:schema:create


importer les données:
php bin/console doctrine:fixtures:load

symfony server:start