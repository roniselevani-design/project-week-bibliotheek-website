<?php
session_start(); // Pak de huidige sessie op
session_destroy(); // Vernietig alle sessie-data (uitloggen)
header("Location: index.php"); // Stuur de gebruiker terug naar de homepagina
exit();
?>