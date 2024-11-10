<?php
// Iniciar a sessão
session_start();

include 'conexao.php';
// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    // Se não estiver logado, redireciona para o login
    header("Location: index.php");
    exit;
}

// Obter dados do usuário logado
$usuario = $_SESSION['usuario'];
$id_usuario = $usuario['id'];  // ID do usuário logado
$nome_usuario = $usuario['nome'];
$email_usuario = $usuario['email'];
$faculdade = $usuario['faculdade'];
$curso = $usuario['curso'];
$ano = $usuario['ano'];  // O ano está armazenado no banco, então recupera-se o valor

// Conectar ao banco de dados


// Atualizar os dados do perfil se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $faculdade = $_POST['faculdade'];
    $curso = $_POST['curso'];
    $ano = $_POST['ano'];

    // Atualizar os dados no banco de dados
    $sql = "UPDATE usuarios SET faculdade = ?, curso = ?, ano = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $faculdade, $curso, $ano, $id_usuario);

    if ($stmt->execute()) {
        // Atualização bem-sucedida, atualiza os dados na sessão
        $_SESSION['usuario']['faculdade'] = $faculdade;
        $_SESSION['usuario']['curso'] = $curso;
        $_SESSION['usuario']['ano'] = $ano;
        $mensagem = "Perfil atualizado com sucesso!";
    } else {
        $mensagem = "Erro ao atualizar o perfil. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Unilicungo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegação -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Unilicungo</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="meus-eventos.php">Meus Eventos</a></li>
                        <li class="nav-item"><a class="nav-link" href="meu_perfil.php">Meu Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Banner -->
    <section class="banner bg-primary text-white text-center py-5">
        <h2>Meu Perfil</h2>
        <p>Aqui você pode ver e editar suas informações pessoais.</p>
    </section>

    <!-- Perfil do Usuário -->
    <section class="container my-5">
        <h3>Informações Pessoais</h3>
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-info"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" value="<?php echo htmlspecialchars($nome_usuario); ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($email_usuario); ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="faculdade" class="form-label">Faculdade</label>
                <input type="text" class="form-control" id="faculdade" name="faculdade" value="<?php echo htmlspecialchars($faculdade); ?>" required>
            </div>

            <div class="mb-3">
                <label for="curso" class="form-label">Curso</label>
                <input type="text" class="form-control" id="curso" name="curso" value="<?php echo htmlspecialchars($curso); ?>" required>
            </div>

            <div class="mb-3">
                <label for="ano" class="form-label">Ano</label>
                <select class="form-control" id="ano" name="ano" required>
                    <option value="1" <?php echo $ano == 1 ? 'selected' : ''; ?>>1º Ano</option>
                    <option value="2" <?php echo $ano == 2 ? 'selected' : ''; ?>>2º Ano</option>
                    <option value="3" <?php echo $ano == 3 ? 'selected' : ''; ?>>3º Ano</option>
                    <option value="4" <?php echo $ano == 4 ? 'selected' : ''; ?>>4º Ano</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
    </section>

    <!-- Rodapé -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 JAFETE JOSE. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
