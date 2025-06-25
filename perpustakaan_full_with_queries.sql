CREATE DATABASE perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE perpustakaan;

CREATE TABLE buku (
  isbn VARCHAR(20) PRIMARY KEY,
  judul VARCHAR(100) NOT NULL,
  penulis VARCHAR(50) NOT NULL,
  penerbit VARCHAR(50) NOT NULL,
  tahun_terbit INT(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE anggota (
  id_anggota INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  alamat TEXT NOT NULL,
  no_hp VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE petugas (
  id_petugas INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE peminjaman (
  id_peminjaman INT AUTO_INCREMENT PRIMARY KEY,
  id_anggota INT,
  isbn VARCHAR(20),
  id_petugas INT,
  tanggal_pinjam DATE NOT NULL,
  tanggal_kembali DATE,
  FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota) ON DELETE CASCADE,
  FOREIGN KEY (isbn) REFERENCES buku(isbn) ON DELETE CASCADE,
  FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
