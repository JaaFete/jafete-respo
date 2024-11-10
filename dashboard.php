    <?php
    // Iniciar a sessão
    session_start();

    // Verificar se o usuário está logado
    if (!isset($_SESSION['usuario'])) {
        // Se não estiver logado, redireciona para o login
        header("Location: index.php");
        exit;
    }

    // Obter dados do usuário logado
    $usuario = $_SESSION['usuario'];
    $nome_usuario = $usuario['nome'];
    $email_usuario = $usuario['email'];
    $tipo_usuario = $usuario['tipo_usuario']; // Pode ser 'normal' ou 'organizador'

    // Incluir a conexão com o banco de dados
    include 'conexao.php';

    // Verificar se existe um termo de pesquisa
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

    // Se houver um termo de pesquisa, adicionar à consulta SQL
    $sql = "SELECT eventos.*, usuarios.nome AS nome_organizador 
            FROM eventos 
            JOIN usuarios ON eventos.id_usuario = usuarios.id";

    // Adicionar a cláusula WHERE para filtrar eventos
    if (!empty($searchTerm)) {
        $searchTerm = "%" . $conn->real_escape_string($searchTerm) . "%";
        $sql .= " WHERE eventos.nome LIKE '$searchTerm' OR eventos.local LIKE '$searchTerm'";
    }

    $sql .= " ORDER BY eventos.data_evento ASC";

    // Executar a consulta
    $result = $conn->query($sql);

    // Fechar a conexão com o banco de dados
    $conn->close();
    ?>

    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Unilicungo - Eventos Universitarios</title>
        <!-- Link para o Bootstrap CSS -->
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
                            <li class="nav-item"><a class="nav-link active" href="index.php">Início</a></li>

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

        <!-- Banner -->
        <section class="banner bg-primary text-white text-center py-5">
            <h2>Bem-vindo a plataforma de eventos da Unilicungo!</h2>
            <p>Encontre, crie e participe dos melhores eventos universitários.</p>
        </section>

        <!-- Busca de Eventos -->
        <section class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar eventos..." id="search-input" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Lista de Eventos -->
        <section class="container my-5">
            <h2 class="text-center">Eventos - Recentes</h2>

            <div class="row">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($evento = $result->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <!-- Exibe a imagem do evento, usando uma imagem padrão caso não tenha sido enviada -->
                                <img src="<?php echo htmlspecialchars($evento['imagem'] ?? 'uploads/default-image.jpg'); ?>" 
                                    class="card-img-top" 
                                    alt="Imagem do Evento">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($evento['nome']); ?></h5>
                                    <p><strong>Data:</strong> <?php echo date("d/m/Y H:i", strtotime($evento['data_evento'])); ?></p>
                                    <p><strong>Local:</strong> <?php echo htmlspecialchars($evento['local']); ?></p>
                                    <a href="detalhes.php?id=<?php echo $evento['id']; ?>" class="btn btn-primary">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="alert alert-warning">Ainda não há eventos disponíveis.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Rodapé -->
        <footer class="bg-dark text-white text-center py-3">
            <p>&copy; 2024 JAFETE JOSE. Todos os direitos reservados.</p>
        </footer>

        <!-- Script do Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    </body>
    </html>
