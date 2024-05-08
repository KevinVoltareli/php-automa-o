
<style>

    html {
        list-style: none; 
    }

    .modal {
        display: none; /* Inicia oculto por padrão */
        position: fixed;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
            max-height: 80%; /* ou max-height: 400px; */
    /* outras propriedades... */
    }

    .modal-content{
        min-width:600px;
        max-height: 800px;
    }

    .close {
        cursor: pointer;
    }

   
</style>


<section class="antialiased bg-gray-100 text-gray-600 w-8/12">
    <div class="flex flex-col">
        <!-- Table -->
        <div class="w-full bg-white shadow-lg rounded-sm border border-gray-200">
            <header class="px-5 py-4 border-b border-gray-100 flex justify-between">
                <h2 class="font-semibold text-gray-800">Produtos Reservados</h2>
                <a href="http://192.168.30.252:9091/ProjetoXoPlanilha/rel_coment.php" class="font-semibold  text-blue-500 hover:text-blue-300">Ver todos comentários</a>
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
                                    <div class="font-semibold text-left">Nome do Produto</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Modelo</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Cor</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Quantidade</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Dia de Reserva</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Dias de Atraso</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Ação</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Comentário</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php
                            $hoje = new DateTime();


       

                            try {
                                $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
                                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                $sql = "SELECT a.SEQUENCIAL, b.MATFANTASIA, d.ARMDESCRICAO, e.ACNDESCRICAO, a.DIA_RESERVA,sum(f.MECQUANTIDADE1) AS QTD
                                        FROM TB_PECAS_RESERVADAS a
                                        LEFT JOIN TB_MAT_MATERIAL b ON b.MATSEQUENCIAL = a.SEQUENCIAL
                                        LEFT JOIN TB_AAT_ATRIBUTOS c ON c.MATID = b.MATID
                                        LEFT JOIN TB_ARM_ATRMODELO d ON d.ARMID = c.ARMID
                                        LEFT JOIN TB_ACN_ATRCORNUMERICA e ON e.ACNID = c.ACNID
                                        LEFT JOIN TB_MEC_MATESTCONTROLE f ON f.MATID = b.MATID 
                                        WHERE NOT b.MATFANTASIA LIKE '%OUTLET%'
                                        AND f.FILID = '5'
                                        GROUP BY a.SEQUENCIAL,b.MATFANTASIA, d.ARMDESCRICAO, e.ACNDESCRICAO,a.DIA_RESERVA";

                                $stmt = $conn->prepare($sql);
                                $stmt->execute();

                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($result as $row) {
                                    $diaReserva = new DateTime($row["DIA_RESERVA"]);
                                    $diasDeAtraso = $diaReserva->diff($hoje)->days;

                                    // Defina a classe CSS com base nos dias de atraso
                                    $classeCSS = 'bg-green-100'; // Verde (sucesso)
                                    if ($diasDeAtraso >= 2 && $diasDeAtraso <= 3) {
                                        $classeCSS = 'bg-yellow-100'; // Amarelo
                                    } elseif ($diasDeAtraso >= 4) {
                                        $classeCSS = 'bg-red-100'; // Vermelho
                                    }

                                    echo '<tr class="' . $classeCSS . '">';
                                    echo '<td class="p-2 whitespace-nowrap"><div class="font-medium text-gray-800">' . $row["SEQUENCIAL"] . '</div></td>';
                                    echo '<td class="p-2 whitespace-nowrap"><div class="text-left">' . $row["MATFANTASIA"] . '</div></td>';
                                    echo '<td class="p-2 whitespace-nowrap"><div class="text-left">' . $row["ARMDESCRICAO"] . '</div></td>';
                                    echo '<td class="p-2 whitespace-nowrap"><div class="text-left">' . $row["ACNDESCRICAO"] . '</div></td>';                                    
                                    echo '<td class="p-2 whitespace-nowrap"><div class="text-left">' .  number_format($row["QTD"],0,",","")  . '</div></td>';
                                    $diaReservaFormatada = $diaReserva->format('d/m/Y');
                                    echo '<td class="p-2 whitespace-nowrap"><div class="text-left">' . $diaReservaFormatada . '</div></td>';
                                    echo '<td class="p-2 whitespace-nowrap"><div class="text-left">' . $diasDeAtraso . '</div></td>';

                                   
                                    echo '<td class="p-2 whitespace-nowrap">';
                                    echo '<form method="post" action="excluir_reserva.php">';
                                    echo '<input type="hidden" name="sequencial" value="' . $row["SEQUENCIAL"] . '">';
                                    echo '<button type="submit" class="text-red-600 hover:text-red-800">Excluir reserva</button>';
                                    echo '</form>';

                                     // Botão para abrir o modal de comentários

                                          echo '<td class="p-2 whitespace-nowrap"><div class="text-left"><button onclick="abrirComentarioModal(' . $row["SEQUENCIAL"] . ')" class="text-blue-600 hover:text-blue-800 ml-2">Comentário</button></div></td>';

                                   
                                    echo '</tr>';
                                   
                                   

                                }
                            } catch (PDOException $e) {
                                echo "Erro na conexão com o banco de dados: " . $e->getMessage();
                            }
                            ?>
                        </tbody>
                    </table>

<!-- Modal para adicionar comentários -->
<div id="comentarioModal" class="modal">
    <!-- Conteúdo do modal -->
    <div class="modal-content p-6 bg-white w-10/12 mx-auto rounded-md shadow-lg overflow-y-auto" >
        <span class="close text-gray-500 cursor-pointer absolute top-0 right-0 p-2">&times;</span>
        <h2 class="text-2xl font-bold mb-4">Comentários</h2>

        <!-- Resultado do filtro -->
        <div id="comentariosReserva" class="p-2"></div>

         <!-- Mensagens de sucesso ou erro -->
    <div id="mensagemSucesso" class="hidden text-green-600 mt-4"></div>
    <div id="mensagemErro" class="hidden text-red-600 mt-4"></div>

        <!-- Formulário para adicionar comentários -->
        <form id="comentarioForm">
            <label for="comentario" class="block text-sm font-medium text-gray-700">Novo Comentário:</label>
            <textarea name="comentario" id="comentario" rows="4" class="w-full border rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300"></textarea>
            <input type="hidden" name="sequencial" id="comentarioSequencial" value="">
            <button type="button" onclick="inserirComentario()" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring focus:border-blue-300 transition-all">Inserir Comentário</button>
        </form>
    </div>
</div>

      <script>

          document.querySelector('.close').addEventListener('click', function () {
        fecharComentarioModal();
    });

    function abrirComentarioModal(sequencial) {
        document.getElementById('comentarioModal').style.display = 'block';
        document.getElementById('comentarioSequencial').value = sequencial;
        document.getElementById('comentarioForm').reset();
        exibirComentarios(sequencial);
    }

    function fecharComentarioModal() {
        document.getElementById('comentarioModal').style.display = 'none';
    }

  function inserirComentario() {
    // Obter dados do formulário
    var sequencial = document.getElementById('comentarioSequencial').value;
    var comentario = document.getElementById('comentario').value;

    // Verificar se o comentário não está vazio
    if (comentario.trim() === '') {
        exibirMensagemErro('O comentário não pode ser vazio.');
        return;
    }

    // Enviar dados para o servidor usando AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'inserir_comentario.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                // Comentário inserido com sucesso
                exibirComentarios(sequencial);
                exibirMensagemSucesso('Comentário inserido com sucesso.');
            } else {
                // Erro ao inserir comentário
                exibirMensagemErro('Erro ao inserir o comentário. Tente novamente.');
            }
        }
    };
    xhr.send('sequencial=' + sequencial + '&comentario=' + comentario);
}

// Função para exibir mensagem de sucesso
function exibirMensagemSucesso(mensagem) {
    var mensagemSucesso = document.getElementById('mensagemSucesso');
    mensagemSucesso.innerText = mensagem;
    mensagemSucesso.style.display = 'block';

    // Ocultar mensagem de erro, se estiver visível
    document.getElementById('mensagemErro').style.display = 'none';
}

// Função para exibir mensagem de erro
function exibirMensagemErro(mensagem) {
    var mensagemErro = document.getElementById('mensagemErro');
    mensagemErro.innerText = mensagem;
    mensagemErro.style.display = 'block';

    // Ocultar mensagem de sucesso, se estiver visível
    document.getElementById('mensagemSucesso').style.display = 'none';
}

  function exibirComentarios(sequencial) {
    // Limpar a lista de comentários antes de adicionar novos
    var comentariosReserva = document.getElementById('comentariosReserva');
    comentariosReserva.innerHTML = '';

    // Enviar requisição AJAX para recuperar comentários
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'recuperar_comentarios.php?sequencial=' + sequencial, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Manipular a resposta (lista de comentários) recebida do servidor
            var comentarios = JSON.parse(xhr.responseText);

            // Adicionar cada comentário à lista
            comentarios.forEach(function (comentario) {
                var comentarioItem = document.createElement('li');

                // Formatar a data como DD/MM
                var dataFormatada = new Date(comentario.DATA);
                var dia = String(dataFormatada.getDate()).padStart(2, '0');
                var mes = String(dataFormatada.getMonth() + 1).padStart(2, '0'); // Mês é base 0, então adicionamos 1
                var ano = dataFormatada.getFullYear();
                var dataComentario = dia + '/' + mes + '/' + ano;

                // Adicionar classes dinâmicas para aplicar estilos individuais
                comentarioItem.className = 'p-2';
              comentarioItem.innerHTML = '<div class="bg-slate-200 flex flex-col flex-wrap p-2 rounded-md w-auto">' +
    '<div class="text-gray-500 text-bold">' + dataComentario + '</div>' +
    '<div class="text-gray-800  mt-2 whitespace-pre-line break-all"> ' + comentario.COMENTARIO + '</div>' +
    '</div>';

                comentariosReserva.appendChild(comentarioItem);
            });

            // Exibir a lista de comentários
            document.getElementById('comentarioModal').style.display = 'block';
        }
    };
    xhr.send();
}

</script>
                </div>
            </div>
        </div>
    </div>
</section>
