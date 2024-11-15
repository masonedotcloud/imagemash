<?php
session_start();
session_regenerate_id(); // Rigenera l'ID di sessione

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include_once('connect.php');

// Funzione per leggere tutte le immagini dalla tabella
function getImages($pdo) {
    $stmt = $pdo->query('SELECT filename FROM images');
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Funzione per selezionare due immagini casuali, evitando ripetizioni immediate
function getUniqueImagePair($pdo) {
    $stmt = $pdo->query('SELECT filename FROM images');
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $shownPairs = $pdo->query('SELECT image1, image2 FROM shown_pairs')->fetchAll(PDO::FETCH_ASSOC);
    $attempts = 0;
    $maxAttempts = 100; // Numero massimo di tentativi per trovare una coppia unica

    do {
        $img1 = $images[array_rand($images)];
        $img2 = $images[array_rand($images)];
        $pair = [$img1, $img2];
        sort($pair); // Ordinare l'array per evitare duplicati come (img1, img2) e (img2, img1)

        $attempts++;
        if ($attempts > $maxAttempts) {
            // Se non riesci a trovare una coppia unica, usa le prime due immagini disponibili
            return [$images[0], $images[1]];
        }
    } while (in_array(['image1' => $pair[0], 'image2' => $pair[1]], $shownPairs));

    return $pair;
}

// Ottieni una coppia unica di immagini
list($img1, $img2) = getUniqueImagePair($pdo);

// Genera un codice temporaneo unico
$voteCode = bin2hex(random_bytes(16)); // 32 caratteri esadecimali

// Memorizza il codice temporaneo e la coppia di immagini nella sessione
$_SESSION['vote_code'] = $voteCode;
$_SESSION['image_pair'] = [$img1, $img2];

// Aggiungi la nuova coppia alla cronologia
$stmt = $pdo->prepare('INSERT INTO shown_pairs (image1, image2) VALUES (?, ?)');
$stmt->execute([$img1, $img2]);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facesmash</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
    <link rel="icon" type="image/png" href="favicon.png">
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            -ms-touch-highlight-color: transparent;
            touch-callout: none;
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            transition: background-color 0.3s, color .3s
        }

        body.dark-mode {
            background-color: #121212;
            color: #eee
        }

        h1 {
            font-size: 2.8em;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700
        }

        .image-name {
            margin-top: 5px;
            font-size: 1.1em;
            word-wrap: break-word
        }

        .container {
            display: flex;
            justify-content: center;
            width: 90%;
            max-width: 900px;
            margin: 0 auto
        }

        .image-box {
            text-align: center;
            padding: 10px;
            margin: 0 10px;
            cursor: pointer;
            transition: transform .3s ease, box-shadow .3s ease;
            width: 45%;
            border-radius: 12px
        }

        .image-box:hover img {
            transform: scale(1.05);
            transition: transform .3s ease, box-shadow .3s ease
        }

        .image-box:active {
            transform: translateY(-5px)
        }

        img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            transition: border-color .3s ease
        }

        body.dark-mode img {
            border-color: #444
        }

        .toggle-dark-mode {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5em;
            color: #333;
        }

        body.dark-mode .toggle-dark-mode {
            color: #eee
        }

        .fixed-link {
            position: fixed;
            bottom: 10px;
            left: 10px;
            background-color: #007bff;
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .fixed-link:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            .container {
                flex-direction: column
            }

            .image-box {
                width: 100%;
                padding: 0;
                margin: 0;
                margin-bottom: 20px
            }

            img {
                max-width: 75%;
                height: auto
            }

            h1 {
                font-size: 1.8em
            }

            .image-name {
                font-size: .8em;
                margin-top: 3px
            }
        }
    </style>
</head>
<body>
    <h1>Fai la tua scelta?</h1>
    <div class="container">
        <div class="image-box" onclick="document.getElementById('form1').submit();">
            <form id="form1" action="vote.php" method="post" style="display: none;">
                <input type="hidden" name="vote" value="<?php echo htmlspecialchars($img1); ?>">
                <input type="hidden" name="vote_code" value="<?php echo htmlspecialchars($voteCode); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            </form>
            <img src="images/<?php echo htmlspecialchars($img1); ?>" alt="Immagine 1">
            <div class="image-name"><?php echo htmlspecialchars($img1); ?></div>
        </div>
        <div class="image-box" onclick="document.getElementById('form2').submit();">
            <form id="form2" action="vote.php" method="post" style="display: none;">
                <input type="hidden" name="vote" value="<?php echo htmlspecialchars($img2); ?>">
                <input type="hidden" name="vote_code" value="<?php echo htmlspecialchars($voteCode); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            </form>
            <img src="images/<?php echo htmlspecialchars($img2); ?>" alt="Immagine 2">
            <div class="image-name"><?php echo htmlspecialchars($img2); ?></div>
        </div>
    </div>
    <button class="toggle-dark-mode" onclick="toggleDarkMode()">🌓</button>
    <a href="results.php" class="fixed-link">Classifica</a>
    <script>
        // Funzione per attivare/disattivare la modalità scura
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        // Inizializza la modalità scura in base alle preferenze dell'utente
        function initializeDarkMode() {
            // Applica la modalità scura se il dispositivo lo preferisce
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.body.classList.add('dark-mode');
            }
        }

        // Chiama la funzione per inizializzare la modalità scura
        initializeDarkMode();
    </script>

</body>
</html>
