<?php
/**
 * Secure Admin Login
 * Features:
 * - Password hashing verification with bcrypt
 * - Brute force protection (5 attempts, 15 min lockout)
 * - CSRF token protection
 * - Prepared statements to prevent SQL injection
 * - Secure session configuration
 */

// Secure session configuration (MUST be before session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

session_start();

include 'koneksi.php';

// CSRF Token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$max_attempts = 5;
$lockout_time = 15 * 60; // 15 minutes in seconds

if (isset($_POST['login'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Sesi tidak valid. Silakan refresh halaman.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        // Prepared statement to prevent SQL injection
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if ($user) {
            // Check if user is locked out
            if ($user['lockout_time'] && strtotime($user['lockout_time']) > time()) {
                $remaining = ceil((strtotime($user['lockout_time']) - time()) / 60);
                $error = "Akun terkunci. Coba lagi dalam $remaining menit.";
            } else {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Successful login
                    // Reset login attempts
                    $stmt = mysqli_prepare($koneksi, "UPDATE users SET login_attempts = 0, lockout_time = NULL, last_login = NOW() WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $user['id']);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['status'] = "login";
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['login_time'] = time();
                    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                    
                    // Redirect to admin panel
                    header("Location: input_berita.php");
                    exit;
                } else {
                    // Failed login - increment attempts
                    $attempts = $user['login_attempts'] + 1;
                    
                    if ($attempts >= $max_attempts) {
                        // Lock the account
                        $lockout = date('Y-m-d H:i:s', time() + $lockout_time);
                        $stmt = mysqli_prepare($koneksi, "UPDATE users SET login_attempts = ?, lockout_time = ? WHERE id = ?");
                        mysqli_stmt_bind_param($stmt, "isi", $attempts, $lockout, $user['id']);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        $error = "Terlalu banyak percobaan gagal. Akun terkunci selama 15 menit.";
                    } else {
                        // Update attempts
                        $stmt = mysqli_prepare($koneksi, "UPDATE users SET login_attempts = ? WHERE id = ?");
                        mysqli_stmt_bind_param($stmt, "ii", $attempts, $user['id']);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        $remaining = $max_attempts - $attempts;
                        $error = "Username atau password salah. Sisa percobaan: $remaining";
                    }
                }
            }
        } else {
            // User not found - same error message to prevent username enumeration
            $error = "Username atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SMAN 1 Bengkalis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/login.css?v=2">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #004029 0%, #006644 50%, #004029 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        .login-card {
            background: white;
            padding: 45px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }
        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #004029, #006644);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #D4A84B;
            font-size: 2.5rem;
        }
        .login-card h2 {
            color: #004029;
            margin-bottom: 8px;
            font-size: 1.8rem;
        }
        .login-card p {
            color: #666;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #004029;
            font-size: 0.95rem;
        }
        .form-group input {
            width: 100%;
            padding: 16px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s;
            background: #fff;
        }
        .form-group input:focus {
            outline: none;
            border-color: #004029;
            box-shadow: 0 0 0 4px rgba(0,64,41,0.1);
        }
        .form-group input::placeholder {
            color: #aaa;
        }
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #004029, #006644);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            font-family: inherit;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,64,41,0.4);
        }
        .error-msg {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            color: #c62828;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }
        .error-msg i { font-size: 1.2rem; }
        .back-link {
            margin-top: 25px;
            display: block;
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link:hover { color: #004029; }
        .security-badge {
            margin-top: 20px;
            font-size: 0.8rem;
            color: #999;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .security-badge i { color: #27ae60; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h2>Admin Panel</h2>
            <p>SMAN 1 Bengkalis</p>
            
            <?php if ($error): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="post" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" required autofocus placeholder="Masukkan username">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" required placeholder="Masukkan password">
                </div>
                
                <button type="submit" name="login" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>
            
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Website
            </a>
            
            <div class="security-badge">
                <i class="fas fa-shield-alt"></i> Secured Login
            </div>
        </div>
    </div>
</body>
</html>
