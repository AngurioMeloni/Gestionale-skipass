<?php
// filepath: /c:/xampp/htdocs/Gestionale-skipass/fidelty_program.php

session_start();

// Controllo accesso: solo utenti loggati possono accedere
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo '<div class="alert alert-danger text-center" role="alert">
            Devi effettuare il <a href="login.php" class="alert-link">login</a> per accedere a questa pagina.
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

// Creazione della tabella transactions se non esiste
$create_table_query = "
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    points INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
if ($conn->query($create_table_query) === FALSE) {
    die("<div class='alert alert-danger text-center' role='alert'>
            Errore nella creazione della tabella: " . htmlspecialchars($conn->error) . "
         </div>");
}

// Creazione della tabella rewards se non esiste
$create_rewards_table = "
CREATE TABLE IF NOT EXISTS rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    points_cost INT NOT NULL
)";
if ($conn->query($create_rewards_table) === FALSE) {
    die("<div class='alert alert-danger text-center' role='alert'>
            Errore nella creazione della tabella rewards: " . htmlspecialchars($conn->error) . "
         </div>");
}

// Recupero saldo punti dell'utente
$points_query = "SELECT points FROM users WHERE id = ?";
$stmt = $conn->prepare($points_query);
if ($stmt === false) {
    die("<div class='alert alert-danger text-center' role='alert'>
            Errore nella preparazione della query: " . htmlspecialchars($conn->error) . "
         </div>");
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($points);
$stmt->fetch();
$stmt->close();

// Recupero storico delle transazioni
$transactions_query = "SELECT description, points, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($transactions_query);
if ($stmt === false) {
    die("<div class='alert alert-danger text-center' role='alert'>
            Errore nella preparazione della query: " . htmlspecialchars($conn->error) . "
         </div>");
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$transactions_result = $stmt->get_result();
$transactions_data = [];
if ($transactions_result && $transactions_result->num_rows > 0) {
    while ($row = $transactions_result->fetch_assoc()) {
        $transactions_data[] = $row;
    }
}
$stmt->close();

// Recupero premi disponibili
$rewards_query = "SELECT id, name, description, points_cost FROM rewards ORDER BY points_cost ASC";
$stmt = $conn->prepare($rewards_query);
if ($stmt === false) {
    die("<div class='alert alert-danger text-center' role='alert'>
            Errore nella preparazione della query per i premi: " . htmlspecialchars($conn->error) . "
         </div>");
}
$stmt->execute();
$rewards_result = $stmt->get_result();
$rewards_data = [];
if ($rewards_result && $rewards_result->num_rows > 0) {
    while ($row = $rewards_result->fetch_assoc()) {
        $rewards_data[] = $row;
    }
}
$stmt->close();

// Gestione del riscatto premio
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reward_id'])) {
    $reward_id = intval($_POST['reward_id']);

    // Recupera i dettagli del premio
    $reward_detail_query = "SELECT name, points_cost FROM rewards WHERE id = ?";
    $stmt = $conn->prepare($reward_detail_query);
    if ($stmt === false) {
        die("<div class='alert alert-danger text-center' role='alert'>
                Errore nella preparazione della query del premio: " . htmlspecialchars($conn->error) . "
             </div>");
    }
    $stmt->bind_param("i", $reward_id);
    $stmt->execute();
    $stmt->bind_result($reward_name, $reward_points_cost);
    if (!$stmt->fetch()) {
        $stmt->close();
        die("<div class='alert alert-danger text-center' role='alert'>
                Premio non trovato.
             </div>");
    }
    $stmt->close();

    // Verifica se l'utente ha abbastanza punti
    if ($points < $reward_points_cost) {
        die("<div class='alert alert-warning text-center' role='alert'>
                Punti insufficienti per riscattare questo premio.
             </div>");
    }

    // Inserisci la transazione di riscatto
    $insert_transaction = "INSERT INTO transactions (user_id, description, points) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_transaction);
    if ($stmt === false) {
        die("<div class='alert alert-danger text-center' role='alert'>
                Errore nella preparazione della transazione di riscatto: " . htmlspecialchars($conn->error) . "
             </div>");
    }
    $description = "Riscattato premio: " . $reward_name;
    $negative_points = -$reward_points_cost;
    $stmt->bind_param("isi", $user_id, $description, $negative_points);
    if ($stmt->execute()) {
        $stmt->close();

        // Aggiorna il saldo punti
        $update_points_query = "UPDATE users SET points = points - ? WHERE id = ?";
        $stmt = $conn->prepare($update_points_query);
        if ($stmt === false) {
            die("<div class='alert alert-danger text-center' role='alert'>
                    Errore nella preparazione dell'aggiornamento dei punti: " . htmlspecialchars($conn->error) . "
                 </div>");
        }
        $stmt->bind_param("ii", $reward_points_cost, $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: fidelty_program.php?redeemed=1");
            exit;
        } else {
            $stmt->close();
            die("<div class='alert alert-danger text-center' role='alert'>
                    Errore nell'aggiornamento dei punti: " . htmlspecialchars($stmt->error) . "
                 </div>");
        }
    } else {
        $stmt->close();
        die("<div class='alert alert-danger text-center' role='alert'>
                Errore nell'inserimento della transazione di riscatto: " . htmlspecialchars($stmt->error) . "
             </div>");
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Programma Fedeltà</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .card-header {
            background-color: #007bff;
            color: #ffffff;
        }
        .dark-mode .card-header {
            background-color: #0056b3;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
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
    <h2 class="text-center">Programma Fedeltà</h2>

    <?php if (isset($_GET['redeemed']) && $_GET['redeemed'] == 1): ?>
        <div class="alert alert-success text-center" role="alert">
            Premio riscattato con successo!
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-coins"></i> Saldo Punti
        </div>
        <div class="card-body">
            <h5>Hai <strong><?= htmlspecialchars($points); ?></strong> punti.</h5>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fas fa-gift"></i> Premi Disponibili
        </div>
        <div class="card-body">
            <?php if (count($rewards_data) > 0): ?>
                <div class="row">
                    <?php foreach ($rewards_data as $reward): ?>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($reward['name']); ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($reward['description']); ?></p>
                                    <p class="card-text"><strong>Costi: <?= htmlspecialchars($reward['points_cost']); ?> punti</strong></p>
                                    <form method="POST" action="fidelty_program.php">
                                        <input type="hidden" name="reward_id" value="<?= htmlspecialchars($reward['id']); ?>">
                                        <button type="submit" class="btn btn-primary <?= ($points < $reward['points_cost']) ? 'disabled' : '' ?>">
                                            Riscatta
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Nessun premio disponibile al momento.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // filepath: /c:/xampp/htdocs/Gestionale-skipass/fidelty_program.php
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