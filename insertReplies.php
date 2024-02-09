<?php
$parametrosEsperados = array('comment_id', 'user_origin_id', 'user_target_id', 'reply');

$parametrosFaltantes = array();
foreach ($parametrosEsperados as $parametro) {
    if (!isset($_POST[$parametro])) {
        $parametrosFaltantes[] = $parametro;
    }
}

if (!empty($parametrosFaltantes)) {
    $Message = "Parâmetros faltando: " . implode(', ', $parametrosFaltantes);
} else {
    $connection = mysqli_connect("localhost", "root", "");
    if (!$connection) {
        die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
    }

    $database = mysqli_select_db($connection, "avanz");
    if (!$database) {
        die("Erro ao selecionar o banco de dados: " . mysqli_error($connection));
    }

    $comment_id = mysqli_real_escape_string($connection, $_POST['comment_id']);
    $user_origin_id = mysqli_real_escape_string($connection, $_POST['user_origin_id']);
    $user_target_id = mysqli_real_escape_string($connection, $_POST['user_target_id']);
    $reply = mysqli_real_escape_string($connection, $_POST['reply']);

    $created = date("Y-m-d H:i:s");

    $query = "INSERT INTO replies (comment_id, user_origin_id, user_target_id, reply, created) VALUES ('$comment_id', '$user_origin_id', '$user_target_id', '$reply', '$created')";
    $result = mysqli_query($connection, $query);

    if ($result) {
        $Message = "Resposta registrada";
    } else {
        $Message = "Erro ao registrar resposta: " . mysqli_error($connection);
    }

    mysqli_close($connection);
}

echo $Message;
?>
