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

// Verificar se o ID do evento foi passado pela URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

// Obter o ID do evento
$id_evento = $_GET['id'];
$id_usuario_logado = $_SESSION['usuario']['id'];

// Consulta para obter os detalhes do evento
$sql = "SELECT * FROM eventos WHERE id = ? AND id_usuario = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ii", $id_evento, $id_usuario_logado);
    $stmt->execute();
    $result = $stmt->get_result();
    $evento = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Erro na consulta de detalhes do evento.";
    exit;
}

// Verificar se o evento existe e se o usuário é o organizador
if (!$evento) {
    echo "Evento não encontrado ou você não tem permissão para editá-lo.";
    exit;
}

// Atualizar o evento quando o formulário é enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $data_evento = $_POST['data_evento'];
    $local = $_POST['local'];
    $descricao = $_POST['descricao'];

    // Validar e atualizar os dados no banco
    $sql_update = "UPDATE eventos SET nome = ?, data_evento = ?, local = ?, descricao = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update) {
        $stmt_update->bind_param("ssssi", $nome, $data_evento, $local, $descricao, $id_evento);
        if ($stmt_update->execute()) {
            header("Location: detalhes.php?id=$id_evento");
            exit;
        } else {
            echo "Erro ao atualizar o evento.";
        }
        $stmt_update->close();
    } else {
        echo "Erro na preparação da consulta.";
    }
}

// Fechar a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Evento - <?php echo htmlspecialchars($evento['nome']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Editar Evento</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Evento</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($evento['nome']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="data_evento" class="form-label">Data e Hora</label>
                <input type="datetime-local" class="form-control" id="data_evento" name="data_evento" value="<?php echo date('Y-m-d\TH:i', strtotime($evento['data_evento'])); ?>" required>
            </div>
            <div class="mb-3">
                <label for="local" class="form-label">Local</label>
                <input type="text" class="form-control" id="local" name="local" value="<?php echo htmlspecialchars($evento['local']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="4" required><?php echo htmlspecialchars($evento['descricao']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <a href="detalhes.php?id=<?php echo $evento['id']; ?>" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
