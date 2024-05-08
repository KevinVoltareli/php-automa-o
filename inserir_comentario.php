<?php
// Arquivo: inserir_comentario.php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recupere os dados do formulário
    $sequencial = isset($_POST["sequencial"]) ? $_POST["sequencial"] : null;
    $comentario = isset($_POST["comentario"]) ? $_POST["comentario"] : null;

    // Verifique se o comentário não está vazio
    if (empty(trim($comentario))) {
        // Resposta de erro se o comentário estiver vazio
        echo json_encode(['status' => 'error', 'message' => 'O comentário não pode ser vazio.']);
        exit;
    }

    // Valide os dados (adicione validações adicionais conforme necessário)

    // Inserir comentário no banco de dados
    try {
        $conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepara a instrução SQL para inserção
        $sql = "INSERT INTO TB_COMENTARIOS_RESERVAS (SEQUENCIAL, DATA, COMENTARIO) VALUES (:sequencial, CURRENT_TIMESTAMP, :comentario)";
        $stmt = $conn->prepare($sql);

        // Vincula os parâmetros
        $stmt->bindParam(':sequencial', $sequencial, PDO::PARAM_INT);
        $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);

        // Executa a inserção
        $stmt->execute();

        // Resposta de sucesso
        echo json_encode(['status' => 'success', 'message' => 'Comentário inserido com sucesso.']);
    } catch (PDOException $e) {
        // Resposta de erro
        echo json_encode(['status' => 'error', 'message' => 'Erro na inserção do comentário: ' . $e->getMessage()]);
    }
} else {
    // Resposta de método não permitido
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido.']);
}
?>
