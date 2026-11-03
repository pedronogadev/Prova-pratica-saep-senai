<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensagem = '';
$erro = '';

// Processar Cadastro de Pet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tutor_id = trim($_POST['tutor_id'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $especie = trim($_POST['especie'] ?? '');
    $raca = trim($_POST['raca'] ?? '');
    $idade = trim($_POST['idade'] ?? '');

    if (!empty($tutor_id) && !empty($nome) && !empty($especie) && !empty($raca) && !empty($idade)) {
        $stmt = $pdo->prepare('INSERT INTO pet (tutor_id, nome, especie, raca, idade) VALUES (:tutor_id, :nome, :especie, :raca, :idade)');
        $stmt->execute([
            'tutor_id' => $tutor_id,
            'nome' => $nome,
            'especie' => $especie,
            'raca' => $raca,
            'idade' => $idade
        ]);
        $mensagem = 'Pet cadastrado com sucesso!';
    } else {
        $erro = 'Preencha todos os campos obrigatórios!';
    }
}

// Buscar tutores para o select
$tutores = $pdo->query('SELECT id, nome FROM tutor ORDER BY nome ASC')->fetchAll();

// Listar pets com o nome do tutor associado
$pets = $pdo->query('
    SELECT pet.*, tutor.nome AS tutor_nome 
    FROM pet 
    JOIN tutor ON pet.tutor_id = tutor.id 
    ORDER BY pet.nome ASC
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Pets - Clínica Veterinária</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f9; }
        .navbar { background-color: #003366; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; font-weight: bold; }
        .container { padding: 30px; max-width: 900px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { padding: 8px 15px; background-color: #003366; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background-color: #002244; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #003366; color: white; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="navbar">
        <span>Gerenciamento de Pets</span>
        <a href="principal.php">Voltar ao Painel</a>
    </div>

    <div class="container">
        <?php if (!empty($mensagem)): ?>
            <div class="alert-success"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)): ?>
            <div class="alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div class="card">
            <h3>Cadastrar Novo Pet</h3>
            <form action="pets.php" method="POST">
                <div class="form-group">
                    <label for="tutor_id">Tutor Responsável:</label>
                    <select id="tutor_id" name="tutor_id" required>
                        <option value="">Selecione um tutor</option>
                        <?php foreach ($tutores as $tutor): ?>
                            <option value="<?= $tutor['id'] ?>"><?= htmlspecialchars($tutor['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nome">Nome do Pet:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="especie">Espécie:</label>
                    <input type="text" id="especie" name="especie" required placeholder="Ex: Cachorro, Gato">
                </div>
                <div class="form-group">
                    <label for="raca">Raça:</label>
                    <input type="text" id="raca" name="raca" required>
                </div>
                <div class="form-group">
                    <label for="idade">Idade (anos):</label>
                    <input type="number" id="idade" name="idade" required min="0">
                </div>
                <button type="submit" class="btn">Salvar Pet</button>
            </form>
        </div>

        <div class="card">
            <h3>Lista de Pets Cadastrados</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Espécie</th>
                        <th>Raça</th>
                        <th>Idade</th>
                        <th>Tutor Responsável</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pets) > 0): ?>
                        <?php foreach ($pets as $pet): ?>
                            <tr>
                                <td><?= $pet['id'] ?></td>
                                <td><?= htmlspecialchars($pet['nome']) ?></td>
                                <td><?= htmlspecialchars($pet['especie']) ?></td>
                                <td><?= htmlspecialchars($pet['raca']) ?></td>
                                <td><?= $pet['idade'] ?> ano(s)</td>
                                <td><?= htmlspecialchars($pet['tutor_nome']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Nenhum pet cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>