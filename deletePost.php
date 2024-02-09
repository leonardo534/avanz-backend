<?php
if (!isset($_POST['post_id'])) {
    echo json_encode(array("success" => false, "message" => "ID do post não fornecido."));
    exit;
}

$post_id = $_POST['post_id'];

$connection = mysqli_connect("localhost", "root", "", "avanz");
if (!$connection) {
    echo json_encode(array("success" => false, "message" => "Erro ao conectar ao banco de dados: " . mysqli_connect_error()));
    exit;
}

$post_id = mysqli_real_escape_string($connection, $post_id);

$query = "DELETE FROM content WHERE id = '$post_id'";
$result = mysqli_query($connection, $query);

if ($result) {
    echo json_encode(array("success" => true, "post_id" => $post_id, "message" => "Post apagado com sucesso."));
} else {
    echo json_encode(array("success" => false, "message" => "Erro ao apagar post: " . mysqli_error($connection)));
}

mysqli_close($connection);
