<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos em Estoque sem Imagem Correspondente</title>

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
<body>
    <div class="flex justify-between"> 
    <h1 class="text-xl font-bold mt-2 ml-2">Produtos em Estoque sem Imagem Correspondente</h1>
    <a href="http://192.168.30.252:9091/ProjetoXoPlanilha/index.php" class="text-xl font-bold mt-2 mr-2 text-red-600 hover:text-red-300">Voltar</a>
    </div>
    <ul>

    <?php

    // Diretório de origem das imagens
    $sourceDir = '\\\\192.168.30.200\\dados$\\Depto E-Commerce\\teste\\estoque\\';

    try {
        // Código de conexão com o banco de dados
        $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consulta ao banco de dados
        $sql = "SELECT a.MATDTCADASTRO AS DT_CADASTRO, a.MATFANTASIA AS FANTASIA, h.ACNDESCRICAO , g.argdescricao AS GRIFE, g.argtipoprod AS TIPO_GRIFE, a.MATSEQUENCIAL AS COD_SEQUENCIAL, d.pesnome AS NOME_LOJA, 
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
        WHERE c.filid = 5
        AND g.argtipoprod = 'A'
       AND NOT a.MATFANTASIA LIKE '%OUTLET%'
        GROUP BY a.MATDTCADASTRO, a.MATFANTASIA, h.ACNDESCRICAO, g.argdescricao, g.argtipoprod, a.MATSEQUENCIAL, d.pesnome,  i.MPVPRECOVENDA,b.MECQUANTIDADE1";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obter uma lista de todas as imagens na pasta de origem
        $sourceImages = scandir($sourceDir);

        $productsWithoutImage = [];

        foreach ($result as $row) {
            $QTD = $row["QUANTIDADE"];
            $codSequencial = $row["COD_SEQUENCIAL"];
            $padraoCodigo = $codSequencial . "_";
            $matchingImages = preg_grep("/^$padraoCodigo/", $sourceImages);

            // Verificar se o produto está em estoque e não tem imagem correspondente
            if ($QTD > 0 && empty($matchingImages)) {
                $productsWithoutImage[] = $row["FANTASIA"]; // Adicione o nome do produto à lista
            }
        }

        foreach ($productsWithoutImage as $productName) {
            echo '<li class="bg-slate-100 hover:bg-slate-50 m-2 p-2">' . $productName . '</li>';
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
