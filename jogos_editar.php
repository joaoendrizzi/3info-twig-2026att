<?php
// jogos_editar.php
$erro = false;

require('carregar_pdo.php');
$id = (int) $_GET['id'] ?? false;
if (!$id) {
    header('location:jogos.php');
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'] ?? false;
    $estilo = $_POST['estilo'] ?? false;
    if (!$nome || !$estilo) {
        $erro = 'Preencha todos os campos';
    } else {
        $capa = $_POST['capa_atual'] ?? false;
        if (isset($_FILES['capa']) && $_FILES['capa']['error'] == 0) {
            $ext = pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION);
            $capa = uniqid().'.'.$ext;
            move_uploaded_file($_FILES['capa']['tmp_name'], "img/{$capa}");
        }
        $dados = $pdo->prepare('UPDATE jogos SET nome = ?, estilo = ?, capa = ? WHERE id = ?');
        $dados->bindParam(1, $nome);
        $dados->bindParam(2, $estilo);
        $dados->bindParam(3, $capa);
        $dados->bindParam(4, $id);
        $dados->execute();
        header('location:jogos.php');
        die;
    }
}

$dados = $pdo->prepare('SELECT * FROM jogos WHERE id = ?');
$dados->bindParam(1, $id);
$dados->execute();
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

