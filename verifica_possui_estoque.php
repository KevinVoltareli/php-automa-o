<?php

// Diretórios de origem e destino das imagens
$destinationDir = __DIR__ . "/estoque/";
$sourceDir = __DIR__ . "/copias/";

try {   
    // Create a new PDO connection
    $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buscar produtos do banco de dados
    $sql = "SELECT a.MATDTCADASTRO AS DT_CADASTRO, a.MATFANTASIA AS FANTASIA, g.argdescricao AS GRIFE, g.argtipoprod AS TIPO_GRIFE, a.MATSEQUENCIAL AS COD_SEQUENCIAL, d.pesnome AS NOME_LOJA,    CAST(b.MECQUANTIDADE1 AS INT) AS QUANTIDADE
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
            WHERE c.filid = 5
            AND g.argtipoprod = 'A'";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $QTD = $row["QUANTIDADE"];
        $codSequencial = $row["COD_SEQUENCIAL"];

        // Construir o nome da imagem baseado no código sequencial
        $imagemNome = $codSequencial . ".jpg";

        $sourceImagePath = $sourceDir . $imagemNome;
        $destinationImagePath = $destinationDir . $imagemNome;

        if ($QTD > 0 && file_exists($destinationImagePath)) {
            // Mover a imagem de volta para a pasta de estoque
            if (rename($destinationImagePath, $sourceImagePath)) {
                echo "Imagem com COD_SEQUENCIAL '$codSequencial' retornou para a pasta de estoque.<br>";
            } else {
                echo "Falha ao mover a imagem com COD_SEQUENCIAL '$codSequencial' de volta para a pasta de estoque.<br>";
            }
        }
    }

} catch (PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}

// Fechar conexão com o banco de dados
$conn = null;
?>
