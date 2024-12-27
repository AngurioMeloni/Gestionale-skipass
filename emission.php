<?php
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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function emettiSkipass($conn, $userId, $tipoSkipass, $dataInizio, $dataFine, $area, $dataNascita, $professionista, $tesseratoFISI, $disabilita) {
    // Determinazione del prezzo base in base al tipo di skipass
    $prezzo = 0;
    switch ($tipoSkipass) {
        case 'giornaliero':
            $prezzo = 66.50;
            break;
        case 'settimanale':
            $prezzo = 150.00;
            break;
        case 'stagionale':
            $prezzo = 500.00;
            break;
        case 'orario':
            $prezzo = 10.00;
            break;
        default:
            echo "Tipo di skipass non valido";
            return;
    }

    // Calcolo dell'età
    $eta = date_diff(date_create($dataNascita), date_create('today'))->y;

    // Applicazione degli sconti basati sull'età
    if ($eta <= 6) {
        $prezzo = 0; // Bambini nati dopo il 2016
    } elseif ($eta >= 7 && $eta <= 16) {
        $prezzo *= 0.70; // Junior nati tra il 2007 e il 2016
    } elseif ($eta >= 64) {
        $prezzo *= 0.90; // Senior nati nel 1959 o prima
    }

    // Applicazione degli sconti per professionisti del settore
    if ($professionista) {
        $prezzo *= 0.70; // Sconto del 30% per maestri di sci e guide alpine
    }

    // Applicazione degli sconti per tesserati FISI
    if ($tesseratoFISI) {
        $prezzo -= 5; // Sconto di €5 per tesserati FISI
    }

    // Applicazione degli sconti per persone con disabilità
    if ($disabilita >= 51) {
        $prezzo *= 0.80; // Sconto del 20% per persone con disabilità del 51% o superiore
    }

    $stmt = $conn->prepare("INSERT INTO skipass (user_id, tipo, prezzo, data_inizio, data_fine, area) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdss", $userId, $tipoSkipass, $prezzo, $dataInizio, $dataFine, $area);
    
    if ($stmt->execute()) {
        echo "Skipass emesso con successo!";
    } else {
        echo "Errore nell'emissione dello skipass: " . $stmt->error;
    }
    
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $tipoSkipass = $_POST['tipo_skipass'];
    $dataInizio = $_POST['data_inizio'];
    $dataFine = $_POST['data_fine'];
    $area = $_POST['area'];
    $dataNascita = $_POST['data_nascita'];
    $professionista = isset($_POST['professionista']) ? 1 : 0;
    $tesseratoFISI = isset($_POST['tesserato_fisi']) ? 1 : 0;
    $disabilita = $_POST['disabilita'];

    emettiSkipass($conn, $userId, $tipoSkipass, $dataInizio, $dataFine, $area, $dataNascita, $professionista, $tesseratoFISI, $disabilita);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emissione Skipass</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }
        h1 {
            font-family: 'Montserrat', sans-serif;
            text-align: center;
            margin-bottom: 2rem;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-height: 90vh;
            overflow-y: auto;
            width: 100%;
            max-width: 600px;
        }
        .input-field {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .input-field i {
            color: #007bff;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 10px;
        }
        .input-field label {
            position: absolute;
            top: -20px;
            left: 40px;
            font-size: 14px;
            color: #007bff;
        }
        .input-field select, .input-field input {
            padding-left: 40px;
            width: 100%;
        }
        .form-check {
            margin-bottom: 1.5rem;
        }
        .btn {
            background-color: #007bff;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .card-panel {
            transition: background-color 0.3s;
        }
        .card-panel:hover {
            background-color: #e57373;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Emissione Skipass</h1>
        <form action="emission.php" method="post">
            <div class="input-field">
                <i class="fas fa-ticket-alt prefix"></i>
                <label for="tipo_skipass">Tipo di Skipass</label>
                <select class="form-control" id="tipo_skipass" name="tipo_skipass" required>
                    <option value="giornaliero">Giornaliero</option>
                    <option value="settimanale">Settimanale</option>
                    <option value="stagionale">Stagionale</option>
                    <option value="orario">Orario</option>
                </select>
            </div>
            <div class="input-field">
                <i class="fas fa-calendar-alt prefix"></i>
                <label for="data_inizio">Data Inizio</label>
                <input type="date" class="form-control" id="data_inizio" name="data_inizio" required>
            </div>
            <div class="input-field">
                <i class="fas fa-calendar-alt prefix"></i>
                <label for="data_fine">Data Fine</label>
                <input type="date" class="form-control" id="data_fine" name="data_fine" required>
            </div>
            <div class="input-field">
                <i class="fas fa-map-marker-alt prefix"></i>
                <label for="area">Area</label>
                <input type="text" class="form-control" id="area" name="area" required>
            </div>
            <div class="input-field">
                <i class="fas fa-birthday-cake prefix"></i>
                <label for="data_nascita">Data di Nascita</label>
                <input type="date" class="form-control" id="data_nascita" name="data_nascita" required>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="professionista" name="professionista">
                <label class="form-check-label" for="professionista">Professionista del Settore</label>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="tesserato_fisi" name="tesserato_fisi">
                <label class="form-check-label" for="tesserato_fisi">Tesserato FISI</label>
            </div>
            <div class="input-field">
                <i class="fas fa-wheelchair prefix"></i>
                <label for="disabilita">Percentuale di Disabilità</label>
                <input type="number" class="form-control" id="disabilita" name="disabilita" min="0" max="100" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Emetti Skipass</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Animazione di input focus
            $('input, select').focus(function() {
                $(this).css('box-shadow', '0 0 5px rgba(81, 203, 238, 1)');
            }).blur(function() {
                $(this).css('box-shadow', 'none');
            });
        });
    </script>
</body>
</html>