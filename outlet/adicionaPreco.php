<?php
// Diretório onde as imagens estão localizadas
$dir = '\\\\192.168.30.200\\dados$\\Depto E-Commerce\\teste\\rename\\';

// Lista de extensões de imagem que você deseja renomear
$extensoes = ['jpg', 'jpeg', 'png', 'gif', 'mp4'];

// Padrão de substituição
$padrao = '/_(TAMGG)_/';

// Listar arquivos no diretório
$arquivos = scandir($dir);

// Iterar sobre os arquivos
foreach ($arquivos as $arquivo) {
    $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);

    // Verificar se a extensão é suportada
    if (in_array(strtolower($extensao), $extensoes)) {
        $novoNome = preg_replace($padrao, '_${1}_PRECO_', $arquivo);

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
