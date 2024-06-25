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

if ($perfil != 'Colaborador') {
    echo "Acesso negado.";
    exit;
}

// Consulta para buscar todos os usuários cadastrados
$stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios");
$stmt->execute();
$usuarios = $stmt->fetchAll();
?>

<style>
    /* Reset básico */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    width: 80%;
    max-width: 800px;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ccc;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.header h1 {
    font-size: 24px;
    color: #333;
}

.logout {
    padding: 10px 20px;
    background-color: #3498db;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.logout:hover {
    background-color: #2980b9;
}

.content {
    margin-top: 20px;
}

a {
    color: #2ac93d  ; 
    text-decoration: none;
    margin-right: 10px;
}

a:hover {
    text-decoration: underline;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f2f2f2;
}
    

</style>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Colaborador</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Dashboard do Colaborador</h1>
            <a class="logout-link" href="logout.php">Logout</a>
        </div>

        <div class="content">
            <h2>Lista de Usuários</h2>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                </tr>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                </tr>

            <script>
                document.querySelector('.logout-link').addEventListener('click', function(event) {
                    event.preventDefault(); 
                    
                    alert("Você saiu da plataforma.");

                    window.location.href = this.getAttribute('href');
                });
            </script>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
