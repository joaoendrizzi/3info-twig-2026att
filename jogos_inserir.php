<?php
// jogos_inserir.php
$erro = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Preparar os dados para inserir no banco
    $nome = $_POST['nome'] ?? false;
    $estilo = $_POST['estilo'] ?? false;
    $lancamento = $_POST['lancamento'] ?? false;

    // Verifica erro
    if (!$nome || !$estilo || !$lancamento) {
        $erro = 'Preencha todos os campos';
    } else {
        // Tudo certo - gravar os dados
        $ext = pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION);
        $capa = uniqid().'.'.$ext;
        
        move_uploaded_file($_FILES['capa']['tmp_name'], "img/{$capa}");

        require('carregar_pdo.php');
        $dados = $pdo->prepare('INSERT INTO jogos (nome, estilo, capa, lancamento) VALUES (?, ?, ?, ?)');
        
        $dados->bindParam(1, $nome);
        $dados->bindParam(2, $estilo);
        $dados->bindParam(3, $capa);
        $dados->bindParam(4, $lancamento);

        $dados->execute();

        header('location:jogos.php');
        die;
    }
}

require('carregar_twig.php');

echo $twig->render('jogos_inserir.html', [
    'erro' => $erro,
]);