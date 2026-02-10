<?php
require 'auth.php';
require 'helpers.php';
require_once 'Database.php';

$pdo = Database::getInstance();

if ($isAdmin) {
    // Авторы
    if (isset($_GET['delete_author'])) {
        $stmt = $pdo->prepare("DELETE FROM authors WHERE id = ?");
        $stmt->execute([(int)$_GET['delete_author']]);
        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['save_author'])) {
        $name = trim($_POST['author_name'] ?? '');
        $authorId = (int)($_POST['author_id_edit'] ?? 0);

        if ($name !== '') {
            if ($authorId > 0) {
                $stmt = $pdo->prepare("UPDATE authors SET name = ? WHERE id = ?");
                $stmt->execute([$name, $authorId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO authors (name) VALUES (?)");
                $stmt->execute([$name]);
            }
        }
        header("Location: admin.php");
        exit;
    }

    // Книги
    if (isset($_GET['delete_book'])) {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$_GET['delete_book']]);
        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['save_book'])) {
        $title = $_POST['title'];
        $author_id = $_POST['author_id'];
        if (!empty($_POST['book_id'])) {
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author_id = ? WHERE id = ?");
            $stmt->execute([$title, $author_id, $_POST['book_id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO books (title, author_id) VALUES (?, ?)");
            $stmt->execute([$title, $author_id]);
        }
        header("Location: admin.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body class="container mt-5">
<?php if (!$isAdmin): ?>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <form method="POST" class="card card-body shadow">
                <h3>Админпанель</h3>
                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <input 
                    type="text" 
                    name="username" 
                    class="form-control mb-2" 
                    placeholder="Логин (admin)" 
                    required
                >
                <input 
                    type="password" 
                    name="password" 
                    class="form-control mb-3" 
                    placeholder="Пароль (123)" 
                    required
                >
                <button 
                    name="login" 
                    class="btn btn-primary w-100"
                >
                    Войти
                </button>
                <a 
                    href="index.php" 
                    class="btn btn-link w-100 mt-2"
                >
                    На главную
                </a>
            </form>
        </div>
    </div>

<?php else: ?>
    <!-- Управление -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Управление картотекой</h1>
        <a 
            href="?logout" 
            class="btn btn-danger"
        >
            Выйти
        </a>
    </div>

    <div>
        <a 
            href="/" 
            class="m-2"
        >
            Страница поиска
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <form 
                method="POST" 
                class="card card-body mb-4"
            >
                <h5>Добавить/Изменить книгу</h5>
                <input 
                    type="hidden" 
                    name="book_id" 
                    id="book_id"
                >
                <input 
                    type="text" 
                    name="title" 
                    id="book_title" 
                    class="form-control mb-2" 
                    placeholder="Название книги" 
                    required
                >
                <select 
                    name="author_id" 
                    id="author_id" 
                    class="form-select mb-2"
                >
                    <?php
                    $authors = $pdo->query("SELECT * FROM authors")->fetchAll();
                    foreach ($authors as $a) echo "<option value='{$a['id']}'>{$a['name']}</option>";
                    ?>
                </select>
                <button 
                    name="save_book" 
                    class="btn btn-success w-100"
                >
                    Сохранить
                </button>
            </form>
        </div>

        <div class="col-md-8">
            <table class="table border">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Книга</th>
                        <th>Автор</th>
                        <th>Действия</th></tr>
                </thead>
                <tbody>
                    <?php
                    $books = $pdo->query("SELECT b.*, a.name as author_name FROM books b JOIN authors a ON b.author_id = a.id")->fetchAll();
                    foreach ($books as $book): ?>
                    <tr>
                        <td><?= $book['id'] ?></td>
                        <td><?= html($book['title']) ?></td>
                        <td><?= html($book['author_name']) ?></td>
                        <td>
                            <button
                                class="btn btn-sm btn-warning"
                                onclick="editBook(<?= $book['id'] ?>, '<?= html($book['title']) ?>', <?= $book['author_id'] ?>)" 
                            >
                                Редактировать
                            </button>
                            <a 
                                href="?delete_book=<?= $book['id'] ?>" 
                                class="btn btn-sm btn-danger" 
                                onclick="return confirm('Удалить?')"
                            >
                                Удалить
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <hr class="my-4">

<div class="row">
    <div class="col-md-4">
        <form 
            method="POST" 
            class="card card-body mb-4"
        >
            <h5>
                Добавить/Изменить автора
            </h5>

            <input 
                type="hidden" 
                name="author_id_edit" 
                id="author_id_edit"
            />

            <input
                type="text"
                name="author_name"
                id="author_name"
                class="form-control mb-2"
                placeholder="Имя автора"
                required
            />
            <button 
                name="save_author" 
                class="btn btn-success w-100"
            >
                Сохранить
            </button>
        </form>
    </div>

    <div 
        class="col-md-8"
    >
        <table 
            class="table border"
        >
            <thead>
            <tr>
                <th>ID</th>
                <th>Автор</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $authors = $pdo->query("SELECT * FROM authors ORDER BY id DESC")->fetchAll();
            foreach ($authors as $author): ?>
                <tr>
                    <td><?= (int)$author['id'] ?></td>
                    <td><?= html($author['name']) ?></td>
                    <td>
                        <button
                            class="btn btn-sm btn-warning"
                            onclick='editAuthor(<?= (int)$author["id"] ?>, <?= json_encode($a["name"], JSON_UNESCAPED_UNICODE) ?>)'
                        >
                            Редактировать
                        </button>
                        <a
                            href="?delete_author=<?= (int)$author['id'] ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Удалить автора? Все его книги тоже удалятся.')"
                        >
                            Удалить
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


    <script>
    function editBook(id, title, authorId) {
        document.getElementById('book_id').value = id;
        document.getElementById('book_title').value = title;
        document.getElementById('author_id').value = authorId;
    }

    function editAuthor(id, name) {
        console.log(id, name);
        document.getElementById('author_id_edit').value = id;
        document.getElementById('author_name').value = name;
    }

    </script>
<?php endif; ?>

</body>
</html>
