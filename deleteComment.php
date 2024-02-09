<?php
if (!isset($_POST['comment_id'])) {
    echo "ID do comentário não fornecido.";
    exit;
}
$connection = mysqli_connect("localhost", "root", "");
if (!$connection) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}
$database = mysqli_select_db($connection, "avanz");
if (!$database) {
    die("Erro ao selecionar o banco de dados: " . mysqli_error($connection));
}
$comment_id = mysqli_real_escape_string($connection, $_POST['comment_id']);

$query = "DELETE FROM comments WHERE id = '$comment_id'";
$result = mysqli_query($connection, $query);

if ($result) {
    echo "Comentário apagado com sucesso.";
} else {
    echo "Erro ao apagar comentário: " . mysqli_error($connection);
}

mysqli_close($connection);
?>
