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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
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

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil_id) VALUES (:nome, :email, :senha, :perfil_id)");
    if ($stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senha, 'perfil_id' => $perfil_id])) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Erro ao adicionar usuário. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Usuário</title>
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
            width: 80%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        a {
            color: #2ac93d; 
            text-decoration: none;
            margin-right: 10px;
        }

        a:hover {
            text-decoration: underline;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
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

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #2ac93d;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: #e74c3c;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Adicionar Usuário</h1>
        <a href="admin_dashboard.php">Voltar</a>
        <form method="POST">
            <input type="text" name="nome" placeholder="Nome" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <select name="perfil" required>
                <option value="Colaborador" <?php if (isset($usuario['perfil']) && $usuario['perfil'] === 'Colaborador') echo 'selected'; ?>>Colaborador</option>
                <option value="Administrador" <?php if (isset($usuario['perfil']) && $usuario['perfil'] === 'Administrador') echo 'selected'; ?>>Administrador</option>
            </select>
            <button type="submit">Adicionar</button>
        </form>
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
