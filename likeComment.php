<?php
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $jsonData = file_get_contents("php://input");

  $data = json_decode($jsonData, true);

  $parametrosEsperados = array('comment_id');

  $parametrosFaltantes = array();
  foreach ($parametrosEsperados as $parametro) {
    if (!isset($data[$parametro])) {
      $parametrosFaltantes[] = $parametro;
    }
  }

  if (!empty($parametrosFaltantes)) {
    $response['status'] = 'error';
    $response['message'] = "Parâmetros faltando: " . implode(', ', $parametrosFaltantes);
  } else {
    $connection = mysqli_connect("localhost", "root", "");
    if (!$connection) {
      $response['status'] = 'error';
      $response['message'] = "Erro ao conectar ao banco de dados: " . mysqli_connect_error();
    } else {
      $database = mysqli_select_db($connection, "avanz");
      if (!$database) {
        $response['status'] = 'error';
        $response['message'] = "Erro ao selecionar o banco de dados: " . mysqli_error($connection);
      } else {
        $comment_id = mysqli_real_escape_string($connection, $data['comment_id']);
        $query = "UPDATE comments SET likes_count = likes_count + 1 WHERE id = '$comment_id'";
        $result = mysqli_query($connection, $query);

        if ($result) {
          $updatedLikesQuery = "SELECT likes_count FROM comments WHERE id = '$comment_id'";
          $updatedLikesResult = mysqli_query($connection, $updatedLikesQuery);
          $row = mysqli_fetch_assoc($updatedLikesResult);
          $updatedLikesCount = $row['likes_count'];

          $response['status'] = 'success';
          $response['likes_count'] = $updatedLikesCount;
        } else {
          $response['status'] = 'error';
          $response['message'] = "Erro ao registrar a curtida: " . mysqli_error($connection);
        }
      }
      mysqli_close($connection);
    }
  }
} else {
  $response['status'] = 'error';
  $response['message'] = "Método de requisição inválido. Este endpoint aceita apenas solicitações POST.";
}

echo json_encode($response);
?>
