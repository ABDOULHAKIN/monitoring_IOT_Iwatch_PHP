<?php

require $_SERVER['DOCUMENT_ROOT'] . '/managers/module_function.php';

// Récupération des données des modules
$sql = "SELECT 
m.id_modulee,
m.nom_module,
temperature.valeur_actuelle AS valeur_temperature,
temperature.date_historique AS date_temperature,
vitesse.valeur_actuelle AS valeur_vitesse,
vitesse.date_historique AS date_vitesse,
humidite.valeur_actuelle AS valeur_humidite,
humidite.date_historique AS date_humidite,
m.numero_serie_module,
COUNT(temperature.id_modulee) AS nombre_donnees,
TIMEDIFF(NOW(), MIN(temperature.date_historique)) AS duree_fonctionnement
FROM modulee m
LEFT JOIN (
SELECT h1.id_modulee, h1.valeur_actuelle, h1.date_historique
FROM historique h1
INNER JOIN type_donnee t1 ON h1.id_type_donnee = t1.id_type_donnee
WHERE t1.libelle_type_donnee = 'temperature'
) AS temperature ON m.id_modulee = temperature.id_modulee
LEFT JOIN (
SELECT h2.id_modulee, h2.valeur_actuelle, h2.date_historique
FROM historique h2
INNER JOIN type_donnee t2 ON h2.id_type_donnee = t2.id_type_donnee
WHERE t2.libelle_type_donnee = 'vitesse'
) AS vitesse ON m.id_modulee = vitesse.id_modulee
LEFT JOIN (
SELECT h3.id_modulee, h3.valeur_actuelle, h3.date_historique
FROM historique h3
INNER JOIN type_donnee t3 ON h3.id_type_donnee = t3.id_type_donnee
WHERE t3.libelle_type_donnee = 'humidite'
) AS humidite ON m.id_modulee = humidite.id_modulee
GROUP BY m.id_modulee;";

$result = $pdo->query($sql);

require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-top.php';
?>

<h1 class="titrefonctionnnement">État de fonctionnement des modules</h1>
<div class="scroll-container">
    <div class="module-grid">
        <?php
        if ($result->rowCount() > 0) {
            $count = 0;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $moduleId = $row["id_modulee"];
                $moduleNom = $row["nom_module"];
                $numeroSerie = $row["numero_serie_module"];
                $nombreDonnees = $row["nombre_donnees"];
                $dureeFonctionnement = $row["duree_fonctionnement"];
                $valeurTemperature = isset($row["valeur_temperature"]) ? $row["valeur_temperature"] : "";
                $dateTemperature = isset($row["date_temperature"]) ? $row["date_temperature"] : "";
                $valeurVitesse = isset($row["valeur_vitesse"]) ? $row["valeur_vitesse"] : "";
                $dateVitesse = isset($row["date_vitesse"]) ? $row["date_vitesse"] : "";
                $valeurHumidite = isset($row["valeur_humidite"]) ? $row["valeur_humidite"] : "";
                $dateHumidite = isset($row["date_humidite"]) ? $row["date_humidite"] : "";
        ?>

                <div class="module-container">
                    <div class="module-info">
                        <h2><?php echo "$moduleNom $numeroSerie"; ?></h2>
                        <?php if ($valeurTemperature !== "") { ?>
                            <p>La température actuelle : <?php echo $valeurTemperature; ?></p>
                            <p>Date de la dernière mesure de température : <?php echo $dateTemperature; ?></p>
                            <p>Durée de fonctionnement : <?php echo $dureeFonctionnement; ?></p>
                            <p>Nombre de données envoyées : <?php echo $nombreDonnees; ?></p>
                        <?php } ?>

                        <?php if ($valeurVitesse !== "") { ?>
                            <p>La vitesse actuelle : <?php echo $valeurVitesse; ?></p>
                            <p>Date de la dernière mesure de vitesse : <?php echo $dateVitesse; ?></p>
                            <p>Durée de fonctionnement : <?php echo $dureeFonctionnement; ?></p>
                            <p>Nombre de données envoyées : <?php echo $nombreDonnees; ?></p>
                        <?php } ?>

                        <?php if ($valeurHumidite !== "") { ?>
                            <p>Humidité détectée : <?php echo $valeurHumidite; ?></p>
                            <p>Nombre de données envoyées : <?php echo $nombreDonnees; ?></p>
                            <p>Durée de fonctionnement : <?php echo $dureeFonctionnement; ?></p>
                        <?php } ?>
                    </div>

                    <div class="chart-container">
                        <canvas class="chartCanvas" id="chart_<?php echo $moduleId; ?>"></canvas>
                    </div>
                </div>

                <?php
                // Préparation des données pour le graphique
                $sqlData = "SELECT t.id_type_donnee, t.libelle_type_donnee, h.valeur_actuelle, h.date_historique
            FROM historique h
            INNER JOIN type_donnee t ON h.id_type_donnee = t.id_type_donnee
            WHERE h.id_modulee = :id_modulee";

                $stmtData = $pdo->prepare($sqlData);
                $stmtData->bindValue(':id_modulee', $moduleId);
                $stmtData->execute();

                $chartData = []; // Tableau pour stocker les données pour chaque type de donnée
                $colors = [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    // Ajoutez d'autres couleurs ici si nécessaire pour plus de courbes
                ];
                $colorIndex = 0;

                while ($rowData = $stmtData->fetch(PDO::FETCH_ASSOC)) {
                    $idDonnee = $rowData['id_type_donnee'];
                    $typeDonnee = $rowData['libelle_type_donnee'];
                    $valeur = $rowData["valeur_actuelle"];
                    $date = $rowData["date_historique"];

                    // Vérifier si le type de donnée existe déjà dans le tableau $chartData
                    // Si non, on l'ajoute avec un tableau vide pour les valeurs et une couleur
                    if (!isset($chartData[$typeDonnee])) {
                        $chartData[$typeDonnee] = [
                            'label' => $typeDonnee,
                            'data' => [],
                            'backgroundColor' => $colors[$colorIndex],
                            'borderColor' => $colors[$colorIndex],
                            'borderWidth' => 2
                        ];

                        // Incrémenter l'indice pour choisir une couleur différente pour le prochain type de donnée
                        $colorIndex = ($colorIndex + 1) % count($colors);
                    }

                    // Ajouter les valeurs dans le tableau pour le type de donnée
                    $chartData[$typeDonnee]['data'][] = $valeur;
                    $chartData[$typeDonnee]['dates'][] = $date;
                }

                // Générer le script pour le graphique avec toutes les courbes
                echo '<script>';
                echo 'var ctx = document.getElementById("chart_' . $moduleId . '").getContext("2d");';
                echo 'var chart = new Chart(ctx, {';
                echo 'type: "line",';
                echo 'data: {';

                // Utiliser les dates d'un des types de données (tous les types auront les mêmes dates)
                // Dans cet exemple, on utilise les dates du premier type de donnée
                $firstTypeData = reset($chartData); // Récupérer le premier élément du tableau $chartData
                echo 'labels: ' . json_encode($firstTypeData['dates']) . ',';

                // Utiliser array_values() pour réindexer le tableau à partir de 0
                echo 'datasets: ' . json_encode(array_values($chartData));
                echo '},';
                echo 'options: {}';
                echo '});';
                echo '</script>';

            //---------------------------------------------Ce code est un deuxieme exmple qui ne contient pas à l'abscisse----------
            /**
             * // Générer le script pour le graphique avec toutes les courbes
             *echo '<script>';
             *echo 'var ctx = document.getElementById("chart_' . $moduleId . '").getContext("2d");';
             *echo 'var chart = new Chart(ctx, {';
             *echo 'type: "line",';
             *echo 'data: {';
             *echo 'labels: ' . json_encode($chartData[array_key_first($chartData)]['data']) . ','; // Utiliser les dates d'un des types de données (tous les types auront les mêmes dates)
             *echo 'datasets: ' . json_encode(array_values($chartData)); // Utiliser array_values() pour réindexer le tableau à partir de 0
             *echo '},';
             *echo 'options: {}';
             *echo '});';
             *echo '</script>';
             *?>
             */
            //-----------------------------------------------------------------------------------------------------------------------------
                ?>


        <?php
                // Incrémenter le compteur pour gérer les colonnes de la grille
                $count++;

                // Si on a atteint la fin de la première ligne (deux modules par ligne), ajouter une nouvelle ligne
                if ($count % 2 === 0) {
                    echo '</div><div class="module-grid">';
                }
            }
        } else {
            echo "Aucun module trouvé.";
        }
        ?>
    </div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-bottom.php';
?>