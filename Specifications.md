# Spécifications

## Vocabulaire

- **l'utilisateur** est l'internaute qui, une fois connecté, peut consulter la liste des documents, et les télécharger
- **l'administrateur** est l'internaute qui, via Google Drive, ajoute/modifie/supprime les documents partagés
- **le site principal** est le site extranet par lequel l'internaute se connecte habituellement.
- **l'application GED** (Gestion électronique des documents) ou **DMS** (Document Management System) est la présente application 
- le **dossier de partage** est, dans Google Drive, le dossier qui est exposé dans l'application GED

## Connexion de l'utilisateur

Le seul moyen de se connecter à l'application est d'utiliser l'authentification unique
(_Single Sign On_) avec le site principal.

L'authentification unique nécessite de mettre en place un serveur d'authentification sur le site principal
et un client d'authentification dans l'application GED

L'autilisateur peut se connecter ou de déconnecter. Son mot de passe n'est pas géré dans l'applciation GED

## Connexion de l'administrateur 

L'administrateur se ne connecte que sur Google Drive pour ajouter/modifier/supprimer les documents dans le dossier de partage

## Statistiques

Les clicks de téléchargements et les recherche par mot-clef sont enregistrés dans Google Analytics

## Recherche 

La recherche se fait sur tous les fichiers et présente les résultats "à plat", paginés

### Tris et filtres de recherche

Le tri ne limite pas la vue des résultats, il ne fait que la ré-ordonnée
Le filtre limite les résultats à l'expression recherchée via le moteur de recherche

Les tris disponibles se font sur ces critères : 

- nom du fichier
- date de dernière modification
- type de fichier


## Résultats

### pagination des résultats

Les fichiers sont affichées sous forme de liste de résultat, paginées à 100 fichiers par défaut




