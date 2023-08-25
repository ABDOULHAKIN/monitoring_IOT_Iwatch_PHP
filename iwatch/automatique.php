<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/managers/module_function.php';

require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-top.php';

if (isset($_POST['generate'])) {
    // Vérification si un module a été sélectionné
    if (isset($_POST['module']) && !empty($_POST['module'])) {
        $selectedModuleId = $_POST['module'];

        // -----------------------Génération aléatoire des valeurs pour les types de données associés au module----------------------
        // TODO: Optionnel (parce qu'il faudrait avoir géré les types de données à l'insertion d'un module)
        // Récupérer les types de données associés à ce module
        $typesDonneesAssocies = getTypeDonneeModule($selectedModuleId);

        // Boucler sur les types de données récupérées

        // Créer un tableau avec ['id_type_donnee', valeur_rand]
        // Générer une valeur aléatoire pour chaque type de données associé et les stocker dans le tableau
        $valeursAleatoires = [];
        foreach ($typesDonneesAssocies as $typeDonnee) {
            $id_type_donnee = $typeDonnee['id_type_donnee'];
            $valeur_random = rand(0, 100); // Génération de la valeur aléatoire entre 0 et 100

            // Stocker la valeur aléatoire associée à l'id_type_donnee dans le tableau $valeursAleatoires
            $valeursAleatoires[$id_type_donnee] = $valeur_random;
        }

        // -------------------------Étape 1: Requête pour récupérer les valeurs voulues de la table historique
        $valeurs_voulues = getAllValeur_voulu($selectedModuleId);
        // $valeurs_actuelles = getAllValeur_actuelle(); // la valeur actuelle est les données générées automatiquement 

//         array (size=2)
//   0 => 
//     array (size=3)
//       'id_modulee' => int 18
//       'id_type_donnee' => int 2
//       'valeur_voulu' => int 50
//   1 => 
//     array (size=3)
//       'id_modulee' => int 18
//       'id_type_donnee' => int 3
//       'valeur_voulu' => int 50

        $historique = array();

        foreach ($typesDonneesAssocies as $index => $typeDonnee) {
            $id_type_donnee = $typeDonnee['id_type_donnee'];
            $libelle_type_donnee = $typeDonnee['libelle_type_donnee'];
            // Vérifier si l'id_type_donnee existe dans le tableau $valeursAleatoires
            if (isset($valeursAleatoires[$id_type_donnee])) {
                // Si oui, créer une entrée dans le tableau $historique pour ce type de données
                $historique[] = [
                    'id_modulee' => $selectedModuleId,
                    'id_type_donnee' => $id_type_donnee,
                    'valeur_actuelle' => $valeursAleatoires[$id_type_donnee], // Utiliser la valeur aléatoire associée à l'id_type_donnee
                    'valeur_voulue' => $valeurs_voulues[$index]['valeur_voulu'], // Utiliser la valeur associée au type de données depuis la table 'historique'
                    'notify' => false,
                    'message' => ''
                ];
            }
        }

        // Parcourir les entrées du tableau $historique et comparer les valeurs actuelles aux valeurs voulues
        foreach ($historique as &$entreeHistorique) {
            $valeur_actuelle = $entreeHistorique['valeur_actuelle'];
            $valeur_voulue = $entreeHistorique['valeur_voulue'];

            // Comparer les valeurs actuelle et voulue pour déterminer si une alerte est nécessaire
            if ($valeur_actuelle >= ($valeur_voulue * 1.1)) {
                // Le cas où la valeur actuelle est supérieure à la valeur voulue + 10%
                $entreeHistorique['notify'] = true;
                $entreeHistorique['message'] = 'La valeur a augmenté et a besoin d\'être corrigée.';
            } elseif ($valeur_actuelle <= ($valeur_voulue * 0.9)) {
                // Le cas où la valeur actuelle est inférieure à la valeur voulue - 10%
                $entreeHistorique['notify'] = true;
                $entreeHistorique['message'] = 'La valeur a baissé et a besoin d\'être corrigée.';
            }
        }

        // Insérer les données générées dans la table historique
        foreach ($historique as $data) {
            insertHistorique($data);
        }

        $_SESSION['success'] = "Les données ont été générées avec succès !";
        header("Location: /iwatch/automatique.php");
        exit();
    } else {
        echo "<p>Veuillez sélectionner un module avant de générer les données.</p>";
    }
}

?>

<h1 class="titrenotif">Génération Automatique de Données pour les Modules</h1>
<div>
    <?php if (isset($_SESSION['success'])) : ?>
        <div class="success-message">
            <?php echo $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
</div>
<div class="contentnotif">
    <form class="formnotif" action="" method="post">
        <label class="choixmodule" for="module">Choisissez un module :</label>
        <select name="module" id="module">
            <?php
            $nomModules = getNameModule();
            if ($nomModules) {
                foreach ($nomModules as $nomModule) {
                    $moduleId = $nomModule['id_modulee'];
                    $moduleName = $nomModule['nom_module'];
                    echo "<option value=\"$moduleId\">$moduleName</option>";
                }
            }
            ?>
        </select>
        <input type="submit" name="generate" value="Générer Automatiquement">
    </form>
</div>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-bottom.php';
?>