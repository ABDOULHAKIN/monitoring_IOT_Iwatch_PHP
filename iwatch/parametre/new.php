<?php
require $_SERVER['DOCUMENT_ROOT'] . '/managers/module_function.php';
session_start();

if (isset($_POST['submit'])) 
{
    // Protection contre les failles XSS
    $errors = checkFormData($_POST['type_donnee'], ['libelle_type_donnee']);

    // Pour verifier s'ils sont vide avant la soumission 
    if (count($errors) == 0) 
    {
        $inserer = insertParam($_POST['type_donnee']);
        if ($inserer) 
        {
            $_SESSION['success'] = "Le module a été ajouté avec succès !";
            header("Location: /iwatch/parametre/index.php");
        }else {
            echo "Une erreur s'est produite";
        }
    } 
    else 
    {
        $_SESSION['erreur'] = "Le formulaire est incomplet";
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-top.php';
?>


<div class="container py-5">
    <h1>Insérer un paramétre</h1>
    <div class="row mb-4">
        <div class="col-auto">
            <a href="/iwatch/parametre/index.php" class="btn btn-secondary">
            <i class="bx bx-arrow-back"></i>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col col-md-6">
            <form action="/iwatch/parametre/new.php" id="form" method="POST" enctype="multipart/form-data">
                <div class="form-control">
                    <label for="libelle">Libellé du paramétre</label>
                    <input id="libelle" type="text" class="form-control" name="type_donnee[libelle_type_donnee]" />
                    <?php if (isset($errors['libelle_type_donnee'])) : ?>
                        <p><small style="color: red;"><?= $errors['libelle_type_donnee'] ?></small></p>
                    <?php endif; ?>
                    <p id="libelle-error" style="color: red; display: none;">Le libellé ne peut pas commencer par un chiffre ou une ponctuation.</p>
                </div>
                <input type="submit" name="submit" value="Enregistrer" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>

<script>
    let form = document.getElementById('form');
    let inputLibelle = document.getElementById('libelle');
    let libelleError = document.getElementById('libelle-error');

    form.addEventListener('submit', function(event) {
        let libelleValue = inputLibelle.value.trim();

        // Vérifier si le libellé est vide
        if (libelleValue === '') {
            inputLibelle.style.borderColor = 'red'; 
            libelleError.textContent = "Ce champ est obligatoire"; 
            libelleError.style.display = 'block'; 

            // Empêcher la soumission du formulaire
            // Le regex : /^[0-9\p{P}]/
            event.preventDefault();
        } else if (/^[0-9\p{P}]/u.test(libelleValue)) {
            // Vérifier si le libellé commence par un chiffre ou une ponctuation
            inputLibelle.style.borderColor = 'red'; 
            libelleError.textContent = "Le libellé ne peut pas commencer par un chiffre ou une ponctuation"; 
            libelleError.style.display = 'block'; 

            // Empêcher la soumission du formulaire
            event.preventDefault();
        } else {
            // Réinitialiser la bordure et le message d'erreur si le libellé est valide
            inputLibelle.style.borderColor = 'green'; 
            libelleError.style.display = 'none'; 
        }
    });
</script>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/includes/inc-bottom.php';
?>

