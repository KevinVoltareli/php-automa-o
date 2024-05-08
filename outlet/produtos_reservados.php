<section class="antialiased bg-gray-100 text-gray-600 w-6/12">
    <div class="flex flex-col">
        <!-- Table -->
        <div class="w-full bg-white shadow-lg rounded-sm border border-gray-200">
            <header class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Produtos Reservados</h2>
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
                                        WHERE b.MATFANTASIA LIKE '%OUTLET%'
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
                                    echo '</tr>';
                                }
                            } catch (PDOException $e) {
                                echo "Erro na conexão com o banco de dados: " . $e->getMessage();
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
