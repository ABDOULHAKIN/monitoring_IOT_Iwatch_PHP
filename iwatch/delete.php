<?php 
require $_SERVER['DOCUMENT_ROOT']. '/managers/function_manager.php';

if(!empty($_POST['id_notification']))
{

    $delete = deleteTrack($_POST['id_notification']);

    if($delete == 1 )
    {
        header("Location: /iwatch"); exit; 
    }
    else{
        echo "Une erreur s'est produite lors de la suppression"; 
    }
}
else
{
    header("Location: /iwatch/index.php"); exit; 
}

require $_SERVER['DOCUMENT_ROOT']. '/includes/inc-top.php';

require $_SERVER['DOCUMENT_ROOT']. '/includes/inc-bottom.php';
?>