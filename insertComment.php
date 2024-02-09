<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonData = file_get_contents("php://input");

    $data = json_decode($jsonData, true);
    $parametrosEsperados = array('user_origin_id', 'content_id', 'comment');

    $parametrosFaltantes = array();
    foreach ($parametrosEsperados as $parametro) {
        if (!isset($data[$parametro])) {
            $parametrosFaltantes[] = $parametro;
        }
    }

    if (!empty($parametrosFaltantes)) {
        $response = array(
            'status' => 'error',
            'message' => "Parâmetros faltando: " . implode(', ', $parametrosFaltantes)
        );
    } else {
        $connection = mysqli_connect("localhost", "root", "");
        if (!$connection) {
            die(json_encode(array('status' => 'error', 'message' => "Erro ao conectar ao banco de dados: " . mysqli_connect_error())));
        }

        $database = mysqli_select_db($connection, "avanz");
        if (!$database) {
            die(json_encode(array('status' => 'error', 'message' => "Erro ao selecionar o banco de dados: " . mysqli_error($connection))));
        }

        $user_origin_id = mysqli_real_escape_string($connection, $data['user_origin_id']);
        $content_id = mysqli_real_escape_string($connection, $data['content_id']);
        $comment = mysqli_real_escape_string($connection, $data['comment']);

        $query = "INSERT INTO comments (user_origin_id, content_id, comment) VALUES ('$user_origin_id', '$content_id', '$comment')";
        $result = mysqli_query($connection, $query);

        if ($result) {
            $response = array('status' => 'success', 'message' => "Comentário registrado");
        } else {
            $response = array('status' => 'error', 'message' => "Erro ao registrar comentário: " . mysqli_error($connection));
        }

        mysqli_close($connection);
    }
} else {
    $response = array('status' => 'error', 'message' => "Método de requisição inválido. Este endpoint aceita apenas solicitações POST.");
}

echo json_encode($response);
?>
