<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado e se é organizador
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] != 'organizador') {
    // Se não for organizador, redireciona para o login
    header("Location: index.php");
    exit;
}

// Conectar ao banco de dados
include 'conexao.php';

// Obter o ID do usuário logado
$id_usuario = $_SESSION['usuario']['id']; // Supondo que o id do usuário está armazenado na sessão

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber os dados do formulário
    $nome_evento = $_POST['nome'];
    $descricao_evento = $_POST['descricao'];
    $data_evento = $_POST['data_evento'];
    $local_evento = $_POST['local'];
    $tipo_evento = $_POST['tipo_evento'];

    // Processar a imagem (caso tenha sido enviada)
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
    $imagem_nome = $_FILES['imagem']['name'];
    $imagem_temp = $_FILES['imagem']['tmp_name'];
    $imagem_destino = 'uploads/' . $imagem_nome; // Caminho relativo
    move_uploaded_file($imagem_temp, $imagem_destino);
} else {
    $imagem_destino = null;
}

// Inserir os dados no banco de dados
$sql = "INSERT INTO eventos (nome, descricao, data_evento, local, tipo_evento, imagem, id_usuario) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $nome_evento, $descricao_evento, $data_evento, $local_evento, $tipo_evento, $imagem_destino, $id_usuario);

if ($stmt->execute()) {
    echo "<script>alert('Evento criado com sucesso!');</script>";
} else {
    echo "<script>alert('Erro ao criar evento. Tente novamente.');</script>";
}

// Fechar a conexão com o banco de dados
$stmt->close();
$conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Evento - Unilicungo</title>
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
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Início</a></li>
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
        <h2>Criar Novo Evento</h2>
        <p>Preencha os campos abaixo para criar um novo evento na plataforma.</p>
    </section>

    <!-- Formulário para Criar Evento -->
    <section class="container my-5">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Evento</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tipo_evento" class="form-label">Tipo de Evento</label>
                        <select class="form-select" id="tipo_evento" name="tipo_evento" required>
                            <option value="palestra">Palestra</option>
                            <option value="workshop">Apresentação de Projetos</option>
                            <option value="festa">Festa</option>
                            <option value="seminario">Seminário</option>
                            <option value="seminario">Atletismo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição do Evento</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="4" required></textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="data_evento" class="form-label">Data e Hora do Evento</label>
                        <input type="datetime-local" class="form-control" id="data_evento" name="data_evento" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="local" class="form-label">Local do Evento</label>
                        <input type="text" class="form-control" id="local" name="local" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="imagem" class="form-label">Imagem do Evento (opcional)</label>
                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*" onchange="previewImagem(event)">
                <img id="preview" src="#" alt="Pré-visualização da imagem" style="display: none; margin-top: 10px; max-height: 200px;">
            </div>

            <button type="submit" class="btn btn-primary">Criar Evento</button>
        </form>
    </section>

    <!-- Rodapé -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 JAFETE JOSE. Todos os direitos reservados.</p>
    </footer>

    <!-- Script do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Script para Pré-visualização da Imagem -->
    <script>
        function previewImagem(event) {
            const preview = document.getElementById('preview');
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.style.display = 'block';
        }
    </script>
</body>
</html>

