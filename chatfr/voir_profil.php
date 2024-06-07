<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    header("Location: connexion.html");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$user_id = $_GET['user_id'];

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <h1>Profil de <?= htmlspecialchars($user['pseudo']) ?></h1>
        <nav>
            <ul>
                <li><a href="ch.php">Accueil</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="conversation.php">Conversations</a></li>
                <li><a href="ajouter_ami.php">Ajouter un ami</a></li>
                <li><a href="recherche.php">Recherche</a></li>
                <li><a href="profile.php">Profil</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <section>
        <h2>Informations personnelles</h2>
        <p>Pseudo: <?= htmlspecialchars($user['pseudo']) ?></p>
        <p>Biographie: <?= htmlspecialchars($user['biographie']) ?></p>
        <button id="add-friend" data-ami-id="<?= $user_id ?>">Ajouter comme ami</button>

        <h2>Posts</h2>
        <?php if (!empty($posts)): ?>
            <ul>
                <?php foreach ($posts as $post): ?>
                    <li>
                        <p><?= htmlspecialchars($post['content']) ?></p>
                        <em><?= date('Y-m-d H:i', strtotime($post['timestamp'])) ?></em>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun post trouvé.</p>
        <?php endif; ?>
    </section>
   
    <script>
        $(document).ready(function() {
            $('#add-friend').click(function() {
                const ami_id = $(this).data('ami-id');

                $.ajax({
                    url: 'ajouter_ami_ajax.php',
                    type: 'POST',
                    data: { ami_id: ami_id },
                    success: function(response) {
                        const data = JSON.parse(response);
                        alert(data.message);
                    },
                    error: function() {
                        alert('Erreur lors de l\'ajout de l\'ami.');
                    }
                });
            });
        });
    </script>
</body>
</html>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        color: #333;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    header {
        background-color: #0073e6;
        color: white;
        width: 100%;
        padding: 10px 20px;
        text-align: center;
    }

    nav ul {
        list-style-type: none;
        padding: 0;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }

    nav ul li {
        margin: 10px;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        font-size: 16px;
    }

    nav ul li a:hover {
        text-decoration: underline;
    }

    section {
        width: 100%;
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        text-align: center;
        background: white;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
    }

    h2 {
        color: #0056b3;
        margin-top: 20px;
    }

    ul {
        list-style-type: none;
        padding: 0;
    }

    ul li {
        background-color: ;
        margin: 10px 0;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    ul li p {
        margin: 0;
    }

    ul li em {
        display: block;
        margin-top: 5px;
        color: #666;
        font-size: 0.9em;
    }

    #add-friend {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s;
    }

    #add-friend:hover {
        background-color: #45a049;
    }

    .message {
        color: #f44336;
        margin-top: 20px;
    }

    footer {
        width: 100%;
        background-color: #333;
        color: white;
        text-align: center;
        padding: 10px 0;
        position: fixed;
        bottom: 0;
    }

    /* Media queries pour les appareils mobiles */
    @media (max-width: 768px) {
        header {
            padding: 15px 20px;
        }

        nav ul li {
            margin: 5px;
        }

        nav ul li a {
            font-size: 14px;
        }

        section {
            margin: 15px;
            padding: 15px;
        }

        ul li {
            font-size: 14px;
        }

        footer {
            padding: 15px 0;
        }
    }

    @media (max-width: 480px) {
        header {
            padding: 10px 15px;
        }

        nav ul li {
            margin: 3px;
        }

        nav ul li a {
            font-size: 12px;
        }

        section {
            margin: 10px;
            padding: 10px;
        }

        ul li {
            font-size: 12px;
        }

        footer {
            padding: 10px 0;
        }
    }
</style>
