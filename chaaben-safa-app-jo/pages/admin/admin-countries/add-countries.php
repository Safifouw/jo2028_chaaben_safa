<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomPays = filter_input(INPUT_POST, 'nomPays', FILTER_SANITIZE_STRING);

    // Vérifiez si le nom du PAYS est vide
    if (empty($nomPays)) {
        $_SESSION['error'] = "Le nom du PAYS ne peut pas être vide.";
        header("Location: add-contrie.php");
        exit();
    }

    try {
        // Vérifiez si le PAYS existe déjà
        $queryCheck = "SELECT  id_pays FROM PAYS WHERE nom_pays = :nomPays";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomPays", $nomPays, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le PAYS existe déjà.";
            header("Location: add-contrie.php");
            exit();
        } else {

            // Requête pour ajouter un PAYS
            $query = "INSERT INTO PAYS (nom_pays) VALUES (:nomPays)";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nomPays", $nomPays, PDO::PARAM_STR);

            // Exécutez la requête
            if ($statement->execute()) {
                $_SESSION['success'] = "Le PAYS a été ajouté avec succès.";
                header("Location: manage-contries.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du PAYS.";
                header("Location: add-contrie.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-contrie.php");
        exit();
    }
}
// Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon.ico" type="image/x-icon">
    <title>Ajouter un PAYS - Jeux Olympiques 2028</title>
    <style>
        /* Ajoutez votre style CSS ici */
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
            <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="../admin-users/manage-users.php">Gestion Utilisateurs</a></li>
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-events/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter un PAYS</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-contrie.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter ce PAYS?')">
            <label for=" nomPays">Nom du PAYS :</label>
            <input type="text" name="nomPays" id="nomPays" required>
            <input type="submit" value="Ajouter le PAYS">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-countries.php">Retour à la gestion des sports</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo.png" alt="logo jeux olympiques 2028">
        </figure>
    </footer>

</body>

</html>