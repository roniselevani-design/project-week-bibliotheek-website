<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $naam = htmlspecialchars($_POST['naam']);
    $email = htmlspecialchars($_POST['email']);
    $telefoon = htmlspecialchars($_POST['telefoon']);
    $wachtwoord = $_POST['wachtwoord'];

    // 1. Check of e-mail al bestaat
    $checkSql = "SELECT * FROM gebruikers WHERE email = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "Dit e-mailadres is al in gebruik. <a href='registratie.html'>Probeer opnieuw</a>";
        exit();
    }

    // 2. Wachtwoord VEILIG hashen
    $hashed_password = password_hash($wachtwoord, PASSWORD_DEFAULT);

    // 3. Opslaan in database
    // Zorg dat je database tabel ook een kolom 'telefoonnummer' heeft als je die wilt opslaan!
    // Zo niet, haal die regel hieronder weg.
    $sql = "INSERT INTO gebruikers (naam, email, wachtwoord, telefoonnummer) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$naam, $email, $hashed_password, $telefoon])) {
        // Gelukt! Stuur door naar login
        header("Location: loginPagina.html");
        exit();
    } else {
        echo "Er ging iets mis bij het opslaan.";
    }

} else {
    header("Location: registratie.html");
    exit();
}
?>