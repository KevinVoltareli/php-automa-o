<?php



$sourceDir = '\\\\192.168.30.200\\dados$\\Depto E-Commerce\\teste\\estoque\\';
$destinationDir = '\\\\192.168.30.200\\dados$\\Depto Marketing\\DESATIVADAS\\';

try {
    $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

    $produtosComPrecosIncorretos = array();

    $sourceImages = scandir($destinationDir);
    $sourceDestinationImages = scandir($destinationDir);

    $desabilitaBotaoEstoque = false;

    foreach ($result as $row) {
        $QTD = $row["QUANTIDADE"];
        $codSequencial = $row["COD_SEQUENCIAL"];
        $padraoCodigo = $codSequencial . "_";

        $matchingImagesDestination = preg_grep("/^$padraoCodigo/", $sourceImages);

        foreach ($matchingImagesDestination as $imagem) {
            $sourceImagePath = $destinationDir . $imagem;
            $destinationImagePath = $destinationDir . $imagem;

            preg_match('/PRECO_(\d+)_/', $imagem, $matches);
            $precoProdutoImagem = isset($matches[1]) ? $matches[1] : '';
            $precoBancoFormatado =  intval($row["PRECO"]);

            if ($precoProdutoImagem !== '' && $precoProdutoImagem != $precoBancoFormatado) {
                $produtosComPrecosIncorretos[] = array(
                    "COD_SEQUENCIAL" => $codSequencial,
                    "PRECO" => $precoBancoFormatado,
                    "PRECO_IMAGEM" => number_format($precoProdutoImagem,2,".",""),  
                    "IMAGEM" => $imagem
                );
                $desabilitaBotaoEstoque = true;
            }
        }
    }
} catch (PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}

$conn = null;
?>

<div class="flex flex-col w-full">
    <?php if ($produtosComPrecosIncorretos) { ?>
        <section class="antialiased bg-gray-100 text-gray-600 w-10/12">
            <div class="flex flex-col ">
                <div class="w-full  bg-white shadow-lg rounded-sm border border-gray-200">
                    <header class="px-5 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-800">Produtos com Preços Incorretos pasta DESATIVADAS</h2>
                    </header>
                    <div class="p-3">
                        <div class="overflow-x-auto">
                            <table class="table-auto w-full">
                                <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50">
                                    <tr>
                                        <th class="p-2 whitespace-nowrap">
                                            <div class="font-semibold text-left">Sequencial</div>
                                        </th>
                                        <th class="p-2 whitespace-nowrap">
                                            <div class="font-semibold text-left">Preço desatualizado</div>
                                        </th>
                                        <th class="p-2 whitespace-nowrap">
                                            <div class="font-semibold text-left">Preço atualizado</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-100">
                                    <?php foreach ($produtosComPrecosIncorretos as $produto) { ?>
                                        <tr>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="font-medium text-gray-800"> <?php echo $produto["COD_SEQUENCIAL"]; ?></div>
                                                </div>
                                            </td>
                                            <td class="p-2 whitespace-nowrap font-medium text-red-500">
                                                <div class="text-left"><?php echo $produto["PRECO_IMAGEM"]; ?></div>
                                            </td>
                                            <td class="p-2 whitespace-nowrap">
                                                <div class="text-left font-medium text-green-500"><?php echo $produto["PRECO"]; ?> </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>

    <?php 
      include 'desabilita_botao.php';   
    if (!empty($produtosComPrecosIncorretos)) { ?>
        <form method='post' action='confirmar_alteracao.php'>
            <input type='hidden' name='confirmar_alteracao' value='1'>
            <?php if($incorrectImageCount === 0) {
 ?>
            <button type='submit' class="mt-4 group relative h-8 w-48 overflow-hidden rounded-2xl bg-purple-500 text-md font-bold text-white">
                Confirmar alterações
                <div class="absolute inset-0 h-full w-full scale-0 rounded-2xl transition-all duration-300 group-hover:scale-100 group-hover:bg-white/30"></div>
            </button>
                <?php } else { ?>
               <button disabled type="button" class="mt-4 group relative h-8 w-48 overflow-hidden rounded-2xl bg-gray-300 text-md font-bold text-white">
                Botão desabilitado
                <div class="absolute inset-0 h-full w-full scale-0 rounded-2xl transition-all duration-300 group-hover:scale-100 group-hover:bg-red-400 text-center">Necessário correções</div>
            </button>
                   <?php } ?>
   
        </form>

      <?php } else { ?>
          <p>Não há produtos com preços incorretos na pasta estoque.</p>
   <?php } ?>
</div>
