<?php
// filepath: /c:/xampp/htdocs/Gestionale-skipass/analisi_datiM.php

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

// Recupera i ricavi per mese (basati sul timestamp di emissione)
$revenue_month_query = "
    SELECT YEAR(created_at) AS year, MONTH(created_at) AS month, SUM(price) AS total_revenue
    FROM skipasses
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at), MONTH(created_at)
";
$revenue_month_result = $conn->query($revenue_month_query);

$revenue_month_data = [];
if ($revenue_month_result && $revenue_month_result->num_rows > 0) {
    while ($row = $revenue_month_result->fetch_assoc()) {
        $revenue_month_data[] = $row;
    }
}

// Recupera il numero di skipass venduti per mese (basati sul timestamp di emissione)
$sales_month_query = "
    SELECT YEAR(created_at) AS year, MONTH(created_at) AS month, COUNT(*) AS sales_count
    FROM skipasses
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at), MONTH(created_at)
";
$sales_month_result = $conn->query($sales_month_query);

$sales_month_data = [];
if ($sales_month_result && $sales_month_result->num_rows > 0) {
    while ($row = $sales_month_result->fetch_assoc()) {
        $sales_month_data[] = $row;
    }
}

// Recupera i ricavi per area (basati sul timestamp di emissione)
$revenue_area_query = "
    SELECT area, SUM(price) AS total_revenue
    FROM skipasses
    GROUP BY area
";
$revenue_area_result = $conn->query($revenue_area_query);

$revenue_area_data = [];
if ($revenue_area_result && $revenue_area_result->num_rows > 0) {
    while ($row = $revenue_area_result->fetch_assoc()) {
        $revenue_area_data[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisi Dati SkiPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
        .chart-container {
            position: relative;
            margin: auto;
            height: 250px;
            width: 250px;
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
                            <i class="fas fa-moon"></i>
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
        <h3 class="text-primary mb-4">Analisi Dati SkiPass</h3>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">Ricavi per Mese</div>
                    <div class="card-body d-flex justify-content-center">
                        <div class="chart-container">
                            <canvas id="revenueMonthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">Skipass Venduti per Mese</div>
                    <div class="card-body d-flex justify-content-center">
                        <div class="chart-container">
                            <canvas id="salesMonthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4 offset-md-3">
                <div class="card">
                    <div class="card-header bg-secondary text-white">Ricavi per Area</div>
                    <div class="card-body d-flex justify-content-center">
                        <div class="chart-container">
                            <canvas id="revenueAreaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleDarkMode = document.getElementById('toggle-dark-mode');
            const body = document.body;
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

            const revenueMonthData = <?php echo json_encode($revenue_month_data); ?>;
            const salesMonthData = <?php echo json_encode($sales_month_data); ?>;
            const revenueAreaData = <?php echo json_encode($revenue_area_data); ?>;

            // Ricavi per Mese
            const revenueMonthLabels = revenueMonthData.map(d => `Mese ${d.month}-${d.year}`);
            const revenueMonthCounts = revenueMonthData.map(d => parseFloat(d.total_revenue));

            // Skipass Venduti per Mese
            const salesMonthLabels = salesMonthData.map(d => `Mese ${d.month}-${d.year}`);
            const salesMonthCounts = salesMonthData.map(d => parseInt(d.sales_count));

            // Istogramma per Ricavi Mensili
            const revenueMonthCtx = document.getElementById('revenueMonthChart').getContext('2d');
            new Chart(revenueMonthCtx, {
                type: 'bar',
                data: {
                    labels: revenueMonthLabels,
                    datasets: [{
                        label: 'Ricavi (€)',
                        data: revenueMonthCounts,
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
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Istogramma per Skipass Venduti Mensilmente
            const salesMonthCtx = document.getElementById('salesMonthChart').getContext('2d');
            new Chart(salesMonthCtx, {
                type: 'bar',
                data: {
                    labels: salesMonthLabels,
                    datasets: [{
                        label: 'Skipass Venduti',
                        data: salesMonthCounts,
                        backgroundColor: 'rgba(255, 206, 86, 0.6)',
                        borderColor: 'rgba(255, 206, 86, 1)',
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
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Grafico a torta per Ricavi per Area
            const revenueAreaLabels = revenueAreaData.map(d => d.area);
            const revenueAreaCounts = revenueAreaData.map(d => parseFloat(d.total_revenue));

            const revenueAreaCtx = document.getElementById('revenueAreaChart').getContext('2d');
            new Chart(revenueAreaCtx, {
                type: 'pie',
                data: {
                    labels: revenueAreaLabels,
                    datasets: [{
                        label: 'Ricavi (€)',
                        data: revenueAreaCounts,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)',
                            'rgba(199, 199, 199, 0.6)',
                            'rgba(83, 102, 255, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(199, 199, 199, 1)',
                            'rgba(83, 102, 255, 1)'
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