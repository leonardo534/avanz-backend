<?php
// Configurações do banco de dados
$dsn = 'mysql:host=localhost;dbname=avanz';
$username = 'root';
$password = '';

try {
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($id) {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT c.id AS id, c.type, c.title, c.description, c.typeMachine,
                    com.id AS comment_id, com.user_origin_id, com.user_target_id, com.comment, com.created, com.likes_count,
                    rep.id AS reply_id, rep.user_origin_id AS reply_user_origin_id, rep.user_target_id AS reply_user_target_id, rep.reply AS reply_text, rep.created AS reply_created
                FROM content c
                LEFT JOIN comments com ON c.id = com.content_id
                LEFT JOIN replies rep ON com.id = rep.comment_id
                WHERE c.id = :id
                ORDER BY com.id, rep.id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $formattedResults = array();
        foreach ($results as $row) {
            $id = $row['id'];
            if (!isset($formattedResults[$id])) {
              
                $formattedResults[$id] = array(
                    'id' => $id,
                    'type' => $row['type'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'typeMachine' => $row['typeMachine'],
                    'comments' => array()
                );
            }
            $createdDateTime = date('Y-m-d H:i:s', $row['created']);
            $commentData = array(
                'id' => $row['comment_id'],
                'user_origin_id' => $row['user_origin_id'],
                'user_target_id' => $row['user_target_id'],
                'comment' => $row['comment'],
                'created' => $createdDateTime, // Usando a data e hora formatada
                'likes_count' => $row['likes_count'],
                'replies' => array()
            );

            $replyId = $row['reply_id'];
            if ($replyId !== null) {
                $commentData['replies'][] = array(
                    'id' => $replyId,
                    'user_origin_id' => $row['reply_user_origin_id'],
                    'user_target_id' => $row['reply_user_target_id'],
                    'reply' => $row['reply_text'],
                    'created' => $row['reply_created']
                );
            }

            $formattedResults[$id]['comments'][] = $commentData;
        }
        $jsonResult = json_encode(array_values($formattedResults));

        if ($jsonResult) {
            echo $jsonResult;
        } else {
            echo json_encode(array("message" => "Nenhum post encontrado."));
        }

    } else {
        echo json_encode(array("error" => "ID do post não fornecido."));
    }

} catch (PDOException $e) {
    echo json_encode(array("error" => "Erro de conexão: " . $e->getMessage()));
}
?>
