<?php
session_start(); // Start de sessie om inloggegevens te onthouden
include 'db_connect.php'; // Gebruik jouw bestaande verbinding

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Haal de data uit het formulier (veilig gemaakt met htmlspecialchars)
    $email = htmlspecialchars($_POST['email']);
    $wachtwoord = $_POST['wachtwoord'];

    // 1. Zoek de gebruiker in de database op basis van email
    // Zorg dat je tabel in de database 'gebruikers' heet, of pas dit aan.
    $sql = "SELECT * FROM gebruikers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Controleer of gebruiker bestaat EN of wachtwoord klopt
    // LET OP: Voor een veilig systeem gebruik je password_verify(). 
    // Als je wachtwoorden als platte tekst hebt opgeslagen (niet veilig, maar vaak zo bij schoolprojecten), gebruik dan: if ($user && $user['wachtwoord'] == $wachtwoord)
    
    if ($user && password_verify($wachtwoord, $user['wachtwoord'])) {
        // INLOGGEN GELUKT!
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['naam'] = $user['naam']; // Of hoe de kolom heet
        
        // Stuur door naar de hoofdpagina (index.php)
        header("Location: index.php");
        exit();
    } else {
        // INLOGGEN MISLUKT
        echo "Verkeerd e-mailadres of wachtwoord. <a href='loginPagina.html'>Probeer opnieuw</a>";
    }
} else {
    // Als iemand direct naar deze pagina gaat zonder formulier
    header("Location: loginPagina.html");
    exit();
}
?>