<?php
session_start();
session_regenerate_id(); // Rigenera l'ID di sessione

include_once('connect.php');

if (!isset($_POST['vote'], $_POST['vote_code'])) {
    die('Dati mancanti.');
}

// Recupera i dati inviati
$vote = filter_input(INPUT_POST, 'vote', FILTER_SANITIZE_STRING);
$vote_code = filter_input(INPUT_POST, 'vote_code', FILTER_SANITIZE_STRING);

// Verifica il token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Token CSRF non valido.');
}

// Verifica se il codice temporaneo è valido
if (!isset($_SESSION['vote_code']) || !hash_equals($_SESSION['vote_code'], $vote_code)) {
    die('Codice temporaneo non valido.');
}


// Recupera la coppia di immagini dalla sessione
$image_pair = $_SESSION['image_pair'] ?? [];

// Verifica che l'immagine votata faccia parte della coppia visualizzata
if (!in_array($vote, $image_pair)) {
    die('Voto non valido.');
}

// Registra il voto
$stmt = $pdo->prepare('INSERT INTO votes (image) VALUES (?)');
$stmt->execute([$vote]);

// Pulisce i dati della sessione
unset($_SESSION['vote_code']);
unset($_SESSION['image_pair']);

// Pulisce i dati della sessione
session_unset(); // Rimuove tutte le variabili di sessione
session_destroy(); // Distrugge la sessione

// Reindirizza alla pagina principale
header('Location: index.php');
exit();