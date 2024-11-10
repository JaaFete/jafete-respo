<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

// Incluir conexão com o banco de dados
include 'conexao.php';

// Obter o ID do usuário logado
$id_usuario = $_SESSION['usuario']['id'];

// Consultar eventos que o usuário está participando
$sql = "SELECT eventos.* FROM eventos 
        JOIN participantes_evento ON eventos.id = participantes_evento.id_evento 
        WHERE participantes_evento.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Eventos - Unilicungo</title>
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

    <!-- Listar Meus Eventos -->
    <section class="container my-5">
        <h2>Meus Eventos</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="list-group">
                <?php while ($evento = $result->fetch_assoc()): ?>
                    <a href="detalhes-evento.php?id=<?php echo $evento['id']; ?>" class="list-group-item list-group-item-action">
                        <h5><?php echo htmlspecialchars($evento['nome']); ?></h5>
                        <p><?php echo date("d/m/Y H:i", strtotime($evento['data_evento'])); ?></p>
                        <p><?php echo htmlspecialchars($evento['local']); ?></p>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Você ainda não está participando de nenhum evento.</p>
        <?php endif; ?>

    </section>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 JAFETE JOSE. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Fechar a conexão com o banco de dados
$stmt->close();
$conn->close();
?>
