<?php
session_start();

require $_SERVER['DOCUMENT_ROOT'] . '/managers/module_function.php';


// Cette fonction permettra de generer toutes les modules enregistre dans la BDD

$modules = getAllModule();
$nomModule = nameModule();

require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-top.php';
?>

<h1 class="titre">Module présent dans la BDD</h1>
<div class="container">
    <div class="revenir">
        <div class="buttonretour">
            <a href="/iwatch/notification.php" class="">
                <i class='bx bxs-bell-ring'></i>
                <?php


                $nombresNotifications = getNbNotifications();
                // Vérifiez si le nombre de notifications est supérieur à zéro, alors affichez le badge avec le nombre.
                if ($nombresNotifications[0]['nbNotifs'] > 0) {
                    echo '<span class="notification-count">' . $nombresNotifications[0]['nbNotifs'] . '</span>';
                }

                ?>
            </a>
        </div>


        <div>
            <?php if (isset($_SESSION['success'])) : ?>
                <div class="success-message">
                    <?php echo $_SESSION['success']; ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
        </div>
        <div class="buttoncree">
            <a href="/iwatch/new.php" class="">
                <i class='bx bxs-file-plus'></i>
            </a>
        </div>
    </div>

    <div class="tableau">
        <table class="tableau-style">
            <thead>
                <tr>
                    <th>Nom du module</th>
                    <th>Détails</th>
                    <th>Etat</th>
                    <th>Température</th>
                    <th>Vitesse</th>
                    <th>Humidité</th>
                    <th>Numéro de série</th>
                    <th>Actif</th>

                </tr>
            </thead>

            <tbody>
                <?php foreach ($modules as $module) : ?>
                    <tr>
                        <td><?= $module['nom_module'] ?></td>
                        <td><?= $module['details_module'] ?></td>
                        <td><?= $module['libelle_etat'] ?></td>
                        <td><?= $module['valeur_voulu_temperature'] ?></td>
                        <td><?= $module['valeur_voulu_vitesse'] ?></td>
                        <td><?= $module['valeur_voulu_humidite'] ?></td>
                        <td><?= $module['numero_serie_module'] ?></td>
                        <td> <i class="fa-solid fa-toggle-on"></i> <?= $module['actif_module'] ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div>
</div>

<?php

require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-bottom.php';
?>