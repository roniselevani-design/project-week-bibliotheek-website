<?php
include 'db_connect.php';

// Controleer ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check of je kolom 'id' of 'ID' heet in de DB
    $stmt = $conn->prepare("SELECT * FROM boeken WHERE ID = ?"); 
    $stmt->execute([$id]);
    $boek = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$boek) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
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
            <a href="index.php">Boeken</a>
            <a href="loginPagina.html">Inloggen</a>
        </nav>
    </header>

    <section class="hero-section">
        <div class="decorative-bg"></div>
    </section>

    <div class="page-overlap-wrapper">
        
        <a href="index.php" class="back-btn">← Terug naar zoekresultaten</a>

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
        // 1. Haal de locatie op. LET OP: kleine letter 'l' bij ['locatie']
        $locatieString = isset($boek['locatie']) ? $boek['locatie'] : ''; 
        
        $doelX = 0;
        $doelY = 0;

        // Splits "15,15" op in x=15 en y=15
        if (!empty($locatieString) && strpos($locatieString, ',') !== false) {
            $coords = explode(',', $locatieString);
            // intval zorgt dat het een nummer wordt, trim haalt spaties weg
            if(count($coords) >= 2) {
                $doelX = intval(trim($coords[0])); 
                $doelY = intval(trim($coords[1])); 
            }
        }

        // 2. Maak het 18x18 rooster
        for ($y = 1; $y <= 18; $y++) {
            for ($x = 1; $x <= 18; $x++) {
                
                $classes = "vakje"; 
                
                // A. IS DIT HET BOEK? (Check of x en y overeenkomen met database)
                if ($x == $doelX && $y == $doelY) {
                    $classes .= " boek-locatie";
                } 
                // B. IS DIT EEN BOEKENKAST? (Alleen op specifieke rijen)
                elseif (($y % 4 == 3) && ($x != 5 && $x != 14)) { 
                    $classes .= " boekenkast";
                } 
                // C. ANDERS IS HET VLOER
                else {
                    $classes .= " vloer";
                }

                // Print het vakje
                echo "<div class='$classes'></div>";
            }
        }
        ?>
    </div>
</div>
                
                <div class="status-box">
                    <?php 
                        // Hier controleren we de waarde in de database
                        // 1 = aanwezig (groen), 0 = afwezig (rood)
                        if ($boek['aanwezig'] == 1) {
                            $statusClass = "available";
                            $statusText = "✔ aanwezig";
                        } else {
                            $statusClass = "unavailable";
                            $statusText = "✖ afwezig"; // Of 'uitgeleend'
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