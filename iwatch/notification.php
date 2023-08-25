<?php
session_start();

require $_SERVER['DOCUMENT_ROOT'] . '/managers/module_function.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-top.php';

if (isset($_POST['verified']) && isset($_POST['id_modulee']) && isset($_POST['id_type_donnee'])) {
    $verified = updateNotif($_POST['id_modulee'], $_POST['id_type_donnee']);
}
// Récupérer tous les identifiants de module
$moduleIDs = getAllModuleIDs();
$notifications = array();

foreach ($moduleIDs as $id_modulee) {
    $moduleNotifications = getAllNotification($id_modulee);
    $notifications = array_merge($notifications, $moduleNotifications);
}

?>

<h1 class="titre">Les notifications</h1>

<div class="container">
    <div class="revenir">
        <div class="buttonretour">
            <a href="/iwatch/index.php" class="">
                <i class='bx bx-arrow-back'></i>
            </a>
        </div>
        <div class="tableau-scroll">
            <div class="tableau">
                <table class="tableau-style">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Type de donnée</th>
                            <th>Valeur actuelle</th>
                            <th>Valeur voulue</th>
                            <th>Message</th>
                            <th>Cheked</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($notifications as $notification) : ?>
                            <tr>
                                <td><?= $notification['nom_module'] ?></td>
                                <td><?= $notification['libelle_type_donnee'] ?></td>
                                <td><?= $notification['valeur_actuelle'] ?></td>
                                <td><?= $notification['valeur_voulu'] ?></td>
                                <td><?= $notification['message'] ?></td>
                                <td>
                                    <form method="post">
                                        <input name="id_modulee" type="hidden" value="<?= $notification['id_modulee'] ?>" />
                                        <input name="id_type_donnee" type="hidden" value="<?= $notification['id_type_donnee'] ?>" />
                                        <button type="submit" name="verified" class="icon-button">
                                            <i class='bx bx-low-vision'></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
            </div>
                    </tbody>
                </table>
        </div>
    </div>
</div>
<?php

require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-bottom.php';
?>