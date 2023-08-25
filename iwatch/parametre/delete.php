<?php 
require $_SERVER['DOCUMENT_ROOT']. '/managers/module_function.php';


if(!empty($_POST['id_type_donnee']))
{
    $delete = deleteParam($_POST['id_type_donnee']);
    if($delete)
    {
        header("Location: /iwatch/parametre/index.php"); exit; 
    }
    else{
        echo "Une erreur s'est produite lors de la suppression"; 
    }
}
else
{
    header("Location: /iwatch/parametre/index.php"); exit; 
}

require $_SERVER['DOCUMENT_ROOT']. '/includes/inc-top.php';

require $_SERVER['DOCUMENT_ROOT']. '/includes/inc-bottom.php';
?>