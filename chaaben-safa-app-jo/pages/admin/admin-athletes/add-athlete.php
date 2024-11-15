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
    $nomAthlete = filter_input(INPUT_POST, 'nomAthlete', FILTER_SANITIZE_STRING);
    $prenomAthlete = filter_input(INPUT_POST, 'prenomAthlete', FILTER_SANITIZE_STRING);
    $idPays = filter_input(INPUT_POST, 'idPays', FILTER_VALIDATE_INT);
    $idGenre = filter_input(INPUT_POST, 'idGenre', FILTER_VALIDATE_INT);

    // Vérifiez si les champs obligatoires sont vides
    if (empty($nomAthlete) || empty($prenomAthlete) || empty($idPays) || empty($idGenre)) {
        $_SESSION['error'] = "Tous les champs doivent être remplis.";
        header("Location: add-athlete.php");
        exit();
    }

    try {
        // Requête pour ajouter un athlète
        $queryAddAthlete = "INSERT INTO ATHLETE (nom_athlete, prenom_athlete, id_pays, id_genre) VALUES (:nomAthlete, :prenomAthlete, :idPays, :idGenre)";
        $statementAddAthlete = $connexion->prepare($queryAddAthlete);
        $statementAddAthlete->bindParam(":nomAthlete", $nomAthlete, PDO::PARAM_STR);
        $statementAddAthlete->bindParam(":prenomAthlete", $prenomAthlete, PDO::PARAM_STR);
        $statementAddAthlete->bindParam(":idPays", $idPays, PDO::PARAM_INT);
        $statementAddAthlete->bindParam(":idGenre", $idGenre, PDO::PARAM_INT);

        // Exécutez la requête
        if ($statementAddAthlete->execute()) {
            // Récupérer l'ID de l'athlète ajouté
            $lastInsertedId = $connexion->lastInsertId();
            
            // Utilisez $lastInsertedId comme nécessaire (par exemple, affichez-le, enregistrez-le, etc.)
            echo "ID de l'athlète ajouté : " . $lastInsertedId;

            // Redirigez ou effectuez d'autres actions si nécessaire
            header("Location: manage-athletes.php");
            exit();
        } else {
            // Gérez les erreurs d'insertion ici
            $_SESSION['error'] = "Erreur lors de l'ajout de l'athlète.";
            header("Location: add-athlete.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-athlete.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon-jo-2024.ico" type="image/x-icon">
    <title>Ajouter un Athlète - Jeux Olympiques 2024</title>
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
                <li><a href="../admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter un Athlète</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-athlete.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter cet athlète?')">
            <!-- Ajoutez les champs pour le nom, prénom, pays et genre de l'athlète -->
            <label for="nomAthlete">Nom de l'athlète :</label>
            <input type="text" name="nomAthlete" id="nomAthlete" required>

            <label for="prenomAthlete">Prénom de l'athlète :</label>
            <input type="text" name="prenomAthlete" id="prenomAthlete" required>

            <label for="idPays">Pays :</label>
            <select name="idPays" id="idPays" required>
                <?php
                // Récupérer la liste des pays depuis la base de données
                $queryPaysList = "SELECT id_pays, nom_pays FROM PAYS";
                $statementPaysList = $connexion->query($queryPaysList);

                // Afficher chaque pays dans la liste déroulante
                while ($rowPays = $statementPaysList->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $rowPays['id_pays'] . '">' . $rowPays['nom_pays'] . '</option>';
                }
                ?>
            </select>

            <label for="idGenre">Genre :</label>
            <select name="idGenre" id="idGenre" required>
                <?php
                // Récupérer la liste des genres depuis la base de données
                $queryGenreList = "SELECT id_genre, nom_genre FROM GENRE";
                $statementGenreList = $connexion->query($queryGenreList);

                // Afficher chaque genre dans la liste déroulante
                while ($rowGenre = $statementGenreList->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $rowGenre['id_genre'] . '">' . $rowGenre['nom_genre'] . '</option>';
                }
                ?>
            </select>

            <input type="submit" value="Ajouter l'Athlète">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-athletes.php">Retour à la gestion des Athlètes</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
</body>

</html>