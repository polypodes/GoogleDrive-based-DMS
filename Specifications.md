# Spécifications

## Vocabulaire

- **l'utilisateur** est l'internaute qui, une fois connecté, peut consulter la liste des documents, et les télécharger
- **l'administrateur** est l'internaute qui, via Google Drive, ajoute/modifie/supprime les documents partagés
- **le site principal** est le site extranet par lequel l'internaute se connecte habituellement.
- **l'application GED** (Gestion électronique des documents) ou **DMS** (Document Management System) est la présente application 
- le **dossier de partage** est, dans Google Drive, le dossier qui est exposé dans l'application GED
- une **fonctionnalité** est une caractéristique de l'application : un _traitement_ qu'elle est capable de réaliser pour l'utilisateur.

## Connexion de l'utilisateur

Le seul moyen de se connecter à l'application est d'utiliser l'authentification unique (_Single Sign On_) avec le site principal. Le principe consiste du Single Sign-On consiste

1. à s'identifier sur le site principal au moyen d'un login/mot de passe
2. à se rendre sur le site secondaire - ici l'application GED
3. à valider une demande de confirmation d'authentification, sans avoir à saisir à nouveau un login/mot de passe
4. l'internaute identifié sur le site principal est alors également identifié et autorisé à visiter le contenu de l'application GED. 

Le Signel-Sign-On n'est pas une authentification "transparente" mais une autorisation qui ne nécessite pas de re-saisir un couple d'idientifiants (login / mot de passe). Il est toujours soumis à l'acceptation de l'internaute, qui consiste à valider une demande : un clic sur un bouton ou un lien proposé, comme pour les  boutons "s'identifier avec Google/Facebook/Twitter/Paypal".

L'authentification unique nécessite de mettre en place un serveur d'authentification sur le site principal
et un client d'authentification dans l'application GED. Son mot de passe d'identification n'est pas stocké dans l'application GED mais reste géré dans le site principal.

L'utilisateur peut se connecter ou de déconnecter du site secondaire.

## Connexion de l'administrateur 

L'administrateur s'identifier directement sur Google Drive pour ajouter/modifier/supprimer les documents dans le dossier de partage

## Statistiques

Les clics de téléchargements et les recherche par mot-clef sont colelctés dans l'application GED et envoyés à Google Analytics pour être enregistrés et analysés. Les résultats de ces statistiques sont consultables sur le site de Google Analytics.

## Raccourcis à des listes liées aux statistiques

Deux listes de documents sont proposés dans un menu latéral ou dans une page dédiée :

- Les _n_ documents les plus téléchargés 
- Les _n_ documents récemment mis à jour

## Navigation

Un menu latéral permet de naviguer dans les dossiers et sous-dossiers et d'en lister le contenu

## Recherche 

La recherche se fait sur tous les fichiers. Elle présente les résultats "à plat", paginés : La recherche et ses résultats sont indépendants de l'organisation des fichiers en dossiers et sous-dossiers arborescents.

### Tris et filtres de recherche

Le tri ne limite pas la vue des résultats, il ne fait que la ré-ordonner.
Le filtre limite les résultats à l'expression recherchée via le moteur de recherche.

Les tris disponibles se font sur ces critères : 

- date de dernière modification
- type de fichier

Les filtres disponibles se font sur ces critères : 

- nom du fichier
- type de fichier

Lorsque les résultats s'affichent, les critères de recherche saisis restent renseigné dans le formulaire de recherche. Il est possible de filtrer des résultats de recherche.

L'URL des résultats de recherche peut être copiée-collée, ou ajoutée en favoris du navigateur, de manière à conserver cette recherche et d'y revenir,  pourvu que l'on soit identifié sur l'application GED.

## Résultats

Les résultats se présentent sous le moteur de recherche, sous forme de liste de documents.
Il n'y a pas de page de détail dédiée à un chaque document : ils se présentent uniquement sous forme de liste et sont immédiatement _cliquables_ pour être téléchargés.

### Pagination des résultats

Les fichiers sont affichées sous forme de liste de résultat, le pas de pagination est fixe à 100 fichiers par défaut
Une pagination au clic (2 liens : précédent / suivant) permet de naviguer de pas en pas.

## Téléchargements (URLs des documents) et Single-Sign-On

Chaque document listé dans les résultats propose un lien de téléchargement. Ce lien n'est pas un lien direct vers Google Drive mais un lien interne à l'application GED. Ce lien ne fonctionne que si l'internaute qui tente de l'utiliser est bien identifié sur le site. Ce lien peut être utilisé en dehors du site.

En cliquant sur le lien depuis un autre site :
- Si le single Sign On n'a pas encore été accepté ou si la personne n'était pas identifiée sur le site, on lui demande de s'identifier ou d'accepter le Single-Sign-On 

Peut-on faire pointer les URL de téléchargement des sites (sedap.com, dhd, lucid) vers les documents stockés sur le drive ?
Afin d’éviter d’avoir 2 bases à gérer.


## Limites

Aucune fonctionnalité n'est induite ou implicite : Seules les fonctionnalités décrites de manière suffisante pour le Client Final et pour l'Agence, dans un document contractuel, sont implémentées. En cas d'imprécision, de doute, de contre-sens ou d'incohérence dans la description d'une fonctionnalité, et en l'absence de précisions supplémentaires, son interprétation est laissée au jugement de l'Agence qui doit la mettre en place. La modification, l'amélioration ou la correction d'une fonctionnalité initialement décrite de manière incomplète, imprécise ou incohérente reste à la charge du Client Final. L'Agence propose au Client Final une offre d'Assistance à la Maîtrise d'Ouvrage pour l'accompagner dans la construction, la rédaction et la validation de son expression de besoin ou de son cahier des charges.



