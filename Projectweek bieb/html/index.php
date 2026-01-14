<?php
include 'db_connect.php';

// 1. BASIS QUERY
$sql = "SELECT * FROM boeken WHERE 1=1";
$params = [];

// 2. FILTER LOGICA (Als er op een filter is geklikt)

// Zoekbalk
if (!empty($_GET['search'])) {
    $sql .= " AND naam LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}

// Genre filter
if (!empty($_GET['genre'])) {
    $sql .= " AND genre = ?";
    $params[] = $_GET['genre'];
}

// Soort filter (E-book / Boek)
if (!empty($_GET['soort'])) {
    $sql .= " AND soort = ?";
    $params[] = $_GET['soort'];
}

// Taal filter
if (!empty($_GET['taal'])) {
    $sql .= " AND taal = ?";
    $params[] = $_GET['taal'];
}

// 3. HAAL DE DATA OP
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$boeken = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bibliotheek Zoeken</title>
        <link rel="stylesheet" href="css/boeken.css

        ">
                <link rel="stylesheet" href="css/header.css">
    </head>
<body>
<header>
    <div class="logo-container">
        <a href="index.html">
            <div class="logo-placeholder"><img src="fotos/zoetermeer-logo.png" alt="Logo Zoetermeer"></div>
        </a>
    </div>

    <nav class="nav-links">
        <a href="index.php">Boeken</a>
        <a href="loginPagina.html">Inloggen</a>
    </nav>
</header>

<section class="hero-section">
    <div class="decorative-bg"></div>
    
    <div class="search-container">
        <form action="index.php" method="GET">
            <input 
                type="text" 
                name="search" 
                class="search-input" 
                placeholder="Zoek op titel..." 
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
            >
        </form>
    </div>
</section>

<div class="main-layout">
    
    <aside class="sidebar">
        <h3>Filter</h3>
        
        <form action="index.php" method="GET" id="filterForm">
            
            <?php if(!empty($_GET['search'])): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
            <?php endif; ?>

            <div class="filter-group">
                <h4>Soort boek</h4>
                <label><input type="radio" name="soort" value="E-book" <?php if(isset($_GET['soort']) && $_GET['soort'] == 'E-book') echo 'checked'; ?>> E-Boeken</label>
                <label><input type="radio" name="soort" value="Hardcover" <?php if(isset($_GET['soort']) && $_GET['soort'] == 'Hardcover') echo 'checked'; ?>> Boeken</label>
            </div>

            <div class="filter-group">
                <h4>Genre</h4>
                <label><input type="radio" name="genre" value="Drama" <?php if(isset($_GET['genre']) && $_GET['genre'] == 'Drama') echo 'checked'; ?>> Drama</label>
                <label><input type="radio" name="genre" value="Horror" <?php if(isset($_GET['genre']) && $_GET['genre'] == 'Horror') echo 'checked'; ?>> Horror</label>
                <label><input type="radio" name="genre" value="Thriller" <?php if(isset($_GET['genre']) && $_GET['genre'] == 'Thriller') echo 'checked'; ?>> Thriller</label>
                <label><input type="radio" name="genre" value="Fantasy" <?php if(isset($_GET['genre']) && $_GET['genre'] == 'Fantasy') echo 'checked'; ?>> Fantasy</label>
                <label><input type="radio" name="genre" value="Avontuur" <?php if(isset($_GET['genre']) && $_GET['genre'] == 'Avontuur') echo 'checked'; ?>> Avontuur</label>
            </div>

            <div class="filter-group">
                <h4>Taal</h4>
                <label><input type="radio" name="taal" value="Nederlands" <?php if(isset($_GET['taal']) && $_GET['taal'] == 'Nederlands') echo 'checked'; ?>> Nederlands</label>
                <label><input type="radio" name="taal" value="Engels" <?php if(isset($_GET['taal']) && $_GET['taal'] == 'Engels') echo 'checked'; ?>> Engels</label>
            </div>

            <a href="index.php" class="reset-btn">Filters wissen</a>
        </form>
    </aside>

    <main class="results">
        <?php 
        if (isset($boeken) && count($boeken) > 0) {
            foreach ($boeken as $boek) { 
        ?>
            <div class="boek-kaart">
                <div class="boek-img">
                    <img src="img/<?php echo $boek['Foto']; ?>" alt="<?php echo $boek['Naam']; ?>">
                </div>
                <div class="boek-info">
                    <div class="titel-row">
                        <h2><?php echo $boek['Naam']; ?></h2>
                        <div class="sterren">
                            <?php 
                            for($i=0; $i<$boek['Sterren']; $i++) { echo "★"; } 
                            for($i=$boek['Sterren']; $i<5; $i++) { echo "<span style='color:#ccc'>★</span>"; }
                            ?>
                        </div>
                    </div>
                    <p class="auteur"><?php echo $boek['Schrijver']; ?></p>
                    <p class="beschrijving">
                        <?php echo $boek['Informatie']; ?>
                    </p>
                    
                    <div class="knoppen">
                        <button class="btn-wit">Opslaan</button>
                        <button class="btn-wit">Reserveren</button>
                    </div>
                </div>
            </div>
            <?php 
            } 
        } else {
            echo "<p>Geen boeken gevonden met deze filters.</p>";
        }
        ?>
    </main>
</div>

<script>
    // Zoek het formulier
    const filterForm = document.getElementById('filterForm');
    
    // Zoek alle radio buttons binnen het formulier
    const radioButtons = filterForm.querySelectorAll('input[type="radio"]');

    // Voeg aan elke knop een actie toe
    radioButtons.forEach(radio => {
        radio.addEventListener('change', () => {
            filterForm.submit(); // Verstuur het formulier zodra er een wijziging is
        });
    });
</script>

</body>
</html>