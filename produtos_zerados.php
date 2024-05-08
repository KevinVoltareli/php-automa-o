<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


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

    <title>Imagens a serem retiradas</title>


     <style>
        body {
            font-family: Arial, sans-serif;
        }

        /* Esconde o modal por padrão */
        #myModal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        /* Estilo para o conteúdo do modal */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        /* Estilo para o botão de fechar o modal */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1 class="text-xl mt-2 ml-2 font-bold" >Imagens a serem retiradas da pasta de estoque</h1>


  <!-- Botão para abrir o modal -->
    <button class="bg-blue-500 p-2 mt-2 ml-2 mb-2 text-white rounded-md hover:bg-blue-300" onclick="openModal()">Abrir relatório</button>

    <div id="myModal" class="modal">
    <!-- Conteúdo do modal -->
    <div class="modal-content p-6">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 class="text-2xl font-bold mb-4">Filtrar por Intervalo de Data</h2>
        <form id="filtroForm" class="mb-4">
            <div class="flex space-x-4">
                <div class="flex-1">
                    <label for="dataInicio" class="block text-sm font-medium text-gray-700">Data Início:</label>
                    <input type="date" name="dataInicio" id="dataInicio" class="mt-1 p-2 w-full border rounded-md">
                </div>
                <div class="flex-1">
                    <label for="dataFim" class="block text-sm font-medium text-gray-700">Data Fim:</label>
                    <input type="date" name="dataFim" id="dataFim" class="mt-1 p-2 w-full border rounded-md">
                </div>
            </div>
            <button type="button" onclick="filtrarPorData()" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md">Filtrar</button>
        </form>

        <!-- Resultado do filtro -->
        <ul id="sequenciaisFiltrados" class="list-disc pl-6">
            <!-- Resultados aqui -->
        </ul>
    </div>
</div>

     
    </div>


        <?php
    // ... Seu código anterior

    if (isset($_POST['dataInicio']) && isset($_POST['dataFim'])) {
        try {
            $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $dataInicio = $_POST['dataInicio'];
            $dataFim = $_POST['dataFim'];

            $sql = "SELECT SEQUENCIAL FROM TB_REL_PECAS_ZERADAS WHERE DATA_ACESSO >= :dataInicio AND DATA_ACESSO <= :dataFim";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':dataInicio', $dataInicio, PDO::PARAM_STR);
            $stmt->bindParam(':dataFim', $dataFim, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo '<script>';
            echo 'var sequenciaisFiltrados = document.getElementById("sequenciaisFiltrados");';
            foreach ($result as $row) {
                echo 'var li = document.createElement("li");';
                echo 'li.textContent = "Sequencial: ' . $row['SEQUENCIAL'] . '";';
                echo 'sequenciaisFiltrados.appendChild(li);';
            }
            echo '</script>';

        } catch (PDOException $e) {
            echo "Erro na conexão com o banco de dados: " . $e->getMessage();
        }
    }
    ?>
        </div>
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

                        $dataAcesso = date("Y-m-d");  // Obter data atual no formato adequado
                    $existsSQL = "SELECT COUNT(*) FROM TB_REL_PECAS_ZERADAS WHERE SEQUENCIAL = :matSequencial AND DATA_ACESSO = :dataAcesso";
                    $existsStmt = $conn->prepare($existsSQL);
                    $existsStmt->bindParam(':matSequencial', $codSequencial, PDO::PARAM_INT);
                    $existsStmt->bindParam(':dataAcesso', $dataAcesso, PDO::PARAM_STR);
                    $existsStmt->execute();

                    $count = $existsStmt->fetchColumn();

                     if ($count == 0) {
                         $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        // Inserir informações na tabela TB_REL_PECAS_ZERADAS
                        $insertSQL = "INSERT INTO TB_REL_PECAS_ZERADAS (SEQUENCIAL, DATA_ACESSO) VALUES (:matSequencial, :dataAcesso)";
                        $insertStmt = $conn->prepare($insertSQL);
                        $insertStmt->bindParam(':matSequencial', $codSequencial, PDO::PARAM_INT);
                        $insertStmt->bindParam(':dataAcesso', $dataAcesso, PDO::PARAM_STR);
                        $insertStmt->execute();

                        // Adicionar imagem à lista de remoção
                        echo '<li>' . $imagem . '</li>';
                    }
                }
            }
        }

        foreach ($imagesToRemove as $imagem) {
            echo '<li class="mt-2 ml-2 mr-2 bg-orange-200 p-2">' . $imagem . '</li>';
        }

    } catch (PDOException $e) {
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    }

    // Fechar conexão com o banco de dados
    $conn = null;
    ?>

    </ul>



<script>
        function openModal() {
            document.getElementById('myModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('myModal').style.display = 'none';
        }

        function filtrarPorData() {
            var formData = $('#filtroForm').serialize();

            $.ajax({
                type: 'POST',
                url: 'busca_prod_zerados_rel.php',
                data: formData,
                success: function(data) {
                    $('#sequenciaisFiltrados').html(data);
                },
                error: function() {
                    alert('Erro ao carregar os dados.');
                }
            });
        }
    </script>
</body>
</html>
