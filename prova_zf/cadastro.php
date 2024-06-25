<?php
session_start();
require 'config.php';

$error_register = '';
$success = '';

// Função para limpar entrada dos usuários
function limpar_entrada($dados) {
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nome = limpar_entrada($_POST['nome']);
    $email = limpar_entrada($_POST['email']);
    $senha = limpar_entrada($_POST['senha']);
    $perfil_nome = limpar_entrada($_POST['perfil']);

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $existe_email = $stmt->fetch();

    if ($existe_email) {
        $error_register = "Este email já está sendo utilizado.";
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

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil_id) VALUES (:nome, :email, :senha, :perfil_id)");
        if ($stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senha_hash, 'perfil_id' => $perfil_id])) {
            $success = "Cadastro realizado com sucesso! Você já pode fazer login.";
        } else {
            $error_register = "Erro ao realizar cadastro. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro</title>
    <style>
        /* Estilos CSS aqui */
    </style>
</head>
<body>
    <h1>Cadastro</h1>
    <div class="container">
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
            <?php elseif ($success): ?>
                <p class="success-message"><?php echo $success; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
