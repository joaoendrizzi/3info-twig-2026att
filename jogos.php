<?php
use Carbon\Carbon;
require_once('vendor/autoload.php');
require('carregar_twig.php');
require('carregar_pdo.php');

Carbon::setLocale('pt_BR');

$hoje = Carbon::now('America/Sao_Paulo');
$proxima_sexta = $hoje->copy()->next(Carbon::FRIDAY);
$daqui_20_dias = $hoje->copy()->addDays(20);

$jogos = $pdo->query('SELECT * FROM jogos');
$todosJogos = $jogos->fetchAll(PDO::FETCH_ASSOC);

foreach ($todosJogos as &$jogo) {
    if ($jogo['lancamento']) {
        $data = Carbon::createFromFormat('Y-m-d', $jogo['lancamento']);
        $jogo['lancamento_extenso'] = $data->translatedFormat('d \d\e F \d\e Y');
    } else {
        $jogo['lancamento_extenso'] = '—';
    }
}

echo $twig->render('jogos.html', [
    'jogos' => $todosJogos,
    'hoje' => $hoje->translatedFormat('d \d\e F \d\e Y'),
    'proxima_sexta' => $proxima_sexta->translatedFormat('d \d\e F \d\e Y'),
    'daqui_20_dias' => $daqui_20_dias->translatedFormat('d \d\e F \d\e Y'),
]);