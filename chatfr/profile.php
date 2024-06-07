<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$password_message = '';

// Récupérer le nombre d'amis
$sql = "SELECT COUNT(*) AS friend_count FROM amis WHERE id_utilisateur1 = ? OR id_utilisateur2 = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$friend_data = $result->fetch_assoc();
$friend_count = $friend_data['friend_count'];
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['biographie'])) {
        $biographie = $_POST['biographie'];
        $sql = "UPDATE utilisateurs SET biographie = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $biographie, $user_id);
        if ($stmt->execute()) {
            $message = "Biographie mise à jour avec succès.";
        } else {
            $message = "Erreur lors de la mise à jour de la biographie.";
        }
        $stmt->close();
    } elseif (isset($_POST['old_password'], $_POST['new_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $sql = "SELECT mot_de_passe FROM utilisateurs WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();
        if (password_verify($old_password, $hashed_password)) {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_hashed_password, $user_id);
            if ($stmt->execute()) {
                $password_message = "Mot de passe mis à jour avec succès.";
            } else {
                $password_message = "Erreur lors de la mise à jour du mot de passe.";
            }
        } else {
            $password_message = "Le mot de passe actuel est incorrect.";
        }
        $stmt->close();
    }
}

// Récupérer les informations de l'utilisateur
$sql = "SELECT pseudo, biographie FROM utilisateurs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Récupérer les posts de l'utilisateur
$sql = "SELECT content, timestamp FROM posts WHERE user_id = ? ORDER BY timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$posts_result = $stmt->get_result();
$posts = [];
while ($row = $posts_result->fetch_assoc()) {
    $posts[] = $row;
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil de <?= htmlspecialchars($user['pseudo']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<header>
    <h1>Profil de <?= htmlspecialchars($user['pseudo']) ?></h1>
    <nav>
        <ul>
            <li><a href="ch.php"><i class="fas fa-home"></i></a></li>
            <li><a href="choisir_ami.php"><i class="fas fa-comments"></i></a></li>
            <li><a href="recherche.php"><i class="fas fa-user-plus"></i></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i></a></li>
            <li><a href="javascript:void(0);" onclick="toggleSettingsMenu()"><i class="fas fa-cog"></i></a></li>
        </ul>
    </nav>
</header>
<div id="settingsMenu" style="display:none;">
    <h2>Paramètres</h2>
    <button onclick="showBiographyForm()">Modifier la biographie</button>
    <button onclick="showPasswordForm()">Changer le mot de passe</button>
</div>

    <div id="biographyForm" style="display:none;">
        <form action="profile.php" method="post">
            <textarea name="biographie" rows="5" cols="50" required><?= htmlspecialchars($user['biographie'] ?? '') ?></textarea><br>
            <input type="submit" value="Mettre à jour">
        </form>
    </div>
    <div id="passwordForm" style="display:none;">
        <form action="profile.php" method="post">
            <input type="password" name="old_password" placeholder="Mot de passe actuel" required><br>
            <input type="password" name="new_password" placeholder="Nouveau mot de passe" required><br>
            <input type="submit" value="Changer le mot de passe">
        </form>
    </div>
</div>
<section>
    <h2>Informations personnelles</h2>
    <p>Pseudo: <?= htmlspecialchars($user['pseudo']) ?></p>
    <p>Biographie: <?= htmlspecialchars($user['biographie']) ?></p>
    <p>Nombre d'amis: <?= $friend_count ?></p>
    <h2>Vos posts</h2>
    <div class="posts">
        <?php if (empty($posts)): ?>
            <p>Vous n'avez pas encore publié de posts.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <p><strong><?= date("d M Y H:i", strtotime($post['timestamp'])) ?></strong></p>
                    <p><?= htmlspecialchars($post['content']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
<script>
function toggleSettingsMenu() {
    var settingsMenu = document.getElementById('settingsMenu');
    settingsMenu.style.display = settingsMenu.style.display === 'block' ? 'none' : 'block';
}

function showBiographyForm() {
    document.getElementById('biographyForm').style.display = 'block';
    document.getElementById('passwordForm').style.display = 'none';
}

function showPasswordForm() {
    document.getElementById('passwordForm').style.display = 'block';
    document.getElementById('biographyForm').style.display = 'none';
}
</script>

</body>
</html>


<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f4f4f4;
    color: #333;
    margin: 0;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

header {
    background-color: #005a43;
    color: #ffffff;
    width: 100%;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    text-align: center;
}

header h1 {
    margin: 0;
    font-size: 24px;
}

nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: center;
}

nav ul li {
    padding: 10px 20px;
}

nav ul li a {
    color: #ffffff;
    text-decoration: none;
    font-size: 16px;
    display: inline-block;
    padding: 8px 16px;
    border-radius: 4px;
    transition: background-color 0.3s, color 0.3s;
}

nav ul li a:hover {
    background-color: #ffffff;
    color: #005a43;
}

section {
    width: 100%;
    max-width: 800px;
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-top: 20px;
}

textarea, input[type="password"], input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

input[type="submit"] {
    background-color: #005a43;
    color: white;
    border: none;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #003d31;
}

.message, .posts .post {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 5px;
    margin-top: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}

.posts .post {
    margin-top: 10px;
}

.posts .post p {
    margin: 5px 0;
}

/* Media queries pour les appareils mobiles */
@media (max-width: 768px) {
    nav ul li a {
        padding: 8px 10px;
    }

    header h1, nav ul li a {
        font-size: 14px;
    }

    section {
        padding: 10px;
    }
}

#settingsMenu button {
    background-color: #00796B; /* Vert plus intense */
    color: white;
    font-size: 16px;
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    outline: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

#settingsMenu button:hover {
    background-color: #004D40; /* Vert plus foncé */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

#settingsMenu button:active {
    background-color: #00695C; /* Vert intermédiaire lors du clic */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) inset;
}

</style>
