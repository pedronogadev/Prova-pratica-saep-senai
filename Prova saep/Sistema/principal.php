<?php
session_start();

// Validação de Sessão (Redireciona para o login se não estiver autenticado)
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Principal - Clínica Veterinária</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f9; }
        .navbar { background-color: #003366; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .user-info { font-size: 16px; }
        .btn-logout { background-color: #d9534f; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; }
        .btn-logout:hover { background-color: #c9302c; }
        .container { padding: 30px; }
        .menu-grid { display: flex; gap: 20px; margin-top: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; width: 200px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-decoration: none; color: #333; font-weight: bold; }
        .card:hover { background-color: #e6f0fa; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="user-info">
            Usuário Logado: <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong>
        </div>
        <a href="logout.php" class="btn-logout">Sair do Sistema</a>
    </div>

    <div class="container">
        <h1>Painel de Controle</h1>
        <p>Selecione uma funcionalidade no menu abaixo:</p>

        <div class="menu-grid">
            <a href="tutores.php" class="card">Gerenciar Tutores</a>
            <a href="pets.php" class="card">Gerenciar Pets</a>
            <a href="agendamentos.php" class="card">Agendamentos</a>
        </div>
    </div>
</body>
</html>