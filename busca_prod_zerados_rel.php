   
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


   <?php
    // ... Seu código anterior

    if (isset($_POST['dataInicio']) && isset($_POST['dataFim'])) {
        try {
            $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $dataInicio = $_POST['dataInicio'];
            $dataFim = $_POST['dataFim'];

            $sql = "SELECT a.SEQUENCIAL AS SEQUENCIAL, b.MATFANTASIA AS FANTASIA, d.ARMDESCRICAO AS CODMODELO,f.ACNDESCRICAO AS CODCOR,e.ARCDESCRICAO AS COR
                FROM TB_REL_PECAS_ZERADAS a
                INNER JOIN TB_MAT_MATERIAL b ON b.MATSEQUENCIAL = a.SEQUENCIAL
                INNER JOIN TB_AAT_ATRIBUTOS c ON c.MATID = b.MATID 
                INNER JOIN TB_ARM_ATRMODELO d ON d.ARMID = c.ARMID 
                INNER JOIN TB_ARC_ATRCOR e ON e.ARCID = c.ARCID 
                INNER JOIN TB_ACN_ATRCORNUMERICA f ON f.ACNID = c.ACNID  
                WHERE DATA_ACESSO >= :dataInicio AND DATA_ACESSO <= :dataFim
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':dataInicio', $dataInicio, PDO::PARAM_STR);
            $stmt->bindParam(':dataFim', $dataFim, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

   
          
 echo '<table class="min-w-full bg-white border border-gray-300">';
        echo '<thead>';
        echo '<tr class="bg-gray-100">';
        echo '<th class="py-2 px-4 border-b border-gray-300 text-left">Sequencial</th>';
        echo '<th class="py-2 px-4 border-b border-gray-300 text-left">Fantasia</th>';
        echo '<th class="py-2 px-4 border-b border-gray-300 text-left">Código Modelo</th>';
        echo '<th class="py-2 px-4 border-b border-gray-300 text-left">Código Cor</th>';
        echo '<th class="py-2 px-4 border-b border-gray-300 text-left">Cor</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($result as $row) {
            echo '<tr>';
            echo '<td class="py-2 px-4 border-b border-gray-300 text-left">' . $row['SEQUENCIAL'] . '</td>';
            echo '<td class="py-2 px-4 border-b border-gray-300 text-left">' . $row['FANTASIA'] . '</td>';
            echo '<td class="py-2 px-4 border-b border-gray-300 text-left">' . $row['CODMODELO'] . '</td>';
            echo '<td class="py-2 px-4 border-b border-gray-300 text-left">' . $row['CODCOR'] . '</td>';
            echo '<td class="py-2 px-4 border-b border-gray-300 text-left">' . $row['COR'] . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';

        } catch (PDOException $e) {
            echo "Erro na conexão com o banco de dados: " . $e->getMessage();
        }
    }
    ?>