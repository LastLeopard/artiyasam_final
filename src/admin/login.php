<?php
session_start();
include "../config.php";

// Zaten giriş yapmışsa ana sayfaya yönlendir
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
    
}

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Admin kullanıcısını veritabanından sorgula
    $result = $conn->query("SELECT * FROM admin_users WHERE username='$username'");
    
    if($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        
        // Şifre kontrolü - Hem düz metin hem hash'li şifreleri destekle
        $password_valid = false;
        
        // Eğer şifre hash'li ise (60 karakter uzunluğunda)
        if(strlen($admin['password']) == 60 && password_verify($password, $admin['password'])) {
            $password_valid = true;
        }
        // Eğer düz metin şifre ise
        elseif($password == $admin['password']) {
            $password_valid = true;
            
            // Güvenlik için: düz metin şifreyi hash'le ve güncelle
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $conn->query("UPDATE admin_users SET password='$hashed_password' WHERE id=" . $admin['id']);
        }
        
        if($password_valid) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_id'] = $admin['id'];
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Kullanıcı adı veya şifre hatalı!";
        }
    } else {
        $error = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - Artıyaşam Turizm</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
<link rel="shortcut icon" type="image/png" href="../uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            max-width: 400px;
            width: 90%;
            margin: 20px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            color: #0f172a;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .login-header p a {
            color: #ff7b00;
            text-decoration: none;
            font-weight: bold;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            border-color: #ff7b00;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255,123,0,0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #ff7b00;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-login:hover {
            background: #e66a00;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,123,0,0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #dc3545;
            font-size: 14px;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .back-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e1e1e1;
        }
        
        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .back-link a:hover {
            color: #ff7b00;
        }
        
        .back-link a i {
            font-size: 16px;
        }
        
        .demo-info {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 13px;
            color: #666;
            border: 1px dashed #ff7b00;
        }
        
        .demo-info p {
            margin: 5px 0;
        }
        
        .demo-info strong {
            color: #ff7b00;
        }
    </style>
</head>
<body>
    
    <div class="login-container">
        <div class="login-header">
    <img src="../artiyasamlogo1.png" alt="Artıyaşam Turizm" style="height: 60px; margin-bottom: 20px;">
    <h2>🔐 Admin Girişi</h2>
    <p>Artıyaşam Turizm Yönetim Paneli</p>
</div>
        
        <?php if($error): ?>
            <div class="error">
                <strong>Hata!</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input type="text" name="username" placeholder="Kullanıcı adınızı girin" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="password" placeholder="Şifrenizi girin" required>
            </div>
            
            <button type="submit" class="btn-login">GİRİŞ YAP</button>
        </form>
        
        <div class="back-link">
            <a href="../index.php">← Ana Sayfaya Dön</a>
        </div>
        
        <div class="demo-info">
            <p><strong>📌 Demo Bilgiler:</strong></p>
            <p>Kullanıcı adı: <strong>admin</strong></p>
            <p>Şifre: <strong>admin123</strong></p>
            <p style="margin-top: 10px; font-size: 12px; color: #999;">İlk girişte şifreniz otomatik olarak güvenli hale getirilecektir.</p>
        </div>
    </div>
</body>
</html>