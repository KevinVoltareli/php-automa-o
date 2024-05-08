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
    <img class=" h-24" src="../img/logo.png">
    <span class="font-semibold text-xl tracking-tight ml-8">Projeto Xô Planilha</span>
  </div>
  <div class="block lg:hidden">
    <button class="flex items-center px-3 py-2 border rounded text-teal-lighter border-teal-light hover:text-white hover:border-white">
      <svg class="h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
    </button>
  </div>
  <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto">
    <div class="text-sm lg:flex-grow">
     <a href="http://192.168.30.252:9091/ProjetoXoPlanilha/index.php" class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter text-white mr-4">
      Estoque
      </a>
      <a href="http://192.168.30.252:9091/ProjetoXoPlanilha/acessorios/index.php" class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter text-orange-300 text-lg mr-4">
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

<a href="http://192.168.30.252:9091/ProjetoXoPlanilha/acessorios/produtos_zerados.php" class="border-slate-900 border-2 rounded-2xl ml-6 p-2 text-black  hover:bg-gray-200">
        Lista de produtos que acabaram em estoque
</a>
<a href="http://192.168.30.252:9091/ProjetoXoPlanilha/acessorios/produtos_sem_foto.php" class="border-slate-900 border-2 rounded-2xl ml-6 p-2 text-black  hover:bg-gray-200">
        Produtos em estoque sem foto na pasta
</a>


</div>

<div class="flex flex-col justify-between w-screen mt-4 ">
    <?php  include 'verifica_sequencial.php';  ?>
</div>

<div class="flex justify-between w-screen mt-8 ml-6 ">

    

    </div>


    <div class="flex justify-between w-screen mt-8 ml-6 ">

    <?php
    // ... Seu código PHP anterior para buscar os produtos com preços incorretos ...


   
    include 'produtos_incorretos.php';
    include 'produtos_incorretos_estoque.php';


    ?>


    </div>



    <?php  if($desabilitaBotao === false) { ?>
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

    <div id="resultadoVerificacao"></div>
    
    <script>
        // Função para executar a verificação de preços
        function verificarPrecos() {
            $.ajax({
                url: 'verifica.php', // Arquivo PHP que será executado
                type: 'POST',
                success: function(response) {
                    // Exibir a resposta na div de resultados
                    $('#resultadoVerificacao').html(response);
                },
                error: function() {
                    $('#resultadoVerificacao').html('Erro ao executar a verificação de preços.');
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