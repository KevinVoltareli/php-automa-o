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

// Caminho UNC para as pastas de origem e destino das imagens no servidor remoto
$sourceDir = '\\\\192.168.30.200\\dados$\\Depto E-Commerce\\teste\\estoque\\';
$destinationDir = '\\\\192.168.30.200\\dados$\\Depto Marketing\\DESATIVADAS\\';

$mensagem = ""; // Variável para armazenar mensagens de sucesso ou erro

if (isset($_POST["confirmar_alteracao"]) && $_POST["confirmar_alteracao"] == 1) {
    try {
        // Código de conexão ao banco de dados
        $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
    // Buscar produtos do banco de dados
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
        AND g.argtipoprod = 'A'
        AND NOT a.MATFANTASIA LIKE '%OUTLET%'    
        AND NOT i.MPVDTALTERACAO IS NULL
        GROUP BY a.MATDTCADASTRO, a.MATFANTASIA, h.ACNDESCRICAO, g.argdescricao, g.argtipoprod, a.MATSEQUENCIAL, d.pesnome,  i.MPVPRECOVENDA,b.MECQUANTIDADE1,j.ARMDESCRICAO";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
             // Obter uma lista de todas as imagens na pasta de origem
    $sourceImages = scandir($sourceDir);
    $sourceDestinationImages = scandir($destinationDir);

foreach ($result as $row) {
        $QTD = $row["QUANTIDADE"];
        $codSequencial = $row["COD_SEQUENCIAL"];

        // Construir um padrão para o código sequencial no nome da imagem
        $padraoCodigo = $codSequencial . "_";

        // Procurar por imagens na pasta de origem que correspondam ao padrão de código sequencial
        $matchingImages = preg_grep("/^$padraoCodigo/", $sourceImages);
        $matchingImagesDestination = preg_grep("/^$padraoCodigo/", $sourceDestinationImages);

        foreach ($matchingImages as $imagem) {
            $sourceImagePath = $sourceDir . $imagem;
            $destinationImagePath = $destinationDir . $imagem;


            // Se o produto não estiver em estoque e a imagem estiver na pasta de estoque
        
        }


        foreach ($matchingImagesDestination as $imagem) {
            $sourceImagePath = $sourceDir . $imagem;
            $destinationImagePath = $destinationDir . $imagem;

        
  // Aqui ele procura no nome o parametro PRECO_ para verificar se o preço esta correto 
        preg_match('/PRECO_(\d+)/', $imagem, $matches);
         $precoProdutoImagem = isset($matches[1]) ? $matches[1] : '';
           $precoBancoFormatado = intval($row["PRECO"]);


   // Comparar o preço extraído da imagem com o preço do produto do banco de dados
    if ($precoProdutoImagem !== '' && $precoProdutoImagem != $precoBancoFormatado) {
       

        // Renomear a imagem com o preço correto
        $novoNomeImagem = str_replace("PRECO_$precoProdutoImagem", "PRECO_" . $precoBancoFormatado, $imagem);
        $novoCaminhoImagem = $destinationDir . $novoNomeImagem;

        if (rename($destinationImagePath, $novoCaminhoImagem)) {

$mensagemSuccess = " Imagem com COD_SEQUENCIAL '$codSequencial' renomeada para '$novoNomeImagem'.";
 $alertClassesSuccess = "flex bg-green-100 rounded-lg p-4 mb-4 text-sm text-green-700";
       
        
        echo '<div class="' . $alertClassesSuccess . '" role="alert">';
       
        echo '<div>';
        echo '<span class="font-medium">Sucesso!</span> ' . $mensagemSuccess;
        echo '</div>';
        echo '</div>';

        } else {

            $mensagemDanger = "Falha ao renomear a imagem com COD_SEQUENCIAL '$codSequencial'.";
 $alertClassesDanger = "flex bg-red-100 rounded-lg p-4 mb-4 text-sm text-red-700";
        $alertIconSuccess = '<svg class="w-5 h-5 inline mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
        
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

    } catch (PDOException $e) {
        $mensagem = "Erro na conexão com o banco de dados: " . $e->getMessage();
    }
}

?>
