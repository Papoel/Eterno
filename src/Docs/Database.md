# Documentation de la Base de Données "Eterno"

## Introduction

Ce document fournit une documentation détaillée de la structure de la base de données "Eterno". La base de données est conçue pour stocker des informations relatives aux utilisateurs, aux profils Lumière et aux messages associés. Chaque section de cette documentation détaille les tables et les champs avec leurs descriptions.

## Table "Users"

- `id` (uuid, clé primaire) : Identifiant unique de l'utilisateur.
- `firstname` (varchar(50)) : Prénom de l'utilisateur.
- `lastname` (varchar(50)) : Nom de famille de l'utilisateur.
- `username` (varchar(50)) : Nom d'utilisateur (Pseudo) de l'utilisateur.
- `email` (varchar(180)) : Adresse e-mail de l'utilisateur.
- `password` (varchar(255)) : Mot de passe de l'utilisateur (stocké de manière sécurisée, c'est-à-dire hashé).
- `roles` (array) : Rôles de l'utilisateur (utilisé pour l'authentification et l'autorisation).
- `mobile` (varchar(10)) : Numéro de téléphone mobile de l'utilisateur. 
- `lights` (OneToMany(mappedBy: 'userAccount', targetEntity: Light ::class, orphanRemoval : true)) : Profils Lumière créés par l'utilisateur.
- `messages` (OneToMany(mappedBy: 'userAccount', targetEntity: Message ::class, orphanRemoval : true)) : Messages envoyés par l'utilisateur.
- `birthday` (date) : Date de naissance de l'utilisateur.
- `createdAt` (DateTimeImmutable) : Date et heure de création du compte utilisateur.

**Description** :
- La table "User" stocke les informations d'identification de l'utilisateur, y compris son nom d'utilisateur, son adresse e-mail, son mot de passe et la date de création du compte.

## Table "Lights"

- `id` (int, clé primaire) : Identifiant unique de chaque profil Lumière.
- `birthdayAt` (DateTimeImmutable) : La date de naissance de la personne décédée.
- `deceasedAt` (DateTimeImmutable) : La date de décès de la personne décédée.
- `userAccount` (ManyToOne (inversedBy : 'lights'), clé étrangère vers la table "User") : Identifiant de l'utilisateur qui a créé la Lumière.
- `messages` (OneToMany (mappedBy : 'light', targetEntity : Message ::class, orphanRemoval : true)) : Une description de la personne décédée que la Lumière représente.
- `firstname` (varchar(50)) : Le prénom de la personne décédée que la Lumière représente.
- `lastname` (varchar(50)) : Le nom de la personne décédée que la Lumière représente.
- `username` (varchar(50)) : Le surnom/pseudo de la personne décédée que la Lumière représente.
- `createdAt` (DateTimeImmutable) : Date et heure de création du profil Lumière.

**Description** :
- La table "Light" représente les profils Lumière en mémoire des personnes décédées. Chaque profil est lié à un 
  utilisateur (référence à la table "User") et contient des informations telles que le nom de la personne décédée, 
  sa date de naissance, sa date de décès et la date de création du profil.

## Table "Messages"

- `id` (int, clé primaire) : Identifiant unique de chaque message.
- `userAccount` (ManyToOne (inversedBy : 'messages')) : Identifiant de l'utilisateur qui a envoyé le message.
- `light` (ManyToOne (inversedBy : 'messages')) : Identifiant de la Lumière à laquelle le message est associé.
- `message` (text) : Le contenu du message.
- `createdAt` (DateTimeImmutable) : Date et heure de création du message.

**Description** :
- La table "Message" stocke les messages envoyés par les utilisateurs aux profils Lumière en mémoire des personnes décédées. 
  Chaque message est associé à l'utilisateur qui l'a envoyé (référence à la table "User") et à la Lumière à laquelle il 
  est destiné (référence à la table "Light").
- Tous les messages seront cryptés et stockés dans la base de données. Le cryptage est effectué par l'application 
  avant l'envoi du message, seul l'auteur du message peut le lire.

## Conclusion

Cette documentation fournit une compréhension détaillée de la structure de la base de données "Eterno." 
Les tables et les champs sont décrits en détail pour faciliter la compréhension et l'utilisation de la base de données.

Pour toute question supplémentaire ou assistance, veuillez contacter @Papoel ou @Honoré sur Discord.
