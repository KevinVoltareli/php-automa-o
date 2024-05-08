<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clifford: '#da373d',
                    }
                }
            }
        }
    </script>
</head>

<?php

$sourceDir = '\\\\192.168.30.200\\dados$\\Depto E-Commerce\\teste\\estoque\\';

try {
    $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
        GROUP BY a.MATDTCADASTRO, a.MATFANTASIA, h.ACNDESCRICAO, g.argdescricao, g.argtipoprod, a.MATSEQUENCIAL, d.pesnome,  i.MPVPRECOVENDA,b.MECQUANTIDADE1,j.ARMDESCRICAO";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sourceImages = scandir($sourceDir);

    $incorrectImages = [];

    foreach ($sourceImages as $imagem) {
        if ($imagem === '.' || $imagem === '..') {
            continue;
        }

  preg_match('/^(\d+)_(.*?)_([a-zA-Z]+)?(\d+)[AC]([^_]+)?_/', $imagem, $matches);

        if (count($matches) >= 4) {
            $codigoImagem = $matches[1];
            $modeloImagem = $matches[3].$matches[4];
            $imagemCorreta = false;

            foreach ($result as $row) {
                $codigoBanco = $row["COD_SEQUENCIAL"]; // Modifique para a coluna correta
                $modeloBanco = $row["MODELO"]; // Modifique para a coluna correta

                // Comparar apenas a parte comum
                if ($codigoBanco == $codigoImagem && $modeloBanco == $modeloImagem) {
                    // Imagem com sequencial e modelo corretos
                    $imagemCorreta = true;
                    break; // Sair do loop ao encontrar uma correspondência
                }
            }

            if (!$imagemCorreta) {
                $incorrectImages[] = $imagem;
            }
        }
    }

    $incorrectImageCount = count($incorrectImages);



    if (!empty($incorrectImages)) {
        foreach ($incorrectImages as $imagem) {
            echo '<div class=" bg-red-100 rounded-lg p-4 mb-4 text-sm text-red-700" role="alert">';
            echo '<div>';
            echo '<span class="font-medium">Nome de imagem incorreto!</span> A imagem \'' . $imagem . '\' está com nome incorreto.';
            echo '</div>';
            echo '</div>';
        }
    }

} catch (PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}

$conn = null;
?>
