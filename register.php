<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Here you would normally save the user to a database
        // For demonstration purposes, we'll just set a session variable
        $_SESSION['registered'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['loggedin'] = true;
        header('Location: dashboard.php'); // Redirect to the dashboard page
        exit;
    }
}
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
                <input type="text" id="username" name="username" required>
                <label for="username">Username</label>
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