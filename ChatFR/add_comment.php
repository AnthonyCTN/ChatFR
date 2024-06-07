<?php
// Inclusion du fichier pour la connexion à la base de données.
include 'db.php';

// Démarrage d'une session pour pouvoir utiliser les variables de session.
session_start();

// Vérification si l'utilisateur est connecté, sinon redirection vers la page de connexion.
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.html");
    exit();
}

// Récupération de l'ID de l'utilisateur connecté à partir de la session.
$user_id = $_SESSION['user_id'];

// Traitement du formulaire si la méthode est POST et que les champs nécessaires sont présents.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id'], $_POST['comment_content'])) {
    // Récupération des données du formulaire.
    $post_id = $_POST['post_id'];
    $comment_content = $_POST['comment_content'];

    // Préparation de la requête SQL pour insérer un nouveau commentaire.
    $sql = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Liaison des paramètres et exécution de la requête.
    $stmt->bind_param("iis", $post_id, $user_id, $comment_content);
    $stmt->execute();

    // Fermeture de l'instruction préparée.
    $stmt->close();

    // Redirection vers une autre page après l'insertion du commentaire.
    header("Location: ch.php");
    exit();
}
?>
