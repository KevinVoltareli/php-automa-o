<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imagens a serem retiradas</title>
</head>
<body>
    <h1>Imagens a serem retiradas da pasta de estoque</h1>
    <ul>

    <?php

    // Diretório de origem das imagens
    $sourceDir = '\\\\192.168.30.200\\dados$\\Depto E-Commerce\\teste\\acessorios\\';

    try {
        // Código de conexão com o banco de dados
        $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consulta ao banco de dados
        $sql = "SELECT a.MATDTCADASTRO AS DT_CADASTRO, a.MATFANTASIA AS FANTASIA, h.ACNDESCRICAO as ACNDESCRICAO, g.argdescricao AS GRIFE, g.argtipoprod AS TIPO_GRIFE, 
        a.MATSEQUENCIAL AS COD_SEQUENCIAL, d.pesnome AS NOME_LOJA, j.ARMDESCRICAO AS MODELO,
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
        AND g.argtipoprod = 'O'
        AND NOT a.MATFANTASIA = 'LIMPA LENTES'
        AND NOT a.MATFANTASIA = 'KIT LIMPEZA'
        AND NOT a.MATFANTASIA = 'LENCO MAGIFLANELASSORTIDAS15X15'
        GROUP BY a.MATDTCADASTRO, a.MATFANTASIA, h.ACNDESCRICAO, g.argdescricao, g.argtipoprod, a.MATSEQUENCIAL, d.pesnome,  i.MPVPRECOVENDA,b.MECQUANTIDADE1,j.ARMDESCRICAO";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obter uma lista de todas as imagens na pasta de origem
        $sourceImages = scandir($sourceDir);

        $imagesToRemove = [];

          foreach ($result as $row) {
            $QTD = $row["QUANTIDADE"];
            $codSequencial = $row["COD_SEQUENCIAL"];
            $padraoCodigo = $codSequencial . "_";
            $matchingImages = preg_grep("/^$padraoCodigo/", $sourceImages);

            foreach ($matchingImages as $imagem) {
                $sourceImagePath = $sourceDir . $imagem;
                // Verificar se o produto não está em estoque
                if ($QTD === 0 && file_exists($sourceImagePath)) {
                    $imagesToRemove[] = $imagem;
                }
            }
        }

        foreach ($imagesToRemove as $imagem) {
            echo '<li>' . $imagem . '</li>';
        }

    } catch (PDOException $e) {
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    }

    // Fechar conexão com o banco de dados
    $conn = null;
    ?>

    </ul>
</body>
</html>
