
<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID de l'athlète est fourni dans l'URL
if (!isset($_GET['id_athlete'])) {
    $_SESSION['error'] = "ID de l'athlète manquant.";
    header("Location: manage-athletes.php");
    exit();
}

$id_athlete = filter_input(INPUT_GET, 'id_athlete', FILTER_VALIDATE_INT);

// Vérifiez si l'ID de l'athlète est un entier valide
if (!$id_athlete && $id_athlete !== 0) {
    $_SESSION['error'] = "ID de l'athlète invalide.";
    header("Location: manage-athletes.php");
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nom_athlete = filter_input(INPUT_POST, 'nom_athlete', FILTER_SANITIZE_STRING);
    $prenom_athlete = filter_input(INPUT_POST, 'prenom_athlete', FILTER_SANITIZE_STRING);
    $id_genre = filter_input(INPUT_POST, 'id_genre', FILTER_VALIDATE_INT);
    $id_pays = filter_input(INPUT_POST, 'id_pays', FILTER_VALIDATE_INT);

    // Vérifiez si les champs obligatoires ne sont pas vides
    if (empty($nom_athlete) || empty($prenom_athlete) || empty($id_genre)) {
        $_SESSION['error'] = "Nom, prénom et genre sont des champs obligatoires.";
        header("Location: modify-athlete.php?id_athlete=$id_athlete");
        exit();
    }

    try {
        // Requête pour mettre à jour l'athlète
        $query = "UPDATE ATHLETE SET nom_athlete = :nomAthlete, prenom_athlete = :prenomAthlete, id_genre = :idGenre, id_pays = :idPays WHERE id_athlete = :idAthlete";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":nomAthlete", $nom_athlete, PDO::PARAM_STR);
        $statement->bindParam(":prenomAthlete", $prenom_athlete, PDO::PARAM_STR);
        $statement->bindParam(":idGenre", $id_genre, PDO::PARAM_INT);
        $statement->bindParam(":idPays", $id_pays, PDO::PARAM_INT);
        $statement->bindParam(":idAthlete", $id_athlete, PDO::PARAM_INT);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "L'athlète a été modifié avec succès.";
            header("Location: manage-athletes.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification de l'athlète.";
            header("Location: modify-athlete.php?id_athlete=$id_athlete");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-athlete.php?id_athlete=$id_athlete");
        exit();
    }
}

// Récupérez les informations de l'athlète pour affichage dans le formulaire
try {
    $queryAthlete = "SELECT nom_athlete, prenom_athlete, id_genre, id_pays FROM ATHLETE WHERE id_athlete = :idAthlete";
    $statementAthlete = $connexion->prepare($queryAthlete);
    $statementAthlete->bindParam(":idAthlete", $id_athlete, PDO::PARAM_INT);
    $statementAthlete->execute();

    if ($statementAthlete->rowCount() > 0) {
        $athlete = $statementAthlete->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "Athlète non trouvé.";
        header("Location: manage-athletes.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-athletes.php");
    exit();
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
    <title>Modifier un Athlète - Jeux Olympiques 2028</title>
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
        <h1>Modifier un Athlète</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="modify-athlete.php?id_athlete=<?php echo $id_athlete; ?>" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir modifier cet athlète?')">
            <label for="nom_athlete">Nom :</label>
            <input type="text" name="nom_athlete" id="nom_athlete"
                value="<?php echo htmlspecialchars($athlete['nom_athlete']); ?>" required>
            <label for="prenom_athlete">Prénom :</label>
            <input type="text" name="prenom_athlete" id="prenom_athlete"
                value="<?php echo htmlspecialchars($athlete['prenom_athlete']); ?>" required>
            <label for="id_genre">Genre (ID du genre) :</label>
            <input type="number" name="id_genre" id="id_genre"
                value="<?php echo htmlspecialchars($athlete['id_genre']); ?>" required>
            <label for="id_pays">Pays (ID du pays) :</label>
            <input type="number" name="id_pays" id="id_pays"
                value="<?php echo htmlspecialchars($athlete['id_pays']); ?>">
            <input type="submit" value="Modifier l'Athlète">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-athletes.php">Retour à la gestion des Athlètes</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo.png" alt="logo jeux olympiques 2028">
        </figure>
    </footer>
</body>

</html>