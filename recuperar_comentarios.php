<?php
// recupera_comentarios.php

// Conecte-se ao banco de dados (substitua pelos seus detalhes de conexão)
$conn = new PDO("firebird:dbname=C:\SavWinRevo\Servidor\DataBase\BDSAVWINREVO.FDB", "SYSDBA", "masterkey");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Recupere os comentários do banco de dados para o sequencial fornecido
$sequencial = $_GET['sequencial'];
$sql = "SELECT * FROM TB_COMENTARIOS_RESERVAS WHERE SEQUENCIAL = :sequencial ORDER BY DATA ASC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':sequencial', $sequencial, PDO::PARAM_INT);
$stmt->execute();

// Prepare os comentários para serem enviados como JSON
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Adicione a data formatada ao array de comentários
foreach ($comentarios as &$comentario) {
    // Formatando a data para o formato desejado (altere conforme necessário)
    $dataFormatada = date('d/m/Y', strtotime($comentario['DATA']));
    $comentario['DATA_FORMATADA'] = $dataFormatada;
}

echo json_encode($comentarios);
?>
