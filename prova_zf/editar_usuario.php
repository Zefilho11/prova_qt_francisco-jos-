    <?php
    session_start();
    require 'config.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $error_editar = '';
    $success_editar = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $perfil = $_POST['perfil'];

        if (empty($nome) || empty($email) || empty($perfil)) {
            $error_editar = "Por favor, preencha todos os campos obrigatórios.";
        } else {

            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email AND id != :id");
            $stmt->execute(['email' => $email, 'id' => $id]);
            $existe_email = $stmt->fetch();

            if ($existe_email) {
                $error_editar = "Este email já está sendo utilizado por outro usuário.";
            } else {

                $stmt = $pdo->prepare("SELECT id FROM perfis WHERE nome = :nome");
                $stmt->execute(['nome' => $perfil]);
                $perfil_db = $stmt->fetch();

                if ($perfil_db) {
                    $perfil_id = $perfil_db['id'];
                } else {
                    $stmt = $pdo->prepare("INSERT INTO perfis (nome) VALUES (:nome)");
                    $stmt->execute(['nome' => $perfil]);
                    $perfil_id = $pdo->lastInsertId();
                }


                $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, perfil_id = :perfil_id WHERE id = :id");
                if ($stmt->execute(['nome' => $nome, 'email' => $email, 'perfil_id' => $perfil_id, 'id' => $id])) {
                    $success_editar = "Usuário atualizado com sucesso.";
                } else {
                    $error_editar = "Erro ao atualizar o usuário. Tente novamente.";
                }
            }
        }
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {

        echo "Usuário não encontrado.";
        exit;
    }


    $stmt = $pdo->query("SELECT * FROM perfils");
    $perfis = $stmt->fetchAll();    
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Editar Usuário</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f0f0f0; 
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
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }

            form {
                background-color: #2ac93d;
                padding: 20px;
                border-radius: 8px;
                box-sizing: border-box;
                color: #fff; 
            }

            form h2 {
                text-align: center;
                margin-bottom: 10px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                width: 100%;
                padding: 10px;
                margin-bottom: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
                background-color: #fff; 
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
                width: 100%;
                padding: 10px;
                background-color: #2ecc71; 
                color: #fff;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            button:hover {
                background-color: #27ae60;
            }

            .error-message {
                color: #c0392b;
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
        <div class="container">
            <h1>Editar Usuário</h1>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" placeholder="Nome" required>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Email" required>
                    <select name="perfil" required>
                        <?php foreach ($perfis as $p): ?>
                            <option value="<?php echo $p['nome']; ?>" <?php echo ($p['id'] == $user['perfil_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="editar">Salvar Alterações</button>
                </form>
            <?php if ($error_editar): ?>
                <p class="error-message"><?php echo $error_editar; ?></p>
            <?php elseif ($success_editar): ?>  
                <p class="success-message"><?php echo $success_editar; ?></p>
            <?php endif; ?>
        </div>
    </body>
    </html>
