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

if ($perfil != 'Administrador') {
    echo "Acesso negado.";
    exit;
}

$usuario = [];

$error = '';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $perfil_nome = $_POST['perfil'];

    $stmt = $pdo->prepare("SELECT id FROM perfils WHERE nome = :nome");
    $stmt->execute(['nome' => $perfil_nome]);
    $perfil = $stmt->fetch();

    if ($perfil) {
        $perfil_id = $perfil['id'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO perfils (nome) VALUES (:nome)");
        $stmt->execute(['nome' => $perfil_nome]);
        $perfil_id = $pdo->lastInsertId();
    }

    $stmt = $pdo->prepare("UPDATE usuarios SET perfil_id = :perfil_id WHERE id = :id");
    if ($stmt->execute(['perfil_id' => $perfil_id, 'id' => $id])) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Erro ao alterar perfil. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Perfil do Usuário</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
            width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2ac93d; 
        }

        form {
            display: flex;
            flex-direction: column;
        }

        select {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        select:focus {
            outline: none;
            border-color: #2ac93d; 
        }

        button {
            padding: 10px 20px;
            background-color: #2ac93d; 
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9; 
        }

        .mensagem {
            margin-top: 10px;
            text-align: center;
            color: #e74c3c; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Alterar Perfil do Usuário</h1>
        <a href="admin_dashboard.php">Voltar</a>
        <form method="POST">
            <select name="perfil" required>
                <option value="Colaborador" <?php if (isset($usuario['perfil']) && $usuario['perfil'] === 'Colaborador') echo 'selected'; ?>>Colaborador</option>
                <option value="Administrador" <?php if (isset($usuario['perfil']) && $usuario['perfil'] === 'Administrador') echo 'selected'; ?>>Administrador</option>
            </select>
            <button type="submit">Alterar</button>
        </form>
        <?php if (!empty($error)): ?>
            <p class="mensagem"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
