<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

$login = $_SESSION['login'];
$nom_utilisateur = $_SESSION['prenom_utilisateur'];
$prenom_utilisateur = $_SESSION['nom_utilisateur'];
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
    <title>Liste des Utilisateurs - Jeux Olympiques 2028</title>
    <style>
        /* Ajoutez votre style CSS ici */
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .action-buttons button {
            background-color: #1b1b1b;
            color: #d7c378;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .action-buttons button:hover {
            background-color: #d7c378;
            color: #1b1b1b;
        }
    </style>


</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
            <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="manage-users.php">Gestion Utilisateurs</a></li>
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
        <h1>Gestion Utilisateurs</h1>
        <div class="action-buttons">
            <button onclick="openAddUserForm()">Ajouter un Utilisateur</button>
            <!-- Autres boutons... -->
        </div>
        <!-- Tableau des utilisateurs -->
        <?php
        require_once("../../../database/database.php");

        try {
            // Requête pour récupérer la liste des utilisateurs depuis la base de données
            $query = "SELECT * FROM UTILISATEUR ORDER BY nom_utilisateur, prenom_utilisateur";
            $statement = $connexion->prepare($query);
            $statement->execute();

            // Vérifier s'il y a des résultats
            if ($statement->rowCount() > 0) {
                echo "<table><tr><th>Nom</th><th>Prénom</th><th>Login</th><th>Modifier</th><th>Supprimer</th></tr>";

                // Afficher les données dans un tableau
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    // Assainir les données avant de les afficher
                    echo "<td>" . htmlspecialchars($row['nom_utilisateur']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom_utilisateur']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['login']) . "</td>";
                    echo "<td><button onclick='openModifyUserForm({$row['id_utilisateur']})'>Modifier</button></td>";
                    echo "<td><button onclick='deleteUserConfirmation({$row['id_utilisateur']})'>Supprimer</button></td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>Aucun utilisateur trouvé.</p>";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
        ?>
        <p class="paragraph-link">
            <a class="link-home" href="../admin.php">Accueil administration</a>
        </p>

    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo.png" alt="logo jeux olympiques 2028">
        </figure>
    </footer>
    <script>
        function openAddUserForm() {
            // Ouvrir une fenêtre pop-up avec le formulaire d'ajout d'utilisateur
            window.location.href = 'add-user.php';
        }

        function openModifyUserForm(id_utilisateur) {
            // Ouvrir une fenêtre pop-up avec le formulaire de modification d'utilisateur
            window.location.href = 'modify-user.php?id_utilisateur=' + id_utilisateur;
        }

        function deleteUserConfirmation(id_utilisateur) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur?")) {
                window.location.href = 'delete-user.php?id=' + id_utilisateur;
            }
        }

    </script>
</body>

</html>