<?php
session_start();
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Vérifie si la requête est de type POST
    $pseudo = $_POST["pseudo"]; // Récupère le pseudo du formulaire
    $email = $_POST["email"]; // Récupère l'email du formulaire
    $password = $_POST["password"]; // Récupère le mot de passe du formulaire
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash le mot de passe pour plus de sécurité

    // Prépare une requête SQL pour vérifier si le pseudo existe déjà
    $sql = "SELECT id FROM utilisateurs WHERE pseudo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $pseudo); // Lie le paramètre pseudo à la requête
    $stmt->execute(); // Exécute la requête
    $stmt->store_result(); // Stocke le résultat pour pouvoir vérifier le nombre de lignes

    if ($stmt->num_rows > 0) { // Si le pseudo existe déjà
        // Stocke un message d'erreur dans la session et redirige vers la page d'inscription
        $_SESSION['message'] = "Ce pseudo est déjà utilisé par un autre utilisateur. Veuillez en choisir un autre.";
        header("Location: inscription1.php");
        exit();
    } else {
        // Prépare une requête SQL pour insérer un nouvel utilisateur
        $sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $pseudo, $email, $hashed_password); // Lie les paramètres pseudo, email et mot_de_passe à la requête
        $stmt->execute(); // Exécute la requête

        if ($stmt->affected_rows > 0) { // Si l'insertion est réussie
            // Stocke un message de succès dans la session et redirige vers la page de connexion
            $_SESSION['message'] = "Utilisateur créé avec succès. Vous pouvez vous connecter maintenant.";
            header("Location: connexion1.php");
            exit();
        } else { // Si une erreur survient lors de l'insertion
            // Stocke un message d'erreur dans la session et redirige vers la page d'inscription
            $_SESSION['message'] = "Erreur lors de la création de l'utilisateur : " . $stmt->error;
            header("Location: inscription1.php");
            exit();
        }
    }

    $stmt->close(); // Ferme l'instruction préparée
    $conn->close(); // Ferme la connexion à la base de données
}
?>
