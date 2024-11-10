<?php
session_start();

// Verificar se o formulário de login foi enviado
$error = "";
$sucesso = "";

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco-dados";  // Altere para o nome do seu banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coletar dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verificar se o e-mail e a senha estão corretos
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Login bem-sucedido
        $_SESSION['usuario'] = $result->fetch_assoc();
        header("Location: dashboard.php");  // Redirecionar para a página principal após login
        exit;
    } else {
        $error = "E-mail ou senha incorretos!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Unilicungo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #4e73df, #1e3d7c);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
        }

        .form-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .form-container .error {
            color: red;
            font-size: 14px;
            text-align: center;
        }

        .form-container .sucesso {
            color: green;
            font-size: 14px;
            text-align: center;
        }

        .btn-primary {
            width: 100%;
        }

        .form-container p {
            text-align: center;
        }

        .brand-title {
            font-size: 36px;
            color: #1e3d7c;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <!-- Título Unilicungo -->
        <h1 class="brand-title">Unilicungo</h1>

        <!-- Exibir mensagem de erro -->
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulário de Login -->
        <form action="index.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>

        <p class="mt-3">Ainda não tem uma conta? <a href="formulario.php">Cadastre-se aqui</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
