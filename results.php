<?php
include_once('connect.php');

// Leggi i voti dalla tabella
$stmt = $pdo->query('SELECT image, COUNT(*) as count FROM votes GROUP BY image');
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$results = array_column($results, 'count', 'image');

arsort($results);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Risultati dei Voti</title>
    <style>
        /* Impostazioni generali */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Modalità chiara */
        body:not(.dark-mode) {
            background-color: #ffffff;
            color: #000000;
        }

        h1 {
            font-size: 2rem;
            text-align: center;
            margin-top: 1rem;
        }

        /* Modalità scura */
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
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

        .results-container {
            display: flex;
            justify-content: center;
            padding: 1rem;
        }

        .grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .image-box {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            background-color: #f9f9f9;
            transition: background-color 0.3s, border-color 0.3s;
        }

        body.dark-mode .image-box {
            background-color: #1e1e1e;
            border-color: #333333;
        }

        .image-thumbnail img {
            width: 100%;
            height: auto;
            display: block;
        }

        .vote-count {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.6);
            color: #ffffff;
            padding: 0.5rem;
            font-size: 1rem;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
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



    </style>
</head>
<body>
    <button class="toggle-dark-mode" onclick="toggleDarkMode()">🌓</button>
    <h1>Risultati dei Voti</h1>
    <div class="results-container">
        <div class="grid">
            <?php foreach ($results as $image => $count): ?>
                <div class="image-box">
                    <div class="image-thumbnail">
                        <img src="images/<?php echo htmlspecialchars($image); ?>" alt="Immagine">
                    </div>
                    <div class="vote-count"><?php echo $count; ?> voti</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <a href="index.php" class="fixed-link">Vota</a>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        // Inizializza la modalità scura in base alle preferenze del sistema
        function initializeDarkMode() {
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.body.classList.add('dark-mode');
            }
        }

        initializeDarkMode();
    </script>
</body>
</html>
