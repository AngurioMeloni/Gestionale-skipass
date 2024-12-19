<?php
session_start();

// Verifica se l'utente è loggato e se è un amministratore o un operatore
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] !== 'amministratore' && $_SESSION['role'] !== 'operatore') {
    header('Location: dashboard.php');
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

// Recupera tutti gli utenti dal database
$sql = "SELECT * FROM Users";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Utenti</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        h1 {
            font-family: 'Montserrat', sans-serif;
            margin-top: 20px;
        }
        .container {
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 90%;
            overflow-x: auto;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .fade-in {
            opacity: 0;
            animation: fadeIn 1s forwards;
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        .btn-refresh {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 20px;
        }
        .btn-refresh:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container fade-in">
        <h1 class="text-center">Database Utenti</h1>
        <button class="btn-refresh" onclick="refreshTable()">Aggiorna</button>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Ruolo</th>
                        <th>Numero di Telefono</th>
                        <th>Data di Nascita</th>
                    </tr>
                </thead>
                <tbody id="clientTable">
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['surname']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['password']}</td>
                                    <td>{$row['role']}</td>
                                    <td>{$row['phone_number']}</td>
                                    <td>{$row['date_of_birth']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>Nessun utente trovato</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function refreshTable() {
            location.reload();
        }

        $(document).ready(function() {
            // Effetto hover per le righe della tabella
            $('tr').hover(
                function() {
                    $(this).css('background-color', '#ddd');
                },
                function() {
                    $(this).css('background-color', '');
                }
            );
        });
    </script>
</body>
</html>