<?php
// filepath: /c:/xampp/htdocs/5IE-TEP/Gestionale-skipass/emission.php

// Abilitare la visualizzazione degli errori (solo per sviluppo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php'; // Include il file di connessione al database

// Gestione Aggiungi SkiPass
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_skipass'])) {
    // Sanitizzazione dei dati di input
    $skipass_price_id = intval($_POST['skipass_price_id']);
    $validity_start_date = htmlspecialchars(trim($_POST['validity_start_date']));
    $validity_end_date = htmlspecialchars(trim($_POST['validity_end_date']));
    $area = htmlspecialchars(trim($_POST['area']));
    $user_id = intval($_POST['user_id']);
    $status = htmlspecialchars(trim($_POST['status']));

    // Validazione dei dati
    $errors = [];
    if (empty($validity_start_date)) {
        $errors[] = "La data di inizio è richiesta.";
    }
    if (empty($validity_end_date)) {
        $errors[] = "La data di fine è richiesta.";
    }
    if (!in_array($status, ['attivo', 'scaduto', 'bloccato'])) {
        $errors[] = "Stato dello SkiPass non valido.";
    }

    if (empty($errors)) {
        // Inserimento nel database
        $stmt = $conn->prepare("INSERT INTO skipasses (user_id, skipass_price_id, validity_start_date, validity_end_date, area, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        if (!$stmt) {
            $_SESSION['error'] = "Errore nella preparazione della query: " . $conn->error;
        } else {
            $stmt->bind_param("iissss", $user_id, $skipass_price_id, $validity_start_date, $validity_end_date, $area, $status);
            if ($stmt->execute()) {
                $_SESSION['success'] = "SkiPass aggiunto con successo.";
            } else {
                $_SESSION['error'] = "Errore nell'aggiunta dello SkiPass: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }

    header("Location: emission.php");
    exit();
}

// Recupera SkiPass
$stmt = $conn->prepare("
    SELECT 
        skipasses.id, 
        skipass_prices.type, 
        skipass_prices.price, 
        skipasses.validity_start_date, 
        skipasses.validity_end_date, 
        skipasses.area, 
        skipasses.status, 
        skipasses.created_at,
        users.name,
        users.surname
    FROM skipasses
    LEFT JOIN skipass_prices ON skipasses.skipass_price_id = skipass_prices.id
    LEFT JOIN users ON skipasses.user_id = users.id
    ORDER BY skipasses.created_at DESC
");
if (!$stmt) {
    die("Errore nella preparazione della query: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
$skipasses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Recupera utenti per il selettore
$user_stmt = $conn->prepare("SELECT id, name, surname FROM users ORDER BY name ASC, surname ASC");
if (!$user_stmt) {
    die("Errore nella preparazione della query: " . $conn->error);
}
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$users = $user_result->fetch_all(MYSQLI_ASSOC);
$user_stmt->close();

// Recupera prezzi degli skipass per il selettore
$price_stmt = $conn->prepare("SELECT id, type, price FROM skipass_prices ORDER BY type ASC");
if (!$price_stmt) {
    die("Errore nella preparazione della query: " . $conn->error);
}
$price_stmt->execute();
$price_result = $price_stmt->get_result();
$prices = $price_result->fetch_all(MYSQLI_ASSOC);
$price_stmt->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emissione SkiPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS Personalizzato */
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
            background-color: #343a40;
        }
        .navbar-brand, .nav-link {
            color: #000 !important;
        }
        .dark-mode .navbar-brand, .dark-mode .nav-link {
            color: #ffffff !important;
        }
        .navbar-toggler-icon {
            filter: invert(0);
        }
        .dark-mode .navbar-toggler-icon {
            filter: invert(1);
        }
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .btn-primary {
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary:hover {
            transform: scale(1.05);
            background-color: #0056b3;
        }
        .alert {
            transition: opacity 0.3s ease-in-out;
        }
        /* Stili Aggiuntivi per le Card delle Skipasses */
        .skipass-card {
            margin-bottom: 20px;
        }
        .skipass-text, .skipass-title {
            color: #000 !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Gestionale SkiPass</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="toggle-dark-mode"><i class="fas fa-moon"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <!-- Messaggi di Successo/Errori -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h2 class="text-secondary mb-4">Emissione SkiPass</h2>

        <!-- Form Aggiungi SkiPass -->
        <div class="card mb-4 p-4 shadow-sm">
            <h3 class="text-primary mb-3">Aggiungi Nuovo SkiPass</h3>
            <form method="POST" action="emission.php">
                <input type="hidden" name="add_skipass" value="1">
                <div class="mb-3">
                    <label for="user_id" class="form-label">Utente</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">Seleziona Utente</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="skipass_price_id" class="form-label">Tipo di SkiPass</label>
                    <select class="form-select" id="skipass_price_id" name="skipass_price_id" required>
                        <option value="">Seleziona Tipo</option>
                        <?php foreach ($prices as $price): ?>
                            <option value="<?php echo $price['id']; ?>">
                                <?php echo htmlspecialchars(ucfirst($price['type'])) . " - €" . number_format($price['price'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="validity_start_date" class="form-label">Data Inizio Validità</label>
                    <input type="date" class="form-control" id="validity_start_date" name="validity_start_date" required>
                </div>
                <div class="mb-3">
                    <label for="validity_end_date" class="form-label">Data Fine Validità</label>
                    <input type="date" class="form-control" id="validity_end_date" name="validity_end_date" required>
                </div>
                <div class="mb-3">
                    <label for="area" class="form-label">Area</label>
                    <input type="text" class="form-control" id="area" name="area" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Stato</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="attivo">Attivo</option>
                        <option value="scaduto">Scaduto</option>
                        <option value="bloccato">Bloccato</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Aggiungi SkiPass</button>
            </form>
        </div>

        <!-- Lista SkiPass -->
        <div class="row" id="skipassContainer">
            <?php foreach ($skipasses as $skipass): ?>
                <div class="col-md-4">
                    <div class="skipass skipass-card card mb-3 p-3 shadow-sm">
                        <h3 class="card-title skipass-title"><?php echo htmlspecialchars(ucfirst($skipass['type'])); ?></h3>
                        <p class="card-text skipass-text">
                            <strong>Utente:</strong> <?php echo htmlspecialchars($skipass['name'] . ' ' . $skipass['surname']); ?><br>
                            <strong>Prezzo:</strong> €<?php echo number_format($skipass['price'], 2); ?><br>
                            <strong>Validità:</strong> <?php echo htmlspecialchars($skipass['validity_start_date']); ?> - <?php echo htmlspecialchars($skipass['validity_end_date']); ?><br>
                            <strong>Area:</strong> <?php echo htmlspecialchars($skipass['area']); ?><br>
                            <strong>Stato:</strong> <?php echo htmlspecialchars(ucfirst($skipass['status'])); ?>
                        </p>
                        <small class="text-muted">Creato il: <?php echo htmlspecialchars($skipass['created_at']); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Toggle Dark Mode
        document.addEventListener('DOMContentLoaded', function () {
            const toggleDarkMode = document.getElementById('toggle-dark-mode');
            const body = document.body;

            // Check for saved dark mode preference
            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                toggleDarkMode.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                toggleDarkMode.innerHTML = '<i class="fas fa-moon"></i>';
            }

            toggleDarkMode.addEventListener('click', function () {
                body.classList.toggle('dark-mode');
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                    toggleDarkMode.innerHTML = '<i class="fas fa-sun"></i>';
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                    toggleDarkMode.innerHTML = '<i class="fas fa-moon"></i>';
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>