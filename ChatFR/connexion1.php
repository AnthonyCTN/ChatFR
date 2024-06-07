<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h2>Connexion</h2>
    <?php
    session_start();
    if (isset($_SESSION['message'])): ?>
        <div class="message"><?= $_SESSION['message']; ?></div>
        <?php unset($_SESSION['message']); // Effacer le message après l'affichage ?>
    <?php endif; ?>
    <form action="connexion.php" method="post">
        Pseudo: <input type="text" name="pseudo" required><br>
        Mot de passe: <input type="password" name="password" required><br>
        <input type="submit" value="Se connecter">
    </form>
    <p>Vous n'avez pas de compte ? <a href="inscription1.php">Inscrivez-vous ici</a></p>
</body>
</html>

<style>
          body {
    font-family: 'Arial', sans-serif;
    background-color: #f0f0f0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    flex-direction: column;
}

form {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    width: 300px;
    margin-bottom: 20px;
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    margin-bottom: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

input[type="submit"] {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    background-color: #007BFF;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

input[type="submit"]:hover {
    background-color: #0056b3;
}

.message {
    text-align: center;
    color: #d8000c;
    background-color: #ffbaba;
    border: 1px solid #d8000c;
    padding: 10px;
    border-radius: 5px;
    width: 300px;
    margin-bottom: 20px;
}

p {
    text-align: center;
    color: #333;
}

p a {
    color: #007BFF;
    text-decoration: none;
}

p a:hover {
    text-decoration: underline;
}

</style>