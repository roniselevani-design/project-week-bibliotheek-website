<?php
// db_connect.php
$servername = "localhost";
$username = "root"; // Standaard bij XAMPP
$password = "";     // Standaard leeg bij XAMPP
$dbname = "bibliotheek_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Zet foutmeldingen aan zodat je ziet als er iets mis is
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Verbinding mislukt: " . $e->getMessage();
    die();
}
?>