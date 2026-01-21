<?php
session_start();
include 'db_connect.php';

// Controleer ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM boeken WHERE ID = ?"); 
    $stmt->execute([$id]);
    $boek = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$boek) {
        header("Location: boeken.php"); // Aangepast naar boeken.php
        exit();
    }
} else {
    header("Location: boeken.php"); // Aangepast naar boeken.php
    exit();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($boek['Naam']); ?></title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/details.css">
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
    </section>

    <div class="page-overlap-wrapper">
        
        <a href="boeken.php" class="back-btn">← Terug naar zoekresultaten</a>

        <div class="three-column-grid">
            
            <div class="col-visual">
                <div class="book-cover">
                    <img src="img/<?php echo htmlspecialchars($boek['Foto']); ?>" alt="<?php echo htmlspecialchars($boek['Naam']); ?>">
                </div>
                <div class="btn-group">
                    <button class="action-btn">Reserveren</button>
                    <button class="action-btn">Favorieten</button>
                </div>
            </div>

            <div class="col-text">
                <h3 class="author"><?php echo htmlspecialchars($boek['Schrijver']); ?></h3>
                <h1 class="title"><?php echo htmlspecialchars($boek['Naam']); ?></h1>
                
                <div class="info-item">
                    <strong>Leeftijd</strong>
                    <p>10+</p> 
                </div>

                <div class="info-item">
                    <strong>Genre</strong>
                    <p><?php echo htmlspecialchars($boek['Genre']); ?></p>
                </div>

                <div class="info-item">
                    <strong>Samenvatting</strong>
                    <p><?php echo htmlspecialchars($boek['Informatie']); ?></p>
                </div>

                <div class="info-item">
                    <strong>Boekenserie</strong>
                    <p>Deel 1</p>
                </div>
            </div>

            <div class="col-location">
                <div class="map-box">
    <div class="plattegrond-grid">
        <?php
        $locatieString = isset($boek['locatie']) ? $boek['locatie'] : ''; 
        $doelX = 0; $doelY = 0;

        if (!empty($locatieString) && strpos($locatieString, ',') !== false) {
            $coords = explode(',', $locatieString);
            if(count($coords) >= 2) {
                $doelX = intval(trim($coords[0])); 
                $doelY = intval(trim($coords[1])); 
            }
        }

        for ($y = 1; $y <= 18; $y++) {
            for ($x = 1; $x <= 18; $x++) {
                $classes = "vakje"; 
                if ($x == $doelX && $y == $doelY) {
                    $classes .= " boek-locatie";
                } elseif (($y % 4 == 3) && ($x != 5 && $x != 14)) { 
                    $classes .= " boekenkast";
                } else {
                    $classes .= " vloer";
                }
                echo "<div class='$classes'></div>";
            }
        }
        ?>
    </div>
</div>
                
                <div class="status-box">
                    <?php 
                        if ($boek['aanwezig'] == 1) {
                            $statusClass = "available";
                            $statusText = "✔ aanwezig";
                        } else {
                            $statusClass = "unavailable";
                            $statusText = "✖ afwezig";
                        }
                    ?>
                    <span class="check <?php echo $statusClass; ?>">
                        <?php echo $statusText; ?>
                    </span>
                    <span class="lib">Bibliotheek Gouda</span>
                </div>
            </div>

        </div> </div> </body>
</html>