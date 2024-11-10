<?php
//// Iniciar a sessão
//session_start();
//
//// Verificar se o usuário está logado
//if (!isset($_SESSION['usuario'])) {
//    header("Location: index.php");
//    exit;
//}
//
//// Incluir conexão com o banco de dados
//include 'conexao.php';
//
//// Obter o ID do usuário e do evento
//$id_usuario = $_SESSION['usuario']['id'];
//$id_evento = $_GET['id'];
//
//// Verificar se o evento existe
//$sql = "SELECT * FROM eventos WHERE id = ?";
//$stmt = $conn->prepare($sql);
//$stmt->bind_param("i", $id_evento);
//$stmt->execute();
//$result = $stmt->get_result();
//
//if ($result->num_rows == 0) {
//    echo "Evento não encontrado.";
//    exit;
//}

// Verificar se o usuário já confirmou participação
//$sql_verificar = "SELECT * FROM participantes_evento WHERE id_evento = ? AND id_usuario = ?";
//$stmt_verificar = $conn->prepare($sql_verificar);
//$stmt_verificar->bind_param("ii", $id_evento, $id_usuario);
//$stmt_verificar->execute();
//$result_verificar = $stmt_verificar->get_result();
//
//if ($result_verificar->num_rows == 0) {
//    // Registrar a participação
//    $sql_inserir = "INSERT INTO participantes_evento (id_evento, id_usuario) VALUES (?, ?)";
//    $stmt_inserir = $conn->prepare($sql_inserir);
//    $stmt_inserir->bind_param("ii", $id_evento, $id_usuario);
//    $stmt_inserir->execute();
//
//    // Redirecionar para a página de detalhes com sucesso
//    header("Location: detalhes-evento.php?id=$id_evento&participacao=sucesso");
//} else {
//    // Caso já tenha confirmado a participação
//    header("Location: detalhes-evento.php?id=$id_evento&participacao=ja_confirmada");
//}
//
//$conn->close();
//?>
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

// Obter o ID do evento
$id_evento = $_GET['id'];
$id_usuario = $_SESSION['usuario']['id'];

// Verificar se o usuário já está participando do evento
$sql_verificar = "SELECT * FROM participantes_evento WHERE id_evento = ? AND id_usuario = ?";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("ii", $id_evento, $id_usuario);
$stmt_verificar->execute();
$result_verificar = $stmt_verificar->get_result();

if ($result_verificar->num_rows > 0) {
    // Se o usuário já estiver participando, redireciona de volta com mensagem
    echo "<script>alert('Você já está participando deste evento!'); window.location.href = 'detalhes-evento.php?id=$id_evento';</script>";
    exit;
}

// Inserir o usuário na tabela de participantes
$sql = "INSERT INTO participantes_evento (id_evento, id_usuario) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_evento, $id_usuario);

if ($stmt->execute()) {
    // Redirecionar para "Meus Eventos" após a inscrição
    echo "<script>alert('Participação confirmada com sucesso!'); window.location.href = 'meus-eventos.php';</script>";
} else {
    echo "<script>alert('Erro ao confirmar participação. Tente novamente.'); window.location.href = 'detalhes-evento.php?id=$id_evento';</script>";
}

// Fechar a conexão com o banco de dados
$stmt->close();
$conn->close();
?>
