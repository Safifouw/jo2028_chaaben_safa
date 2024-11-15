<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID de l'utilisateur est fourni dans l'URL
if (!isset($_GET['id_utilisateur'])) {
    $_SESSION['error'] = "ID de l'utilisateur manquant.";
    header("Location: manage-users.php");
    exit();
}

$id_utilisateur = filter_input(INPUT_GET, 'id_utilisateur', FILTER_VALIDATE_INT);

// Vérifiez si l'ID de l'utilisateur est un entier valide
if (!$id_utilisateur && $id_utilisateur !== 0) {
    $_SESSION['error'] = "ID de l'utilisateur invalide.";
    header("Location: manage-users.php");
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nom_utilisateur = filter_input(INPUT_POST, 'nom_utilisateur', FILTER_SANITIZE_STRING);
    $prenom_utilisateur = filter_input(INPUT_POST, 'prenom_utilisateur', FILTER_SANITIZE_STRING);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);

    // Vérifiez si les champs obligatoires ne sont pas vides
    if (empty($nom_utilisateur) || empty($prenom_utilisateur) || empty($login)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: modify-user.php?id_utilisateur=$id_utilisateur");
        exit();
    }

    try {
        // Vérifiez si le login existe déjà pour un autre utilisateur
        $queryCheck = "SELECT id_utilisateur FROM UTILISATEUR WHERE login = :login AND id_utilisateur <> :idUtilisateur";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":login", $login, PDO::PARAM_STR);
        $statementCheck->bindParam(":idUtilisateur", $id_utilisateur, PDO::PARAM_INT);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le login existe déjà pour un autre utilisateur.";
            header("Location: modify-user.php?id_utilisateur=$id_utilisateur");
            exit();
        }

        // Requête pour mettre à jour l'utilisateur
        $query = "UPDATE UTILISATEUR SET nom_utilisateur = :nomUtilisateur, prenom_utilisateur = :prenomUtilisateur, login = :login WHERE id_utilisateur = :idUtilisateur";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":nomUtilisateur", $nom_utilisateur, PDO::PARAM_STR);
        $statement->bindParam(":prenomUtilisateur", $prenom_utilisateur, PDO::PARAM_STR);
        $statement->bindParam(":login", $login, PDO::PARAM_STR);
        $statement->bindParam(":idUtilisateur", $id_utilisateur, PDO::PARAM_INT);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "L'utilisateur a été modifié avec succès.";
            header("Location: manage-users.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification de l'utilisateur.";
            header("Location: modify-user.php?id_utilisateur=$id_utilisateur");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-user.php?id_utilisateur=$id_utilisateur");
        exit();
    }
}

// Récupérez les informations de l'utilisateur pour affichage dans le formulaire
try {
    $queryUser = "SELECT nom_utilisateur, prenom_utilisateur, login FROM UTILISATEUR WHERE id_utilisateur = :idUtilisateur";
    $statementUser = $connexion->prepare($queryUser);
    $statementUser->bindParam(":idUtilisateur", $id_utilisateur, PDO::PARAM_INT);
    $statementUser->execute();

    if ($statementUser->rowCount() > 0) {
        $user = $statementUser->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "Utilisateur non trouvé.";
        header("Location: manage-users.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-users.php");
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
    <title>Modifier un Utilisateur - Jeux Olympiques 2028</title>
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
                <li><a href="manage-users.php">Gestion Utilisateurs</a></li>
                <li><a href="../admin-sports/modify-sport.php">Gestion Sports</a></li>
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
        <h1>Modifier un Utilisateur 
        </h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="modify-user.php?id_utilisateur=<?php echo $id_utilisateur; ?>" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir modifier cet utilisateur?')">
            <label for="nom_utilisateur">Nom :</label>
            <input type="text" name="nom_utilisateur" id="nom_utilisateur"
                value="<?php echo htmlspecialchars($user['nom_utilisateur']); ?>" required>
            <label for="prenom_utilisateur">Prénom :</label>
            <input type="text" name="prenom_utilisateur" id="prenom_utilisateur"
                value="<?php echo htmlspecialchars($user['prenom_utilisateur']); ?>" required>
            <label for="login">Login :</label>
            <input type="text" name="login" id="login" value="<?php echo htmlspecialchars($user['login']); ?>" required>
            <input type="submit" value="Modifier l'Utilisateur">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-users.php">Retour à la gestion des utilisateurs</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="logo-jo.png" alt="logo jeux olympiques 2028">
        </figure>
    </footer>
</body>

</html>