<?php
// filepath: /c:/xampp/htdocs/Gestionale-skipass/emission.php

// Abilita la visualizzazione degli errori per debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skipassmanagement";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica connessione
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}

// Imposta la codifica dei caratteri
$conn->set_charset("utf8");

// Recupera utenti
$user_stmt = $conn->prepare("SELECT id, name, surname FROM users ORDER BY name ASC, surname ASC");
if (!$user_stmt) {
    die("Errore nella preparazione della query: " . $conn->error);
}
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$users = $user_result->fetch_all(MYSQLI_ASSOC);
$user_stmt->close();

// Recupera skipasses
$result = $conn->query("SELECT * FROM skipasses");
$skipasses = $result->fetch_all(MYSQLI_ASSOC);
$result->close();

// Processamento del form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_skipass'])) {
    // Recupero dei dati dal form
    $user_id = intval($_POST['user_id']);
    $category = $conn->real_escape_string($_POST['category']);
    $age_group = $conn->real_escape_string($_POST['age_group']);
    $validity_start_date = $_POST['validity_start_date'];
    $validity_end_date = $_POST['validity_end_date'];
    $area = $conn->real_escape_string($_POST['area']);
    $discount_type = isset($_POST['discount_type']) ? $conn->real_escape_string($_POST['discount_type']) : 'none';
    $status = $conn->real_escape_string($_POST['status']);

    // Determina la tabella da cui recuperare il prezzo
    if ($discount_type !== 'none') {
        // Recupera il prezzo dalla tabella 'skipass_prices' con discount_type specifico
        $price_stmt = $conn->prepare("SELECT id, price FROM skipass_prices WHERE discount_type = ? AND category = ? AND age_group = ?");
        if (!$price_stmt) {
            $_SESSION['error'] = "Errore nella preparazione della query: " . $conn->error;
            header("Location: emission.php");
            exit;
        }
        $price_stmt->bind_param("sss", $discount_type, $category, $age_group);
    } else {
        // Recupera il prezzo dalla tabella 'skipass_prices' senza sconto
        $price_stmt = $conn->prepare("SELECT id, price FROM skipass_prices WHERE discount_type = 'none' AND category = ? AND age_group = ?");
        if (!$price_stmt) {
            $_SESSION['error'] = "Errore nella preparazione della query: " . $conn->error;
            header("Location: emission.php");
            exit;
        }
        $price_stmt->bind_param("ss", $category, $age_group);
    }

    $price_stmt->execute();
    $price_result = $price_stmt->get_result();

    if ($price_result->num_rows === 0) {
        $_SESSION['error'] = "Tariffa non valida per la combinazione selezionata.";
        $price_stmt->close();
        header("Location: emission.php");
        exit;
    }

    $price_row = $price_result->fetch_assoc();
    $skipass_price_id = intval($price_row['id']);
    $final_price = floatval($price_row['price']);
    $price_stmt->close();

    // Inserimento nel database
    $insert_stmt = $conn->prepare("INSERT INTO skipasses (user_id, skipass_price_id, category, age_group, validity_start_date, validity_end_date, area, discount_type, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$insert_stmt) {
        $_SESSION['error'] = "Errore nella preparazione della query di inserimento: " . $conn->error;
        header("Location: emission.php");
        exit;
    }
    $insert_stmt->bind_param("iissssssds", $user_id, $skipass_price_id, $category, $age_group, $validity_start_date, $validity_end_date, $area, $discount_type, $final_price, $status);

    if ($insert_stmt->execute()) {
        $_SESSION['success'] = "SkiPass emesso con successo!";
    } else {
        $_SESSION['error'] = "Errore nell'emissione dello SkiPass: " . $insert_stmt->error;
    }

    $insert_stmt->close();

    // Reindirizzamento
    header("Location: emission.php");
    exit;
}

$conn->close();
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
        .card-text, .card-title {
            color: #000; /* Imposta il colore del testo a nero */
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

    <div class="container mt-5">
        <!-- Messaggi di Successo o Errore -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Form Aggiungi SkiPass -->
        <div class="card mb-4 p-4 shadow-sm">
            <h3 class="text-primary mb-3">Aggiungi Nuovo SkiPass</h3>
            <form method="POST" action="emission.php">
                <input type="hidden" name="add_skipass" value="1">

                <div class="mb-3">
                    <label for="user_id" class="form-label text-dark">Utente</label>
                    <select class="form-select text-dark" id="user_id" name="user_id" required>
                        <option value="">Seleziona Utente</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Selezione Categoria -->
                <div class="mb-3">
                    <label for="category" class="form-label text-dark">Categoria SkiPass</label>
                    <select class="form-select text-dark" id="category" name="category" required>
                        <option value="">Seleziona Categoria</option>
                        <option value="Giornaliero">Giornaliero</option>
                        <option value="2 Giorni">2 Giorni</option>
                        <option value="3 Giorni">3 Giorni</option>
                        <option value="Settimanale (6 Giorni)">Settimanale (6 Giorni)</option>
                        <option value="Stagionale">Stagionale</option>
                    </select>
                </div>

                <!-- Selezione Fascia di Età -->
                <div class="mb-3">
                    <label for="age_group" class="form-label text-dark">Fascia di Età</label>
                    <select class="form-select text-dark" id="age_group" name="age_group" required>
                        <option value="">Seleziona Fascia di Età</option>
                        <option value="Adulti">Adulti</option>
                        <option value="Junior">Junior (fino a 16 anni)</option>
                        <option value="Senior">Senior (oltre 65 anni)</option>
                        <option value="Baby">Baby (fino a 8 anni)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="validity_start_date" class="form-label text-dark">Data Inizio Validità</label>
                    <input type="date" class="form-control text-dark" id="validity_start_date" name="validity_start_date" required>
                </div>

                <div class="mb-3">
                    <label for="validity_end_date" class="form-label text-dark">Data Fine Validità</label>
                    <input type="date" class="form-control text-dark" id="validity_end_date" name="validity_end_date" required>
                </div>

                <div class="mb-3">
                    <label for="area" class="form-label text-dark">Area</label>
                    <input type="text" class="form-control text-dark" id="area" name="area" required>
                </div>

                <!-- Selezione Tipo di Sconto -->
                <div class="mb-3">
                    <label class="form-label text-dark">Scegli il Tipo di Sconto</label>
                    <select class="form-select text-dark" id="discount_type" name="discount_type" required>
                        <option value="none">Nessuno</option>
                        <option value="fisi">Tesserato FISI</option>
                        <option value="maestro">Maestro di Sci</option>
                        <option value="disabile">Persone con Disabilità</option>
                        <option value="disabile_accompagnatore">Accompagnatore Disabilità Visiva</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label text-dark">Stato</label>
                    <select class="form-select text-dark" id="status" name="status" required>
                        <option value="">Seleziona Stato</option>
                        <option value="attivo">Attivo</option>
                        <option value="inattivo">Inattivo</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Emetti SkiPass</button>
            </form>
        </div>

        <!-- Lista SkiPass -->
        <div class="row" id="skipassContainer">
            <?php foreach ($skipasses as $skipass): ?>
                <div class="col-md-4">
                    <div class="skipass skipass-card card mb-3 p-3 shadow-sm">
                        <h5 class="card-title">SkiPass ID: <?php echo htmlspecialchars($skipass['id']); ?></h5>
                        <p class="card-text"><strong>Utente ID:</strong> <?php echo htmlspecialchars($skipass['user_id']); ?></p>
                        <p class="card-text"><strong>Categoria:</strong> <?php echo htmlspecialchars($skipass['category']); ?></p>
                        <p class="card-text"><strong>Fascia di Età:</strong> <?php echo htmlspecialchars($skipass['age_group']); ?></p>
                        <p class="card-text"><strong>Validità:</strong> <?php echo htmlspecialchars($skipass['validity_start_date']) . " a " . htmlspecialchars($skipass['validity_end_date']); ?></p>
                        <p class="card-text"><strong>Area:</strong> <?php echo htmlspecialchars($skipass['area']); ?></p>
                        <p class="card-text"><strong>Sconto:</strong> <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $skipass['discount_type']))); ?></p>
                        <p class="card-text"><strong>Prezzo:</strong> €<?php echo number_format($skipass['price'], 2); ?></p>
                        <p class="card-text"><strong>Stato:</strong> <?php echo htmlspecialchars($skipass['status']); ?></p>
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

            // Controlla la preferenza di dark mode salvata
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
</body>
</html>