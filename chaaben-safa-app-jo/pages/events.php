<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/styles-computer.css">
    <link rel="stylesheet" href="../css/styles-responsive.css">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <title>Calendrier des Épreuves - Jeux Olympiques 2028</title>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="sports.php">Sports</a></li>
                <li><a href="events.php">Calendrier des évènements</a></li>
                <li><a href="results.php">Résultats</a></li>
                <li><a href="login.php">Accès administrateur</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Calendrier des Épreuves</h1>
        <?php
        require_once("../database/database.php");

        try {
            // Requête pour récupérer les épreuves depuis la base de données
            $query = "SELECT nom_epreuve, date_epreuve, heure_epreuve FROM epreuve ORDER BY date_epreuve, heure_epreuve";
            $statement = $connexion->prepare($query);
            $statement->execute();

            // Vérifier s'il y a des résultats
            if ($statement->rowCount() > 0) {
                echo "<table>";
                echo "<tr><th class='color'>Épreuve</th><th class='color'>Date</th><th class='color'>Heure</th></tr>";

                // Afficher les données dans un tableau
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nom_epreuve']) . "</td>";
                    
                    // Conversion de la date au format "jour mois année"
                    $date_formattee = date('d F Y', strtotime($row['date_epreuve'])); // 'd F Y' pour jour mois année (ex: 25 February 2028)
                    echo "<td>" . htmlspecialchars($date_formattee) . "</td>";
                
                    echo "<td>" . htmlspecialchars($row['heure_epreuve']) . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>Aucune épreuve trouvée.</p>";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
        ?>
        <p class="paragraph-link">
            <a class="link-home" href="../index.php">Retour Accueil</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../img/logo-jo.png" alt="logo jeux olympiques 2028">
        </figure>
    </footer>
</body>

</html>