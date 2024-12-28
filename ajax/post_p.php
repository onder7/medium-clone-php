<?php
$root_path = dirname(dirname(__FILE__));
require_once $root_path . '/core/init.php';
protect_page();

header('Content-Type: application/json');

try {
    if(!isset($db) || $db === null) {
        throw new Exception('Veritabanı bağlantısı kurulamadı');
    }

    // Gelen verileri işle
    $title = isset($_POST['post_title']) ? urldecode(trim($_POST['post_title'])) : '';
    $subtitle = isset($_POST['post_sub']) ? urldecode(trim($_POST['post_sub'])) : '';
    $content = isset($_POST['post_data']) ? urldecode(trim($_POST['post_data'])) : '';
    $published = isset($_POST['post_view']) ? (int)$_POST['post_view'] : 0;

    // Veri doğrulama
    if (empty($title)) {
        throw new Exception('Başlık gereklidir');
    }

    // Veritabanına kaydet
    $query = $db->prepare("
        INSERT INTO posts (
            user_id,
            title,
            subtitle,
            content,
            published,
            created_at
        ) VALUES (
            :user_id,
            :title,
            :subtitle,
            :content,
            :published,
            NOW()
        )
    ");

    $result = $query->execute([
        ':user_id' => $_SESSION['user_id'],
        ':title' => $title,
        ':subtitle' => $subtitle,
        ':content' => $content,
        ':published' => $published
    ]);

    if ($result) {
        $post_id = $db->lastInsertId();
        echo json_encode([
            'success' => true,
            'message' => $published ? 'Gönderi yayınlandı' : 'Taslak kaydedildi',
            'post_id' => $post_id
        ]);
    } else {
        throw new Exception('Gönderi kaydedilemedi: ' . implode(", ", $query->errorInfo()));
    }

} catch (Exception $e) {
    error_log("Post Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>