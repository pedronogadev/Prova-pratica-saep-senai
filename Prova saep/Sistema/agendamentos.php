<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensagem = '';
$erro = '';

// Processar Inserção ou Edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $tutor_id = trim($_POST['tutor_id'] ?? '');
    $pet_id = trim($_POST['pet_id'] ?? '');
    $data_agendamento = trim($_POST['data_agendamento'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if (!empty($tutor_id) && !empty($pet_id) && !empty($data_agendamento) && !empty($descricao)) {
        // Extrai o ano automaticamente da data informada (YYYY-MM-DD HH:MM:SS)
        $ano = date('Y', strtotime($data_agendamento));

        if (!empty($id)) {
            // Atualizar agendamento existente
            $stmt = $pdo->prepare('UPDATE agendamento SET tutor_id = :tutor_id, pet_id = :pet_id, data_agendamento = :data_agendamento, ano = :ano, descricao = :descricao WHERE id = :id');
            $stmt->execute([
                'tutor_id' => $tutor_id,
                'pet_id' => $pet_id,
                'data_agendamento' => $data_agendamento,
                'ano' => $ano,
                'descricao' => $descricao,
                'id' => $id
            ]);
            $mensagem = 'Agendamento atualizado com sucesso!';
        } else {
            // Inserir novo agendamento
            $stmt = $pdo->prepare('INSERT INTO agendamento (tutor_id, pet_id, data_agendamento, ano, descricao) VALUES (:tutor_id, :pet_id, :data_agendamento, :ano, :descricao)');
            $stmt->execute([
                'tutor_id' => $tutor_id,
                'pet_id' => $pet_id,
                'data_agendamento' => $data_agendamento,
                'ano' => $ano,
                'descricao' => $descricao
            ]);
            $mensagem = 'Agendamento criado com sucesso!';
        }
    } else {
        $erro = 'Preencha todos os campos obrigatórios!';
    }
}

// Buscar agendamento para edição
$agendamentoEdicao = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM agendamento WHERE id = :id');
    $stmt->execute(['id' => $_GET['editar']]);
    $agendamentoEdicao = $stmt->fetch();
}

// Listas auxiliares para selects
$tutores = $pdo->query('SELECT id, nome FROM tutor ORDER BY nome ASC')->fetchAll();
$pets = $pdo->query('SELECT id, nome, tutor_id FROM pet ORDER BY nome ASC')->fetchAll();

// Listagem de agendamentos ordenada por ano (conforme requisito)
$agendamentos = $pdo->query('
    SELECT agendamento.*, tutor.nome AS tutor_nome, pet.nome AS pet_nome 
    FROM agendamento 
    JOIN tutor ON agendamento.tutor_id = tutor.id 
    JOIN pet ON agendamento.pet_id = pet.id 
    ORDER BY agendamento.ano ASC, agendamento.data_agendamento ASC
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Agendamentos - Clínica Veterinária</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f9; }
        .navbar { background-color: #003366; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; font-weight: bold; }
        .container { padding: 30px; max-width: 950px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { padding: 8px 15px; background-color: #003366; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background-color: #002244; }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #003366; color: white; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="navbar">
        <span>Gerenciamento de Agendamentos</span>
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
            <h3><?= $agendamentoEdicao ? 'Editar Agendamento' : 'Novo Agendamento' ?></h3>
            <form action="agendamentos.php" method="POST">
                <input type="hidden" name="id" value="<?= $agendamentoEdicao['id'] ?? '' ?>">

                <div class="form-group">
                    <label for="tutor_id">Tutor:</label>
                    <select id="tutor_id" name="tutor_id" required>
                        <option value="">Selecione o tutor</option>
                        <?php foreach ($tutores as $tutor): ?>
                            <option value="<?= $tutor['id'] ?>" <?= ($agendamentoEdicao && $agendamentoEdicao['tutor_id'] == $tutor['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tutor['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pet_id">Pet:</label>
                    <select id="pet_id" name="pet_id" required>
                        <option value="">Selecione o pet</option>
                        <?php foreach ($pets as $pet): ?>
                            <option value="<?= $pet['id'] ?>" <?= ($agendamentoEdicao && $agendamentoEdicao['pet_id'] == $pet['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pet['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="data_agendamento">Data e Hora:</label>
                    <input type="datetime-local" id="data_agendamento" name="data_agendamento" required value="<?= $agendamentoEdicao ? date('Y-m-d\TH:i', strtotime($agendamentoEdicao['data_agendamento'])) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição / Procedimento:</label>
                    <textarea id="descricao" name="descricao" rows="3" required><?= htmlspecialchars($agendamentoEdicao['descricao'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn"><?= $agendamentoEdicao ? 'Atualizar Agendamento' : 'Salvar Agendamento' ?></button>
                <?php if ($agendamentoEdicao): ?>
                    <a href="agendamentos.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h3>Lista de Agendamentos (Ordenados por Ano)</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ano</th>
                        <th>Data/Hora</th>
                        <th>Tutor</th>
                        <th>Pet</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($agendamentos) > 0): ?>
                        <?php foreach ($agendamentos as $ag): ?>
                            <tr>
                                <td><?= $ag['id'] ?></td>
                                <td><strong><?= $ag['ano'] ?></strong></td>
                                <td><?= date('d/m/Y H:i', strtotime($ag['data_agendamento'])) ?></td>
                                <td><?= htmlspecialchars($ag['tutor_nome']) ?></td>
                                <td><?= htmlspecialchars($ag['pet_nome']) ?></td>
                                <td><?= htmlspecialchars($ag['descricao']) ?></td>
                                <td>
                                    <a href="agendamentos.php?editar=<?= $ag['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Nenhum agendamento cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>