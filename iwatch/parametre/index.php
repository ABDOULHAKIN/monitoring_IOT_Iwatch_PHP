<?php
session_start();

require $_SERVER['DOCUMENT_ROOT'] . '/managers/module_function.php';


$parametres = getAllParametre();

require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-top.php';
?>

<h1 class="titre">Les paramétres présent dans la BDD</h1>
<div class="container">
    <div class="revenir">
        <div>
            <?php if (isset($_SESSION['success'])) : ?>
                <div class="success-message">
                    <?php echo $_SESSION['success']; ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
        </div>
        <div class="buttoncree">
            <a href="/iwatch/parametre/new.php" class="">
                <i class='bx bxs-file-plus'></i>
            </a>
        </div>
    </div>

    <div class="tableau">
        <table class="tableau-style">
            <thead>
                <tr>
                    <th>Nom des parametres</th>
                    <th>Editer</th>
                    <th>Supprimer</th>
                    

                </tr>
            </thead>

            <tbody>
                <?php foreach ($parametres as $parametre) : ?>
                    <tr>
                        <td><?= $parametre['libelle_type_donnee']?></td>
                        <td>
                            <a href="/iwatch/parametre/edit.php?id=<?= $parametre['id_type_donnee'] ?>" class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i> Modifier
                            </a>
                        </td>
                        <td>

                            <form 
                            action="/iwatch/parametre/delete.php" 
                            method="POST"
                            onsubmit="return confirm('Voulez-vous vraiment supprimer le paramètre <?= $parametre['libelle_type_donnee'] ?>');"
                            >
                                <input type="hidden" name="id_type_donnee" value="<?= $parametre['id_type_donnee'] ?>">
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div>
</div>

<?php

require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-bottom.php';
?>