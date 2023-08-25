Application monitoring IOT, exemple d’une montre connectée


Dans le cadre de la réalisation de ce projet, j’ai utilisé à la fois css et bootstrap.


1. Cadre de travail : 


* Avoir WAMP installé avec les trois services en marche
* Dézipper les sources du dossier
* Ouvrir phpmyadmin pour créer un outil virtuel ou, depuis le CMD, un localhost avec une plage d’IP dédiée (ex: php -S localhost:92).


2. Les interfaces : 


2.1. Interface affichage des modules enregistrés


* Cloche de notification qui donne accès à l’interface des notifications : 
* Affichage du nombre de notifications 
* Lors du clic sur l’icône “vu” (le nombre diminue jusqu’à la disparition du nombre de notifs)
* Présence du message de dysfonctionnement de l’appareil


* Création d’un module : 
* Accès au formulaire 
* Vérification du formulaire avec des (required) HTML
* Vérification du formulaire en PHP : 
* Tout le formulaire contient des renseignements du module que l’utilisateur veut enregistrer
* Sur les paramètres de température, vitesse et humidité:
* Vérifier s’il y a au moins une checkbox cochée
* Et si cette checkbox cochée contient une valeur en entier mais pas en décimal


* J’ai pris, pour que l' utilisateur n'insère pas deux montres similaires, le champ numero_serie_module comme paramètre unique pour chaque montre. Afficher un message d’erreur si le numero_serie_module existe déjà dans la BDD.
* Protection contre les injections SQL avec les requêtes préparées et les failles XSS lors de la création des fonctions avec les requêtes dans le fichier manager.




2.2. Interface de l’historique


* Deux tableaux, un comptant les informations du modules et l’autre avec chartJS, le graphe des différents paramètres.
* J’aimerais attirer votre attention sur le fait que je mis deux exemples pour afficher les graphes différemment, surtout l’axe des abscisses qui va changer.




2.3. Interface des données


* Choix de l’utilisateur de générer des données pour un module : 
* Données générées en fonction du (type_donnee) que contient ce module.  


2.4. Interface des différents paramètres


* CRUD des paramètres : 
* Laisser le choix à l’utilisateur de modifier, ajouter, supprimer un paramètre en fonction de la montre. 
* Vérification en JS avec un regex puis PHP (en stockant en $_SESSION)  avant la soumission du formulaire.  
                   
3. Base des données
* Vous trouverez le script de ma base des données avec un exemple d’un module déjà enregistré dans le fichier zippé.