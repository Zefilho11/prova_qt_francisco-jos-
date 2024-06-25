<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$perfil_id = $_SESSION['perfil_id'];
$stmt = $pdo->prepare("SELECT nome FROM perfils WHERE id = :id");
$stmt->execute(['id' => $perfil_id]);
$perfil = $stmt->fetchColumn();

if ($perfil == 'Administrador') {
    include 'admin_dashboard.php';
} else if ($perfil == 'Colaborador') {
    include 'colaborador_dashboard.php';
} else {
    echo "Acesso negado.";
    exit;
}
?>
