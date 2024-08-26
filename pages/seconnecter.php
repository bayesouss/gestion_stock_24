<?php
session_start();

// Inclusion du fichier de configuration pour la connexion à la base de données
require_once "../configuration/conect.php";

// Récupère les valeurs de login et de mot de passe depuis le formulaire POST
$login = isset($_POST['login']) ? $_POST['login'] : "";
$pwd = isset($_POST['pwd']) ? $_POST['pwd'] : "";

// Affiche les valeurs de login et de mot de passe pour le débogage
echo "Login: " . $login . "<br>";
echo "Password: " . $pwd . "<br>";

// Vérifie que les champs login et mot de passe ne sont pas vides
if (!empty($login) && !empty($pwd)) {
    // Prépare la requête SQL pour vérifier les informations de l'utilisateur
    $requete = "SELECT * FROM utilisateur WHERE login = :login AND pwd = :pwd";
    $requete_preparee = $bdd->prepare($requete);
    // Exécute la requête en liant les paramètres :login et :pwd
    $requete_preparee->execute(array(':login' => $login, ':pwd' => sha1($pwd)));

    // Récupère l'utilisateur correspondant aux informations fournies
    $user = $requete_preparee->fetch(PDO::FETCH_ASSOC);

    // Vérifie si un utilisateur a été trouvé
    if ($user) {
        // Affiche les informations de l'utilisateur pour le débogage
        echo "User found: <pre>";
        print_r($user);
        echo "</pre>";

        // Vérifie si le compte de l'utilisateur est activé
        if ($user['etat'] == 1) {
            // Stocke les informations de l'utilisateur dans la session
            $_SESSION['user'] = $user;
            // Redirige vers la page d'accueil
            header("Location: ../index.php?id=".$_SESSION['id']);
            exit();
        } else {
            // Stocke un message d'erreur dans la session si le compte est désactivé
            $_SESSION['erreurLogin'] = "<strong>Erreur!!</strong> Votre compte est désactivé.<br> Veuillez contacter l'administrateur";
            // Redirige vers la page de connexion
            header('Location: login.php');
            exit();
        }
    } else {
        // Stocke un message d'erreur dans la session si le login ou le mot de passe est incorrect
        $_SESSION['erreurLogin'] = "<strong>Erreur!!</strong> Login ou mot de passe incorrect!";
        // Redirige vers la page de connexion
        header('Location: login.php');
        exit();
    }
} else {
    // Stocke un message d'erreur dans la session si les champs ne sont pas remplis
    $_SESSION['erreurLogin'] = "<strong>Erreur!!</strong> Veuillez remplir tous les champs.";
    // Redirige vers la page de connexion
    header('Location: login.php');
    exit();
}
?>
