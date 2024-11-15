<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon.ico" type="image/x-icon">
    <title>Ajouter un lieu - Jeux Olympiques 2028</title>
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
                <li><a href="manage-places.php">Gestion Lieux</a></li>
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
        <h1>Ajouter un lieu</h1>
        <?php
        session_start();
        require_once("../../../database/database.php");

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nomLieu = filter_input(INPUT_POST, 'nomLieu', FILTER_DEFAULT, FILTER_FLAG_STRIP_HIGH);
            $adresseLieu = filter_input(INPUT_POST, 'adresseLieu', FILTER_DEFAULT, FILTER_FLAG_STRIP_HIGH);
            $codePostal = filter_input(INPUT_POST, 'codePostal', FILTER_VALIDATE_INT);
            $villeLieu = filter_input(INPUT_POST, 'villeLieu', FILTER_DEFAULT, FILTER_FLAG_STRIP_HIGH);

            if (empty($nomLieu) || empty($adresseLieu) || empty($codePostal) || empty($villeLieu)) {
                echo '<p style="color: red;">Tous les champs doivent être remplis.</p>';
            } else {
                try {
                    $query = "INSERT INTO lieu (nom_lieu, adresse_lieu, cp_lieu, ville_lieu) VALUES (:nomLieu, :adresseLieu, :codePostal, :villeLieu)";
                    $statement = $connexion->prepare($query);
                    $statement->bindParam(":nomLieu", $nomLieu, PDO::PARAM_STR);
                    $statement->bindParam(":adresseLieu", $adresseLieu, PDO::PARAM_STR);
                    $statement->bindParam(":codePostal", $codePostal, PDO::PARAM_INT);
                    $statement->bindParam(":villeLieu", $villeLieu, PDO::PARAM_STR);

                    if ($statement->execute()) {
                        echo '<p style="color: green;">Le lieu a été ajouté avec succès.</p>';
                    } else {
                        echo '<p style="color: red;">Erreur lors de l\'ajout du lieu.</p>';
                    }
                } catch (PDOException $e) {
                    echo '<p style="color: red;">Erreur de base de données : ' . $e->getMessage() . '</p>';
                }
            }
        }
        ?>

        <form action="add-place.php" method="post">
            <label for="nomLieu">Nom lieu:</label>
            <input type="text" name="nomLieu" id="nomLieu" required>

            <label for="adresseLieu">Adresse:</label>
            <input type="text" name="adresseLieu" id="adresseLieu" required>

            <label for="codePostal">Code postal:</label>
            <input type="number" name="codePostal" id="codePostal" required>

            <label for="villeLieu">Ville:</label>
            <input type="text" name="villeLieu" id="villeLieu" required>

            <input type="submit" value="Ajouter un lieu">
        </form>

        <p class="paragraph-link">
            <a class="link-home" href="manage-places.php">Retour à la gestion des Lieux</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="logo-jo.png" alt="logo jeux olympiques 2028">
        </figure>
    </footer>
</body>

</html>