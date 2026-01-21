<?php
session_start(); // Sessie starten voor de naam
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Openingstijden</title>
    <link rel="stylesheet" href="css/header.css"> 
    <link rel="stylesheet" href="css/openingstijden.css">
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

    <div class="openingstijden-container">
      <div class="titel-balk">
        <h3>openingstijden</h3>
      </div>
      
      <div class="tijden-lijst">
        <div class="dag-rij">
          <span class="dag">Maandag</span>
          <span class="tijd">12:00 – 18:00</span>
        </div>
        <div class="dag-rij">
          <span class="dag">Dinsdag</span>
          <span class="tijd">10:00 – 18:00</span>
        </div>
        <div class="dag-rij">
          <span class="dag">Woensdag</span>
          <span class="tijd">10:00 – 18:00</span>
        </div>
        <div class="dag-rij">
          <span class="dag">Donderdag</span>
          <span class="tijd">10:00 – 21:00</span>
        </div>
        <div class="dag-rij">
          <span class="dag">Vrijdag</span>
          <span class="tijd">10:00 – 18:00</span>
        </div>
        <div class="dag-rij">
          <span class="dag">Zaterdag</span>
          <span class="tijd">10:00 – 17:00</span>
        </div>
        <div class="dag-rij">
          <span class="dag">Zondag</span>
          <span class="tijd">12:00 – 17:00</span>
        </div>
      </div>
    </div>
    
</body>
</html>