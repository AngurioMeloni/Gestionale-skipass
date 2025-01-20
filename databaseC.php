<?php
// filepath: /c:/xampp/htdocs/5IE-TEP/Gestionale-skipass/databaseC.php
session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['error_message'] = 'Devi effettuare il login per accedere a questa pagina.';
    header('Location: login.php');
    exit;
}

// Verifica se l'utente è un amministratore
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    $_SESSION['error_message'] = 'Non hai i permessi necessari per accedere a questa pagina.';
    header('Location: dashboard.php');
    exit;
}

// Connessione al database
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "skipassmanagement";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Verifica la connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Query per recuperare i dati degli utenti
$sql = "SELECT id, name, surname, email, phone_number, date_of_birth, created_at FROM users";
$result = $conn->query($sql);

// Controlla se la query ha avuto successo
if ($result === false) {
    die("Errore nella query: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Utenti</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            transition: background-color 0.3s, color 0.3s;
        }
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .admin-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .admin-card {
            padding: 30px;
            border: none;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, color 0.3s, transform 0.3s, box-shadow 0.3s;
            width: 100%;
            max-width: 1000px;
        }
        .dark-mode .admin-card {
            background-color: #1e1e1e;
            color: #ffffff;
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.1);
        }
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        .navbar-brand {
            font-weight: bold;
        }
        .navbar-nav .nav-link, .navbar-nav .btn {
            font-size: 1rem;
            margin-left: 15px;
        }
        /* Scrollbar styling for dark mode */
        .dark-mode ::-webkit-scrollbar {
            width: 10px;
        }
        .dark-mode ::-webkit-scrollbar-track {
            background: #1e1e1e;
        }
        .dark-mode ::-webkit-scrollbar-thumb {
            background-color: #555;
            border-radius: 10px;
            border: 2px solid #1e1e1e;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Gestionale SkiPass</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- Pulsante Aggiorna -->
                    <li class="nav-item">
                        <button class="btn btn-primary me-2" onclick="window.location.reload();">Aggiorna</button>
                    </li>
                    <!-- Link Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <!-- Toggle Dark Mode -->
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="toggle-dark-mode"><i class="fas fa-moon"></i></a>
                    </li>
                    <!-- Logout -->
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Container -->
    <div class="container admin-container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="admin-card mx-auto">
                    <h2 class="text-center mb-4">Gestione Utenti</h2>
                    <?php
                    // Mostra messaggio di errore se presente
                    if (isset($_SESSION['error_message'])):
                    ?>
                        <div class="alert alert-danger">
                            <?php 
                                echo htmlspecialchars($_SESSION['error_message']); 
                                unset($_SESSION['error_message']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <table class="table table-striped table-responsive">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Email</th>
                                <th>Telefono</th>
                                <th>Data di Nascita</th>
                                <th>Registrato il</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['surname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Bootstrap e Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Toggle Dark Mode
        document.addEventListener('DOMContentLoaded', function () {
            const toggleDarkMode = document.getElementById('toggle-dark-mode');
            const body = document.body;

            // Imposta l'icona iniziale
            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                toggleDarkMode.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                toggleDarkMode.innerHTML = '<i class="fas fa-moon"></i>';
            }

            // Evento di toggle
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