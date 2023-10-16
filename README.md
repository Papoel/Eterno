# Projet Eterno

Bienvenue dans le projet Eterno ! Ce README vous fournira une vue d'ensemble du projet, des technologies utilisées et 
des liens vers des documents importants pour la compréhension et le développement.

## Vue d'ensemble

Eterno est un projet qui offre un espace pour l'expression personnelle et le partage de messages pour les personnes en 
deuil. 
Il permet aux utilisateurs de communiquer leurs pensées, leurs émotions et leurs messages à un être cher décédé, 
symbolisé par une "Lumière". 
Contrairement à une messagerie traditionnelle, Eterno ne fournit pas de réponses ou d'interactions avec des 
intelligences artificielles. 
Au lieu de cela, il offre un moyen intime de se connecter et de se confier à l'être disparu.

Le fonctionnement est simple : lorsque la Lumière est allumée, les utilisateurs peuvent écrire et envoyer des messages. 
Ces messages restent confidentiels, seuls les utilisateurs peuvent les lire. Une fois que le processus de deuil est 
complet, l'utilisateur a la possibilité d'éteindre la Lumière, ce qui signifie qu'ils ont pris la décision de ne plus 
échanger de messages.

Eterno est un projet respectueux et sécurisé qui honore la mémoire des êtres chers disparus tout en offrant un espace 
d'expression pour ceux qui restent.

## Installation

Pour installer le projet Eterno, suivez ces étapes :
1. Cloner le projet depuis GitHub https://www.github.com/Papoel/Eterno :
   ```shell
   git clone https://www.github.com/Papoel/Eterno
   ```
2. Installer les dépendances du projet :
   ```shell
    composer install
    ```
3. Créer un fichier d'environnement `.env.local` à la racine du projet et renseigner les variables d'environnement 
   suivantes :
   ```shell
   make create-env
   ```

4. Créer la base de données :
   ```shell
    make init-db
    ```

5. Lancer les conteneur Docker et ouvrir le projet dans le navigateur :
   ```shell
    make start
    ```
   
6. Pour arrêter les conteneur Docker ainsi que le projet :
   ```shell
    make stop
    ```
   
## Technologies utilisées

Le projet Eterno est construit en utilisant les technologies suivantes :

- Symfony : [lien vers la documentation de Symfony](https://symfony.com/doc/current/index.html)
- PostgreSQL : [lien vers la documentation de PostgreSQL](https://www.postgresql.org/docs/current/)
- React: [lien vers la documentation de React](https://reactjs.org/)
- Bootstrap 5: [lien vers la documentation de Bootstrap 5](https://getbootstrap.com/docs/5.2/)
- Tailwind CSS: [lien vers la documentation de Tailwind CSS](https://tailwindcss.com/docs)

## Documentation

- [Documentation de la base de données](src/docs/database.md) : Consultez cette documentation pour comprendre la 
  structure de la base de données du projet.

## Connexion à la base de données avec pgAdmin

Se connecter à la base de données PostgresSQL utilisée dans le projet Eterno.

Le conteneur Docker doit être lancé pour pouvoir se connecter à la base de données.

   ```shell
   make start
  ```

- Accédez à l'URL suivante : [http://localhost:8085](http://localhost:8085).
- Connectez-vous en utilisant les identifiants suivants :
  - Adresse e-mail : `eterno@admin.fr`
  - Mot de passe : `admin`
Maintenant, vous êtes dans l'interface de pgAdmin.
Pour vous connecter à la base de données, suivez ces étapes :
  - Faire un clic droit sur `Servers` et sélectionner `Register` > `Server...`
  - Dans l'onglet `General`, renseigner les champs suivants :
    - Name : `Eterno`
  - Dans l'onglet `Connection`, renseigner les champs suivants :
    - Host name/address : `database`
    - Port : `5432`
    - Maintenance database : `eterno_db`
    - Username : `root`
    - Kerberos Authentication : `No`
    - Password : `DevMode2023!`
    - Save password : `Yes`

## Comment contribuer

Si vous souhaitez contribuer à ce projet, suivez ces étapes :

1. ...
2. ...
3. ...

N'hésitez pas à nous contacter si vous avez des questions ou des suggestions.

## Liste des contributeurs
- [Papoel](https://www.github.com/Papoel)


## Licence

Ce projet est sous licence MIT. Pour plus de détails, veuillez consulter le fichier [licence](./LICENSE).

---

Nous vous remercions pour votre intérêt pour le projet **Eterno** !
