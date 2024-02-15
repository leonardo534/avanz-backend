<?php
$dsn = 'mysql:host=localhost;dbname=avanz';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT c.id AS id, c.type, c.idAuthor, c.title, c.description, c.typeMachine,
                com.id AS comment_id, com.user_origin_id, com.user_target_id, com.comment, com.created, com.likes_count,
                rep.id AS reply_id, rep.user_origin_id AS reply_user_origin_id, rep.user_target_id AS reply_user_target_id, rep.reply AS reply_text, rep.created AS reply_created
            FROM content c
            LEFT JOIN comments com ON c.id = com.content_id
            LEFT JOIN replies rep ON com.id = rep.comment_id
            ORDER BY c.id, com.id, rep.id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $formattedResults = array();
    foreach ($results as $row) {
        $postId = $row['id'];
        if (!isset($formattedResults[$postId])) {
            $formattedResults[$postId] = array(
                'id' => $postId,
                'type' => $row['type'],
                'idAuthor' => $row['idAuthor'],
                'title' => $row['title'],
                'description' => $row['description'],
                'typeMachine' => $row['typeMachine'],
                'comments' => array()
            );
        }

        if ($row['comment_id'] !== null) {
            $createdDateTime = date('Y-m-d H:i:s', $row['created']);
            $commentData = array(
                'id' => $row['comment_id'],
                'user_origin_id' => $row['user_origin_id'],
                'user_target_id' => $row['user_target_id'],
                'comment' => $row['comment'],
                'likes_count' => $row['likes_count'],
                'created' => $createdDateTime, // Usando a data e hora formatada
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

            $formattedResults[$postId]['comments'][] = $commentData;
        }
    }
    $jsonResult = json_encode(array_values($formattedResults));

    if ($jsonResult) {
        echo $jsonResult;
    } else {
        echo json_encode(array("message" => "Nenhum post encontrado."));
    }
} catch (PDOException $e) {
    echo json_encode(array("error" => "Erro de conexão: " . $e->getMessage()));
}
