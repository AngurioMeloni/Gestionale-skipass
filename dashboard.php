<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
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
        }
        h1, h5 {
            font-family: 'Montserrat', sans-serif;
        }
        .container {
            margin-top: 50px;
            position: relative;
            z-index: 2;
        }
        .carousel {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 1;
        }
        .carousel-inner img {
            width: 100%;
            height: 100vh;
            object-fit: cover;
        }
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .card i {
            font-size: 4rem;
            color: #007bff;
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
        }
        .card-body {
            flex: 1;
        }
        .welcome-text {
            font-size: 2.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Inizio Slider di Immagini -->
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="3000">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="5"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="6"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="7"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="8"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="9"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/1.jpg" class="d-block w-100" alt="Image 1">
            </div>
            <div class="carousel-item">
                <img src="img/2.jpg" class="d-block w-100" alt="Image 2">
            </div>
            <div class="carousel-item">
                <img src="img/3.jpg" class="d-block w-100" alt="Image 3">
            </div>
            <div class="carousel-item">
                <img src="img/4.jpg" class="d-block w-100" alt="Image 4">
            </div>
            <div class="carousel-item">
                <img src="img/5.jpg" class="d-block w-100" alt="Image 5">
            </div>
            <div class="carousel-item">
                <img src="img/6.jpg" class="d-block w-100" alt="Image 6">
            </div>
            <div class="carousel-item">
                <img src="img/7.jpg" class="d-block w-100" alt="Image 7">
            </div>
            <div class="carousel-item">
                <img src="img/8.jpg" class="d-block w-100" alt="Image 8">
            </div>
            <div class="carousel-item">
                <img src="img/9.jpg" class="d-block w-100" alt="Image 9">
            </div>
            <div class="carousel-item">
                <img src="img/10.jpg" class="d-block w-100" alt="Image 10">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
    <!-- Fine Slider di Immagini -->

    <div class="container">
        <p class="text-center welcome-text">Benvenuto, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-ticket-alt"></i>
                        <h5 class="card-title">Vendita e gestione degli skipass</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="btn btn-link">Emissione di skipass</a></li>
                            <li><a href="#" class="btn btn-link">Personalizzazione degli skipass</a></li>
                            <li><a href="#" class="btn btn-link">Prenotazioni online</a></li>
                            <li><a href="#" class="btn btn-link">Ricariche online</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-chart-line"></i>
                        <h5 class="card-title">Amministrazione e reportistica</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="btn btn-link">Report finanziari</a></li>
                            <li><a href="#" class="btn btn-link">Analisi dati</a></li>
                            <li><a href="#" class="btn btn-link">Gestione fiscale</a></li>
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
                            <li><a href="#" class="btn btn-link">Fidelity program</a></li>
                            <li><a href="#" class="btn btn-link">Gestione gruppi</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Effetto hover per le card
            $('.card').hover(
                function() {
                    $(this).css('transform', 'scale(1.05)');
                    $(this).css('box-shadow', '0 10px 20px rgba(0, 0, 0, 0.2)');
                },
                function() {
                    $(this).css('transform', 'scale(1)');
                    $(this).css('box-shadow', '0 4px 8px rgba(0, 0, 0, 0.1)');
                }
            );

            // Nuovo effetto visivo: animazione di fade-in per le card
            $('.card').css('opacity', '0');
            $('.card').each(function(index) {
                $(this).delay(200 * index).animate({ opacity: 1 }, 500);
            });

            // Nuovo effetto visivo: animazione di pulsazione per le icone
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
    </script>
</body>
</html>