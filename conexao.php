<?php
// Arquivo de conexão com o banco de dados

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco-dados";  // Substitua com o nome do seu banco de dados

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Opcional: Definir o conjunto de caracteres da conexão para UTF-8
$conn->set_charset("utf8");
?>
