<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'identifiant de l'athlète à supprimer est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Identifiant d'athlète manquant.";
    header("Location: manage-athletes.php");
    exit();
}

$idAthlete = $_GET['id'];

try {
    // Vérifiez si l'athlète existe
    $queryCheck = "SELECT id_athlete FROM ATHLETE WHERE id_athlete = :idAthlete";
    $statementCheck = $connexion->prepare($queryCheck);
    $statementCheck->bindParam(":idAthlete", $idAthlete, PDO::PARAM_INT);
    $statementCheck->execute();

    if ($statementCheck->rowCount() === 0) {
        $_SESSION['error'] = "L'athlète n'existe pas.";
        header("Location: manage-athletes.php");
        exit();
    }

    // Requête pour supprimer un athlète
    $queryDelete = "DELETE FROM ATHLETE WHERE id_athlete = :idAthlete";
    $statementDelete = $connexion->prepare($queryDelete);
    $statementDelete->bindParam(":idAthlete", $idAthlete, PDO::PARAM_INT);

    // Exécutez la requête
    if ($statementDelete->execute()) {
        $_SESSION['success'] = "L'athlète a été supprimé avec succès.";
        header("Location: manage-athletes.php");
        exit();
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de l'athlète.";
        header("Location: manage-athletes.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-athletes.php");
    exit();
}
?>