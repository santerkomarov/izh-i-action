<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Картотека - Поиск</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body 
    class="container mt-5"
>
    <div 
        class="d-flex justify-content-between mb-4"
    >
        <h1>
            Поиск книг
        </h1>
        <a 
            href="admin.php" 
            class="btn btn-outline-primary"
        >
            Вход для админа
        </a>
    </div>

    <input 
        type="text" 
        id="searchInput" 
        class="form-control" 
        placeholder="Введите название или автора..."
    >
    
    <table 
        class="table mt-4"
    >
        <thead>
            <tr>
                <th>Название</th>
                <th>Автор</th>
                <th>Читателей</th>
            </tr>
        </thead>
        <tbody 
            id="results"
        ></tbody>
    </table>

    <script>
    const searchInput = document.getElementById('searchInput');
    const resultsTable = document.getElementById('results');    
    let debounce;
    let delay = 500;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value;
        clearTimeout(debounce);

        if (query.length < 2) { 
            resultsTable.innerHTML = ''; 
            return; 
        }

        debounce = setTimeout(async () => {
            const response = await fetch(`search.php?searching=${encodeURIComponent(query)}`);

            if (!response.ok) {
                const errorData = await response.json();
                resultsTable.innerHTML = `<tr><td colspan="3" class="text-danger">Ошибка: ${errorData.error}</td></tr>`;
                return;
            }

            const books = await response.json();
            resultsTable.innerHTML = books.map(book => `
                <tr>
                    <td>${book.title}</td>
                    <td>${book.author_name}</td>
                    <td>${book.reader_count}</td>
                </tr>
            `).join('');
        }, delay); 
    });
</script>

</body>
</html>
