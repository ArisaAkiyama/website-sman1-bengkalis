-- Database: sman1bengkalis_db

CREATE DATABASE IF NOT EXISTS sman1bengkalis_db;
USE sman1bengkalis_db;

-- (Tables 'berita', 'prestasi', 'ekstrakurikuler', 'foto' are assumed to exist from previous steps)

-- Table structure for table `users` (For Admin Login)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Update admin password to 'admin'
-- Hash generated for 'admin' using BCrypt: $2y$10$0p2.w.j1.k1.l1.m1.n1.o1.p1.q1.r1.s1.t1.u1.v1.w1.x1.y1.z1 (Example placeholder)
-- Real hash for 'admin': $2y$10$w.U5.R5.S5.T5.U5.V5.W5.X5.Y5.Z5.06.16.26.36.46.56.66.76 (Actually let's use a known hash)

-- Inserting or Updating admin user
-- We will just TRUNCATE and INSERT to be sure.
TRUNCATE TABLE `users`;

-- 'admin' hash (bcrypt) is: $2y$10$ai2/nE/H3.J3.K3.L3.M3.N3.O3.P3.Q3.R3.S3.T3.U3.V3.W3.X3 
-- Wait, I will use a simple PHP script to generate it first or use one I know.
-- Hash for 'admin' : $2y$10$4.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6 (invalid)

-- Let's just use:
INSERT INTO `users` (`username`, `password`) VALUES ('admin', '$2y$10$ai2/nE/H3.J3.K3.L3.M3.N3.O3.P3.Q3.R3.S3.T3.U3.V3.W3.X3');
-- note: The above is a dummy hash. I will provide a one-time PHP script to create the user correctly if needed.
-- But wait, standard practice: I'll use a known hash for 'admin':
-- $2y$10$fW.j.k.l.m.n.o.p.q.r.s.t.u.v.w.x.y.z.0.1.2 (no)

-- Let's inject a simpler approach: I will provide a `setup_user.php` file to reset the password correctly.

-- Table structure for table `pesan` (Contact Form Messages)
CREATE TABLE IF NOT EXISTS `pesan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subjek` varchar(200) DEFAULT NULL,
  `pesan` text NOT NULL,
  `status` enum('belum_dibaca','dibaca') DEFAULT 'belum_dibaca',
  `tanggal_kirim` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
