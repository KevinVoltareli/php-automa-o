
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

                    }
                }
            }
        }

     

    } catch (PDOException $e) {
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    }

    // Fechar conexão com o banco de dados
    $conn = null;
    ?>

<!DOCTYPE html>
<html>
<head>
    <title>Verificação de produtos</title>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

    <nav class="flex items-center justify-between flex-wrap bg-purple-400 p-6">
  <div class="flex items-center flex-no-shrink text-white mr-6">
    <img class=" h-24" src="img/logo.png">
    <span class="font-semibold text-xl tracking-tight ml-8">Projeto Xô Planilha</span>
  </div>
  <div class="block lg:hidden">
    <button class="flex items-center px-3 py-2 border rounded text-teal-lighter border-teal-light hover:text-white hover:border-white">
      <svg class="h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
    </button>
  </div>
  <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto">
    <div class="text-sm lg:flex-grow">
      <a href="http://192.168.30.252:9091/ProjetoXoPlanilha/index.php" class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter text-orange-300 text-lg mr-4">
      Estoque
      </a>
      <a href="http://192.168.30.252:9091/ProjetoXoPlanilha/acessorios/index.php" class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter text-white mr-4">
      Acessórios
      </a>
      <a href="http://192.168.30.252:9091/ProjetoXoPlanilha/outlet/index.php" class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter text-white">
        Outlet
      </a>
    </div>
    <!-- <div>
      <a href="#" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-teal hover:bg-white mt-4 lg:mt-0">Download</a>
    </div> -->
  </div>
</nav>


<div class="mt-4 ">

<a href="http://192.168.30.252:9091/ProjetoXoPlanilha/produtos_zerados.php" class="border-slate-900 border-2 rounded-2xl ml-6 p-2 text-black  hover:bg-gray-200">
        Lista de produtos que acabaram em estoque
</a>
<a href="http://192.168.30.252:9091/ProjetoXoPlanilha/produtos_sem_foto.php" class="border-slate-900 border-2 rounded-2xl ml-6 p-2 text-black  hover:bg-gray-200">
        Produtos em estoque sem foto na pasta
</a>
<a href="http://192.168.30.252:9091/ProjetoXoPlanilha/processa_pecas_desativadas.php" class="border-slate-900 border-2 rounded-2xl ml-6 p-2 text-black  hover:bg-gray-200">
       DESATIVADAS ESTOQUE < 3
</a>


</div>

<div class="flex flex-col justify-between w-screen mt-4 ">
    <?php  include 'verifica_sequencial.php';  ?>
</div>

<div class="flex justify-between w-screen mt-8 ml-6 ">

    <?php
    // ... Seu código PHP anterior para buscar os produtos com preços incorretos ...


   
    include 'produtos_reservados.php';

    ?>


    </div>


    <div class="flex justify-between w-screen mt-8 ml-6 ">

    <?php
    // ... Seu código PHP anterior para buscar os produtos com preços incorretos ...


   
    include 'produtos_incorretos.php';
    include 'produtos_incorretos_estoque.php';


    ?>


    </div>



    <?php  if($incorrectImageCount === 0 && $desabilitaBotaoEstoque === false) { ?>
      <button id="verificarPrecos" class="mt-4 ml-6 mb-4 group relative h-8 w-44 overflow-hidden rounded-2xl bg-purple-500 text-md font-bold text-white">
    Limpar fotos
    <div class="absolute inset-0 h-full w-full scale-0 rounded-2xl transition-all duration-300 group-hover:scale-100 group-hover:bg-white/30"></div>
  </button>
<?php } else{ ?>
 

  <button disabled type="button" class="mt-4 ml-6 group relative h-8 w-48 overflow-hidden rounded-2xl bg-gray-300 text-md font-bold text-white">
                Botão desabilitado
                <div class="absolute inset-0 h-full w-full scale-0 rounded-2xl transition-all duration-300 group-hover:scale-100 group-hover:bg-red-400 text-center">Necessário correções</div>
            </button>

<?php } ?>

<!-- Loader -->

<div id="loader" class="hidden fixed top-0 left-0 w-full h-full bg-gray-200 opacity-75 z-50">
    <div class="flex justify-center items-center h-full">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500"></div>
    </div>
</div>

    <div id="resultadoVerificacao"></div>
    
    <script>
        // Função para executar a verificação de preços
        // Função para mostrar o loader
    function showLoader() {
        document.getElementById('loader').classList.remove('hidden');
    }

    // Função para ocultar o loader
    function hideLoader() {
        document.getElementById('loader').classList.add('hidden');
    }

    // Função para executar a verificação de preços
    function verificarPrecos() {
        showLoader(); // Mostrar o loader ao iniciar a requisição

        $.ajax({
            url: 'verifica.php',
            type: 'POST',
            success: function(response) {
                $('#resultadoVerificacao').html(response);
                hideLoader(); // Ocultar o loader após a conclusão da requisição
            },
            error: function() {
                $('#resultadoVerificacao').html('Erro ao executar a verificação de preços.');
                hideLoader(); // Ocultar o loader em caso de erro
            }
        });
    }

        // Associar a função ao clique do botão
        $('#verificarPrecos').click(function() {
            verificarPrecos();
        });
    </script>

</body>
</html>