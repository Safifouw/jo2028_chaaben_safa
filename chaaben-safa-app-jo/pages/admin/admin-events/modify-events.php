<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du sport est fourni dans l'URL
if (!isset($_GET['id_epreuve'])) {
    $_SESSION['error'] = "ID de l'épreuve manquant.";
    header("Location: manage-events.php");
    exit();
}

$id_epreuve = filter_input(INPUT_GET, 'id_epreuve', FILTER_VALIDATE_INT);

// Vérifiez si l'ID du sport est un entier valide
if (!$id_epreuve && $id_epreuve !== 0) {
    $_SESSION['error'] = "ID de l'épreuve invalide.";
    header("Location: manage-events.php");
    exit();
}

// Récupérez les informations de l'épreuve pour affichage dans le formulaire
try {
    $queryEpreuve = "SELECT e.id_epreuve, e.nom_epreuve, e.date_epreuve, e.heure_epreuve, l.id_lieu, l.nom_lieu, l.adresse_lieu, s.id_sport, s.nom_sport 
    FROM epreuve e
    INNER JOIN lieu l ON e.id_lieu = l.id_lieu
    INNER JOIN sport s ON e.id_sport = s.id_sport
    WHERE id_epreuve = :idEpreuve";

    $statementEpreuve = $connexion->prepare($queryEpreuve);
    $statementEpreuve->bindParam(":idEpreuve", $id_epreuve, PDO::PARAM_INT);
    $statementEpreuve->execute();

    if ($statementEpreuve->rowCount() > 0) {
        $epreuve = $statementEpreuve->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "Épreuve non trouvée.";
        header("Location: manage-events.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-events.php");
    exit();
}

// Récupérez les lieux et sports pour les listes déroulantes
try {
    $queryLieux = "SELECT id_lieu, nom_lieu, adresse_lieu FROM lieu";
    $statementLieux = $connexion->prepare($queryLieux);
    $statementLieux->execute();
    $lieux = $statementLieux->fetchAll(PDO::FETCH_ASSOC);

    $querySports = "SELECT id_sport, nom_sport FROM sport";
    $statementSports = $connexion->prepare($querySports);
    $statementSports->execute();
    $sports = $statementSports->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-events.php");
    exit();
}

// Initialisez les valeurs des champs avec les données récupérées ou des valeurs par défaut si elles sont absentes
$nom_epreuve = isset($epreuve['nom_epreuve']) ? htmlspecialchars($epreuve['nom_epreuve']) : '';
$date_epreuve = isset($epreuve['date_epreuve']) ? htmlspecialchars($epreuve['date_epreuve']) : '';
$heure_epreuve = isset($epreuve['heure_epreuve']) ? htmlspecialchars($epreuve['heure_epreuve']) : '';
$id_lieu = isset($epreuve['id_lieu']) ? htmlspecialchars($epreuve['id_lieu']) : '';
$adresse_lieu = isset($epreuve['adresse_lieu']) ? htmlspecialchars($epreuve['adresse_lieu']) : '';
$id_sport = isset($epreuve['id_sport']) ? htmlspecialchars($epreuve['id_sport']) : '';

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomEpreuve = filter_input(INPUT_POST, 'nomEpreuve', FILTER_SANITIZE_STRING);
    $dateEpreuve = filter_input(INPUT_POST, 'dateEpreuve', FILTER_SANITIZE_STRING);
    $heureEpreuve = filter_input(INPUT_POST, 'heureEpreuve', FILTER_SANITIZE_STRING);
    $idLieu = filter_input(INPUT_POST, 'nomLieu', FILTER_VALIDATE_INT);
    $adresseLieu = filter_input(INPUT_POST, 'adresseLieu', FILTER_SANITIZE_STRING);
    $idSport = filter_input(INPUT_POST, 'nomSport', FILTER_VALIDATE_INT);

    // Vérifiez si les champs requis sont vides
    if (empty($nomEpreuve) || empty($dateEpreuve) || empty($heureEpreuve) || empty($idLieu) || empty($adresseLieu) || empty($idSport)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: modify-events.php?id_epreuve=$id_epreuve");
        exit();
    }

    try {
        $query = "UPDATE EPREUVE SET nom_epreuve = :nomEpreuve, date_epreuve = :dateEpreuve, heure_epreuve = :heureEpreuve, id_lieu = :idLieu, adresse_lieu = :adresseLieu, id_sport = :idSport WHERE id_epreuve = :idEpreuve";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
        $statement->bindParam(":dateEpreuve", $dateEpreuve, PDO::PARAM_STR);
        $statement->bindParam(":heureEpreuve", $heureEpreuve, PDO::PARAM_STR);
        $statement->bindParam(":idLieu", $idLieu, PDO::PARAM_INT);
        $statement->bindParam(":adresseLieu", $adresseLieu, PDO::PARAM_STR);
        $statement->bindParam(":idSport", $idSport, PDO::PARAM_INT);
        $statement->bindParam(":idEpreuve", $id_epreuve, PDO::PARAM_INT);

        if ($statement->execute()) {
            $_SESSION['success'] = "L'épreuve a été modifiée avec succès.";
            header("Location: manage-events.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification de l'épreuve.";
            header("Location: modify-events.php?id_epreuve=$id_epreuve");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-events.php?id_epreuve=$id_epreuve");
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
    <title>Modifier une Épreuve - Jeux Olympiques 2028</title>
    <script>
        function updateAddress() {
            const lieux = <?php echo json_encode($lieux); ?>;
            const select = document.getElementById("nomLieu");
            const adresseLieuInput = document.getElementById("adresseLieu");
            const selectedId = select.value;

            // Cherche l'adresse correspondante au lieu sélectionné
            for (const lieu of lieux) {
                if (lieu.id_lieu == selectedId) {
                    adresseLieuInput.value = lieu.adresse_lieu;
                    break;
                }
            }
        }
    </script>
</head>

<body>
    <header>
        <nav>
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="../admin-user/manage-users.php">Gestion Utilisateurs</a></li>
                <li><a href="manage-sports.php">Gestion Sports</a></li>
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
        <h1>Modifier une Épreuve</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="modify-events.php?id_epreuve=<?php echo $id_epreuve; ?>" method="post">
            <label for="nomEpreuve">Nom de l'épreuve :</label>
            <input type="text" id="nomEpreuve" name="nomEpreuve" value="<?php echo $nom_epreuve; ?>" required>

            <label for="dateEpreuve">Date de l'épreuve :</label>
            <input type="date" id="dateEpreuve" name="dateEpreuve" value="<?php echo $date_epreuve; ?>" required>

            <label for="heureEpreuve">Heure de l'épreuve :</label>
            <input type="time" id="heureEpreuve" name="heureEpreuve" value="<?php echo $heure_epreuve; ?>" required>

            <label for="nomLieu">Choisissez un lieu :</label>
            <select id="nomLieu" name="nomLieu" onchange="updateAddress()" required>
                <option value="" disabled selected>Sélectionnez un lieu</option>
                <?php foreach ($lieux as $lieu): ?>
                    <option value="<?php echo $lieu['id_lieu']; ?>" <?php echo $lieu['id_lieu'] == $id_lieu ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($lieu['nom_lieu']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="adresseLieu">Adresse du lieu :</label>
            <input type="text" id="adresseLieu" name="adresseLieu" value="<?php echo $adresse_lieu; ?>" required readonly>

            <label for="nomSport">Choisissez un sport :</label>
            <select id="nomSport" name="nomSport" required>
                <option value="" disabled selected>Sélectionnez un sport</option>
                <?php foreach ($sports as $sport): ?>
                    <option value="<?php echo $sport['id_sport']; ?>" <?php echo $sport['id_sport'] == $id_sport ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($sport['nom_sport']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Modifier l'épreuve</button>
        </form>
    </main>
</body>
</html>
