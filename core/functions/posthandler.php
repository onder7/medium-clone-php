<?php
function allPosts() {
    global $db;
    $query = $db->prepare("
        SELECT p.*, u.username, u.first_name, u.last_name 
        FROM posts p 
        LEFT JOIN users u ON p.user_id = u.user_id 
        WHERE p.published = 1 
        ORDER BY p.created_at DESC
    ");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getUserPosts($user_id) {
    global $db;
    $query = $db->prepare("
        SELECT * FROM posts 
        WHERE user_id = :user_id 
        ORDER BY created_at DESC
    ");
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getPost($id) {
    global $db;
    $query = $db->prepare("
        SELECT p.*, u.username, u.first_name, u.last_name 
        FROM posts p 
        LEFT JOIN users u ON p.user_id = u.user_id 
        WHERE p.id = :id
    ");
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function createPost($data) {
    global $db;
    
    try {
        $query = $db->prepare("
            INSERT INTO posts (
                user_id,
                title,
                subtitle,
                content,
                published,
                created_at,
                updated_at
            ) VALUES (
                :user_id,
                :title,
                :subtitle,
                :content,
                :published,
                :created_at,
                :updated_at
            )
        ");

        $query->execute([
            ':user_id' => $_SESSION['user_id'],
            ':title' => $data['title'],
            ':subtitle' => $data['subtitle'],
            ':content' => $data['content'],
            ':published' => $data['published'],
            ':created_at' => date('Y-m-d H:i:s'),
            ':updated_at' => date('Y-m-d H:i:s')
        ]);

        return $db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Post creation error: " . $e->getMessage());
        return false;
    }
}

function updatePost($post_id, $data) {
    global $db;
    
    try {
        $query = $db->prepare("
            UPDATE posts SET 
                title = :title,
                subtitle = :subtitle,
                content = :content,
                published = :published,
                updated_at = :updated_at
            WHERE id = :post_id AND user_id = :user_id
        ");

        return $query->execute([
            ':title' => $data['title'],
            ':subtitle' => $data['subtitle'],
            ':content' => $data['content'],
            ':published' => $data['published'],
            ':updated_at' => date('Y-m-d H:i:s'),
            ':post_id' => $post_id,
            ':user_id' => $_SESSION['user_id']
        ]);
    } catch (PDOException $e) {
        error_log("Post update error: " . $e->getMessage());
        return false;
    }
}

function deletePost($post_id) {
    global $db;
    
    try {
        $query = $db->prepare("
            DELETE FROM posts 
            WHERE id = :post_id AND user_id = :user_id
        ");
        
        return $query->execute([
            ':post_id' => $post_id,
            ':user_id' => $_SESSION['user_id']
        ]);
    } catch (PDOException $e) {
        error_log("Post deletion error: " . $e->getMessage());
        return false;
    }
}

function isPostOwner($post_id) {
    global $db;
    $query = $db->prepare("
        SELECT COUNT(*) FROM posts 
        WHERE id = :post_id AND user_id = :user_id
    ");
    $query->execute([
        ':post_id' => $post_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    return $query->fetchColumn() > 0;
}
?>