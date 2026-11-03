<?php
session_start();
require_once 'conexao.php';

// Validação de Sessão
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensagem = '';
$erro = '';

// Chave secreta simples para simular criptografia reversível do CPF (em ambiente de produção, use OpenSSL)
define('CHAVE_CRIPTOGRAFIA', 'SenaiSaep2026!#');

function criptografarCPF($cpf) {
    return openssl_encrypt($cpf, 'AES-128-ECB', CHAVE_CRIPTOGRAFIA);
}

function descriptografarCPF($cpfCriptografado) {
    $decrypted = openssl_decrypt($cpfCriptografado, 'AES-128-ECB', CHAVE_CRIPTOGRAFIA);
    return $decrypted !== false ? $decrypted : $cpfCriptografado; // Retorna o original se falhar
}

// Processar Cadastro ou Edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');

    if (!empty($nome) && !empty($cpf) && !empty($telefone)) {
        $cpfCriptografado = criptografarCPF($cpf);

        if (!empty($id)) {
            // Atualização (Editar)
            $stmt = $pdo->prepare('UPDATE tutor SET nome = :nome, cpf = :cpf, telefone = :telefone, endereco = :endereco WHERE id = :id');
            $stmt->execute(['nome' => $nome, 'cpf' => $cpfCriptografado, 'telefone' => $telefone, 'endereco' => $endereco, 'id' => $id]);
            $mensagem = 'Tutor atualizado com sucesso!';
        } else {
            // Inserção (Cadastrar)
            $stmt = $pdo->prepare('INSERT INTO tutor (nome, cpf, telefone, endereco) VALUES (:nome, :cpf, :telefone, :endereco)');
            $stmt->execute(['nome' => $nome, 'cpf' => $cpfCriptografado, 'telefone' => $telefone, 'endereco' => $endereco]);
            $mensagem = 'Tutor cadastrado com sucesso!';
        }
    } else {
        $erro = 'Preencha todos os campos obrigatórios!';
    }
}

// Buscar Tutor para Edição
$tutorEdicao = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM tutor WHERE id = :id');
    $stmt->execute(['id' => $_GET['editar']]);
    $tutorEdicao = $stmt->fetch();
    if ($tutorEdicao) {
        $tutorEdicao['cpf'] = descriptografarCPF($tutorEdicao['cpf']);
    }
}

// Pesquisa por nome
$termoBusca = trim($_GET['busca'] ?? '');
if (!empty($termoBusca)) {
    $stmt = $pdo->prepare('SELECT * FROM tutor WHERE nome LIKE :busca ORDER BY nome ASC');
    $stmt->execute(['busca' => '%' . $termoBusca . '%']);
} else {
    $stmt = $pdo->query('SELECT * FROM tutor ORDER BY nome ASC');
}
$tutores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Tutores - Clínica Veterinária</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f9; }
        .navbar { background-color: #003366; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; font-weight: bold; }
        .container { padding: 30px; max-width: 900px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { padding: 8px 15px; background-color: #003366; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background-color: #002244; }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #003366; color: white; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="navbar">
        <span>Gerenciamento de Tutores</span>
        <a href="principal.php">Voltar ao Painel</a>
    </div>

    <div class="container">
        <?php if (!empty($mensagem)): ?>
            <div class="alert-success"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)): ?>
            <div class="alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <!-- Formulário de Cadastro / Edição -->
        <div class="card">
            <h3><?= $tutorEdicao ? 'Editar Tutor' : 'Cadastrar Novo Tutor' ?></h3>
            <form action="tutores.php" method="POST">
                <input type="hidden" name="id" value="<?= $tutorEdicao['id'] ?? '' ?>">
                
                <div class="form-group">
                    <label for="nome">Nome Completo:</label>
                    <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($tutorEdicao['nome'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="cpf">CPF (Será armazenado criptografado):</label>
                    <input type="text" id="cpf" name="cpf" required placeholder="000.000.000-00" value="<?= htmlspecialchars($tutorEdicao['cpf'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="text" id="telefone" name="telefone" required value="<?= htmlspecialchars($tutorEdicao['telefone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="endereco">Endereço:</label>
                    <input type="text" id="endereco" name="endereco" required value="<?= htmlspecialchars($tutorEdicao['endereco'] ?? '') ?>">
                </div>

                <button type="submit" class="btn"><?= $tutorEdicao ? 'Atualizar Tutor' : 'Salvar Tutor' ?></button>
                <?php if ($tutorEdicao): ?>
                    <a href="tutores.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Área de Pesquisa e Listagem -->
        <div class="card">
            <h3>Lista de Tutores Cadastrados</h3>
            
            <form action="tutores.php" method="GET" class="search-bar">
                <input type="text" name="busca" placeholder="Pesquisar tutor por nome..." value="<?= htmlspecialchars($termoBusca) ?>">
                <button type="submit" class="btn">Buscar</button>
                <?php if (!empty($termoBusca)): ?>
                    <a href="tutores.php" class="btn btn-secondary">Limpar</a>
                <?php endif; ?>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF (Descriptografado)</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($tutores) > 0): ?>
                        <?php foreach ($tutores as $tutor): ?>
                            <tr>
                                <?= "<td>{$tutor['id']}</td>" ?>
                                <?= "<td>" . htmlspecialchars($tutor['nome']) . "</td>" ?>
                                <?= "<td>" . htmlspecialchars(descriptografarCPF($tutor['cpf'])) . "</td>" ?>
                                <?= "<td>" . htmlspecialchars($tutor['telefone']) . "</td>" ?>
                                <?= "<td>" . htmlspecialchars($tutor['endereco']) . "</td>" ?>
                                <td>
                                    <a href="tutores.php?editar=<?= $tutor['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Nenhum tutor encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>