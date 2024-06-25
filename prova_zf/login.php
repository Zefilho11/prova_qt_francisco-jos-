<?php
session_start();
require 'config.php';

$error_login = '';
$error_register = '';
$success_register = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['perfil_id'] = $user['perfil_id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error_login = "Credenciais inválidas. Por favor, tente novamente.";
        }
    }

    if (isset($_POST['register'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $perfil_nome = $_POST['perfil'];

        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $existe_email = $stmt->fetch();

        if ($existe_email) {
            $error_register = "Este email já está registrado em nossa base de dados.";
        } else {
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
                $success_register = "Cadastro realizado com sucesso! Agora você pode fazer login.";
            } else {
                $error_register = "Houve um erro ao tentar cadastrar. Por favor, tente novamente mais tarde.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login e Cadastro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4; 
            color: #333; 
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
            padding: 0px;
            border-radius: 0px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap; 
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #8c8c8c; 
            border-radius: 8px;
            box-sizing: border-box;
            color: #fff; 
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #f2f2f2; 
        }

        select {
            border: 1px solid #ddd; 
            transition: border-color 0.3s ease;
        }

        select:focus {
            outline: none;
            border-color: #2980b9; 
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #27ae60; 
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2ecc71; 
        }

        .error-message {
            color: #e74c3c; 
            margin-top: 10px;
            text-align: center;
        }

        .success-message {
            color: #2ecc71; 
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center; color: #333;">Login e Cadastro</h1>
    <div class="container">
        <div class="form-container">
            <h2>Login</h2>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit" name="login">Entrar</button>
            </form>
            <?php if ($error_login): ?>
                <p class="error-message"><?php echo $error_login; ?></p>
            <?php endif; ?>
        </div>

        <div class="form-container">
            <h2>Cadastro</h2>
            <form method="POST">
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <select name="perfil" required>
                    <option value="Colaborador">Colaborador</option>
                    <option value="Administrador">Administrador</option>
                </select>
                <button type="submit" name="register">Cadastrar</button>
            </form>
            <?php if ($error_register): ?>
                <p class="error-message"><?php echo $error_register; ?></p>
            <?php elseif ($success_register): ?>
                <p class="success-message"><?php echo $success_register; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
