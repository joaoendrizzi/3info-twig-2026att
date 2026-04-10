<?php
// jogos_editar.php
$erro = false;

require('carregar_pdo.php');


$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
if (!$id) {
    header('location:jogos.php');
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'] ?? false;
    $estilo = $_POST['estilo'] ?? false;
    $lancamento = $_POST['lancamento'] ?? false;
    $id = (int) ($_POST['id'] ?? $id);
    if (!$nome || !$estilo || !$lancamento) {
        $erro = 'Preencha todos os campos';
    } else {
        $capa_atual = $_POST['capa_atual'] ?? '';
        $capa = $capa_atual;

        if (isset($_FILES['capa']) && $_FILES['capa']['error'] == 0) {
            $ext = pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION);
            $novo_nome = uniqid().'.'.$ext;
            move_uploaded_file($_FILES['capa']['tmp_name'], "img/{$novo_nome}");
            if ($capa_atual && file_exists(__DIR__.'/img/'.$capa_atual)) {
                @unlink(__DIR__.'/img/'.$capa_atual);
            }
            $capa = $novo_nome;
        } elseif (isset($_POST['remover_capa'])) {
            if ($capa_atual && file_exists(__DIR__.'/img/'.$capa_atual)) {
                @unlink(__DIR__.'/img/'.$capa_atual);
            }
            $capa = '';
        }

        $dados = $pdo->prepare('UPDATE jogos SET nome = ?, estilo = ?, capa = ?, lancamento = ? WHERE id = ?');
        $dados->execute([$nome, $estilo, $capa, $lancamento, $id]);
        header('location:jogos.php');
        die;
    }
}

$dados = $pdo->prepare('SELECT * FROM jogos WHERE id = ?');
$dados->execute([$id]);
$jogo = $dados->fetch(PDO::FETCH_ASSOC);
if (!$jogo) {
    header('location:jogos.php');
    die;
}

require('carregar_twig.php');
echo $twig->render('jogos_editar.html', [
    'erro' => $erro,
    'jogo' => $jogo
]);

