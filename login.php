<?php
// filepath: /c:/xampp/htdocs/5IE-TEP/Gestionale-skipass/login.php
session_start();

// Connessione al database
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "skipassmanagement";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $errors = [];

    if (empty($email) || empty($password)) {
        $errors[] = 'Tutti i campi sono obbligatori.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email non valida.';
    }

    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Errore nella preparazione della query: " . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $row['username'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['role'] = $row['role']; // Memorizza il ruolo nella sessione
                header('Location: dashboard.php');
                exit();
            } else {
                $errors[] = "Password errata.";
            }
        } else {
            $errors[] = "Nessun utente trovato con questa email.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        .login-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .login-card {
            padding: 30px;
            border: none;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, color 0.3s, transform 0.3s, box-shadow 0.3s;
            width: 100%;
            max-width: 600px; /* Aumentata la larghezza massima */
        }
        .dark-mode .login-card {
            background-color: #1e1e1e;
            color: #ffffff;
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.1);
        }
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        .form-label {
            font-size: 0.95rem;
        }
        .form-control {
            font-size: 0.95rem;
            padding-right: 2.5rem; /* Spazio per l'icona */
        }
        .input-group .input-group-text {
            background-color: transparent;
            border: none;
            color: #aaa;
        }
        .input-group .form-control:focus + .input-group-text {
            color: #007bff;
        }
        .btn-primary {
            font-size: 1rem;
            padding: 0.6rem;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .navbar-brand {
            font-weight: bold;
        }
        .navbar-nav .nav-link {
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
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Registrati</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="toggle-dark-mode"><i class="fas fa-moon"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Container -->
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="login-card mx-auto">
                    <h2 class="text-center mb-4">Login</h2>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <div><?php echo htmlspecialchars($error); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="login.php">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="basic-addon1" id="email" name="email" required>
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text" id="basic-addon2"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="basic-addon2" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Accedi</button>
                    </form>
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