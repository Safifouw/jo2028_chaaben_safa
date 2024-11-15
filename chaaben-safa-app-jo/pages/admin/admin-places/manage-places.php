<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon.ico" type="image/x-icon">
    <title>Liste des Lieux - Jeux Olympiques 2028</title>
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
        <h1>Gestion Lieux</h1>
        <div class="action-buttons">
            <button onclick="openAddUserForm()">Ajouter un lieu</button>
            <!-- Autres boutons... -->
        </div>
        <!-- Tableau des lieux -->
        <table>
            <tr>
                <th>Nom lieu</th>
                <th>Adresse</th>
                <th>Code postal</th>
                <th>Ville</th>
                <th>Modifier</th>
                <th>Supprimer</th>
            </tr>
            <?php
            session_start();
            require_once("../../../database/database.php");

            try {
                $query = "SELECT * FROM lieu";
                $statement = $connexion->query($query);

                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row['nom_lieu'] . "</td>";
                    echo "<td>" . $row['adresse_lieu'] . "</td>";
                    echo "<td>" . $row['cp_lieu'] . "</td>";
                    echo "<td>" . $row['ville_lieu'] . "</td>";
                    echo "<td><button onclick='openModifyUserForm(" . $row['id_lieu'] . ")'>Modifier</button></td>";
                    echo "<td><button onclick='deleteUserConfirmation(" . $row['id_lieu'] . ")'>Supprimer</button></td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo '<p style="color: red;">Erreur de base de données : ' . $e->getMessage() . '</p>';
            }
            ?>
        </table>
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
            alert('Ajouter un lieu');
            window.location.href = 'add-place.php';
        }

        function openModifyUserForm(id_lieu) {
            alert('ID du lieu à modifier : ' + id_lieu);
            window.location.href = 'modify-place.php?id_lieu=' + id_lieu;
        }

        function deleteUserConfirmation(id_lieu) {
            if (confirm("Êtes-vous sûr(e) de vouloir supprimer ce lieu?")) {
                alert('Supprimer un lieu : ' + id_lieu);
                window.location.href = 'delete-place.php?id_lieu=' + id_lieu;
            }
        }
    </script>
</body>

</html>