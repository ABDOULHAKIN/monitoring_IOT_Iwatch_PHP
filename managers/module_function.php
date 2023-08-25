<?php

require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-db-connect.php';

// 1. Fonction qui permet d'afficher toutes les modules dans la base des données 

function getAllModule()
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT m.nom_module, m.details_module, e.libelle_etat,
                   temp.valeur_voulu AS valeur_voulu_temperature,
                   vit.valeur_voulu AS valeur_voulu_vitesse,
                   hum.valeur_voulu AS valeur_voulu_humidite,
                   m.numero_serie_module, m.actif_module
            FROM modulee AS m
            LEFT JOIN etat AS e ON m.id_etat = e.id_etat
            LEFT JOIN appartenir AS temp ON m.id_modulee = temp.id_modulee 
               AND temp.id_type_donnee = (SELECT id_type_donnee FROM type_donnee WHERE libelle_type_donnee = 'temperature')
            LEFT JOIN appartenir AS vit ON m.id_modulee = vit.id_modulee 
               AND vit.id_type_donnee = (SELECT id_type_donnee FROM type_donnee WHERE libelle_type_donnee = 'vitesse')
            LEFT JOIN appartenir AS hum ON m.id_modulee = hum.id_modulee 
               AND hum.id_type_donnee = (SELECT id_type_donnee FROM type_donnee WHERE libelle_type_donnee = 'humidite')";

    return $pdo->query($sql)->fetchAll();
}


// 2. Fonction qui permet de récuperer depuis la BDD le differents états enregistrés

function getAllEtat()
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT*
        FROM etat";
    return $pdo->query($sql)->fetchAll();
}


// 2. Fonction qui recupere les modules 

function getNameModule()
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT id_modulee, nom_module
            FROM modulee
            WHERE actif_module = 1";
    return $pdo->query($sql);
}

// 2. Fonction qui m'affiche le nom du module seulement

function nameModule()
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT nom_module FROM modulee";

    return $pdo->query($sql)->fetch();
}

// 3. Fonction qui permet d'ajouter un module 


function insertModule(array $moduleData)
{
    // La protection contre les injections SQL
    $moduleData['nom_module'] = htmlspecialchars($moduleData['nom_module']);
    $moduleData['details_module'] = htmlspecialchars($moduleData['details_module']);
    $moduleData['numero_serie_module'] = htmlspecialchars($moduleData['numero_serie_module']);

    // Insertion dans la table modulee avec actif_module défini à true

    $pdo = $GLOBALS['pdo'];
    $sql = "INSERT INTO modulee (nom_module, details_module, numero_serie_module, actif_module, id_etat)
            VALUES (:nom_module, :details_module, :numero_serie_module, true, :id_etat)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'nom_module' => $moduleData['nom_module'],
        'details_module' => $moduleData['details_module'],
        'numero_serie_module' => $moduleData['numero_serie_module'],
        'id_etat' => $moduleData['id_etat']
    ]);

    $id_modulee = $pdo->lastInsertId();

    // TODO:  Factoriser cet requête dans une fonction car aussi appelé dans new1.php
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT id_type_donnee, libelle_type_donnee FROM type_donnee";
    $result = $pdo->query($sql);
    $typesDonnees = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach ($typesDonnees as $type) {
        if (isset($moduleData["{$type['libelle_type_donnee']}_check"])) {
            $valeur_voulue = $moduleData["{$type['libelle_type_donnee']}_value"];

            $sql = "INSERT INTO appartenir (id_modulee, id_type_donnee, valeur_voulu)
            VALUES (:id_modulee, :id_type_donnee, :valeur_voulu)";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'id_modulee' => $id_modulee,
                'id_type_donnee' => $type['id_type_donnee'],
                'valeur_voulu' => $valeur_voulue
            ]);
        }
    }

    return $id_modulee;
}


// Fonction qui verifie si le numéro de serie a été saisie au préalable

function verifModuleExiste($numeroSerieModule)
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT id_modulee FROM modulee WHERE numero_serie_module = :numero_serie_module";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['numero_serie_module' => $numeroSerieModule]);

    // Vérifier si la requête a renvoyé des résultats
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_modulee = $row['id_modulee'];
        return $id_modulee;
    } else {
        return false; // Le module n'existe pas
    }
}





// 4. Fonction qui recupere les états de chaque module

function getEtat()
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT *
            FROM etat";

    return $pdo->query($sql)->fetchAll();
}




// 7. Fonction qui verifie si les champs sont 

// tableau d'erreur qui est vide 
// verifie chaque champs si c'est vide 

function checkFormData(array $data, array $requireds = []): array
{
    $errors = [];
    // $key = value 
    foreach ($data as $key => $value) {
        if ($requireds == [] || in_array($key, $requireds)) {
            if (empty($value)) {
                $errors[$key] = "Ce champs ne doit pas être vide";
            }
        }
    }

    return $errors;
}



function getAllValeur_actuelle()
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT valeur_actuelle
            FROM historique
            WHERE notify = 1
            ORDER BY date_historique DESC";

    return $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN, 0);
}



// Fonction qui permet d'insérer les valeurs en dysfonctionnement 
function insertHistorique(array $data)
{
    // La protection contre les injections SQL

    $data['valeur_actuelle'] = htmlspecialchars($data['valeur_actuelle']);
    $data['message'] = htmlspecialchars($data['message']);

    $pdo = $GLOBALS['pdo'];
    $sql = "INSERT INTO historique (valeur_actuelle, message, notify, id_type_donnee, id_modulee, date_historique)
            VALUES (:valeur_actuelle, :message, :notify, :id_type_donnee, :id_modulee, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam('valeur_actuelle', $data['valeur_actuelle']);
    $stmt->bindParam('message', $data['message']);
    $stmt->bindParam('id_type_donnee', $data['id_type_donnee']);
    $stmt->bindParam('id_modulee', $data['id_modulee']);
    $stmt->bindParam('notify', $data['notify']);
    $stmt->execute();

    $lastInsertId = $pdo->lastInsertId();
    return $lastInsertId;
}




// Requête pour récupérer la valeur_voulu de chaque module et type_donnee depuis la table historique
function getAllValeur_voulu($id_module)
{

    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT 
    id_modulee, id_type_donnee, 
    valeur_voulu 
    FROM appartenir
    WHERE id_modulee = {$id_module}";
    return $pdo->query($sql)->fetchAll();
}


// Fonction qui permet de supprimer une notification grâce à son ID

function deleteModule(int $int)
{
    $pdo = $GLOBALS['pdo'];
    $sql = "DELETE FROM notification
            WHERE id_modulee=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $int
    ]);

    return $stmt->rowCount();
}


// Fonction pour obtenir l'id_type_donnee à partir du libellé
function getId_type_donnee($libelle_type_donnee)
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT id_type_donnee FROM type_donnee WHERE libelle_type_donnee = :libelle";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['libelle' => $libelle_type_donnee]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['id_type_donnee'];
}

// Fonction pour obtenir l'id_modulee à partir du nom_module
function getId_modulee($nom_module)
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT id_modulee FROM modulee WHERE nom_module = :nom";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nom' => $nom_module]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['id_modulee'];
}

// Fonction qui recupere l'id_modulee depuis la table historique 

function getAllModuleIDs()
{
    $pdo = $GLOBALS['pdo'];

    $sql = "SELECT DISTINCT id_modulee FROM historique";

    $stmt = $pdo->query($sql);
    $moduleIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return $moduleIDs;
}

// Fonction qui permet d'afficher tous les valeurs de la table notification 

function getAllNotification($id_modulee)
{
    $pdo = $GLOBALS['pdo'];

    $sql = "SELECT modulee.id_modulee, type_donnee.id_type_donnee, modulee.nom_module, type_donnee.libelle_type_donnee, valeur_actuelle, notify, message, appartenir.valeur_voulu, MAX(date_historique) AS last_generated_date
    FROM historique
    INNER JOIN modulee ON historique.id_modulee = modulee.id_modulee
    INNER JOIN type_donnee ON historique.id_type_donnee = type_donnee.id_type_donnee
    INNER JOIN appartenir ON modulee.id_modulee = appartenir.id_modulee AND type_donnee.id_type_donnee = appartenir.id_type_donnee
    WHERE modulee.id_modulee = :id_modulee AND notify = 1
    GROUP BY modulee.id_modulee, type_donnee.id_type_donnee
    ORDER BY modulee.id_modulee, type_donnee.id_type_donnee";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_modulee', $id_modulee, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function getNbNotifications()
{
    $pdo = $GLOBALS['pdo'];

    $sql = "SELECT COUNT(*) as nbNotifs FROM 
    (
        SELECT modulee.id_modulee, type_donnee.id_type_donnee, modulee.nom_module, type_donnee.libelle_type_donnee, valeur_actuelle, notify, message, appartenir.valeur_voulu, MAX(date_historique) AS last_generated_date
        FROM historique
        INNER JOIN modulee ON historique.id_modulee = modulee.id_modulee
        INNER JOIN type_donnee ON historique.id_type_donnee = type_donnee.id_type_donnee
        INNER JOIN appartenir ON modulee.id_modulee = appartenir.id_modulee AND type_donnee.id_type_donnee = appartenir.id_type_donnee
        WHERE notify = 1
        GROUP BY modulee.id_modulee, type_donnee.id_type_donnee
        ORDER BY modulee.id_modulee, type_donnee.id_type_donnee
    ) AS T";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}


// Fonction Update pour les notifications vu par un utilisateur

function updateNotif($id_modulee, $id_type_donnee)
{
    $pdo = $GLOBALS['pdo'];
    $sql = "UPDATE historique SET notify = 0
    WHERE id_modulee = {$id_modulee} AND id_type_donnee = {$id_type_donnee}";
    $pdo->query($sql);
}

// Fonction qui permet de compter les nombres des notifications qui ont un notify = 1

function nbNotif()
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT COUNT(*) as notify_count
          FROM historique
          WHERE notify = 1";
    $result = $pdo->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['notify_count'];
}


// Fonction qui permet de récuperer tous les parametres

function getAllParametre()
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT *
            FROM type_donnee";
    return $pdo->query($sql)->fetchAll();
}

// Fonction qui affiche le titre de la musique grâce à son ID

function updateParam(array $data)
{

    $data['libelle_type_donnee'] = htmlspecialchars($data['libelle_type_donnee']);


    $pdo = $GLOBALS['pdo'];
    $sql = "UPDATE type_donnee
    SET libelle_type_donnee = :libelle_type_donnee
    WHERE id_type_donnee = :id_type_donnee";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    return $stmt->rowCount();
}

function getParamById(int $id)
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT * FROM type_donnee WHERE id_type_donnee = :id_type_donnee";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_type_donnee' => $id,
    ]);

    return $stmt->fetch();
}

// Fonction qui permet de supprimer un type des données grâce à son ID

function deleteParam(int $int)
{
    $pdo = $GLOBALS['pdo'];
    $sql = "DELETE FROM type_donnee
            WHERE id_type_donnee=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $int
    ]);

    return $stmt->rowCount();
}

// Fonction qui permet d'ajouter un paramétre 

function insertParam(array $data)
{
    $data['libelle_type_donnee'] = htmlspecialchars($data['libelle_type_donnee']);

    $pdo = $GLOBALS['pdo'];
    $sql = "INSERT INTO type_donnee
    (libelle_type_donnee)
    VALUES(:libelle_type_donnee)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute($data);
    // $stmt->debugDumpParms(); die;

    $id_article = $pdo->lastInsertId();

    return $id_article;
}

// Récupérer les types de données associés à ce module
function getTypeDonneeModule($id_modulee)
{
    $pdo = $GLOBALS['pdo'];

    $sql = "SELECT type_donnee.id_type_donnee, type_donnee.libelle_type_donnee
    FROM appartenir
    INNER JOIN type_donnee ON appartenir.id_type_donnee = type_donnee.id_type_donnee
    WHERE appartenir.id_modulee = :id_modulee";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_modulee', $id_modulee, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

// Fonction 

// function verifCheckTypeDonnee()
// {
//     $pdo = $GLOBALS['pdo'];
//     $sql = "SELECT id_type_donnee, libelle_type_donnee FROM type_donnee";
//     $result = $pdo->query($sql);
//     $result->fetchAll(PDO::FETCH_ASSOC);
// }

function getAllTypesDonnees() 
{
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT id_type_donnee, libelle_type_donnee FROM type_donnee";
    $result = $pdo->query($sql);
    $typesDonnees = $result->fetchAll(PDO::FETCH_ASSOC);

    // Retourner les types de données récupérés
    return $typesDonnees;
}

// Fonction pour vérifier si une valeur est un nombre entier
function isInteger($value) {
    return is_numeric($value) && intval($value) == $value;
}

