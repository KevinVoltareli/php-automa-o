<?php
// Diretório onde as imagens estão localizadas
$dir = '\\\\192.168.30.200\\dados$\\Depto TI\\Kevin\\adicionapreco\\';

// Lista de extensões de imagem que você deseja renomear
$extensoes = ['jpg', 'jpeg', 'png', 'gif', 'mp4'];

// Padrão de substituição
$padrao = '/_TAM([A-Za-z]+)_/';

// Listar arquivos no diretório
$arquivos = scandir($dir);

// Iterar sobre os arquivos
foreach ($arquivos as $arquivo) {
    $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);

    // Verificar se a extensão é suportada
    if (in_array(strtolower($extensao), $extensoes)) {
        // Adicionar "_PRECO_" após "TAM[VARIAÇÃO]"
        $novoNome = preg_replace($padrao, '_TAM$1_PRECO_', $arquivo);

        // Renomear o arquivo
        if (rename($dir . $arquivo, $dir . $novoNome)) {
            echo "Renomeado: $arquivo => $novoNome<br>";
        } else {
            echo "Erro ao renomear: $arquivo<br>";
        }
    }
}

echo "Concluído!";
?>
