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

$stmt = $pdo->prepare("SELECT u.id, u.nome, u.email, p.nome AS perfil FROM usuarios u JOIN perfils p ON u.perfil_id = p.id");
$stmt->execute();
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Administrador</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            width: 80%;
            max-width: 1200px;
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #2ac93d; 
        }

        a {
            color: #2ac93d; 
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

        .actions {
            text-align: center;
        }

        .actions a {
            margin-right: 10px;
        }

        .actions a:last-child {
            margin-right: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard do Administrador</h1>
        <a class="logout-link" href="logout.php">Logout</a>
        <h2>Usuários Cadastrados</h2>
        <a href="adicionar_usuario.php">Adicionar Novo Usuário</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['perfil']); ?></td>
                    <td class="actions">
                        <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>">Editar</a>
                        <a href="deletar_usuario.php?id=<?php echo $usuario['id']; ?>" onclick="return confirm('Tem certeza que deseja deletar este usuário?');">Deletar</a>
                        <a href="atualizar_perfil.php?id=<?php echo $usuario['id']; ?>">Alterar Perfil</a>
                    </td>
                </tr>

            <script>
                document.querySelector('.logout-link').addEventListener('click', function(event) {
                    event.preventDefault(); 
                    
                    alert("Você saiu da plataforma.");

                    window.location.href = this.getAttribute('href');
                });
            </script>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>


