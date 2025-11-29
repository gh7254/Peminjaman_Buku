-- db/schema.sql
CREATE DATABASE IF NOT EXISTS sistem_peminjaman;
USE sistem_peminjaman;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(200),
  role ENUM('admin','member') DEFAULT 'member',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE authors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  isbn VARCHAR(50),
  category_id INT,
  publisher VARCHAR(200),
  year YEAR,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE book_authors (
  book_id INT,
  author_id INT,
  PRIMARY KEY (book_id, author_id),
  FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
  FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE
);

CREATE TABLE book_copies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  book_id INT NOT NULL,
  barcode VARCHAR(100) UNIQUE,
  status ENUM('available','borrowed','lost') DEFAULT 'available',
  FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

CREATE TABLE borrowings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  copy_id INT NOT NULL,
  borrowed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  due_date DATE,
  returned_at DATETIME,
  status ENUM('borrowed','returned','overdue') DEFAULT 'borrowed',
  fine_decimal DECIMAL(10,2) DEFAULT 0.00,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (copy_id) REFERENCES book_copies(id) ON DELETE CASCADE
);

-- sample data
INSERT INTO users (username, password, full_name, role) VALUES
('admin', SHA2('admin123',256), 'Administrator', 'admin'),
('rizky', SHA2('password',256), 'Rizky Mahasiswa', 'member');

INSERT INTO authors (name) VALUES ('Pramoedya Ananta Toer'), ('Budi Santoso');
INSERT INTO categories (name) VALUES ('Pemrograman'), ('Sastra');

INSERT INTO books (title, isbn, category_id, publisher, year, description) VALUES
('Belajar PHP untuk Pemula', '978-1-11111', 1, 'PT Komputer', 2021, 'Buku pengantar PHP.'),
('Cerita Rakyat Nusantara', '978-2-22222', 2, 'Penerbit Nusantara', 2019, 'Kumpulan cerita.');

INSERT INTO book_authors (book_id, author_id) VALUES (1,2), (2,1);
INSERT INTO book_copies (book_id, barcode) VALUES (1,'BC-0001'), (1,'BC-0002'), (2,'BC-0003');

INSERT INTO borrowings (user_id, copy_id, due_date) VALUES (2,1, DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY));
