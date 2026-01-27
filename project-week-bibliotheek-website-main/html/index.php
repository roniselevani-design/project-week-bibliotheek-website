<?php
session_start(); // Sessie starten
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliotheek Home</title>
    
    <link rel="stylesheet" href="css/style.css"> 
    <link rel="stylesheet" href="css/header.css"> 
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

    <header>
        <div class="logo-container">
            <a href="index.php">
                <div class="logo-placeholder">
                    <img src="fotos/zoetermeer-logo.png" alt="Logo">
                </div>
            </a>
        </div>

 <nav class="nav-links">
    <a href="boeken.php">Boeken</a>
    
    <?php if(isset($_SESSION['naam'])): ?>
        <a href="uitloggen.php">Uitloggen</a> 
    <?php else: ?>
        <a href="loginPagina.html">Inloggen</a>
    <?php endif; ?>
</nav>
    </header>

    <section class="hero-section">
        <div class="decorative-bg"></div>
        
        <div class="search-container">
            
            <?php if (isset($_SESSION['naam'])): ?>
                <h1 style="text-align: center; margin-bottom: 15px; color: #d35400; font-size: 2rem;">
                    Welkom, <?php echo htmlspecialchars($_SESSION['naam']); ?>!
                </h1>
            <?php endif; ?>

            <form action="boeken.php" method="GET">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Zoeken"
                    autocomplete="off"
                >
            </form>
        </div>
    </section>

<div class="blokken-container">
    
    <a href="openingstijden.php" class="link-zonder-opmaak">
        <div class="info-blok">
            <img src="fotos/Klok.png" alt="Klok" class="blok-plaatje klok">
            <div class="blok-label">Openingstijden</div>
        </div>
    </a>

    <a href="" class="link-zonder-opmaak">
        <div class="info-blok">
            <img src="fotos/Quiz.png" alt="Quiz" class="blok-plaatje quiz">
            <div class="blok-label">boeken quiz</div>
        </div>
    </a>

    <a href="boeken.php" class="link-zonder-opmaak">
        <div class="info-blok">
            <img src="fotos/boeken.png" alt="Boekenstapel" class="blok-plaatje boeken">
            <div class="blok-label">Boeken-Catologus</div>
        </div>
    </a>

</div>

</body>
</html>