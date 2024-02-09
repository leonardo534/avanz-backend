<?php
$parametrosEsperados = array('idAuthor', 'type', 'title', 'description', 'typeMachine');

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
    $idAuthor = mysqli_real_escape_string($connection, $_POST['idAuthor']);
    $type = mysqli_real_escape_string($connection, $_POST['type']);
    $title = mysqli_real_escape_string($connection, $_POST['title']);
    $description = mysqli_real_escape_string($connection, $_POST['description']);
    $typeMachine = mysqli_real_escape_string($connection, $_POST['typeMachine']);

    $query = "INSERT INTO content (idAuthor, type, title, description, typeMachine) VALUES ('$idAuthor', '$type', '$title', '$description', '$typeMachine')";
    $result = mysqli_query($connection, $query);

    if ($result) {
        $Message = "Post registrado";
    } else {
        $Message = "Erro ao registrar post: " . mysqli_error($connection);
    }
    mysqli_close($connection);
}

echo $Message;
?>
