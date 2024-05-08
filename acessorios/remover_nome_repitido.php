<?php

// Função para remover valores repetidos no início do nome da imagem
function removeRepetitions($imageName) {
    $parts = explode('_', $imageName);
    $uniqueParts = array_unique($parts);
    return implode('_', $uniqueParts);
}

// Caminho UNC para a pasta de origem das imagens no servidor remoto
$sourceDir = '\\\\192.168.30.200\\dados$\\Depto E-Commerce\\teste\\estoque\\';

$mensagem = ""; // Variável para armazenar mensagens de sucesso ou erro

try {
    // Código de conexão ao banco de dados
    $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buscar produtos do banco de dados
    $sql = "SELECT a.MATDTCADASTRO AS DT_CADASTRO, a.MATFANTASIA AS FANTASIA, h.ACNDESCRICAO as ACNDESCRICAO, g.argdescricao AS GRIFE, g.argtipoprod AS TIPO_GRIFE, a.MATSEQUENCIAL AS COD_SEQUENCIAL, d.pesnome AS NOME_LOJA, j.ARMDESCRICAO AS MODELO,
        CAST(b.MECQUANTIDADE1 AS INT) AS QUANTIDADE, i.MPVPRECOVENDA AS PRECO
        FROM tb_mat_material a
        LEFT JOIN TB_MEC_MATESTCONTROLE b
        ON a.matid = b.matid
        LEFT JOIN TB_FIL_FILIAL c
        ON b.filid = c.filid
        LEFT JOIN TB_PES_PESSOA d
        ON c.pesid = d.pesid
        LEFT JOIN TB_AAT_ATRIBUTOS f
        ON f.matid = a.matid
        LEFT JOIN TB_ARG_ATRGRIFE g
        ON g.argid = f.argid
        LEFT JOIN TB_ACN_ATRCORNUMERICA h
        ON h.ACNID = f.ACNID
        LEFT JOIN TB_MPV_MATPRECOVENDA i
        ON i.MATID = a.MATID
        LEFT JOIN TB_ARM_ATRMODELO j ON j.ARMID = f.ARMID
        WHERE c.filid = 5
        AND g.argtipoprod = 'A'
        GROUP BY a.MATDTCADASTRO, a.MATFANTASIA, h.ACNDESCRICAO, g.argdescricao, g.argtipoprod, a.MATSEQUENCIAL, d.pesnome,  i.MPVPRECOVENDA,b.MECQUANTIDADE1,j.ARMDESCRICAO, QUANTIDADE";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obter uma lista de todas as imagens na pasta de origem
    $sourceImages = scandir($sourceDir);

    foreach ($sourceImages as $imagem) {
        $codigoImagem = pathinfo($imagem, PATHINFO_FILENAME);

        // Verificar se $codigoImagem não está vazio
        if (!empty($codigoImagem)) {
            foreach ($result as $row) {
                $codigoBanco = $row["MODELO"] . $row["ACNDESCRICAO"];
                $codSequencial = $row["COD_SEQUENCIAL"];

                // Comparar apenas a parte comum
                if (strpos($codigoBanco, $codigoImagem) !== false) {
                    $sourceImagePath = $sourceDir . $imagem;

                    // Remova repetições do código de imagem
                    $codigoImagemSemRepeticoes = removeRepetitions($codigoImagem);

                    $novoNomeImagem = "$codSequencial" . "_" . $codigoImagemSemRepeticoes . ".jpg"; // Ou a extensão correta da imagem
                    $novoCaminhoImagem = $sourceDir . $novoNomeImagem;

                    if (rename($sourceImagePath, $novoCaminhoImagem)) {
                        $mensagemSuccess = "Imagem com COD_SEQUENCIAL '$codSequencial' renomeada para '$novoNomeImagem'.";
                        $alertClassesSuccess = "flex bg-green-100 rounded-lg p-4 mb-4 text-sm text-green-700";

                        echo '<div class="' . $alertClassesSuccess . '" role="alert">';
                        echo '<div>';
                        echo '<span class="font-medium">Sucesso!</span> ' . $mensagemSuccess;
                        echo '</div>';
                        echo '</div>';
                    } else {
                        $mensagemDanger = "Falha ao renomear a imagem com COD_SEQUENCIAL '$codSequencial'.";
                        $alertClassesDanger = "flex bg-red-100 rounded-lg p-4 mb-4 text-sm text-red-700";

                        echo '<div class="' . $alertClassesDanger . '" role="alert">';
                        echo $alertIconSuccess;
                        echo '<div>';
                        echo '<span class="font-medium">Ocorreu algum erro!</span> ' . $mensagemDanger;
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }
        }
    }
} catch (PDOException $e) {
    $mensagem = "Erro na conexão com o banco de dados: " . $e->getMessage();
}

// ... Código posterior ...

?>
