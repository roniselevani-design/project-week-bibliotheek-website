<?php
include 'db_connect.php';

// 1. BASIS QUERY
$sql = "SELECT * FROM boeken WHERE 1=1";
$params = [];

// 2. FILTER LOGICA

// Zoekbalk


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

// 3. HAAL DE DATA OP
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$boeken = $stmt->fetchAll(PDO::FETCH_ASSOC);
// FUZZY SEARCH
if ($searchTerm) {
    $filtered = [];

    foreach ($boeken as $boek) {
        $velden = [
            $boek['Naam'],
            $boek['Schrijver'],
            $boek['Genre']
        ];

        $besteScore = 0;

        foreach ($velden as $veld) {
            similar_text(
                strtolower($searchTerm),
                strtolower($veld),
                $percentage
            );

            // Hogere drempel voor precisie
            if ($percentage > $besteScore) {
                $besteScore = $percentage;
            }
        }

        // Minimale overeenkomst verhogen
        if ($besteScore >= 85) {
            $boek['match_score'] = $besteScore;
            $filtered[] = $boek;
        }
    }

    // Sorteer op beste match
    usort($filtered, function($a, $b) {
        return $b['match_score'] <=> $a['match_score'];
    });

    $boeken = $filtered;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bibliotheek Zoeken</title>
    <link rel="stylesheet" href="css/boeken.css">
    <link rel="stylesheet" href="css/header.css"> 
    <style>
        /* Zorgt voor het handje als je over een boek gaat */
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
            <a href="index.html">
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
        
        <div class="search-container">
            <form action="index.php" method="GET">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Zoeken"
                    autocomplete="off"
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

            <a href="index.php" class="reset-btn">Filters wissen</a>
        </form>
    </aside>

    <main class="results">
        <?php 
        if (!empty($boeken)) {
            foreach ($boeken as $boek) { 
                // Check of de database 'ID' of 'id' gebruikt
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
                            $aantalSterren = $boek['Sterren'];
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