# projet-o-lala-la-regalade-back

## Installation du projet :

- Git clone du repo sur votre serveur
- Rentrer dans le projet
- Créer un fichier .env.local en y indiquant la partie DATABASE_URL=
- Composer install
- Création de la base de données avec bin/console doctrine:database:create
- Création de la structure de la base de données avec bin/console doctrine:migrations:migrate
- Exécution des fixtures : bin/console doctrine:fixture:load
- Génération de la clé JWT : bin/console lexik:jwt:generate-keypair

## Liste des bundles utilisés :

- [lexik/jwt-authentication-bundle](https://github.com/lexik/LexikJWTAuthenticationBundle) :
Pour gérer l'authentification via token

- [gesdinet/jwt-refresh-token-bundle](https://packagist.org/packages/gesdinet/jwt-refresh-token-bundle) :
Pour gérer le refresh automatique du token

- [knplabs/knp-paginator-bundle](https://github.com/KnpLabs/KnpPaginatorBundle) :
Pour permettre la pagination

- [nelmio/cors-bundle](https://github.com/nelmio/NelmioCorsBundle) :
Pour permettre au serveur front de pouvoir envoyer des requêtes à notre API

- [vich/uploader-bundle](https://packagist.org/packages/vich/uploader-bundle) :
Pour gérer l'upload des images dans le back-office
