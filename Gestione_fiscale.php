<?php
// filepath: /c:/xampp/htdocs/Gestionale-skipass/Gestione_fiscale.php

session_start();

// Controllo accesso: solo utenti loggati e admin possono accedere
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

// Simuliamo i valori delle tasse e dei profitti per la torta
$total_taxes = 483.12;
$net_profit = 1712.88;
$total_amount = $total_taxes + $net_profit;

// Recupero ricavi mensili dal DB (esempio)
$revenue_query = "
    SELECT YEAR(created_at) AS year, MONTH(created_at) AS month, SUM(price) AS total_revenue
    FROM skipasses
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at), MONTH(created_at)
";
$revenue_result = $conn->query($revenue_query);

$revenue_data = [];
$total_revenue = 0;
if ($revenue_result && $revenue_result->num_rows > 0) {
    while ($row = $revenue_result->fetch_assoc()) {
        $revenue_data[] = $row;
        $total_revenue += (float)$row['total_revenue'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Fiscale SkiPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        }
        .dark-mode .card {
            background-color: #2c2c2c;
            color: #ffffff;
            border: 1px solid #555555;
        }
        /* Rendi i contenitori dei due grafici della stessa dimensione */
        .chart-container {
            position: relative;
            margin: auto;
            width: 300px;
            height: 300px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <button id="toggle-dark-mode" class="btn btn-secondary me-3">
                        <span id="dark-mode-icon" class="fas fa-moon"></span> Modalità Scura
                    </button>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h3 class="text-primary mb-4">Gestione Fiscale SkiPass</h3>

    <!-- Sezione Report Ricavi Mensili -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">Report Ricavi Mensili</div>
                <div class="card-body d-flex justify-content-center">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sezione Tasse e Profitti (Tabella) -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-white">Tasse e Profitti</div>
                <div class="card-body">
                    <p><strong>Ricavi Totali:</strong> €<?php echo number_format($total_revenue, 2); ?></p>
                    <p><strong>Tasse (22%):</strong> €<?php echo number_format($total_taxes, 2); ?></p>
                    <p><strong>Profitti Netti:</strong> €<?php echo number_format($net_profit, 2); ?></p>
                    <!-- Grafico a torta su Tasse vs Profitti -->
                    <div class="chart-container mt-3">
                        <canvas id="taxProfitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sezione Fatture, e altri contenuti -->

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestione Dark Mode
        const toggleDarkMode = document.getElementById('toggle-dark-mode');
        const darkModeIcon = document.getElementById('dark-mode-icon');
        const body = document.body;
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            darkModeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            darkModeIcon.classList.replace('fa-sun', 'fa-moon');
        }
        toggleDarkMode.addEventListener('click', function () {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                darkModeIcon.classList.replace('fa-moon', 'fa-sun');
            } else {
                localStorage.setItem('darkMode', 'disabled');
                darkModeIcon.classList.replace('fa-sun', 'fa-moon');
            }
        });

        // Ricavi Mensili
        const revenueData = <?php echo json_encode($revenue_data); ?>;
        const revenueLabels = revenueData.map(d => `Mese ${d.month}-${d.year}`);
        const revenueCounts = revenueData.map(d => parseFloat(d.total_revenue));

        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Ricavi (€)',
                    data: revenueCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Creazione grafico a torta Tasse vs Profitti
        const totalTaxes = <?php echo $total_taxes; ?>;
        const netProfit = <?php echo $net_profit; ?>;
        const taxProfitCtx = document.getElementById('taxProfitChart').getContext('2d');
        new Chart(taxProfitCtx, {
            type: 'pie',
            data: {
                labels: ['Tasse (22%)', 'Profitti Netti'],
                datasets: [{
                    data: [totalTaxes, netProfit],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)', // Tasse
                        'rgba(75, 192, 192, 0.6)'  // Profitti Netti
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                }
            }
        });
    });
</script>
</body>
</html>