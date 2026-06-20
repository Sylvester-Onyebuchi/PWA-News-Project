<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function categories() {
    return ['sport' => 'Sport', 'kultura' => 'Kultura'];
}

function categoryLabel($key) {
    $categories = categories();
    return $categories[$key] ?? ucfirst($key);
}

function uploadImage($fieldName = 'pphoto') {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $tmp = $_FILES[$fieldName]['tmp_name'];
    $mime = mime_content_type($tmp);
    if (!isset($allowed[$mime])) {
        return null;
    }
    if ($_FILES[$fieldName]['size'] > 3 * 1024 * 1024) {
        return null;
    }
    $name = pathinfo($_FILES[$fieldName]['name'], PATHINFO_FILENAME);
    $name = preg_replace('/[^A-Za-z0-9_-]/', '-', $name);
    $filename = $name . '-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $target = __DIR__ . '/img/' . $filename;
    if (!move_uploaded_file($tmp, $target)) {
        return null;
    }
    return $filename;
}

function getNewsByCategory($dbc, $category, $limit = null) {
    $sql = 'SELECT * FROM vijesti WHERE arhiva = 0 AND kategorija = ? ORDER BY id DESC';
    if ($limit !== null) $sql .= ' LIMIT ?';
    $stmt = mysqli_prepare($dbc, $sql);
    if ($limit !== null) {
        mysqli_stmt_bind_param($stmt, 'si', $category, $limit);
    } else {
        mysqli_stmt_bind_param($stmt, 's', $category);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function getArticle($dbc, $id) {
    $stmt = mysqli_prepare($dbc, 'SELECT * FROM vijesti WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt)->fetch_assoc();
}

function requirePost() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php');
        exit;
    }
}

function deleteImageFile($filename) {
    if (empty($filename)) {
        return;
    }

    $path = __DIR__ . '/img/' . basename($filename);

    if (is_file($path)) {
        unlink($path);
    }
}
?>
