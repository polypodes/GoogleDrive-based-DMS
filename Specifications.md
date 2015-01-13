# Spécifications

## Vocabulaire

- **l'utilisateur** est l'internaute qui, une fois connecté, peut consulter la liste des documents, et les télécharger
- **l'administrateur** est l'internaute qui, via Google Drive, ajoute/modifie/supprime les documents partagés
- **le site principal** est le site extranet par lequel l'internaute se connecte habituellement.
- **l'application GED** (Gestion électronique des documents) ou **DMS** (Document Management System) est la présente application 
- le **dossier de partage** est, dans Google Drive, le dossier qui est exposé dans l'application GED
- une **fonctionnalité** est une caractéristique de l'application : un _traitement_ qu'elle est capable de réaliser pour l'utilisateur.

## Connexion de l'utilisateur

Le seul moyen de se connecter à l'application est d'utiliser l'authentification unique
(_Single Sign On_) avec le site principal.

L'authentification unique nécessite de mettre en place un serveur d'authentification sur le site principal
et un client d'authentification dans l'application GED

L'utilisateur peut se connecter ou de déconnecter. Son mot de passe n'est pas géré dans l'applciation GED

## Connexion de l'administrateur 

L'administrateur s'identifier directement sur Google Drive pour ajouter/modifier/supprimer les documents dans le dossier de partage

## Statistiques

Les clicks de téléchargements et les recherche par mot-clef sont enregistrés dans Google Analytics

## Navigation

Un menu latéral permet de naivguer dans les dossiers et sous-dossiers et d'en lister le contenu

## Recherche 

La recherche se fait sur tous les fichiers et présente les résultats "à plat", paginés : Elle est donc indépendante de l'organisation des fichiers en dossiers et sous-dossiers.

### Tris et filtres de recherche

Le tri ne limite pas la vue des résultats, il ne fait que la ré-ordonnée
Le filtre limite les résultats à l'expression recherchée via le moteur de recherche

Les tris disponibles se font sur ces critères : 

- date de dernière modification
- type de fichier

Les filtres disponibles se font sur ces critères : 

- nom du fichier
- type de fichier

## Résultats

Les résultats se présentent sous le moteur de recherche, sous forme de liste.

### Pagination des résultats

Les fichiers sont affichées sous forme de liste de résultat, le pas de pagination est fixe à 100 fichiers par défaut
Une pagination au clic (2 liens : précédent / suivant) permet de naviguer de pas en pas.

## Limites

Aucune fonctionnalité n'est induite ou implicite : Seules les fonctionnalités décrites de manière suffisante pour le Client Finale et pour l'Agence, dans un document contractuel, sont implémentées. En cas d'imprécision, de doute ou de contre-sens sur une fonctionnalité, et en l'absence de précision apportée, l'interprétation de ce que la fonctionnalité décrit est laissée au jugement de l'Agence qui doit mettre en place cette fonctionnalité. La modification ou la correction d'une fonctionnalité initialement décrite de manière imprécise, inconsistante ou imparfaite reste à la charge du Client Final.



