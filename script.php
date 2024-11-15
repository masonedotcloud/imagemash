<?php

exit();

try {
    // Connessione al database
    $pdo = new PDO('mysql:host=localhost;dbname=facemash', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Errore di connessione al database: " . $e->getMessage());
}

// Directory delle immagini
$imageDir = 'images';

// Funzione per ottenere tutte le immagini nella cartella
function getImageFiles($dir) {
    $files = array_diff(scandir($dir), array('..', '.'));
    return array_filter($files, function($file) use ($dir) {
        return is_file($dir . '/' . $file) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
    });
}

// Ottieni tutti i file immagine dalla cartella
$imageFiles = getImageFiles($imageDir);

if (empty($imageFiles)) {
    die("Nessuna immagine trovata nella cartella $imageDir.");
}

// Preparare la query per inserire le immagini
$stmt = $pdo->prepare('INSERT IGNORE INTO images (filename) VALUES (?)');

// Inserisci ogni immagine nel database
foreach ($imageFiles as $file) {
    $stmt->execute([$file]);
}

echo "Immagini inserite nel database con successo.";
?>
