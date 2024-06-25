<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$perfil_id = $_SESSION['perfil_id'];
$stmt = $pdo->prepare("SELECT nome FROM perfils WHERE id = :id");
$stmt->execute(['id' => $perfil_id]);
$perfil = $stmt->fetchColumn();

if ($perfil != 'Administrador') {
    echo "Acesso negado.";
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $id]);

header("Location: admin_dashboard.php");
exit;
?>
