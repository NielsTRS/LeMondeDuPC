# [Le Monde Du PC](https://www.lemondedupc.fr)
Dépot de la nouvelle version (v2.0) du site 

[License](LICENSE.md)

## Présentation
<table>
<tr>
<td>
Les sujets du numérique et du high tech ne doivent pas être compris uniquement par ceux qui les maitrisent
</tr>
</table>

## Outils
* PHPStorm
* Sass
* Symfony
* Mjml

## Réseaux
* [Discord](https://discord.gg/WHYRZfU)
* [Instagram](https://www.instagram.com/lemondedupc)
* [Twitter](https://twitter.com/LeMondeDuPC)
* [LinkedIn](https://www.linkedin.com/company/lemondedupc)

## Installation
Paquets nécessaires : 
* php
* mysql
* symfony 

Téléchargement des fichiers :
``` bash 
git clone https://github.com/LeMondeDuPC/LeMondeDuPC.git
```
Installation des dépendances :
``` bash 
cd LeMondeDuPC
composer install
```
Connectez la base de données au site via le fichier .env généré automatiquement et décommentez la variable MAILER_DSN

Configuration de la base de données :
``` bash 
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
Lancement du site (http://127.0.0.1:8000):
``` bash 
symfony server:start 
```
