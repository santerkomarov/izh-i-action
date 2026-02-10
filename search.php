<?php
require_once 'Database.php';

header('Content-Type: application/json; charset=utf-8');

$searching = trim($_GET['searching'] ?? '');

if (strlen($searching) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = Database::getInstance();
    $pdo->beginTransaction();

    $statemant = $pdo->prepare("
        SELECT b.id, b.title, a.name as author_name, 
        (SELECT COUNT(*) FROM readers WHERE book_id = b.id) as reader_count
        FROM books b
        JOIN authors a ON b.author_id = a.id
        WHERE b.title LIKE :s OR a.name LIKE :s
        ORDER BY reader_count DESC
    ");
    $statemant->execute(['s' => "%$searching%"]);
    $books = $statemant->fetchAll();

    if (!empty($books)) {
        $ids = array_column($books, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '(?)'));        
        $stmtLog = $pdo->prepare("INSERT INTO readers (book_id) VALUES $placeholders");
        $stmtLog->execute($ids);
    }

    $pdo->commit();
    echo json_encode($books);
    

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        error_log($e->getMessage());
    }
    
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
}

