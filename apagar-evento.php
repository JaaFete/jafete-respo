<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include 'conexao.php';

// Verificar se o ID do evento foi passado pela URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

// Obter o ID do evento
$id_evento = $_GET['id'];
$id_usuario_logado = $_SESSION['usuario']['id'];

// Consulta para verificar se o usuário e o organizador do evento
$sql = "SELECT id FROM eventos WHERE id = ? AND id_usuario = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ii", $id_evento, $id_usuario_logado);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se o evento existe e se o usuario e o criador
    if ($result->num_rows > 0) {
        // Excluir o evento
        $sql_delete = "DELETE FROM eventos WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        if ($stmt_delete) {
            $stmt_delete->bind_param("i", $id_evento);
            if ($stmt_delete->execute()) {
                // Redirecionar para a pagina inicial ou de eventos apos a exclusao
                header("Location: dashboard.php");
                exit;
            } else {
                echo "Erro ao apagar o evento.";
            }
            $stmt_delete->close();
        }
    } else {
        echo "Evento nao encontrado ou você não tem permissão para apaga-lo.";
    }
    $stmt->close();
} else {
    echo "Erro na consulta de validacao.";
}

// Fechar a conexao com o banco de dados
$conn->close();
?>
