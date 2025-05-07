# P6-Snow-Tricks

Installation du projet:

git clone https://github.com/enzomu/P6-Snow-Tricks.git


composer install

créer la BDD:
php bin/console doctrine:database:create
php bin/console doctrine:schema:create


importer les données:
mysql -u root -p snowtricks < data/_localhost__2_-2025_05_07_19_31_21-dump.sql


symfony server:start