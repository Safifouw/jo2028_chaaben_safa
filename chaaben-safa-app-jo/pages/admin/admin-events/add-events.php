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
    $nomEpreuve = filter_input(INPUT_POST, 'nomEpreuve', FILTER_SANITIZE_STRING);
    $dateEpreuve = filter_input(INPUT_POST, 'dateEpreuve', FILTER_SANITIZE_STRING);
    $idLieu = filter_input(INPUT_POST, 'idLieu', FILTER_VALIDATE_INT);
    $idSport = filter_input(INPUT_POST, 'idSport', FILTER_VALIDATE_INT);

    // Vérifiez si les champs obligatoires sont vides
    if (empty($nomEpreuve) || empty($dateEpreuve) || empty($idLieu) || empty($idSport)) {
        $_SESSION['error'] = "Tous les champs doivent être remplis.";
        header("Location: add-events.php");
        exit();
    }

    try {
        $queryAddEpreuve = "INSERT INTO EPREUVE (nom_epreuve, date_epreuve, id_sport, id_lieu) VALUES (:nomEpreuve, :dateEpreuve, :idSport, :idLieu)";
        $statementAddEpreuve = $connexion->prepare($queryAddEpreuve);
        $statementAddEpreuve->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
        $statementAddEpreuve->bindParam(":dateEpreuve", $dateEpreuve, PDO::PARAM_STR);
        $statementAddEpreuve->bindParam(":idSport", $idSport, PDO::PARAM_INT);
        $statementAddEpreuve->bindParam(":idLieu", $idLieu, PDO::PARAM_INT);
        
        // Exécutez la requête
        if ($statementAddEpreuve->execute()) {
            // Récupérer l'ID de l'épreuve ajoutée
            $lastInsertedId = $connexion->lastInsertId();
            
            // Utilisez $lastInsertedId comme nécessaire (par exemple, affichez-le, enregistrez-le, etc.)
            echo "ID de l'épreuve ajoutée : " . $lastInsertedId;

            // Redirigez ou effectuez d'autres actions si nécessaire
            header("Location: manage-events.php");
            exit();
        } else {
            // Gérez les erreurs d'insertion ici
            $_SESSION['error'] = "Erreur lors de l'ajout de l'épreuve.";
            header("Location: add-events.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-events.php");
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
    <link rel="shortcut icon" href="../../../img/favicon.ico" type="image/x-icon">
    <title>Ajouter une Epreuve - Jeux Olympiques 2028</title>
    
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages Lieus, events, et results -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="../admin-users/manage-users.php">Gestion Utilisateurs</a></li>
                <li><a href="../admin-Lieus/manage-Lieus.php">Gestion Lieus</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-events/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-countries/manage-countries.php">Gestion Lieu</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Sports</a></li>
                <li><a href="../admin-EPREUVEs/manage-EPREUVEs.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter une Epreuve</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-epreuve.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter cette épreuve?')">
            <!-- Ajoutez les champs pour le nom, prénom, Lieu et Sport de l'athlète -->
            <label for="nomEpreuve">Nom de l'épreuve :</label>
            <input type="text" name="nomEpreuve" id="nomEpreuve" required>

            <label for="dateEpreuve">Date de l'épreuve :</label>
            <input type="text" name="dateEpreuve" id="dateEpreuve" required>

            <label for="idLieu">Lieu :</label>
            <select name="idLieu" id="idLieu" required>
                <?php
                // Récupérer la liste des Lieux depuis la base de données
                $queryLieuList = "SELECT id_lieu, nom_lieu FROM Lieu";
                $statementLieuList = $connexion->query($queryLieuList);

                // Afficher chaque lieu dans la liste déroulante
                while ($rowLieu = $statementLieuList->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $rowLieu['id_lieu'] . '">' . $rowLieu['nom_lieu'] . '</option>';
                }
                ?>
            </select>

            <label for="idSport">Sport :</label>
            <select name="idSport" id="idSport" required>
                <?php
                // Récupérer la liste des Sports depuis la base de données
                $querySportList = "SELECT id_sport, nom_sport FROM SPORT";
                $statementSportList = $connexion->query($querySportList);

                // Afficher chaque Sport dans la liste déroulante
                while ($rowSport = $statementSportList->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $rowSport['id_sport'] . '">' . $rowSport['nom_sport'] . '</option>';
                }
                ?>
            </select>

            <input type="submit" value="Ajouter l'épreuve">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-events.php">Retour à la gestion des épreuves</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo.png" alt="logo jeux olympiques 2028">
        </figure>
    </footer>
</body>

</html>