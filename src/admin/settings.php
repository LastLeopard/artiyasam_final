<?php
session_start();
include "../config.php";

// Giriş kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Varsayılan ayarlar
$default_settings = [
    // ----- GENEL AYARLAR -----
    'site_title' => 'Artıyaşam Turizm',
    'site_description' => 'Ankara çıkışlı butik turlar, doğa ve kültür gezileri.',
    'site_keywords' => 'tur, seyahat, gezi, tatil, doğa, kültür, Ankara',
    'site_logo' => 'artiyasamlogo2.png',
    'site_favicon' => 'favicon.ico',
    'site_language' => 'tr',
    'maintenance_mode' => '0',
    
    // ----- İLETİŞİM -----
    'contact_phone' => '+90 530 489 87 00',
    'contact_phone2' => '0532 123 45 67',
    'contact_fax' => '',
    'contact_email' => 'info@artiyasam.com',
    'contact_address' => 'Meşrutiyet, Selanik Cd 78/7, 06420 Çankaya/Ankara',
    'contact_map' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3639.147061344414!2d32.85586118139792!3d39.91575510143188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14d34fab191a5765%3A0xf98a5f70247375d8!2zQXJ0xLF5YcWfYW0gVHVyaXpt!5e0!3m2!1str!2str!4v1772292355485!5m2!1str!2str" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
    'working_hours' => 'Hafta içi 09:00 - 18:00',
    'whatsapp_number' => '905304898700',
    'whatsapp_message' => 'Merhaba, tur hakkında bilgi almak istiyorum.',
    
    // ----- SOSYAL MEDYA -----
    'facebook_url' => 'https://facebook.com/artiyasam',
    'twitter_url' => 'https://twitter.com/artiyasam',
    'instagram_url' => 'https://instagram.com/artiyasam',
    'youtube_url' => 'https://youtube.com/artiyasam',
    'linkedin_url' => '',
    'pinterest_url' => '',
    'tiktok_url' => '',
    
    // ----- FOOTER -----
    'footer_about' => 'Ankara çıkışlı butik turlar, doğa ve kültür gezileri.',
    'footer_copyright' => '© 2026 Artıyaşam Turizm | Tüm Hakları Saklıdır',
    'footer_newsletter_text' => 'Yeni turlar ve fırsatlardan ilk siz haberdar olun',
    
    // ----- SEO -----
    'meta_author' => 'Artıyaşam Turizm',
    'google_analytics' => '',
    'google_verification' => '',
    'yandex_verification' => '',
    'facebook_pixel' => '',
    'og_image' => '',
    'og_title' => 'Artıyaşam Turizm - Unutulmaz Tur Deneyimleri',
    'og_description' => 'Ankara çıkışlı butik turlar, yurtiçi ve yurtdışı gezileri',
    
    // ----- TEMA -----
    'theme_color' => '#ff8800',
    'secondary_color' => '#0f172a',
    'font_family' => 'Segoe UI',
    'hero_background_image' => '',
    'hero_overlay_opacity' => '0.5',
    
    // ----- SİTE DAVRANIŞI -----
    'tours_per_page' => '12',
    'show_featured_tours' => '1',
    'show_category_counts' => '1',
    'hero_auto_change' => '1',
    'hero_change_interval' => '10',
    
    // ----- GÜVENLİK -----
    'max_login_attempts' => '5',
    'session_timeout' => '30',
    
    // ----- İSTATİSTİK -----
    'total_visitors' => '0',
    'total_bookings' => '0',
    'last_backup' => '',
    
    // ----- YENİ HERO AYARLARI -----
    'hero_default_title' => '%100 Size Özel Butik Turlar',
    'hero_subtitle' => 'Ankara çıkışlı yurtiçi, yurtdışı ve günübirlik turlar',
    
    // ----- YENİ BUTON AYARLARI -----
    'hero_button_text' => 'Turları İncele',
    'hero_button_color' => '#ff7b00',
    'hero_button_text_color' => '#ffffff',
    'hero_button_hover_color' => '#e66a00',
    'admin_button_text' => '🔐 ADMIN',
    'admin_button_color' => '#ff7b00',
    'admin_button_size' => 'small',
    'whatsapp_button_text' => '📱 WHATSAPP\'TAN YAZ',
    
    // ----- ÖZEL KODLAR -----
    'custom_head_code' => '',
    'custom_body_code' => '',
    'custom_css' => ''
];

// Varsayılan ayarları veritabanına ekle
foreach ($default_settings as $key => $value) {
    $check = $conn->query("SELECT id FROM site_settings WHERE setting_key = '$key'");
    if ($check->num_rows == 0) {
        $escaped_value = mysqli_real_escape_string($conn, $value);
        $conn->query("INSERT INTO site_settings (setting_key, setting_value) VALUES ('$key', '$escaped_value')");
    }
}

// Şifre değiştirme işlemi
$password_message = "";
$password_error = "";

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $admin_result = $conn->query("SELECT * FROM admin_users WHERE username='" . $_SESSION['admin_username'] . "'");
    $admin = $admin_result->fetch_assoc();

    if (!password_verify($current_password, $admin['password'])) {
        $password_error = "Mevcut şifre hatalı!";
    } elseif (strlen($new_password) < 6) {
        $password_error = "Yeni şifre en az 6 karakter olmalı!";
    } elseif ($new_password != $confirm_password) {
        $password_error = "Yeni şifreler eşleşmiyor!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $conn->query("UPDATE admin_users SET password='$hashed_password' WHERE username='" . $_SESSION['admin_username'] . "'");
        $password_message = "Şifreniz başarıyla değiştirildi!";
    }
}

// Ayarları güncelleme
$settings_message = "";

if (isset($_POST['save_settings'])) {
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Logo yükleme
    if (isset($_FILES["site_logo"]) && $_FILES["site_logo"]["error"] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES["site_logo"]["type"], $allowed_types)) {
            $extension = pathinfo($_FILES["site_logo"]["name"], PATHINFO_EXTENSION);
            $logo_name = "logo_" . time() . "." . $extension;
            if (move_uploaded_file($_FILES["site_logo"]["tmp_name"], $target_dir . $logo_name)) {
                $_POST['site_logo'] = $logo_name;
            }
        }
    }

    // Favicon yükleme
    if (isset($_FILES["site_favicon"]) && $_FILES["site_favicon"]["error"] == 0) {
        $allowed_types = ['image/x-icon', 'image/png', 'image/jpeg'];
        if (in_array($_FILES["site_favicon"]["type"], $allowed_types)) {
            $extension = pathinfo($_FILES["site_favicon"]["name"], PATHINFO_EXTENSION);
            $favicon_name = "favicon_" . time() . "." . $extension;
            if (move_uploaded_file($_FILES["site_favicon"]["tmp_name"], $target_dir . $favicon_name)) {
                $_POST['site_favicon'] = $favicon_name;
            }
        }
    }

    // Hero arkaplan yükleme
    if (isset($_FILES["hero_background_image"]) && $_FILES["hero_background_image"]["error"] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($_FILES["hero_background_image"]["type"], $allowed_types)) {
            $extension = pathinfo($_FILES["hero_background_image"]["name"], PATHINFO_EXTENSION);
            $hero_name = "hero_" . time() . "." . $extension;
            if (move_uploaded_file($_FILES["hero_background_image"]["tmp_name"], $target_dir . $hero_name)) {
                $_POST['hero_background_image'] = $hero_name;
            }
        }
    }

    // Ayarları kaydet
    foreach ($_POST as $key => $value) {
        if (array_key_exists($key, $default_settings)) {
            $escaped_value = mysqli_real_escape_string($conn, $value);
            $conn->query("UPDATE site_settings SET setting_value='$escaped_value' WHERE setting_key='$key'");
        }
    }
    $settings_message = "Ayarlar başarıyla güncellendi!";
}

// Mevcut ayarları çek
$settings = [];
$result = $conn->query("SELECT * FROM site_settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gelişmiş Ayarlar - Artıyaşam</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
<link rel="shortcut icon" type="image/png" href="../uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
    <style>
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #0f172a; color: white; padding: 20px 0; }
        .sidebar a { display: block; padding: 12px 20px; color: white; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #1e293b; }
        .main-content { flex: 1; background: #f5f7fa; padding: 20px; }
        .header-bar { background: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        
        .tab-nav { 
            display: flex; 
            gap: 5px; 
            flex-wrap: wrap; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #ddd; 
            padding-bottom: 10px; 
            position: sticky;
            top: 0;
            background: white;
            z-index: 100;
        }
        .tab-btn { 
            padding: 12px 20px; 
            background: #f5f7fa; 
            border: none; 
            border-radius: 8px 8px 0 0; 
            cursor: pointer; 
            font-weight: 600;
            transition: all 0.3s;
        }
        .tab-btn:hover { background: #e9ecef; }
        .tab-btn.active { background: #ff7b00; color: white; }
        .tab-content { display: none; padding: 20px; background: white; border-radius: 0 0 8px 8px; }
        .tab-content.active { display: block; }
        
        .settings-container { display: flex; flex-wrap: wrap; gap: 20px; }
        .settings-card { 
            background: white; 
            border-radius: 8px; 
            padding: 25px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
            flex: 1 1 calc(33.333% - 20px); 
            min-width: 300px;
            border: 1px solid #eee;
        }
        .settings-card h3 { 
            margin-bottom: 20px; 
            padding-bottom: 10px; 
            border-bottom: 2px solid #f0f0f0;
            color: #ff7b00;
        }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group textarea { height: 100px; resize: vertical; }
        
        .btn-save { 
            background: #ff7b00; 
            color: white; 
            padding: 12px 30px; 
            border: none; 
            border-radius: 5px; 
            font-size: 16px; 
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-save:hover { background: #e66a00; }
        
        .message { padding: 12px 20px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .image-preview { 
            width: 100px; 
            height: 100px; 
            border-radius: 5px; 
            border: 1px solid #ddd; 
            margin-top: 10px; 
            background-size: cover; 
            background-position: center;
            background-color: #f5f5f5;
        }
        .image-preview.small { width: 50px; height: 50px; }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        
        hr {
            margin: 30px 0;
            border: none;
            border-top: 2px dashed #eee;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div style="text-align: center; padding: 20px;">
                <img src="../uploads/<?php echo $settings['site_logo']; ?>" style="max-height: 50px; max-width: 200px;">
            </div>
            <a href="index.php">📊 Turlar</a>
            <a href="add_tour.php">➕ Tur Ekle</a>
            <a href="settings.php" class="active">⚙️ Gelişmiş Ayarlar</a>
            <a href="logout.php">🚪 Çıkış Yap</a>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header-bar">
                <h2>⚙️ Gelişmiş Site Ayarları</h2>
                <span>Hoşgeldin, <?php echo $_SESSION['admin_username']; ?></span>
            </div>
            
            <?php if ($settings_message): ?>
                <div class="message success"><?php echo $settings_message; ?></div>
            <?php endif; ?>
            <?php if ($password_message): ?>
                <div class="message success"><?php echo $password_message; ?></div>
            <?php endif; ?>
            <?php if ($password_error): ?>
                <div class="message error"><?php echo $password_error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <!-- SEKME MENÜSÜ -->
                <div class="tab-nav">
                    <button type="button" class="tab-btn active" onclick="openTab('general')">🌐 Genel</button>
                    <button type="button" class="tab-btn" onclick="openTab('contact')">📞 İletişim</button>
                    <button type="button" class="tab-btn" onclick="openTab('social')">📱 Sosyal Medya</button>
                    <button type="button" class="tab-btn" onclick="openTab('seo')">🔍 SEO</button>
                    <button type="button" class="tab-btn" onclick="openTab('theme')">🎨 Tema</button>
                    <button type="button" class="tab-btn" onclick="openTab('settings')">⚙️ Davranış</button>
                    <button type="button" class="tab-btn" onclick="openTab('hero')">🖼️ Hero Slayt</button>
                    <button type="button" class="tab-btn" onclick="openTab('buttons')">🔘 Butonlar</button>
                    <button type="button" class="tab-btn" onclick="openTab('footer')">🦶 Footer</button>
                    <button type="button" class="tab-btn" onclick="openTab('codes')">📝 Özel Kodlar</button>
                </div>
                
                <!-- 1. GENEL SEKMESİ -->
                <div id="tab-general" class="tab-content active">
                    <div class="settings-container">
                        <div class="settings-card">
                            <h3>🌐 Site Bilgileri</h3>
                            <div class="form-group">
                                <label>Site Başlığı</label>
                                <input type="text" name="site_title" value="<?php echo htmlspecialchars($settings['site_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Site Açıklaması</label>
                                <textarea name="site_description"><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Anahtar Kelimeler</label>
                                <input type="text" name="site_keywords" value="<?php echo htmlspecialchars($settings['site_keywords']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Site Dili</label>
                                <select name="site_language">
                                    <option value="tr" <?php echo $settings['site_language'] == 'tr' ? 'selected' : ''; ?>>Türkçe</option>
                                    <option value="en" <?php echo $settings['site_language'] == 'en' ? 'selected' : ''; ?>>İngilizce</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <h3>🖼️ Logo & Favicon</h3>
                            <div class="form-group">
                                <label>Mevcut Logo</label>
                                <div class="image-preview" style="background-image: url('../uploads/<?php echo $settings['site_logo']; ?>');"></div>
                                <input type="file" name="site_logo" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label>Mevcut Favicon</label>
                                <div class="image-preview small" style="background-image: url('../uploads/<?php echo $settings['site_favicon']; ?>');"></div>
                                <input type="file" name="site_favicon" accept="image/x-icon,image/png">
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" name="maintenance_mode" value="1" <?php echo $settings['maintenance_mode'] == '1' ? 'checked' : ''; ?>>
                                <label>Bakım Modu (siteyi geçici olarak kapat)</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 2. İLETİŞİM SEKMESİ -->
                <div id="tab-contact" class="tab-content">
                    <div class="settings-container">
                        <div class="settings-card">
                            <h3>📞 İletişim Bilgileri</h3>
                            <div class="form-group">
                                <label>Telefon 1</label>
                                <input type="text" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Telefon 2</label>
                                <input type="text" name="contact_phone2" value="<?php echo htmlspecialchars($settings['contact_phone2']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Faks</label>
                                <input type="text" name="contact_fax" value="<?php echo htmlspecialchars($settings['contact_fax']); ?>">
                            </div>
                            <div class="form-group">
                                <label>E-posta</label>
                                <input type="email" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Adres</label>
                                <textarea name="contact_address"><?php echo htmlspecialchars($settings['contact_address']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Çalışma Saatleri</label>
                                <input type="text" name="working_hours" value="<?php echo htmlspecialchars($settings['working_hours']); ?>">
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <h3>🗺️ Harita & WhatsApp</h3>
                            <div class="form-group">
                                <label>Google Maps Kodu</label>
                                <textarea name="contact_map"><?php echo htmlspecialchars($settings['contact_map']); ?></textarea>
                                <small style="color: #666;">iframe kodunu yapıştırın</small>
                            </div>
                            <div class="form-group">
                                <label>WhatsApp Numarası</label>
                                <input type="text" name="whatsapp_number" value="<?php echo htmlspecialchars($settings['whatsapp_number']); ?>">
                                <small style="color: #666;">Başında 0 olmadan, ülke koduyla (90530...)</small>
                            </div>
                            <div class="form-group">
                                <label>WhatsApp Mesajı</label>
                                <input type="text" name="whatsapp_message" value="<?php echo htmlspecialchars($settings['whatsapp_message']); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 3. SOSYAL MEDYA SEKMESİ -->
                <div id="tab-social" class="tab-content">
                    <div class="settings-container">
                        <div class="settings-card">
                            <h3>📱 Sosyal Medya Hesapları</h3>
                            <div class="form-group">
                                <label>Facebook URL</label>
                                <input type="url" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Twitter URL</label>
                                <input type="url" name="twitter_url" value="<?php echo htmlspecialchars($settings['twitter_url']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Instagram URL</label>
                                <input type="url" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url']); ?>">
                            </div>
                            <div class="form-group">
                                <label>YouTube URL</label>
                                <input type="url" name="youtube_url" value="<?php echo htmlspecialchars($settings['youtube_url']); ?>">
                            </div>
                            <div class="form-group">
                                <label>LinkedIn URL</label>
                                <input type="url" name="linkedin_url" value="<?php echo htmlspecialchars($settings['linkedin_url']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Pinterest URL</label>
                                <input type="url" name="pinterest_url" value="<?php echo htmlspecialchars($settings['pinterest_url']); ?>">
                            </div>
                            <div class="form-group">
                                <label>TikTok URL</label>
                                <input type="url" name="tiktok_url" value="<?php echo htmlspecialchars($settings['tiktok_url']); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 4. SEO SEKMESİ -->
                <div id="tab-seo" class="tab-content">
                    <div class="settings-container">
                        <div class="settings-card">
                            <h3>🔍 SEO Ayarları</h3>
                            <div class="form-group">
                                <label>Meta Yazar</label>
                                <input type="text" name="meta_author" value="<?php echo htmlspecialchars($settings['meta_author']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Google Analytics ID</label>
                                <input type="text" name="google_analytics" value="<?php echo htmlspecialchars($settings['google_analytics']); ?>" placeholder="UA-XXXXX-X veya G-XXXXXXX">
                            </div>
                            <div class="form-group">
                                <label>Facebook Pixel ID</label>
                                <input type="text" name="facebook_pixel" value="<?php echo htmlspecialchars($settings['facebook_pixel']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Google Doğrulama Kodu</label>
                                <input type="text" name="google_verification" value="<?php echo htmlspecialchars($settings['google_verification']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Yandex Doğrulama Kodu</label>
                                <input type="text" name="yandex_verification" value="<?php echo htmlspecialchars($settings['yandex_verification']); ?>">
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <h3>🖼️ Open Graph (Sosyal Medya Paylaşımları)</h3>
                            <div class="form-group">
                                <label>OG Başlık</label>
                                <input type="text" name="og_title" value="<?php echo htmlspecialchars($settings['og_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>OG Açıklama</label>
                                <textarea name="og_description"><?php echo htmlspecialchars($settings['og_description']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>OG Görsel</label>
                                <input type="file" name="og_image" accept="image/*">
                                <?php if(!empty($settings['og_image'])): ?>
                                    <div class="image-preview" style="background-image: url('../uploads/<?php echo $settings['og_image']; ?>');"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 5. TEMA SEKMESİ -->
                <div id="tab-theme" class="tab-content">
                    <div class="settings-container">
                        <div class="settings-card">
                            <h3>🎨 Renkler</h3>
                            <div class="form-group">
                                <label>Ana Renk (Turuncu)</label>
                                <input type="color" name="theme_color" value="<?php echo $settings['theme_color']; ?>">
                            </div>
                            <div class="form-group">
                                <label>İkincil Renk (Lacivert)</label>
                                <input type="color" name="secondary_color" value="<?php echo $settings['secondary_color']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Font Ailesi</label>
                                <select name="font_family">
                                    <option value="Segoe UI" <?php echo $settings['font_family'] == 'Segoe UI' ? 'selected' : ''; ?>>Segoe UI</option>
                                    <option value="Poppins" <?php echo $settings['font_family'] == 'Poppins' ? 'selected' : ''; ?>>Poppins</option>
                                    <option value="Roboto" <?php echo $settings['font_family'] == 'Roboto' ? 'selected' : ''; ?>>Roboto</option>
                                    <option value="Open Sans" <?php echo $settings['font_family'] == 'Open Sans' ? 'selected' : ''; ?>>Open Sans</option>
                                    <option value="Montserrat" <?php echo $settings['font_family'] == 'Montserrat' ? 'selected' : ''; ?>>Montserrat</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <h3>🖼️ Hero Arkaplan</h3>
                            <div class="form-group">
                                <label>Hero Görseli</label>
                                <input type="file" name="hero_background_image" accept="image/*">
                                <?php if (!empty($settings['hero_background_image'])): ?>
                                    <div class="image-preview" style="background-image: url('../uploads/<?php echo $settings['hero_background_image']; ?>');"></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Overlay Opaklık (0-1)</label>
                                <input type="range" name="hero_overlay_opacity" min="0" max="1" step="0.1" value="<?php echo $settings['hero_overlay_opacity']; ?>">
                                <span><?php echo $settings['hero_overlay_opacity']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 6. DAVRANIŞ SEKMESİ -->
                <div id="tab-settings" class="tab-content">
                    <div class="settings-container">
                        <div class="settings-card">
                            <h3>⚙️ Site Davranışı</h3>
                            <div class="form-group">
                                <label>Sayfa Başına Tur Sayısı</label>
                                <input type="number" name="tours_per_page" value="<?php echo $settings['tours_per_page']; ?>" min="1" max="50">
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" name="show_featured_tours" value="1" <?php echo $settings['show_featured_tours'] == '1' ? 'checked' : ''; ?>>
                                <label>Öne Çıkan Turları Göster</label>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" name="show_category_counts" value="1" <?php echo $settings['show_category_counts'] == '1' ? 'checked' : ''; ?>>
                                <label>Kategori Sayılarını Göster</label>
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <h3>📊 İstatistikler</h3>
                            <div class="info-box">
                                <p><strong>Toplam Ziyaretçi:</strong> <?php echo $settings['total_visitors']; ?></p>
                                <p><strong>Toplam Rezervasyon:</strong> <?php echo $settings['total_bookings']; ?></p>
                                <p><strong>Son Yedekleme:</strong> <?php echo $settings['last_backup'] ? date('d.m.Y H:i', strtotime($settings['last_backup'])) : 'Henüz yok'; ?></p>
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <h3>🔐 Güvenlik</h3>
                            <div class="form-group">
                                <label>Maks. Giriş Denemesi</label>
                                <input type="number" name="max_login_attempts" value="<?php echo $settings['max_login_attempts']; ?>" min="1" max="20">
                            </div>
                            <div class="form-group">
                                <label>Oturum Süresi (dakika)</label>
                                <input type="number" name="session_timeout" value="<?php echo $settings['session_timeout']; ?>" min="5" max="480">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 7. HERO SLAYT SEKMESİ (YENİ) -->
                <div id="tab-hero" class="tab-content">
                    <div class="settings-container">
                        <div class="settings-card">
                            <h3>🖼️ Hero Slayt Ayarları</h3>
                            <div class="form-group">
                                <label>Hero Başlık (Varsayılan)</label>
                                <input type="text" name="hero_default_title" value="<?php echo htmlspecialchars($settings['hero_default_title'] ?? '%100 Size Özel Butik Turlar'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Hero Alt Başlık</label>
                                <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($settings['hero_subtitle'] ?? 'Ankara çıkışlı yurtiçi, yurtdışı ve günübirlik turlar'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Hero Slayt Hızı (saniye)</label>
                                <input type="number" name="hero_change_interval" value="<?php echo $settings['hero_change_interval'] ?? '10'; ?>" min="2" max="30">
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" name="hero_auto_change" value="1" <?php echo ($settings['hero_auto_change'] ?? '1') == '1' ? 'checked' : ''; ?>> 
                                <label>Slayt Otomatik Değişsin</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 8. BUTON AYARLARI SEKMESİ (YENİ) -->
                <div id="tab-buttons" class="tab-content">
                    <div class="settings-container">
                        <div class="settings-card">
                            <h3>🔘 Hero Butonu</h3>
                            <div class="form-group">
                                <label>Buton Metni</label>
                                <input type="text" name="hero_button_text" value="<?php echo htmlspecialchars($settings['hero_button_text'] ?? 'Turları İncele'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Buton Rengi</label>
                                <input type="color" name="hero_button_color" value="<?php echo $settings['hero_button_color'] ?? '#ff7b00'; ?>">
                            </div>
                            <div class="form-group">
                                <label>Buton Yazı Rengi</label>
                                <input type="color" name="hero_button_text_color" value="<?php echo $settings['hero_button_text_color'] ?? '#ffffff'; ?>">
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <h3>🔐 Admin Butonu</h3>
                            <div class="form-group">
                                <label>Buton Metni</label>
                                <input type="text" name="admin_button_text" value="<?php echo htmlspecialchars($settings['admin_button_text'] ?? '🔐 ADMIN'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Buton Rengi</label>
                                <input type="color" name="admin_button_color" value="<?php echo $settings['admin_button_color'] ?? '#ff7b00'; ?>">
                            </div>
                            <div class="form-group">
                                <label>Buton Boyutu</label>
                                <select name="admin_button_size">
                                    <option value="small" <?php echo ($settings['admin_button_size'] ?? 'small') == 'small' ? 'selected' : ''; ?>>Küçük</option>
                                    <option value="medium" <?php echo ($settings['admin_button_size'] ?? '') == 'medium' ? 'selected' : ''; ?>>Orta</option>
                                    <option value="large" <?php echo ($settings['admin_button_size'] ?? '') == 'large' ? 'selected' : ''; ?>>Büyük</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <h3>📱 WhatsApp Butonu</h3>
                            <div class="form-group">
                                <label>Buton Metni</label>
                                <input type="text" name="whatsapp_button_text" value="<?php echo htmlspecialchars($settings['whatsapp_button_text'] ?? '📱 WHATSAPP\'TAN YAZ'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 9. FOOTER SEKMESİ (YENİ) -->
                <div id="tab-footer" class="tab-content">
                    <div class="settings-container">
                        <div class="settings-card">
                            <h3>🦶 Footer Metinleri</h3>
                            <div class="form-group">
                                <label>Hakkımızda Metni</label>
                                <textarea name="footer_about"><?php echo htmlspecialchars($settings['footer_about'] ?? 'Ankara çıkışlı butik turlar, doğa ve kültür gezileri. Size özel rotalar, unutulmaz deneyimler.'); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Telif Hakkı Metni</label>
                                <input type="text" name="footer_copyright" value="<?php echo htmlspecialchars($settings['footer_copyright'] ?? '© 2026 Artıyaşam Turizm | Tüm Hakları Saklıdır'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Bülten Metni</label>
                                <input type="text" name="footer_newsletter_text" value="<?php echo htmlspecialchars($settings['footer_newsletter_text'] ?? 'Yeni turlar ve fırsatlardan ilk siz haberdar olun'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 10. ÖZEL KODLAR SEKMESİ (YENİ) -->
                <div id="tab-codes" class="tab-content">
                    <div class="settings-container">
                        <div class="settings-card" style="flex: 1 1 100%;">
                            <h3>📝 Head Bölümü Özel Kodlar</h3>
                            <div class="form-group">
                                <label>&lt;head&gt; içine eklenecek kodlar (Google Analytics, Facebook Pixel, vb.)</label>
                                <textarea name="custom_head_code" rows="8" placeholder="<meta name='...' content='...'>
<script>...</script>"><?php echo htmlspecialchars($settings['custom_head_code'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="settings-card" style="flex: 1 1 100%;">
                            <h3>📝 Body Bölümü Özel Kodlar</h3>
                            <div class="form-group">
                                <label>&lt;body&gt; sonuna eklenecek kodlar (Chat widget, reklam kodları, vb.)</label>
                                <textarea name="custom_body_code" rows="8" placeholder="<script>...</script>"><?php echo htmlspecialchars($settings['custom_body_code'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="settings-card" style="flex: 1 1 100%;">
                            <h3>🎨 Özel CSS</h3>
                            <div class="form-group">
                                <label>Siteye özel CSS kodları</label>
                                <textarea name="custom_css" rows="8" placeholder=".ozel-class { color: red; }
.hero { border-radius: 0; }"><?php echo htmlspecialchars($settings['custom_css'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- KAYDET BUTONU -->
                <div style="text-align: center; margin: 30px 0;">
                    <button type="submit" name="save_settings" class="btn-save" style="font-size: 18px; padding: 15px 50px;">💾 TÜM AYARLARI KAYDET</button>
                </div>
            </form>
            
            <!-- Şifre Değiştirme Bölümü -->
            <div style="background: white; border-radius: 8px; padding: 25px; margin-top: 30px;">
                <h3 style="color: #ff7b00; margin-bottom: 20px;">🔐 Şifre Değiştir</h3>
                <form method="POST" action="">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>Mevcut Şifre</label>
                            <input type="password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label>Yeni Şifre</label>
                            <input type="password" name="new_password" id="new_password" required>
                        </div>
                        <div class="form-group">
                            <label>Yeni Şifre Tekrar</label>
                            <input type="password" name="confirm_password" required>
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn-save" style="background: #28a745;">Şifreyi Değiştir</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    function openTab(tabName) {
        var tabs = document.getElementsByClassName('tab-content');
        for(var i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('active');
        }
        
        var btns = document.getElementsByClassName('tab-btn');
        for(var i = 0; i < btns.length; i++) {
            btns[i].classList.remove('active');
        }
        
        document.getElementById('tab-' + tabName).classList.add('active');
        event.target.classList.add('active');
    }
    </script>
</body>
</html>