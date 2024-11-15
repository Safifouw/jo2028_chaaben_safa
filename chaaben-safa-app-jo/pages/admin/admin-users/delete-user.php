<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'identifiant de l'utilisateur à supprimer est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Identifiant d'utilisateur manquant.";
    header("Location: manage-users.php");
    exit();
}

$idUtilisateur = $_GET['id'];

try {
    // Vérifiez si l'utilisateur existe
    $queryCheck = "SELECT id_utilisateur FROM UTILISATEUR WHERE id_utilisateur = :idUtilisateur";
    $statementCheck = $connexion->prepare($queryCheck);
    $statementCheck->bindParam(":idUtilisateur", $idUtilisateur, PDO::PARAM_INT);
    $statementCheck->execute();

    if ($statementCheck->rowCount() === 0) {
        $_SESSION['error'] = "L'utilisateur n'existe pas.";
        header("Location: manage-users.php");
        exit();
    }

    // Requête pour supprimer un utilisateur
    $queryDelete = "DELETE FROM UTILISATEUR WHERE id_utilisateur = :idUtilisateur";
    $statementDelete = $connexion->prepare($queryDelete);
    $statementDelete->bindParam(":idUtilisateur", $idUtilisateur, PDO::PARAM_INT);

    // Exécutez la requête
    if ($statementDelete->execute()) {
        $_SESSION['success'] = "L'utilisateur a été supprimé avec succès.";
        header("Location: manage-users.php");
        exit();
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur.";
        header("Location: manage-users.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-users.php");
    exit();
}
?>