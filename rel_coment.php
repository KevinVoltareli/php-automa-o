<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exibição de Comentários</title>
    <!-- Inclua os arquivos do Tailwind CSS -->
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
<body class="bg-gray-100 p-4">

    <div class="max-w-xl mx-auto bg-white p-8 shadow-md rounded-md">

        <h1 class="text-3xl font-bold mb-4">Comentários por Sequencial</h1>

        <?php
            try {
                $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql = "SELECT SEQUENCIAL, COMENTARIO FROM TB_COMENTARIOS_RESERVAS ORDER BY SEQUENCIAL, DATA ASC";

                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $currentSequencial = null;

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['SEQUENCIAL'] !== $currentSequencial) {
                        if ($currentSequencial !== null) {
                            echo '</ul>';
                        }
                        echo '<div class="mb-6">';
                        echo '<h2 class="text-xl font-bold">Sequencial: ' . $row['SEQUENCIAL'] . '</h2>';
                        echo '<ul class=" pl-6">';
                        $currentSequencial = $row['SEQUENCIAL'];
                    }

                    echo '<li class="bg-slate-200 hover:bg-slate-100 p-2 mt-2 rounded-md">' . nl2br($row['COMENTARIO']) . '</li>';
                }

                if ($currentSequencial !== null) {
                    echo '</ul>';
                    echo '</div>';
                }
            } catch (PDOException $e) {
                echo '<p class="text-red-500">Erro: ' . $e->getMessage() . '</p>';
            } finally {
                // Fechar a conexão
                $conn = null;
            }
        ?>

    </div>

</body>
</html>
