<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du PAYS est fourni dans l'URL
if (!isset($_GET['id_pays'])) {
    $_SESSION['error'] = "ID du PAYS manquant.";
    header("Location: manage-countries.php");
    exit();
} else {
    $id_pays = filter_input(INPUT_GET, 'id_pays', FILTER_VALIDATE_INT);
    // Vérifiez si l'ID du PAYS est un entier valide
    if (!$id_pays && $id_pays !== 0) {
        $_SESSION['error'] = "ID du PAYS invalide.";
        header("Location: manage-countries.php");
        exit();
    } else {
        try {
            // Préparez la requête SQL pour supprimer le PAYS
            $sql = "DELETE FROM PAYS WHERE id_pays = :id_pays";
            // Exécutez la requête SQL avec le paramètre
            $statement = $connexion->prepare($sql);
            $statement->bindParam(':id_pays', $id_pays, PDO::PARAM_INT);
            $statement->execute();
            // Redirigez vers la page précédente après la suppression
            header('Location: manage-countries.php');
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }
}
?>