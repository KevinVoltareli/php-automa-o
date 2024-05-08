<script src="https://cdn.tailwindcss.com"></script>
<?php
// Diretório de destino das imagens (pasta "DESATIVADAS")
$destinationDir = '\\\\192.168.30.200\\dados$\\Depto Marketing\\DESATIVADAS\\';
$sourceDir = '\\\\192.168.30.200\\dados$\\Depto E-Commerce\\teste\\estoque\\';

if (isset($_POST['reserve_product']) && isset($_POST['cod_sequencial'])) {
    // Produto selecionado para reserva
    $selectedProduct = $_POST['cod_sequencial'];

    // Inserir um registro na tabela produtos_reservados
    try {
       $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $insertQuery = "INSERT INTO TB_PECAS_RESERVADAS (SEQUENCIAL, DIA_RESERVA) VALUES (:cod_sequencial, Current_timestamp)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bindParam(':cod_sequencial', $selectedProduct, PDO::PARAM_INT);
        $stmt->execute();

        // Você pode exibir uma mensagem informando que o produto foi reservado
        echo "<div class='p-4 bg-blue-200 text-blue-700 rounded-lg mt-4'> Produto com Código Sequencial: $selectedProduct foi reservado.</div><br>";

    } catch (PDOException $e) {
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    }
} elseif(isset($_POST['return_product']) && isset($_POST['cod_sequencial'])) {
    // Produto selecionado para retornar
    $selectedProduct = $_POST['cod_sequencial'];

    // Obtém a lista de arquivos na pasta de destino
   // $destinationImages = scandir($destinationDir);

       try {
        $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $deleteQuery = "DELETE FROM TB_PECAS_RESERVADAS WHERE SEQUENCIAL = :cod_sequencial";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bindParam(':cod_sequencial', $selectedProduct, PDO::PARAM_INT);
        $stmt->execute();

        // Obtém a lista de arquivos na pasta de destino
        $destinationImages = scandir($destinationDir);

        // Itera sobre os arquivos na pasta de destino
        foreach ($destinationImages as $image) {
            if ($image !== '.' && $image !== '..') {
                // Verifica se o nome do arquivo contém o código sequencial
                if (strpos($image, $selectedProduct) !== false) {
                    // Constrói o caminho completo da imagem na pasta de destino
                    $destinationImagePath = $destinationDir . $image;

                    // Constrói o caminho completo da imagem na pasta de origem
                    $sourceImagePath = $sourceDir . $image;

                    // Verifica se o arquivo de destino existe e copia-o de volta para a pasta de origem
                    if (file_exists($destinationImagePath)) {
                        if (copy($destinationImagePath, $sourceImagePath)) {
                            // Remove o arquivo da pasta de destino
                            unlink($destinationImagePath);
                            echo "<div class='p-4 bg-green-200 text-green-700 rounded-lg mt-4'> Produto com Código Sequencial: $selectedProduct foi retornado para a pasta de origem.</div><br>";
                        } else {
                            echo "<div class='p-4 bg-green-200 text-red-500 rounded-lg mt-4'>Falha ao mover o produto com Código Sequencial: $selectedProduct de volta para a pasta de origem.</div><br>";
                        }
                    }
                }
            }
        }

        // Exiba uma mensagem informando que o produto foi retornado e o registro foi removido
        echo "<div class='p-4 bg-green-200 text-green-700 rounded-lg mt-4'> Produto com Código Sequencial: $selectedProduct foi retornado para a pasta de origem e a reserva foi removida.</div><br>";

    } catch (PDOException $e) {
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    }
}

try {
    // Create a new PDO connection
    $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter uma lista de todas as imagens na pasta de destino
    $destinationImages = scandir($destinationDir);

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
        GROUP BY a.MATDTCADASTRO, a.MATFANTASIA, h.ACNDESCRICAO, g.argdescricao, g.argtipoprod, a.MATSEQUENCIAL, d.pesnome,  i.MPVPRECOVENDA,b.MECQUANTIDADE1,j.ARMDESCRICAO";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Iterar sobre os produtos no banco de dados
    foreach ($result as $row) {
        $QTD = $row["QUANTIDADE"];
        $codSequencial = $row["COD_SEQUENCIAL"];
        $productName = $row["FANTASIA"];

        // Construir um padrão para o código sequencial no nome da imagem
        $padraoCodigo = $codSequencial . "_";

        // Procurar por imagens na pasta de destino que correspondam ao padrão de código sequencial
        $matchingImages = preg_grep("/^$padraoCodigo/", $destinationImages);

        // Verificar se a imagem existe na pasta de destino (DESATIVADAS)
        if (count($matchingImages) > 0) {
            // Verificar se o produto está em estoque (quantidade maior que 0 e menor ou igual a 3)
            if ($QTD > 0 && $QTD <= 3) {
                // O produto atende aos critérios, você pode exibir as informações aqui
              echo '<div class="bg-gray-100 p-4 my-4 rounded-lg">';
                echo '<p class="text-xl font-bold text-blue-600">Código Sequencial: ' . $codSequencial . '</p>';
                echo '<p class="text-lg font-semibold">Nome do Produto: ' . $productName . '</p>';
                echo '<p class="text-lg font-semibold">Preço: R$ ' . number_format($row['PRECO'], 2, ',', '') . '</p>';
                echo '<p class="text-lg font-semibold">Quantidade em Estoque: ' . $QTD . '</p>';
                echo '<form method="post" class="mt-4">';
                echo '<input type="hidden" name="cod_sequencial" value="' . $codSequencial . '">';
                echo '<button type="submit" name="return_product" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Retornar Produto</button>';
                echo '<button type="submit" name="reserve_product" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 ml-4 rounded">Reservar Produto</button>';

                echo '</form>';
                echo '</div>';



            }
        }
    }
} catch (PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}

// Fechar conexão com o banco de dados
$conn = null;
?>
