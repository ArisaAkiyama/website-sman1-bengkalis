<?php
session_start();
include 'koneksi.php';

// Auto-create pesan table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS pesan (
  id int(11) NOT NULL AUTO_INCREMENT,
  nama varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  subjek varchar(200) DEFAULT NULL,
  pesan text NOT NULL,
  status enum('belum_dibaca','dibaca') DEFAULT 'belum_dibaca',
  tanggal_kirim datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($koneksi, $create_table);

// Check if logged in (simple check)
if (!isset($_SESSION['admin_logged_in'])) {
    // For now, allow access without login - can add login check later
}

// Handle mark as read
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $id = (int)$_GET['mark_read'];
    mysqli_query($koneksi, "UPDATE pesan SET status='dibaca' WHERE id=$id");
    header('Location: admin_pesan.php');
    exit;
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM pesan WHERE id=$id");
    header('Location: admin_pesan.php');
    exit;
}

// Get messages
$query = mysqli_query($koneksi, "SELECT * FROM pesan ORDER BY tanggal_kirim DESC");
$unread_count = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as cnt FROM pesan WHERE status='belum_dibaca'"))['cnt'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pesan Masuk | SMAN 1 Bengkalis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #004029;
            --accent: #D4A84B;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-500: #6c757d;
            --gray-700: #495057;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--gray-100); min-height: 100vh; font-size: 14px; }
        
        .admin-header { background: var(--primary); color: white; padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-header h1 { font-size: 1.5rem; display: flex; align-items: center; gap: 0.8rem; }
        .admin-header a { color: white; text-decoration: none; opacity: 0.8; transition: opacity 0.3s; }
        .admin-header a:hover { opacity: 1; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        
        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 1rem; }
        .stat-card i { font-size: 2rem; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
        .stat-card.total i { background: #e3f2fd; color: #1976d2; }
        .stat-card.unread i { background: #fff3e0; color: #f57c00; }
        .stat-card .stat-info h3 { font-size: 1.8rem; color: var(--gray-700); }
        .stat-card .stat-info p { color: var(--gray-500); font-size: 0.9rem; }
        
        .messages-card { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; }
        .messages-header { padding: 1.5rem; border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; }
        .messages-header h2 { font-size: 1.2rem; color: var(--gray-700); }
        
        .message-list { max-height: 600px; overflow-y: auto; }
        .message-item { padding: 1.5rem; border-bottom: 1px solid var(--gray-200); display: grid; grid-template-columns: auto 1fr auto; gap: 1.5rem; align-items: start; transition: background 0.3s; }
        .message-item:hover { background: var(--gray-100); }
        .message-item.unread { background: #fffbf0; border-left: 4px solid var(--accent); }
        
        .message-avatar { width: 50px; height: 50px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.2rem; }
        
        .message-content h4 { color: var(--gray-700); margin-bottom: 0.3rem; display: flex; align-items: center; gap: 0.5rem; }
        .message-content h4 .badge { background: var(--accent); color: var(--primary); font-size: 0.7rem; padding: 0.2rem 0.6rem; border-radius: 20px; font-weight: 600; }
        .message-content .email { color: var(--gray-500); font-size: 0.85rem; margin-bottom: 0.5rem; }
        .message-content .subject { font-weight: 500; color: var(--gray-700); margin-bottom: 0.3rem; }
        .message-content .preview { color: var(--gray-500); font-size: 0.9rem; line-height: 1.5; }
        .message-content .time { color: var(--gray-500); font-size: 0.8rem; margin-top: 0.5rem; }
        
        .message-actions { display: flex; gap: 0.5rem; }
        .message-actions a { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s; }
        .message-actions .btn-read { background: #e8f5e9; color: var(--success); }
        .message-actions .btn-read:hover { background: var(--success); color: white; }
        .message-actions .btn-delete { background: #ffebee; color: var(--danger); }
        .message-actions .btn-delete:hover { background: var(--danger); color: white; }
        
        .empty-state { padding: 4rem 2rem; text-align: center; color: var(--gray-500); }
        .empty-state i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.3; }
        .empty-state h3 { color: var(--gray-700); margin-bottom: 0.5rem; }
        
        .back-link { display: inline-flex; align-items: center; gap: 0.5rem; color: var(--primary); text-decoration: none; margin-bottom: 1.5rem; font-weight: 500; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-envelope"></i> Pesan Masuk</h1>
        <a href="index.php"><i class="fas fa-home"></i> Kembali ke Website</a>
    </div>
    
    <div class="container">
        <a href="input_berita.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        
        <div class="stats-row">
            <div class="stat-card total">
                <i class="fas fa-envelope"></i>
                <div class="stat-info">
                    <h3><?php echo mysqli_num_rows($query); ?></h3>
                    <p>Total Pesan</p>
                </div>
            </div>
            <div class="stat-card unread">
                <i class="fas fa-envelope-open-text"></i>
                <div class="stat-info">
                    <h3><?php echo $unread_count; ?></h3>
                    <p>Belum Dibaca</p>
                </div>
            </div>
        </div>
        
        <div class="messages-card">
            <div class="messages-header">
                <h2><i class="fas fa-inbox"></i> Daftar Pesan</h2>
            </div>
            
            <div class="message-list">
                <?php if(mysqli_num_rows($query) > 0): ?>
                    <?php mysqli_data_seek($query, 0); while($pesan = mysqli_fetch_assoc($query)): ?>
                    <div class="message-item <?php echo $pesan['status'] == 'belum_dibaca' ? 'unread' : ''; ?>">
                        <div class="message-avatar">
                            <?php echo strtoupper(substr($pesan['nama'], 0, 1)); ?>
                        </div>
                        <div class="message-content">
                            <h4>
                                <?php echo htmlspecialchars($pesan['nama']); ?>
                                <?php if($pesan['status'] == 'belum_dibaca'): ?>
                                <span class="badge">Baru</span>
                                <?php endif; ?>
                            </h4>
                            <div class="email"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($pesan['email']); ?></div>
                            <?php if($pesan['subjek']): ?>
                            <div class="subject"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($pesan['subjek']); ?></div>
                            <?php endif; ?>
                            <div class="preview"><?php echo htmlspecialchars($pesan['pesan']); ?></div>
                            <div class="time"><i class="fas fa-clock"></i> <?php echo date('d M Y, H:i', strtotime($pesan['tanggal_kirim'])); ?></div>
                        </div>
                        <div class="message-actions">
                            <?php if($pesan['status'] == 'belum_dibaca'): ?>
                            <a href="?mark_read=<?php echo $pesan['id']; ?>" class="btn-read" title="Tandai sudah dibaca"><i class="fas fa-check"></i></a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $pesan['id']; ?>" class="btn-delete" title="Hapus" onclick="return confirm('Hapus pesan ini?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Belum Ada Pesan</h3>
                        <p>Pesan dari pengunjung akan muncul di sini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
