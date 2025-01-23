<?php
// filepath: /c:/xampp/htdocs/5IE-TEP/Gestionale-skipass/dashboard.php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            transition: background-color 0.3s, color 0.3s;
        }
        h1, h5 {
            font-family: 'Montserrat', sans-serif;
        }
        .container {
            margin-top: 100px; /* Aumentato per compensare la navbar fissa */
            position: relative;
            z-index: 2;
        }
        /* Modalità Scura */
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .dark-mode .navbar {
            background-color: #1e1e1e !important;
        }
        .dark-mode .card {
            background-color: #1e1e1e;
            color: #ffffff;
        }
        .dark-mode .btn-primary {
            background-color: #bb86fc;
            border-color: #bb86fc;
        }
        .dark-mode .btn-primary:hover {
            background-color: #985eff;
            border-color: #7a4de0;
        }
        /* Ripristinare la dimensione originale delle card */
        .card {
            min-height: 250px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3);
        }
        .card i {
            font-size: 4rem;
            color: #007bff;
            transition: color 0.3s;
        }
        .card:hover i {
            color: #0056b3;
        }
        .card-title {
            font-size: 1.5rem;
            margin-top: 10px;
        }
        .btn-link {
            transition: color 0.3s;
        }
        .btn-link:hover {
            color: #0056b3;
            text-decoration: none;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .welcome-text {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <a class="navbar-brand" href="dashboard.php">Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Pulsante Aggiorna -->
                <li class="nav-item">
                    <button class="btn btn-primary mr-2" onclick="location.reload();">Aggiorna</button>
                </li>
                <!-- Toggle Modalità Scura -->
                <li class="nav-item">
                    <button class="btn btn-outline-secondary mr-2" id="toggle-dark-mode">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>
                <!-- Logout -->
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-danger" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Contenuto Principale -->
    <div class="container">
        <p class="text-center welcome-text">Benvenuto, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-ticket-alt"></i>
                        <h5 class="card-title">Vendita e gestione degli skipass</h5>
                        <ul class="list-unstyled">
                            <li><a href="emission.php" class="btn btn-link">Emissione di skipass</a></li>
                            <li><a href="assistenza.php" class="btn btn-link">Assistenza</a></li>
                            <li><a href="rispondi_assistenza.php" class="btn btn-link">Risposte Assistenza</a></li>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-chart-line"></i>
                        <h5 class="card-title">Amministrazione e reportistica</h5>
                        <ul class="list-unstyled">
                            <li class="nav-item">
                            <a class="nav-link" href="finanza.php">Report Finanziari</a>
                            </li>
                            <li><a href="analisi_datiM.php" class="btn btn-link">Analisi dati</a></li>
                            <li><a href="Gestione_fiscale.php" class="btn btn-link">Gestione fiscale</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-users"></i>
                        <h5 class="card-title">Gestione utenti</h5>
                        <ul class="list-unstyled">
                            <li><a href="databaseC.php" class="btn btn-link">Database clienti</a></li>
                            <li><a href="fidelty_program.php" class="btn btn-link">Fidelity program</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Aggiungi altre card qui se necessario -->
        </div>
    </div>

    <!-- JavaScript per Bootstrap e Modalità Scura -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Effetto hover per le card
            $('.card').hover(
                function() {
                    $(this).css('transform', 'scale(1.05)');
                    $(this).css('box-shadow', '0 15px 25px rgba(0, 0, 0, 0.3)');
                },
                function() {
                    $(this).css('transform', 'scale(1)');
                    $(this).css('box-shadow', '0 4px 8px rgba(0, 0, 0, 0.1)');
                }
            );

            // Animazione di fade-in per le card
            $('.card').css('opacity', '0');
            $('.card').each(function(index) {
                $(this).delay(200 * index).animate({ opacity: 1 }, 500);
            });

            // Animazione di pulsazione per le icone
            $('.card i').hover(
                function() {
                    $(this).css('animation', 'pulse 1s infinite');
                },
                function() {
                    $(this).css('animation', 'none');
                }
            );

            // Definizione dell'animazione di pulsazione
            $('<style>@keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }</style>').appendTo('head');
        });

        // Modalità Scura
        document.addEventListener('DOMContentLoaded', function () {
            const toggleDarkModeButton = document.getElementById('toggle-dark-mode');
            const body = document.body;

            // Controlla la preferenza salvata
            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                toggleDarkModeButton.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                toggleDarkModeButton.innerHTML = '<i class="fas fa-moon"></i>';
            }

            // Evento di toggle
            toggleDarkModeButton.addEventListener('click', function () {
                body.classList.toggle('dark-mode');
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                    toggleDarkModeButton.innerHTML = '<i class="fas fa-sun"></i>';
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                    toggleDarkModeButton.innerHTML = '<i class="fas fa-moon"></i>';
                }
            });
        });
    </script>
</body>
</html>