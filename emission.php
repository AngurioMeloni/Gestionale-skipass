<?php
session_start();
include 'db_connection.php';

function emettiSkipass($userId, $tipoSkipass, $dataInizio, $dataFine, $area) {
    $conn = openConnection();
    
    // Determinazione del prezzo in base al tipo di skipass
    $prezzo = 0;
    switch ($tipoSkipass) {
        case 'giornaliero':
            $prezzo = 30.00;
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

    $stmt = $conn->prepare("INSERT INTO skipass (user_id, tipo, prezzo, data_inizio, data_fine, area) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdss", $userId, $tipoSkipass, $prezzo, $dataInizio, $dataFine, $area);
    
    if ($stmt->execute()) {
        echo "Skipass emesso con successo!";
    } else {
        echo "Errore nell'emissione dello skipass: " . $stmt->error;
    }
    
    $stmt->close();
    closeConnection($conn);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $tipoSkipass = $_POST['tipo_skipass'];
    $dataInizio = $_POST['data_inizio'];
    $dataFine = $_POST['data_fine'];
    $area = $_POST['area'];
    
    emettiSkipass($userId, $tipoSkipass, $dataInizio, $dataFine, $area);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emissione Skipass</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Emissione Skipass</h1>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <p class="text-center">Devi essere registrato per emettere uno skipass.</p>
        <?php else: ?>
            <form action="emission.php" method="post">
                <div class="form-group">
                    <label for="tipo_skipass">Tipo di Skipass</label>
                    <select class="form-control" id="tipo_skipass" name="tipo_skipass" required>
                        <option value="giornaliero">Giornaliero</option>
                        <option value="settimanale">Settimanale</option>
                        <option value="stagionale">Stagionale</option>
                        <option value="orario">Orario</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data_inizio">Data Inizio</label>
                    <input type="date" class="form-control" id="data_inizio" name="data_inizio" required>
                </div>
                <div class="form-group">
                    <label for="data_fine">Data Fine</label>
                    <input type="date" class="form-control" id="data_fine" name="data_fine" required>
                </div>
                <div class="form-group">
                    <label for="area">Area</label>
                    <input type="text" class="form-control" id="area" name="area" required>
                </div>
                <button type="submit" class="btn btn-primary">Emetti Skipass</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>