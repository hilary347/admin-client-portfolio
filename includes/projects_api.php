<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

// Create table if it doesn't exist
$createSql = "CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name TEXT,
  pill VARCHAR(255),
  description TEXT,
  tags TEXT,
  image LONGTEXT,
  link TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $createSql);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $stmt = mysqli_prepare($conn, "SELECT id, name, pill, description, tags, image, link FROM projects WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        if ($row) {
            $row['tags'] = $row['tags'] ? json_decode($row['tags'], true) : [];
            echo json_encode($row);
            exit;
        }
        echo json_encode(null);
        exit;
    }
    $res = mysqli_query($conn, "SELECT id, name, pill, description, tags, image, link FROM projects ORDER BY id DESC");
    $out = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $r['tags'] = $r['tags'] ? json_decode($r['tags'], true) : [];
        $out[] = $r;
    }
    echo json_encode($out);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'No data provided']);
        exit;
    }

    $name = $input['name'] ?? '';
    $pill = $input['pill'] ?? '';
    $description = $input['desc'] ?? '';
    $tags = isset($input['tags']) ? json_encode(array_values($input['tags'])) : json_encode([]);
    $image = $input['image'] ?? '';
    $link = $input['link'] ?? '';

    if (isset($input['id']) && $input['id']) {
        $id = (int)$input['id'];
        $stmt = mysqli_prepare($conn, "UPDATE projects SET name = ?, pill = ?, description = ?, tags = ?, image = ?, link = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'ssssssi', $name, $pill, $description, $tags, $image, $link, $id);
        $ok = mysqli_stmt_execute($stmt);
        if ($ok) {
            echo json_encode(['success' => true, 'id' => $id]);
            exit;
        }
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update']);
        exit;
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO projects (name, pill, description, tags, image, link) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssssss', $name, $pill, $description, $tags, $image, $link);
        $ok = mysqli_stmt_execute($stmt);
        if ($ok) {
            $id = mysqli_insert_id($conn);
            echo json_encode(['success' => true, 'id' => $id]);
            exit;
        }
        http_response_code(500);
        echo json_encode(['error' => 'Failed to insert']);
        exit;
    }
}

if ($method === 'DELETE') {
    parse_str(file_get_contents('php://input'), $delVars);
    $id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($delVars['id']) ? (int)$delVars['id'] : 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'No id provided']);
        exit;
    }
    $stmt = mysqli_prepare($conn, "DELETE FROM projects WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    if ($ok) {
        echo json_encode(['success' => true]);
        exit;
    }
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete']);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
