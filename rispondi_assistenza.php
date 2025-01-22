<?php
// filepath: /c:/xampp/htdocs/Gestionale-skipass/rispondi_assistenza.php

session_start();

// Controllo accesso: solo admin possono accedere
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    echo '<div class="alert alert-danger text-center" role="alert">
            Accesso negato.
          </div>';
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skipassmanagement";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("<div class='alert alert-danger text-center' role='alert'>
            Connessione al database fallita: " . htmlspecialchars($conn->connect_error) . "
         </div>");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['respond'])) {
    $request_id = intval($_POST['request_id']);
    $response = trim($_POST['response']);

    if (!empty($response)) {
        $stmt = $conn->prepare("UPDATE support_requests SET response = ?, responded_at = NOW(), status = 'risolto' WHERE id = ?");
        $stmt->bind_param("si", $response, $request_id);

        if ($stmt->execute()) {
            echo '<div class="alert alert-success" role="alert">Risposta inviata con successo.</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Errore nell\'invio della risposta.</div>';
        }
        $stmt->close();
    } else {
        echo '<div class="alert alert-warning" role="alert">La risposta non può essere vuota.</div>';
    }
}

// Recupera tutte le richieste non risolte
$requests_query = "SELECT sr.id, u.name, sr.subject, sr.message, sr.status, sr.created_at FROM support_requests sr JOIN users u ON sr.user_id = u.id WHERE sr.status != 'risolto' ORDER BY sr.created_at DESC";
$result = $conn->query($requests_query);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Rispondi Assistenza</title>
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
            transition: background-color 0.3s, color 0.3s;
        }
        .dark-mode .navbar {
            background-color: #1f1f1f;
        }
        .navbar-nav .nav-link {
            color: #000000;
            transition: color 0.3s;
        }
        .dark-mode .navbar-nav .nav-link {
            color: #ffffff;
        }
        .navbar-nav .nav-link:hover {
            color: #007bff;
        }
        .dark-mode .navbar-nav .nav-link:hover {
            color: #4dabf7;
        }
        .navbar-brand {
            color: #000000;
            transition: color 0.3s;
        }
        .dark-mode .navbar-brand {
            color: #ffffff;
        }
        .navbar-brand:hover {
            color: #0056b3;
        }
        .dark-mode .navbar-brand:hover {
            color: #66afe9;
        }
        .btn-secondary {
            transition: background-color 0.3s, border-color 0.3s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        .dark-mode .btn-secondary:hover {
            background-color: #4e555b;
            border-color: #4a5058;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: #ffffff;
        }
        .card {
            background-color: #ffffff;
            color: #000000;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }
        .dark-mode .card {
            background-color: #2c2c2c;
            color: #ffffff;
            border: 1px solid #555555;
        }
        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .badge {
            transition: background-color 0.3s, color 0.3s;
        }
        /* Additional Hover Effects for Buttons */
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        /* Table Hover Effect */
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.05);
        }
        .dark-mode .table-hover tbody tr:hover {
            background-color: rgba(255,255,255,0.1);
        }
        /* FAQ Section Styling */
        .card-header.bg-info {
            background-color: #17a2b8 !important;
        }
        .dark-mode .card-header.bg-info {
            background-color: #0d6efd !important;
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
    <h2>Rispondi alle Richieste di Assistenza</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="card">
                <div class="card-header">
                    <strong>ID:</strong> <?= htmlspecialchars($row['id']); ?> | <strong>Utente:</strong> <?= htmlspecialchars($row['name']); ?>
                </div>
                <div class="card-body">
                    <p><strong>Oggetto:</strong> <?= htmlspecialchars($row['subject']); ?></p>
                    <p><strong>Messaggio:</strong> <?= htmlspecialchars($row['message']); ?></p>
                    <form method="POST" action="">
                        <input type="hidden" name="request_id" value="<?= htmlspecialchars($row['id']); ?>">
                        <div class="mb-3">
                            <label for="response" class="form-label">Risposta</label>
                            <textarea class="form-control" id="response" name="response" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="respond" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Invia Risposta
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info" role="alert"><i class="fas fa-info-circle"></i> Nessuna richiesta da rispondere.</div>
    <?php endif; ?>
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