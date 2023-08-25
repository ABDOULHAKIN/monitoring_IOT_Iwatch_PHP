<?php

require $_SERVER['DOCUMENT_ROOT'] . '/managers/module_function.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-top.php';


if (isset($_POST['submit'])) 
{
 
    $errors = checkFormData($_POST['type_donnee'], ['libelle_type_donnee']);

    // Pour verifier s'ils sont vide avant la soumission 

    if (count($errors) == 0) 
    {
        $modif = updateParam($_POST['type_donnee']);
        
   
        if ($modif == 1) 
        {
            echo "Le type de donner selectionner a bien été modifier";
            header("Location: /iwatch");
        }
        else
        {
            echo "Une erreur est survenue lors de la modification";
        }
    } 
    else 
    {
        $_SESSION['erreur'] = "Le formulaire est incomplet";
    }
}

// 1. Verification de l'URL 

// Verifiez le paramétre de la fonction "vu qu'on a passé en parametre juste l'ID" : si l'ID de l'article dans l'URL n'est pas vide
// Si c'est vide, on redirige vers la page
if (empty($_GET['id'])) 
{
    header("Location: /iwatch/parametre/index.php");
    die;
}



// 3. Récuperez les contenus du parametre à modifier grâce à son ID
$param = getParamById($_GET['id']);

// 4. Verifiez si le parametre récuperer grâce à son ID est bien présent dans la BDD
if (!$param) 
{
    header("Location: /iwatch/parametre/index.php");
    die;
}


?>

<div class="container py-5">
    <h1>Modifier cette paramétre : <?= $param['libelle_type_donnee'] ?></h1>
    <div class="row mb-4">
        <div class="col-auto">
            <a href="/iwatch/parametre/index.php" class="btn btn-secondary">
            <i class="bx bx-arrow-back"></i>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col col-md-6">
            <form action="/iwatch/parametre/edit.php?id=<?= $_GET['id'] ?>" method="POST">
                
                <!--1. Afficher le titre de l'article avec le contenu à modifier-->
                <div class="form-group">
                    <label for="titre">Libellé du type des données</label>
                    <input id="titre" type="text" class="form-control" name="type_donnee[libelle_type_donnee]" value="<?= $param['libelle_type_donnee'] ?>" />
                    <?php if (isset($errors['libelle_type_donnee'])) : ?>
                        <p><small><?= $errors['libelle_type_donnee'] ?></small></p>
                    <?php endif; ?>
                </div>              
    
                <!--Pour un input hidden, parce que il faut qu'on envoie l'id dans le POST pour pourvoir faire notre requete-->
                <input type="hidden" name="type_donnee[id_type_donnee]" value="<?= $param['id_type_donnee'] ?>">
                <input type="submit" name="submit" value="Envoyer" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-bottom.php';
?>