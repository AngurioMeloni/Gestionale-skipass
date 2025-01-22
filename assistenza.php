<?php
// filepath: /c:/xampp/htdocs/Gestionale-skipass/assistenza.php

session_start();

// Controllo accesso: solo utenti loggati possono accedere
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo '<div class="alert alert-danger text-center" role="alert">
            Devi effettuare il <a href="login.php" class="alert-link">login</a> per accedere a questa pagina.
          </div>';
    exit;
}

// Verifica se l'ID utente è presente nella sessione
if (!isset($_SESSION['id'])) {
    echo '<div class="alert alert-danger text-center" role="alert">
            ID utente mancante nella sessione. Contatta l\'amministratore.
          </div>';
    exit;
}

$user_id = $_SESSION['id'];

// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skipassmanagement";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica connessione
if ($conn->connect_error) {
    die("<div class='alert alert-danger text-center' role='alert'>
            Connessione al database fallita: " . htmlspecialchars($conn->connect_error) . "
         </div>");
}

$conn->set_charset("utf8");

// Inserimento nuova richiesta di assistenza
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_request'])) {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($subject) || empty($message)) {
        $error = "Tutti i campi sono obbligatori.";
    } else {
        // Prepara e bind dei parametri
        $stmt = $conn->prepare("INSERT INTO support_requests (user_id, subject, message, status) VALUES (?, ?, ?, 'in attesa')");
        if ($stmt === false) {
            $error = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("iss", $user_id, $subject, $message);
            
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: assistenza.php?success=1");
                exit;
            } else {
                $error = "Errore nell'invio della richiesta: " . htmlspecialchars($stmt->error);
            }
        }
    }
}

// Recupero richieste di assistenza dell'utente
$requests_query = "SELECT id, subject, message, status, response, created_at, responded_at FROM support_requests WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($requests_query);
if ($stmt === false) {
    die("<div class='alert alert-danger text-center' role='alert'>
            Errore nella preparazione della query: " . htmlspecialchars($conn->error) . "
         </div>");
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$requests_result = $stmt->get_result();
$requests_data = [];
if ($requests_result && $requests_result->num_rows > 0) {
    while ($row = $requests_result->fetch_assoc()) {
        $requests_data[] = $row;
    }
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Assistenza Clienti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .dark-mode .navbar {
            background-color: #1f1f1f;
        }
        .card {
            background-color: #ffffff;
            color: #000000;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        .dark-mode .card {
            background-color: #2c2c2c;
            color: #ffffff;
            border: 1px solid #555555;
        }
        .chart-container {
            width: 100%;
            height: auto;
            margin: auto;
        }
        .welcome-text {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item me-3">
                    <button id="toggle-dark-mode" class="btn btn-secondary">
                        <i id="dark-mode-icon" class="fas fa-moon"></i>
                        Modalità Scura
                    </button>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <p class="text-center welcome-text">Benvenuto, <?= htmlspecialchars($_SESSION['name']); ?>!</p>

    <!-- Form per l'invio di richieste di assistenza -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-envelope-square"></i> Invia una Richiesta di Assistenza
        </div>
        <div class="card-body">
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> Richiesta inviata con successo!
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="subject" class="form-label"><i class="fas fa-heading"></i> Oggetto</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label"><i class="fas fa-comment-dots"></i> Messaggio</label>
                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                </div>
                <button type="submit" name="submit_request" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Invia Richiesta
                </button>
            </form>
        </div>
    </div>

    <!-- Monitoraggio dello stato delle richieste -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <i class="fas fa-tasks"></i> Le Tue Richieste di Assistenza
        </div>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-heading"></i> Oggetto</th>
                        <th><i class="fas fa-comment"></i> Messaggio</th>
                        <th><i class="fas fa-stream"></i> Stato</th>
                        <th><i class="fas fa-comment-dots"></i> Risposta</th>
                        <th><i class="fas fa-calendar-alt"></i> Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($requests_data) > 0): ?>
                        <?php foreach ($requests_data as $request): ?>
                            <tr>
                                <td><?= htmlspecialchars($request['id']); ?></td>
                                <td><?= htmlspecialchars($request['subject']); ?></td>
                                <td><?= htmlspecialchars($request['message']); ?></td>
                                <td>
                                    <?php
                                        switch ($request['status']) {
                                            case 'in attesa':
                                                echo '<span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half"></i> In Attesa</span>';
                                                break;
                                            case 'in lavorazione':
                                                echo '<span class="badge bg-info text-white"><i class="fas fa-tools"></i> In Lavorazione</span>';
                                                break;
                                            case 'risolto':
                                                echo '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Risolto</span>';
                                                break;
                                            default:
                                                echo htmlspecialchars($request['status']);
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($request['response'] ?? 'Nessuna risposta'); ?>
                                    <?php if (!empty($request['response'])): ?>
                                        <br><small><em>Risposto il: <?= htmlspecialchars($request['responded_at']); ?></em></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($request['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6"><i class="fas fa-exclamation-triangle"></i> Nessuna richiesta trovata.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FAQ automatizzate -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <i class="fas fa-question-circle"></i> FAQ
        </div>
        <div class="card-body">
            <h5><i class="fas fa-lightbulb"></i> Come posso emettere uno skipass?</h5>
            <p>Puoi emettere uno skipass andando nella sezione <a href="emission.php" class="btn btn-link"><i class="fas fa-ticket-alt"></i> Emissione di skipass</a>.</p>

            <h5><i class="fas fa-lightbulb"></i> Cosa fare se ho perso il mio skipass?</h5>
            <p>Invia una richiesta di assistenza tramite questa pagina specificando il problema.</p>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleDarkMode = document.getElementById('toggle-dark-mode');
        const darkModeIcon = document.getElementById('dark-mode-icon');
        const body = document.body;

        // Verifica lo stato della Dark Mode dal localStorage
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            darkModeIcon.classList.remove('fa-moon');
            darkModeIcon.classList.add('fa-sun');
        } else {
            darkModeIcon.classList.remove('fa-sun');
            darkModeIcon.classList.add('fa-moon');
        }

        toggleDarkMode.addEventListener('click', function () {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                darkModeIcon.classList.remove('fa-moon');
                darkModeIcon.classList.add('fa-sun');
            } else {
                localStorage.setItem('darkMode', 'disabled');
                darkModeIcon.classList.remove('fa-sun');
                darkModeIcon.classList.add('fa-moon');
            }
        });
    });
</script>
</body>
</html>