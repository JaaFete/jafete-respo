<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

// Definir o tipo de usuário
$tipo_usuario = $_SESSION['usuario']['tipo_usuario'];
$id_usuario_logado = $_SESSION['usuario']['id'];

// Incluir conexão com o banco de dados
include 'conexao.php';

// Verificar se o ID do evento foi passado pela URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

// Obter o ID do evento
$id_evento = $_GET['id'];

// Contar o número de participantes confirmados
$sql_participantes = "SELECT COUNT(*) AS total_participantes FROM participantes_evento WHERE id_evento = ?";
$stmt_participantes = $conn->prepare($sql_participantes);
if ($stmt_participantes) {
    $stmt_participantes->bind_param("i", $id_evento);
    $stmt_participantes->execute();
    $result_participantes = $stmt_participantes->get_result();
    $total_participantes = $result_participantes->fetch_assoc()['total_participantes'];
    $stmt_participantes->close();
} else {
    echo "Erro na consulta de participantes.";
    exit;
}

// Consulta para obter os detalhes do evento
$sql = "SELECT eventos.*, usuarios.nome AS nome_organizador, usuarios.id AS criador_id
        FROM eventos 
        JOIN usuarios ON eventos.id_usuario = usuarios.id
        WHERE eventos.id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $id_evento);
    $stmt->execute();
    $result = $stmt->get_result();
    $evento = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Erro na consulta de detalhes do evento.";
    exit;
}

// Verificar se o evento existe
if (!$evento) {
    echo "Evento não encontrado.";
    exit;
}

// Fechar a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($evento['nome']); ?> - Detalhes do Evento</title>
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

                        <!-- Mostrar "Criar Evento" somente para organizadores -->
                        <?php if ($tipo_usuario == 'organizador'): ?>
                            <li class="nav-item"><a class="nav-link" href="criar-evento.php">Criar Evento</a></li>
                        <?php endif; ?>

                        <li class="nav-item"><a class="nav-link" href="meus-eventos.php">Meus Eventos</a></li>
                        <li class="nav-item"><a class="nav-link" href="meu_perfil.php">Meu Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Detalhes do Evento -->
    <section class="container my-5">
        <h2><?php echo htmlspecialchars($evento['nome']); ?></h2>
        <p><strong>Organizador:</strong> <?php echo htmlspecialchars($evento['nome_organizador']); ?></p>
        <p><strong>Data:</strong> <?php echo date("d/m/Y H:i", strtotime($evento['data_evento'])); ?></p>
        <p><strong>Local:</strong> <?php echo htmlspecialchars($evento['local']); ?></p>
        <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($evento['descricao'])); ?></p>

        <!-- Mostrar o número de participantes apenas para o organizador -->
        <?php if ($tipo_usuario == 'organizador'): ?>
            <p><strong>Participantes confirmados:</strong> <?php echo $total_participantes; ?></p>
        <?php endif; ?>

        <!-- Opções de Editar e Apagar, visíveis apenas para o organizador criador do evento -->
        <?php if ($id_usuario_logado == $evento['criador_id']): ?>
            <div class="d-flex justify-content-between">
                <a href="editar-evento.php?id=<?php echo $evento['id']; ?>" class="btn btn-warning">Editar Evento</a>
                <a href="apagar-evento.php?id=<?php echo $evento['id']; ?>" onclick="return confirm('Tem certeza que deseja apagar este evento?');" class="btn btn-danger">Apagar Evento</a>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-between">
                <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
                <a href="participar-evento.php?id=<?php echo $evento['id']; ?>" class="btn btn-primary">Participar do Evento</a>
            </div>
        <?php endif; ?>
    </section>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 JAFETE JOSE. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
