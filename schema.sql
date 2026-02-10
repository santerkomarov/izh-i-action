CREATE TABLE authors (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(255) NOT NULL
);
CREATE TABLE books (
id INT AUTO_INCREMENT PRIMARY KEY,
author_id INT NOT NULL,
title VARCHAR(255) NOT NULL,
FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE
);
CREATE TABLE readers (
id INT AUTO_INCREMENT PRIMARY KEY,
book_id INT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);
CREATE INDEX idx_book_title ON books(title);
CREATE INDEX idx_author_name ON authors(name);

INSERT INTO `authors` (`id`, `name`) VALUES
(1, 'Толстой'),
(2, 'Достоевский'),
(3, 'Чехов');

INSERT INTO `books` (`id`, `author_id`, `title`) VALUES
(1, 1, 'Война и мир'),
(2, 2, 'Преступление и наказание'),
(3, 3, 'Сказка о царе Салтане');