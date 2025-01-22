<?php
// filepath: /c:/xampp/htdocs/Gestionale-skipass/finanza.php

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

// Recupera i dati delle vendite per tipologia di skipass
$sales_query = "SELECT category, COUNT(*) as sales_count, SUM(price) as total_revenue FROM skipasses GROUP BY category";
$sales_result = $conn->query($sales_query);

$sales_data = [];
$totalRevenue = 0;

if ($sales_result->num_rows > 0) {
    while ($row = $sales_result->fetch_assoc()) {
        $sales_data[] = $row;
        $totalRevenue += $row['total_revenue'];
    }
}

// Recupera i ricavi per tipo di sconto
$discount_query = "SELECT discount_type, SUM(price) as total_revenue FROM skipasses GROUP BY discount_type";
$discount_result = $conn->query($discount_query);

$discount_data = [];

if ($discount_result->num_rows > 0) {
    while ($row = $discount_result->fetch_assoc()) {
        $discount_data[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanza SkiPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            background-color: #1f1f1f;
        }
        .card {
            background-color: #ffffff;
            color: #000000; /* Testo nero per migliorare la visibilità */
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
            height: 200px;
            width: 200px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
        <h3 class="text-primary mb-4">Analisi Finanziaria SkiPass</h3>

        <?php if (!empty($sales_data)): ?>
            <div class="mb-4">
                <button class="btn btn-primary" onclick="location.reload();">Aggiorna</button>
            </div>
            <ul class="list-group mb-4">
                <?php foreach ($sales_data as $data): ?>
                    <li class="list-group-item">
                        <strong>Categoria:</strong> <?php echo htmlspecialchars($data['category']); ?>,
                        <strong>Vendite:</strong> <?php echo htmlspecialchars($data['sales_count']); ?>,
                        <strong>Ricavi:</strong> €<?php echo number_format($data['total_revenue'], 2); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="mb-5">
                <strong>Incasso totale: </strong>€<?php echo number_format($totalRevenue, 2); ?>
            </div>

            <div class="row">
                <!-- Grafico Vendite per Tipologia -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">Vendite per Tipologia</div>
                        <div class="card-body d-flex justify-content-center">
                            <div class="chart-container">
                                <canvas id="salesPieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Grafico Ricavi per Tipologia -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header bg-warning text-white">Ricavi per Tipologia</div>
                        <div class="card-body d-flex justify-content-center">
                            <div class="chart-container">
                                <canvas id="revenuePieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Grafico Ricavi per Tipo di Sconto -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">Ricavi per Tipo di Sconto</div>
                        <div class="card-body d-flex justify-content-center">
                            <div class="chart-container">
                                <canvas id="revenueDiscountPieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-danger">Nessun dato disponibile per l'analisi.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modalità oscura
        document.addEventListener('DOMContentLoaded', function() {
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

            <?php if (!empty($sales_data)): ?>
                // Dati per i grafici
                const salesData = <?php echo json_encode($sales_data); ?>;
                const discountData = <?php echo json_encode($discount_data); ?>;

                // Grafico Vendite per Tipologia
                const salesCategories = salesData.map(data => data.category);
                const salesCounts = salesData.map(data => data.sales_count);

                // Grafico Ricavi per Tipologia
                const revenueCategories = salesData.map(data => data.category);
                const revenueCounts = salesData.map(data => data.total_revenue);

                // Grafico Ricavi per Tipo di Sconto
                const discountTypes = discountData.map(data => data.discount_type);
                const revenueDiscountCounts = discountData.map(data => data.total_revenue);

                // Colori per i grafici
                const backgroundColors = [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ];
                const borderColors = [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ];

                // Configurazione del grafico Vendite per Tipologia
                const salesPieCtx = document.getElementById('salesPieChart').getContext('2d');
                new Chart(salesPieCtx, {
                    type: 'pie',
                    data: {
                        labels: salesCategories,
                        datasets: [{
                            data: salesCounts,
                            backgroundColor: backgroundColors.slice(0, salesCategories.length),
                            borderColor: borderColors.slice(0, salesCategories.length),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: false,
                            }
                        }
                    }
                });

                // Configurazione del grafico Ricavi per Tipologia
                const revenuePieCtx = document.getElementById('revenuePieChart').getContext('2d');
                new Chart(revenuePieCtx, {
                    type: 'pie',
                    data: {
                        labels: revenueCategories,
                        datasets: [{
                            data: revenueCounts,
                            backgroundColor: backgroundColors.slice(0, revenueCategories.length),
                            borderColor: borderColors.slice(0, revenueCategories.length),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: false,
                            }
                        }
                    }
                });

                // Configurazione del grafico Ricavi per Tipo di Sconto
                const revenueDiscountPieCtx = document.getElementById('revenueDiscountPieChart').getContext('2d');
                new Chart(revenueDiscountPieCtx, {
                    type: 'pie',
                    data: {
                        labels: discountTypes,
                        datasets: [{
                            data: revenueDiscountCounts,
                            backgroundColor: backgroundColors.slice(0, discountTypes.length),
                            borderColor: borderColors.slice(0, discountTypes.length),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: false,
                            }
                        }
                    }
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>