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
    <footer>
        &copy; 2024 ChatFr. Tous droits réservés.
    </footer>
    <script>
        // Your Firebase configuration (replace with your own details)
        var firebaseConfig = {
            apiKey: "YOUR_API_KEY",
            authDomain: "YOUR_AUTH_DOMAIN",
            projectId: "YOUR_PROJECT_ID",
            storageBucket: "YOUR_STORAGE_BUCKET",
            messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
            appId: "YOUR_APP_ID"
        };
        // Initialize Firebase
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
            // Customize notification here
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
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header {
            background: linear-gradient(to right, #00796B, #004D40);
            color: #ffffff;
            width: 100%;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
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
        }

        nav ul li {
            margin: 0 10px;
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
            background: #00796B;
            border-radius: 50%;
            transition: background-color 0.3s, color 0.3s;
        }

        nav ul li a:hover {
            background-color: #004D40;
        }

        section {
            padding: 20px;
            width: 100%;
            max-width: 800px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .intro, .post-form, .posts {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
            width: 100%;
        }

        textarea, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #00796B;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #004D40;
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

            section {
                padding: 10px;
            }

            .intro, .post-form, .posts {
                padding: 15px;
            }
        }

        .posts {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
    margin-top: 20px;
}

.post-card {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease-in-out;
}

.post-card:hover {
    transform: translateY(-5px);
}

.post-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.post-content {
    padding: 15px;
}

.post-actions {
    display: flex;
    justify-content: space-between;
    padding: 10px 15px 15px;
    border-top: 1px solid #eee;
}

.post-actions i {
    cursor: pointer;
}

@media (max-width: 768px) {
    .posts {
        grid-template-columns: 1fr;
    }
}

    </style>