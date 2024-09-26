<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_acceuil')]
    public function index(): Response
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
}
session_start(); // Démarrer la session

// Connexion à la base de données
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "afterwork"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Échec de la connexion: " . $conn->connect_error);
}

// Gestion de l'inscription
if (isset($_POST['register'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hachage du mot de passe

    $sql = "INSERT INTO users (first_name, last_name, email, password_hash) VALUES ('$first_name', '$last_name', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Inscription réussie !";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }
}

// Gestion de la connexion
if (isset($_POST['login'])) {
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Vérifiez le mot de passe
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['user_id']; // Enregistrez l'ID utilisateur dans la session
            $_SESSION['first_name'] = $row['first_name']; // Enregistrez le prénom dans la session
            header("Location: afterwork.php"); // Rediriger vers la même page
            exit();
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Aucun utilisateur trouvé avec cet email.";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy(); // Détruire la session
    header("Location: afterwork.php"); // Rediriger vers la page d'accueil
    exit();
}

// Vérifiez si l'utilisateur est connecté
$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : null;

$conn->close();
?>