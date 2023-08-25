<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/images/iwatch.png">
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="stylesheet" href="/assets/new.css">
    <link rel="stylesheet" href="/assets/histo.css">
    <title>Accueil</title>
    <!--Boxicons CDN Link : Les icônes-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <!-- Inclure la bibliothèque Chart.js -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="sidebar">
        <div class="logo_content">
            <div class="logo">
                <img src="/images/watch-apple.jpg" alt="" srcset="">
            </div>
            <i class='bx bx-menu' id="btn"></i>
        </div>

        <ul class="nav list">

            <!-- 1. Modules-->
            <li>
                <a href="/iwatch/index.php">
                    <i class='bx bxs-watch-alt'></i>
                    <span class="links_user">Modules</span>
                </a>
                <span class="tooltip">Modules</span>
            </li>

            <!-- 2. notifications-->
            <li>
                <a href="/iwatch/notification.php">
                    <i class='bx bxs-bar-chart-alt-2'></i>
                    <span class="links_user">Les notifications</span>
                </a>
                <span class="tooltip">Les notifications</span>
            </li>

            <!-- 2. fonctionnement-->
            <li>
                <a href="/iwatch/fonctionnement.php">
                    <i class='bx bx-history'></i>
                    <span class="links_user">Fonctionnement</span>
                </a>
                <span class="tooltip">Fonctionnement</span>
            </li>

            <!-- 3. Données aléatoires-->
            <li>
                <a href="/iwatch/automatique.php">
                    <i class='bx bx-plus'></i>
                    <span class="links_user">Données aléatoires</span>
                </a>
                <span class="tooltip">Données</span>
            </li>

            <!--4. Les paramétres -->
            <li>
                <a href="/iwatch/parametre/index.php">
                    <i class='bx bx-folder-plus'></i>
                    <span class="links_user">Les paramétres</span>
                </a>
                <span class="tooltip">Les paramétres</span>
            </li>
        </ul>
        <div class="profile_content">
            <div class="profile">
                <div class="profile_details">
                    <img src="/images/watch-apple.jpg" alt="" srcset="">
                    <div class="name_job">
                        <div class="name">ABDOULHAKIN Mohamed</div>
                    </div>
                </div>

                <button><i class='bx bxs-log-out' id="log_out"><a href="/logout.php"></a></i></button>

            </div>
        </div>
    </div>

    <!--La partie centrale de l'application-->

    <div class="home_content">
        <div class="text">
            <?php
            // Si on aucun message flash
            // Crée une fonction qui permet d'afficher le message flash

            if (isset($_SESSION['flash'])) : ?>
                <?php foreach ($_SESSION['flash'] as $type => $message) : ?>
                    <div class="alert alert-<?= $type ?>">
                        <?= $message ?>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']) ?>
            <?php endif; ?>