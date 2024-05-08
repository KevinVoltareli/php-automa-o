<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sequencial = $_POST["sequencial"];

    try {
        $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Inicia uma transação
        $conn->beginTransaction();

        // Primeira exclusão: Excluir reserva
        $sql1 = "DELETE FROM TB_PECAS_RESERVADAS WHERE SEQUENCIAL = :sequencial";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bindParam(':sequencial', $sequencial, PDO::PARAM_INT);
        $stmt1->execute();

        // Segunda exclusão: Excluir comentários
        $sql2 = "DELETE FROM TB_COMENTARIOS_RESERVAS WHERE SEQUENCIAL = :sequencial";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(':sequencial', $sequencial, PDO::PARAM_INT);
        $stmt2->execute();

        // Se chegou até aqui, as duas operações de exclusão foram bem-sucedidas
        $conn->commit();

        // Redireciona para a página principal
        header("Location: http://192.168.30.252:9091/ProjetoXoPlanilha/index.php");
        exit();
    } catch (PDOException $e) {
        // Se houver algum erro, desfaz a transação e exibe a mensagem de erro
        $conn->rollBack();
        echo "Erro na exclusão do registro: " . $e->getMessage();
    }
}
?>
    