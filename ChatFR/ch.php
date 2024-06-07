<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_content'])) {
    $post_content = $_POST['post_content'];

    $sql = "INSERT INTO posts (user_id, content) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $post_content);
    $stmt->execute();
    $stmt->close();

    header("Location: ch.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - ChatFr</title>
    <link rel="stylesheet" href="styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-messaging.js"></script>
</head>
<body>
    <header>
        <h1>ChatFr</h1>
        <nav>
            <ul>
                <li><a href="choisir_ami.php"><i class="fas fa-envelope"></i></a></li>
                <li><a href="recherche.php"><i class="fas fa-user-plus"></i></a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i></a></li>
                <li><a href="recherche.php"><i class="fas fa-search"></i></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i></a></li>
            </ul>
        </nav>
    </header>
    <section>
        <div class="intro">
            <h2>Présentation de ChatFr</h2>
            <p>ChatFr est votre application de messagerie ultime pour rester connecté avec vos amis et votre famille. Envoyez des messages instantanément, ajoutez de nouveaux amis, et bien plus encore !</p>
        </div>
        <div class="post-form">
            <h2>Créer un nouveau post</h2>
            <form action="ch.php" method="post">
                <textarea name="post_content" rows="4" cols="50" placeholder="Quoi de neuf?" required></textarea><br>
                <input type="submit" value="Publier">
            </form>
        </div>
        <div class="posts">
            <h2>Posts récents</h2>
            <?php
            // Inclure la logique pour afficher les posts
            include 'display_posts.php';
            ?>
        </div>
    </section>
    <script>
        var firebaseConfig = {
            apiKey: "YOUR_API_KEY",
            authDomain: "YOUR_AUTH_DOMAIN",
            projectId: "YOUR_PROJECT_ID",
            storageBucket: "YOUR_STORAGE_BUCKET",
            messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
            appId: "YOUR_APP_ID"
        };
                firebase.initializeApp(firebaseConfig);

        const messaging = firebase.messaging();

        messaging.getToken({ vapidKey: 'YOUR_PUBLIC_VAPID_KEY' }).then((currentToken) => {
            if (currentToken) {
                console.log('Token received: ', currentToken);
                // Send the token to your server to store it
                $.post('store_fcm_token.php', {token: currentToken});
            } else {
                console.log('No registration token available. Request permission to generate one.');
            }
        }).catch((err) => {
            console.log('An error occurred while retrieving token. ', err);
        });

        messaging.onMessage((payload) => {
            console.log('Message received. ', payload);
            const notificationTitle = payload.notification.title;
            const notificationOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon
            };

            if (Notification.permission === 'granted') {
                var notification = new Notification(notificationTitle, notificationOptions);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            function checkNewFriends() {
                fetch('check_new_friends.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.new_friends > 0) {
                            alert(`Vous avez ${data.new_friends} nouveau(x) ami(s)!`);
                        }
                    })
                    .catch(error => console.error('Erreur lors de la vérification des nouveaux amis:', error));
            }
        
            // Vérifiez les nouveaux amis toutes les 30 secondes
            setInterval(checkNewFriends, 30000);
        });
    </script>
</body>
</html>

<style>
   body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
}

header {
    background: linear-gradient(to right, #008000, #333333);
    color: #ffffff;
    width: 100%;
    padding: 20px 0;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

header h1 {
    margin: 0;
    font-size: 2em;
}

nav ul {
    list-style: none;
    padding: 0;
    margin: 20px 0 0;
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
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: linear-gradient(to right, #008000, #333333);
    border-radius: 50%;
    transition: background-color 0.3s, color 0.3s;
}

nav ul li a:hover {
    background-color: #006600;
    color: #ffffff;
}

section {
    padding: 20px;
    width: 100%;
    max-width: 800px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin: 20px 0;
    text-align: center;
    width: 100%;
}

footer {
    background-color: #333;
    color: #ffffff;
    text-align: center;
    padding: 10px 0;
    width: 100%;
    position: fixed;
    bottom: 0;
}

@media (max-width: 768px) {
    header, footer {
        padding: 15px;
    }

    nav ul li {
        margin: 5px;
    }

    nav ul li a {
        padding: 8px 16px;
    }

    section {
        padding: 15px;
    }
}

@media (max-width: 480px) {
    header h1 {
        font-size: 1.5em;
    }

    nav ul li {
        margin: 3px;
    }

    nav ul li a {
        padding: 6px 12px;
        font-size: 1em;
    }

    section {
        padding: 10px;
    }

    .card {
        padding: 15px;
    }
}

</style>
