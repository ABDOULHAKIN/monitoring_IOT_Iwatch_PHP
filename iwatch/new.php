<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/managers/module_function.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-top.php';

if (!empty($_POST['submit'])) {
    // Vérification des champs obligatoires
    if (empty($_POST['modulee']['nom_module']) || empty($_POST['modulee']['details_module']) || empty($_POST['modulee']['numero_serie_module'])) {
        $_SESSION['erreur'] = "Tous les champs obligatoires n'ont pas été remplis";
    } else {
        unset($_SESSION['erreur']);
    }

    // Vérification des éléments cochés et des valeurs
    $elementsCoches = [];
    $valeursManquantes = [];
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT id_type_donnee, libelle_type_donnee FROM type_donnee";
    $result = $pdo->query($sql);
    $typesDonnees = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach ($typesDonnees as $type) {
        $elementName = "{$type['libelle_type_donnee']}_check";
        if (isset($_POST['modulee'][$elementName]) && $_POST['modulee'][$elementName] === "on") {
            $elementsCoches[] = $elementName;

            // Vérifier s'il y a une valeur pour l'élément coché
            $valeurElementName = "{$type['libelle_type_donnee']}_value";
            if (empty($_POST['modulee'][$valeurElementName])) {
                $valeursManquantes[] = $valeurElementName; // Stockez les éléments cochés sans valeur
            }
        }
    }

    if (count($elementsCoches) === 0) {
        $_SESSION['erreurP'] = "Veuillez sélectionner au moins un élément.";
    } else {
        unset($_SESSION['erreurP']);
    }

    if (count($valeursManquantes) !== 0) {
        $_SESSION['erreurL'] = "Veuillez saisir une valeur pour les éléments cochés : " . implode(', ', $valeursManquantes);
    } else {
        unset($_SESSION['erreurL']);
    }

    // Vérification si le numéro de série existe déjà dans la base de données
    $numeroSerieModule = $_POST['modulee']['numero_serie_module'];
    $moduleExiste = verifModuleExiste($numeroSerieModule);
    if ($moduleExiste) {
        $_SESSION['erreurM'] = "Le numéro de série est déjà lié à une montre.";
    } else {
        unset($_SESSION['erreurM']);
    }

    // Si des erreurs sont détectées, afficher les messages d'erreur sur la page new.php
    if (isset($_SESSION['erreur']) || isset($_SESSION['erreurP']) || isset($_SESSION['erreurL']) || isset($_SESSION['erreurM'])) {
    } else {
        // Aucune erreur, insérer le module dans la base de données
        $moduleData = $_POST['modulee'];
        $inserer = insertModule($moduleData);
        if ($inserer) {
            $_SESSION['success'] = "Le module a été ajouté avec succès !";
            header("Location: /iwatch/index.php");
            exit();
        } else {
            $_SESSION['erreur'] = "Une erreur est survenue lors de l'insertion du module.";
            header("Location: /iwatch/new.php");
            exit();
        }
    }
} else {
    unset($_SESSION);
}


$pdo = $GLOBALS['pdo'];
$sql = "SELECT id_type_donnee, libelle_type_donnee FROM type_donnee";
$result = $pdo->query($sql);
$typesDonnees = $result->fetchAll(PDO::FETCH_ASSOC);

// Définir le message d'erreur global si des champs sont manquants
$erreurGlobale = isset($_SESSION['erreur']) ? $_SESSION['erreur'] : null;
unset($_SESSION['erreur']);

$etats = getAllEtat();
?>

<div class="global">

    <h1 class="title">Ajouter un nouveau module</h1>
    <?php if (isset($_SESSION['erreurL'])) : ?>
        <div class="error-message">
            <?= $_SESSION['erreurL'] ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['erreurP'])) : ?>
        <div class="error-message">
            <?= $_SESSION['erreurP'] ?>
        </div>
    <?php endif; ?>

    <!-- Vérifier s'il y a un message d'erreur dans la session et l'afficher -->
    <?php if (isset($_SESSION['erreurM'])) : ?>
        <div class="error-message">
            <?= $_SESSION['erreurM'] ?>
        </div>
    <?php endif; ?>

    <!-- Vérifier s'il y a un message d'erreur dans la session et l'afficher -->


    <div class="back">
        <div class="backbutton">
            <a href="/iwatch/index.php" class="boutton">
                <i class="bx bx-arrow-back"></i>
            </a>
        </div>

    </div>

    <div class="ajout">
        <div class="form">
            <form action="/iwatch/new.php" id="form" method="POST" enctype="multipart/form-data">

                <div class="input-container">
                    <div class="divinput">
                        <label for="nom" class="titreinput">Nom du module : </label>
                        <input id="nom" type="text" class="form-control" name="modulee[nom_module]" required />
                    </div>

                </div>

                <div class="input-container">
                    <div class="divinput">
                        <label for="details" class="titreinput">Détails du module : </label>
                        <textarea id="details" class="form-control" name="modulee[details_module]" required></textarea>
                    </div>

                </div>

                <div class="input-container">
                    <div class="divinput">
                        <label for="details" class="titreinput">Etat du montre : </label>
                        <select name="modulee[id_etat]" class="form-control">
                            <?php foreach ($etats as $etat) : ?>
                                <option value="<?= $etat['id_etat'] ?>">
                                    <?= $etat['libelle_etat'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="input-container">
                    <span class="info-icon" data-tooltip="Le numéro de série est unique"><i class='bx bx-info-circle'></i></span>
                </div>

                <div class="input-container">
                    <div class="divinput">
                        <label for="details" class="titreinput">Numero de série : </label>
                        <input type="text" class="form-control" name="modulee[numero_serie_module]" id="details" required>
                    </div>
                </div>
                <?php
                $count = 0;
                foreach ($typesDonnees as $type) {
                ?>
                    <div class="divinput input-container">
                        <label for="modulee[<?= $type['libelle_type_donnee'] ?>_check]" class="titreinput"> <?= $type['libelle_type_donnee'] ?> :</label>
                        <input type="checkbox" name="modulee[<?= $type['libelle_type_donnee'] ?>_check]" id="<?= $type['libelle_type_donnee'] ?>_module" value="on" />
                        <span class="checkmark"></span>
                        <input type="number" class="form-control data-input" name="modulee[<?= $type['libelle_type_donnee'] ?>_value]" placeholder="Entrez des données" />
                        <?php if (isset($_SESSION['erreurL']) && in_array($type['libelle_type_donnee'], $valeursManquantes)) : ?>
                            <p class="error-message"><small><?= $_SESSION['erreurL'] ?></small></p>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($errors['valeur_voulu'][$count++])) : ?>
                        <p class="error-message"><small><?= $errors['valeur_voulu'][$count] ?></small></p>
                    <?php endif; ?>
                <?php
                    $count++;
                }
                ?>
                <input type="submit" name="submit" value="Ajouter" class="buttonsubmit">
            </form>
        </div>
    </div>
</div>
</div>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-bottom.php';
?>