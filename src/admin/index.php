<?php
session_start();
include "../config.php";

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Tur silme işlemi
if(isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Önce resmi sil
    $result = $conn->query("SELECT image FROM tours WHERE id=$id");
    if($row = $result->fetch_assoc()) {
        if($row['image'] && file_exists("../uploads/".$row['image'])) {
            unlink("../uploads/".$row['image']);
        }
    }
    
    $conn->query("DELETE FROM tours WHERE id=$id");
    header("Location: index.php?msg=deleted");
    exit();
}

$tours = $conn->query("SELECT * FROM tours ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Artıyaşam Turizm</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
<link rel="shortcut icon" type="image/png" href="../uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #0f172a;
            color: white;
            padding: 20px 0;
        }
        .sidebar h3 {
            padding: 0 20px;
            margin-bottom: 30px;
            color: #ff7b00;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #1e293b;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .main-content {
            flex: 1;
            background: #f5f7fa;
            padding: 20px;
        }
        .header-bar {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .btn-add {
            background: #ff7b00;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-add:hover {
            background: #e66a00;
        }
        .btn-logout {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-passive {
            background: #f8d7da;
            color: #721c24;
        }
        .action-btns a {
            margin: 0 5px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 13px;
        }
        .btn-edit {
            background: #007bff;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .tour-thumb {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }
        .alert {
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
           <div class="sidebar" style="text-align: center;">
    <img src="../artiyasamlogo2.png" alt="Artıyaşam" style="height: 50px; margin: 20px auto; display: block;">
    <!-- ... -->
</div>
            <a href="index.php" class="active">📊 Turlar</a>
            <a href="add_tour.php">➕ Tur Ekle</a>
            <a href="settings.php">⚙️ Ayarlar</a>
            <a href="logout.php">🚪 Çıkış Yap</a>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header-bar">
                <h2>Turlar</h2>
                <div>
                    <span style="margin-right: 15px;">Hoşgeldin, <?php echo $_SESSION['admin_username']; ?></span>
                    <a href="add_tour.php" class="btn-add">➕ Yeni Tur Ekle</a>
                </div>
            </div>
            
            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success">
                    <?php 
                    if($_GET['msg'] == 'added') echo "Tur başarıyla eklendi!";
                    if($_GET['msg'] == 'updated') echo "Tur başarıyla güncellendi!";
                    if($_GET['msg'] == 'deleted') echo "Tur başarıyla silindi!";
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Görsel</th>
                            <th>Tur Başlığı</th>
                            <th>Kalkış Tarihi</th>
                            <th>Tur Tarihi</th>
                            <th>Fiyat</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($tours->num_rows > 0): ?>
                            <?php while($row = $tours->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td>
                                        <?php if($row['image']): ?>
                                            <img src="../uploads/<?php echo $row['image']; ?>" 
                                                 class="tour-thumb"
                                                 onerror="this.src='https://picsum.photos/60/40?travel'">
                                        <?php else: ?>
                                            <img src="https://picsum.photos/60/40?travel" class="tour-thumb">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row['title']; ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($row['departure_date'])); ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($row['tour_date'])); ?></td>
                                    <td>₺<?php echo number_format($row['price'],2,',','.'); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $row['status'] == 'active' ? 'status-active' : 'status-passive'; ?>">
                                            <?php echo $row['status'] == 'active' ? 'Aktif' : 'Pasif'; ?>
                                        </span>
                                    </td>
                                    <td class="action-btns">
                                        <a href="edit_tour.php?id=<?php echo $row['id']; ?>" class="btn-edit">Düzenle</a>
                                        <a href="?delete=<?php echo $row['id']; ?>" 
                                           class="btn-delete" 
                                           onclick="return confirm('Bu turu silmek istediğinize emin misiniz?')">Sil</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 30px;">
                                    Henüz hiç tur eklenmemiş. 
                                    <a href="add_tour.php" style="color: #ff7b00;">Hemen ekle</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>