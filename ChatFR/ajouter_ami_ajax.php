<?php
// Inclut le fichier responsable de la connexion à la base de données.
include 'db.php';

// Démarre une session pour accéder aux variables de session.
session_start();

// Vérifie si l'utilisateur est connecté, sinon renvoie un message d'erreur.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['message' => 'Vous devez être connecté pour ajouter des amis.']);
    exit();
}

// Récupère l'ID de l'utilisateur connecté et l'ID de l'ami potentiel à partir du POST.
$user_id = $_SESSION['user_id'];
$ami_id = $_POST['ami_id'];

// Prépare une requête SQL pour vérifier si une relation d'amitié existe déjà entre les deux utilisateurs.
$sql = "SELECT * FROM amis WHERE (id_utilisateur1 = ? AND id_utilisateur2 = ?) OR (id_utilisateur1 = ? AND id_utilisateur2 = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $ami_id, $ami_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Si aucun enregistrement n'est trouvé, ils ne sont pas encore amis.
if ($result->num_rows == 0) {
    // Insère une nouvelle relation d'amitié dans la base de données.
    $sql = "INSERT INTO amis (id_utilisateur1, id_utilisateur2) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $ami_id);
    if ($stmt->execute()) {
        // Si l'insertion réussit, envoie un message de succès.
        echo json_encode(['message' => 'Ami ajouté avec succès!']);
    } else {
        // En cas d'échec de l'insertion, envoie un message d'erreur.
        echo json_encode(['message' => 'Erreur lors de l\'ajout de l\'ami.']);
    }
} else {
    // Si une relation d'amitié existe déjà, informe l'utilisateur qu'ils sont déjà amis.
    echo json_encode(['message' => 'Vous êtes déjà amis!']);
}

// Ferme l'instruction préparée et la connexion à la base de données.
$stmt->close();
$conn->close();
?>
