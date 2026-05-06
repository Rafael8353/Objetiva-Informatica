<?php
session_start();

// ==========================================
// 1. CONFIGURAÇÕES DE LOGIN
// ==========================================
$usuario_painel = 'ADMIN';
$senha_painel = 'SENHA DO ADMIN'; 

// ==========================================
// 2. BANCO DE DADOS (LOCAWEB)
// ==========================================
$db_host = 'SEU HOST'; 
$db_name = 'SEU NOME DO DB';
$db_user = 'SEU USER DO DB';
$db_pass = 'SUA SENHA DO DB';

if (isset($_GET['sair'])) { session_destroy(); header("Location: gestao-assistencia.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    if ($_POST['usuario'] === $usuario_painel && $_POST['senha'] === $senha_painel) {
        $_SESSION['logado'] = true;
    } else { $erro_login = "Usuário ou senha incorretos!"; }
}

if (!isset($_SESSION['logado'])) {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Página de Administrador</title>
    <link rel="icon" type="image/png" href="img/Favicon.png">
    <style>body{font-family:Arial; background:#f3f4fa; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;} .box{background:white; padding:40px; border-radius:12px; box-shadow:0 8px 20px rgba(98, 6, 155, 0.15); border-top:6px solid #62069b; text-align:center; width: 300px;} input{width:100%; padding:12px; margin:10px 0; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;} input:focus{outline:none; border-color:#62069b;} button{width:100%; padding:12px; background:#62069b; color:white; border:none; cursor:pointer; font-weight:bold; border-radius:6px; margin-top:10px; transition:0.3s;} button:hover{background:#4a0575;}</style></head>
    <body><div class="box">
    <img src="img/Logo.png" alt="Logo Objetiva" style="max-width: 120px; margin-bottom: 20px;">
    <h2 style="margin-top:0; color:#62069b; font-size:18px;">Página de Administrador Objetiva</h2>';
    if(isset($erro_login)) echo "<p style='color:red;'>$erro_login</p>";
    echo '<form method="POST"><input type="text" name="usuario" placeholder="Usuário" required><input type="password" name="senha" placeholder="Senha" required><button type="submit" name="login">Entrar</button></form></div></body></html>';
    exit;
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Tabela nova: servicos
    $sql = "CREATE TABLE IF NOT EXISTS servicos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        descricao_curta TEXT,
        descricao_longa TEXT,
        imagem VARCHAR(255) NOT NULL
    )";
    $pdo->exec($sql);
} catch(PDOException $e) { die("Erro de conexão: " . $e->getMessage()); }

$servico_edit = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $servico_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salvar_servico'])) {
    $id_servico = $_POST['id_servico'];
    $imagem = $_POST['imagem_atual']; 
    
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid() . '.' . $extensao;
        // Cria a pasta img se não existir
        if (!file_exists('img')) { mkdir('img', 0777, true); }
        move_uploaded_file($_FILES['imagem']['tmp_name'], 'img/' . $novo_nome);
        $imagem = 'img/' . $novo_nome;
    }

    if (!empty($id_servico)) {
        $stmt = $pdo->prepare("UPDATE servicos SET titulo=?, descricao_curta=?, descricao_longa=?, imagem=? WHERE id=?");
        $stmt->execute([$_POST['titulo'], $_POST['descricao_curta'], $_POST['descricao_longa'], $imagem, $id_servico]);
        $mensagem = "Serviço atualizado!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO servicos (titulo, descricao_curta, descricao_longa, imagem) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['titulo'], $_POST['descricao_curta'], $_POST['descricao_longa'], $imagem]);
        $mensagem = "Novo serviço salvo!";
    }
    header("Location: gestao-assistencia.php?msg=" . urlencode($mensagem));
    exit;
}

if (isset($_GET['deletar'])) {
    $stmt = $pdo->prepare("DELETE FROM servicos WHERE id = ?");
    $stmt->execute([$_GET['deletar']]);
    header("Location: gestao-assistencia.php?msg=Servico+excluido"); exit;
}

$servicos = $pdo->query("SELECT * FROM servicos ORDER BY titulo ASC")->fetchAll(PDO::FETCH_ASSOC);
$msg_sucesso = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Painel Serviços</title>
    <link rel="icon" type="image/png" href="img/Favicon.png">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f3f4fa; color: #333; margin: 0; padding: 40px 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 8px 20px rgba(98, 6, 155, 0.1); border-top: 6px solid #62069b; }
        h2 { color: #62069b; margin-top: 0; }
        form { display: grid; grid-template-columns: 1fr; gap: 15px; background: #fafafa; padding: 25px; border-radius: 8px; border: 1px solid #eee; }
        input, textarea { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        input:focus, textarea:focus { border-color: #62069b; outline:none; }
        button { padding: 15px; background: #62069b; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #4a0575; }
        .btn-cancelar { background: #6c757d; text-align: center; color: white; padding: 12px; text-decoration: none; border-radius: 6px; display: block; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; background: white; }
        th { background: #62069b; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        .acoes a { margin-right: 10px; padding: 8px 14px; text-decoration: none; font-weight: bold; border-radius: 4px; font-size: 13px; color: white; }
        .btn-edit { background: #62069b; }
        .btn-del { background: #dc3545; }
        .btn-sair { background: #dc3545; color: white; padding: 8px 16px; text-decoration: none; font-weight: bold; border-radius: 6px; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items: center;">
        <h2>Gerenciar Serviços / Loja</h2>
        <a href="?sair=1" class="btn-sair">Sair</a>
    </div>
    
    <?php if($msg_sucesso) echo "<p style='color:green; font-weight:bold; padding: 10px; background: #d4edda; border-radius: 6px;'>$msg_sucesso</p>"; ?>
    
    <h3 style="margin-top:0; color:#555;"><?= $servico_edit ? "Editando: " . htmlspecialchars($servico_edit['titulo']) : "Adicionar Novo Serviço" ?></h3>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_servico" value="<?= $servico_edit ? $servico_edit['id'] : '' ?>">
        <input type="hidden" name="imagem_atual" value="<?= $servico_edit ? $servico_edit['imagem'] : '' ?>">

        <input type="text" name="titulo" placeholder="Título (Ex: Manutenção de Computadores)" value="<?= $servico_edit ? htmlspecialchars($servico_edit['titulo']) : '' ?>" required>
        
        <input type="text" name="descricao_curta" placeholder="Descrição Curta (Aparece no Card Principal)" value="<?= $servico_edit ? htmlspecialchars($servico_edit['descricao_curta']) : '' ?>" required>
        
        <textarea name="descricao_longa" placeholder="Descrição Longa (Aparece ao clicar em Saiba Mais)" rows="4" required><?= $servico_edit ? htmlspecialchars($servico_edit['descricao_longa']) : '' ?></textarea>
        
        <div>
            <label style="display:block; margin-bottom:5px; font-weight:bold; color:#555;">Imagem do Serviço:</label>
            <input type="file" name="imagem" accept="image/*" <?= $servico_edit ? '' : 'required' ?> style="background: white;">
        </div>
        
        <button type="submit" name="salvar_servico"><?= $servico_edit ? "Salvar Alterações" : "Adicionar Serviço" ?></button>
        <?php if($servico_edit): ?><a href="gestao-assistencia.php" class="btn-cancelar">Cancelar Edição</a><?php endif; ?>
    </form>

    <table>
        <tr><th>Img</th><th>Título</th><th>Ações</th></tr>
        <?php foreach($servicos as $s): ?>
        <tr>
            <td><img src="<?= $s['imagem'] ?>" width="50" style="border-radius:4px; object-fit:cover; height:50px;"></td>
            <td style="font-weight: bold;"><?= $s['titulo'] ?></td>
            <td class="acoes">
                <a href="?editar=<?= $s['id'] ?>" class="btn-edit">Editar</a>
                <a href="?deletar=<?= $s['id'] ?>" class="btn-del" onclick="return confirm('Excluir este serviço?');">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div></body></html>