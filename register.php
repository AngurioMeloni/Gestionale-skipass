<?php
session_start();

// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skipassmanagement";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $phone_number = $_POST['phone_number'];
    $date_of_birth = $_POST['date_of_birth'];

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Hash della password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Inserimento dell'utente nel database
        $stmt = $conn->prepare("INSERT INTO Users (name, surname, email, password, role, phone_number, date_of_birth) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $surname, $email, $hashed_password, $role, $phone_number, $date_of_birth);

        if ($stmt->execute()) {
            $_SESSION['registered'] = true;
            $_SESSION['username'] = $email;
            $_SESSION['name'] = $name;
            $_SESSION['loggedin'] = true;
            header('Location: dashboard.php'); // Redirect to the dashboard page
            exit;
        } else {
            $error = 'Error: ' . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
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
        h2 {
            font-family: 'Montserrat', sans-serif;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .container:hover {
            transform: scale(1.02);
        }
        .input-field i {
            color: #007bff;
        }
        .btn {
            background-color: #007bff;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
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
        <h2 class="center-align">Register</h2>
        <?php if (isset($error)): ?>
            <div class="card-panel red lighten-2 white-text"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="register.php" method="post">
            <div class="input-field">
                <i class="fas fa-user prefix"></i>
                <input type="text" id="name" name="name" required>
                <label for="name">Name</label>
            </div>
            <div class="input-field">
                <i class="fas fa-user prefix"></i>
                <input type="text" id="surname" name="surname" required>
                <label for="surname">Surname</label>
            </div>
            <div class="input-field">
                <i class="fas fa-calendar-alt prefix"></i>
                <input type="date" id="date_of_birth" name="date_of_birth" required>
                <label for="date_of_birth">Date of Birth</label>
            </div>
            <div class="input-field">
                <i class="fas fa-envelope prefix"></i>
                <input type="email" id="email" name="email" required>
                <label for="email">Email</label>
            </div>
            <div class="input-field">
                <i class="fas fa-user-tag prefix"></i>
                <input type="text" id="role" name="role" placeholder="cliente/amministratore" required>
                <label for="role">Role</label>
            </div>
            <div class="input-field">
                <i class="fas fa-phone prefix"></i>
                <input type="text" id="phone_number" name="phone_number" required>
                <label for="phone_number">Phone Number</label>
            </div>
            <div class="input-field">
                <i class="fas fa-lock prefix"></i>
                <input type="password" id="password" name="password" required>
                <label for="password">Password</label>
            </div>
            <div class="input-field">
                <i class="fas fa-lock prefix"></i>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <label for="confirm_password">Confirm Password</label>
            </div>
            <button type="submit" class="btn waves-effect waves-light btn-block">Register</button>
        </form>
        <p class="center-align">Hai già un account? <a href="login.php">Accedi qui</a></p>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        $(document).ready(function() {
            // Effetto hover per il container
            $('.container').hover(
                function() {
                    $(this).css('transform', 'scale(1.02)');
                },
                function() {
                    $(this).css('transform', 'scale(1)');
                }
            );

            // Effetto hover per i pulsanti
            $('.btn').hover(
                function() {
                    $(this).css('background-color', '#0056b3');
                },
                function() {
                    $(this).css('background-color', '#007bff');
                }
            );

            // Effetto hover per il card-panel
            $('.card-panel').hover(
                function() {
                    $(this).css('background-color', '#e57373');
                },
                function() {
                    $(this).css('background-color', '#ef5350');
                }
            );

            // Nuovo effetto visivo: animazione di input focus
            $('input').focus(function() {
                $(this).css('box-shadow', '0 0 5px rgba(81, 203, 238, 1)');
            }).blur(function() {
                $(this).css('box-shadow', 'none');
            });
        });
    </script>
</body>
</html>