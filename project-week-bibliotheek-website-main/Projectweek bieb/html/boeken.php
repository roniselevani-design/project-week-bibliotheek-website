<?php
session_start(); // Sessie starten
include 'db_connect.php';

// 1. SEARCH VARIABELE DEFINIËREN
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// 2. BASIS QUERY
$sql = "SELECT * FROM boeken WHERE 1=1";
$params = [];

// 3. FILTER LOGICA

// --- ZOEKBALK ---
if (!empty($searchTerm)) {
    $sql .= " AND (Naam LIKE ? OR Schrijver LIKE ? OR Genre LIKE ?)";
    $params[] = "%" . $searchTerm . "%";
    $params[] = "%" . $searchTerm . "%";
    $params[] = "%" . $searchTerm . "%";
}

// Genre filter
if (!empty($_GET['genre'])) {
    $gekozenGenres = (array)$_GET['genre'];
    $genreQueryParts = [];
    foreach ($gekozenGenres as $genre) {
        $genreQueryParts[] = "genre LIKE ?";
        $params[] = "%" . $genre . "%";
    }
    if (count($genreQueryParts) > 0) {
        $sql .= " AND (" . implode(" AND ", $genreQueryParts) . ")";
    }
}

// Soort filter
if (!empty($_GET['soort'])) {
    $sql .= " AND TRIM(soort) = ?";
    $params[] = $_GET['soort'];
}

// Taal filter
if (!empty($_GET['taal'])) {
    $sql .= " AND taal = ?";
    $params[] = $_GET['taal'];
}

// 4. HAAL DE DATA OP
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$boeken = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bibliotheek Zoeken</title>
    <link rel="stylesheet" href="css/boeken.css">
    <link rel="stylesheet" href="css/header.css"> 
    <style>
        .boek-kaart {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .boek-kaart:hover {
            transform: scale(1.02);
        }
    </style>
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
                    value="<?php echo htmlspecialchars($searchTerm); ?>"
                >
            </form>
        </div>
    </section>

<div class="main-layout">
    
    <aside class="sidebar">
        <h3>Filter</h3>
        <form action="boeken.php" method="GET" id="filterForm">
            <?php if(!empty($searchTerm)): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <?php endif; ?>

            <div class="filter-group">
                <h4>Soort boek</h4>
                <label><input type="radio" name="soort" value="e-book" <?php if(isset($_GET['soort']) && $_GET['soort'] == 'e-book') echo 'checked'; ?>> E-Boeken</label>
                <label><input type="radio" name="soort" value="boeken" <?php if(isset($_GET['soort']) && $_GET['soort'] == 'boeken') echo 'checked'; ?>> Boeken</label>
                <label><input type="radio" name="soort" value="manga" <?php if(isset($_GET['soort']) && $_GET['soort'] == 'manga') echo 'checked'; ?>> Manga</label>
                <label><input type="radio" name="soort" value="stripboeken" <?php if(isset($_GET['soort']) && $_GET['soort'] == 'stripboeken') echo 'checked'; ?>> Stripboeken</label>
            </div>

            <div class="filter-group">
                <h4>Genre (Meerdere mogelijk)</h4>
                <?php
                    function isGenreChecked($val) {
                        if (isset($_GET['genre']) && is_array($_GET['genre'])) {
                            return in_array($val, $_GET['genre']) ? 'checked' : '';
                        }
                        return '';
                    }
                ?>
                <label><input type="checkbox" name="genre[]" value="Drama" <?php echo isGenreChecked('Drama'); ?>> Drama</label>
                <label><input type="checkbox" name="genre[]" value="Horror" <?php echo isGenreChecked('Horror'); ?>> Horror</label>
                <label><input type="checkbox" name="genre[]" value="Thriller" <?php echo isGenreChecked('Thriller'); ?>> Thriller</label>
                <label><input type="checkbox" name="genre[]" value="Fantasy" <?php echo isGenreChecked('Fantasy'); ?>> Fantasy</label>
                <label><input type="checkbox" name="genre[]" value="Avontuur" <?php echo isGenreChecked('Avontuur'); ?>> Avontuur</label>
                <label><input type="checkbox" name="genre[]" value="Fictie" <?php echo isGenreChecked('Fictie'); ?>> Fictie</label>
                <label><input type="checkbox" name="genre[]" value="Non-fictie" <?php echo isGenreChecked('Non-fictie'); ?>> Non-fictie</label>
                <label><input type="checkbox" name="genre[]" value="Romantiek" <?php echo isGenreChecked('Romantiek'); ?>> Romantiek</label>
                <label><input type="checkbox" name="genre[]" value="Actie" <?php echo isGenreChecked('Actie'); ?>> Actie</label>
            </div>

            <div class="filter-group">
                <h4>Taal</h4>
                <label><input type="radio" name="taal" value="Nederlands" <?php if(isset($_GET['taal']) && $_GET['taal'] == 'Nederlands') echo 'checked'; ?>> Nederlands</label>
                <label><input type="radio" name="taal" value="Engels" <?php if(isset($_GET['taal']) && $_GET['taal'] == 'Engels') echo 'checked'; ?>> Engels</label>
            </div>

            <a href="boeken.php" class="reset-btn">Filters wissen</a>
        </form>
    </aside>

    <main class="results">
        <?php 
        if (!empty($boeken)) {
            foreach ($boeken as $boek) { 
                $boekId = isset($boek['ID']) ? $boek['ID'] : (isset($boek['id']) ? $boek['id'] : '');
        ?>
            <div class="boek-kaart" onclick="window.location.href='details.php?id=<?php echo $boekId; ?>'">
                <div class="boek-img">
                    <img src="img/<?php echo htmlspecialchars($boek['Foto']); ?>" alt="<?php echo htmlspecialchars($boek['Naam']); ?>">
                </div>
                <div class="boek-info">
                    <div class="titel-row">
                        <h2><?php echo htmlspecialchars($boek['Naam']); ?></h2>
                        <div class="sterren">
                            <?php 
                            $aantalSterren = isset($boek['Sterren']) ? $boek['Sterren'] : 0;
                            for($i=0; $i<$aantalSterren; $i++) { echo "★"; } 
                            for($i=$aantalSterren; $i<5; $i++) { echo "<span style='color:#ccc'>★</span>"; }
                            ?>
                        </div>
                    </div>
                    <p class="auteur" style="color:#d35400; font-size: 0.9em; font-weight:bold;">
                        <?php echo htmlspecialchars($boek['Genre']); ?>
                    </p>
                    <p class="auteur"><?php echo htmlspecialchars($boek['Schrijver']); ?></p>
                    <p class="beschrijving"><?php echo htmlspecialchars($boek['Informatie']); ?></p>
                    <div class="knoppen">
                        <button class="btn-wit" onclick="event.stopPropagation()">Opslaan</button>
                        <button class="btn-wit" onclick="event.stopPropagation()">Reserveren</button>
                    </div>
                </div>
            </div>
        <?php 
            } 
        } else {
            echo "<p>Geen boeken gevonden die aan al je eisen voldoen.</p>";
        }
        ?>
    </main>
</div>

<script>
    const filterForm = document.getElementById('filterForm');
    const inputs = filterForm.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('change', () => { filterForm.submit(); });
    });
</script>

</body>
</html>