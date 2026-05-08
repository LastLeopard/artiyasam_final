<?php
session_start();
include "../config.php";

// Giriş kontrolü
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $departure_date = $_POST['departure_date'];
    $tour_date = $_POST['tour_date'];
    $status = $_POST['status'];
    $category = $_POST['category'];
    
    // KALKIŞ NOKTALARI
    $pickup_locations = [];
    if(isset($_POST['pickup_location']) && isset($_POST['pickup_time'])) {
        foreach($_POST['pickup_location'] as $key => $location) {
            if(!empty($location) && !empty($_POST['pickup_time'][$key])) {
                $pickup_locations[] = [
                    'location' => mysqli_real_escape_string($conn, $location),
                    'time' => mysqli_real_escape_string($conn, $_POST['pickup_time'][$key])
                ];
            }
        }
    }
    
    // KAPAK GÖRSELİ YÜKLEME
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $cover_image = "";
    if(isset($_FILES["cover_image"]) && $_FILES["cover_image"]["error"] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/webp'];
        $file_type = $_FILES["cover_image"]["type"];
        
        if(in_array($file_type, $allowed_types)) {
            $extension = pathinfo($_FILES["cover_image"]["name"], PATHINFO_EXTENSION);
            $cover_image = "cover_" . time() . "_" . uniqid() . "." . $extension;
            $target_file = $target_dir . $cover_image;
            
            if(!move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
                $error = "Kapak görseli yüklenirken bir hata oluştu!";
            }
        } else {
            $error = "Sadece JPG, JPEG, PNG, WEBP ve GIF dosyaları yüklenebilir!";
        }
    }
    
    if(empty($error)) {
        // Ana tur kaydı
        $sql = "INSERT INTO tours (title, description, image, departure_date, tour_date, status, category) 
                VALUES ('$title', '$description', '$cover_image', '$departure_date', '$tour_date', '$status', '$category')";
        
        if($conn->query($sql)) {
            $tour_id = $conn->insert_id;
            
            // TEMEL TUR DETAYLARI
            $duration = mysqli_real_escape_string($conn, $_POST['duration']);
            $transportation = mysqli_real_escape_string($conn, $_POST['transportation']);
            $accommodation = mysqli_real_escape_string($conn, $_POST['accommodation']);
            $meals = mysqli_real_escape_string($conn, $_POST['meals']);
            $guide = mysqli_real_escape_string($conn, $_POST['guide']);
            $insurance = mysqli_real_escape_string($conn, $_POST['insurance']);
            $min_participant = $_POST['min_participant'];
            $max_participant = $_POST['max_participant'];
            $difficulty = $_POST['difficulty'];
            $what_to_bring = mysqli_real_escape_string($conn, $_POST['what_to_bring']);
            
            $conn->query("INSERT INTO tour_details 
                (tour_id, duration, transportation, accommodation, meals, guide, insurance, min_participant, max_participant, difficulty, what_to_bring) 
                VALUES 
                ('$tour_id', '$duration', '$transportation', '$accommodation', '$meals', '$guide', '$insurance', '$min_participant', '$max_participant', '$difficulty', '$what_to_bring')");
            
            // DETAYLI FİYAT TABLOSUNU KAYDET
            $price_types = [
                'yetiskin', 'iki_kisilik', 'uc_kisilik', 'tek_kisilik', 
                'cocuk_7_12', 'cocuk_3_12', 'cocuk_0_6', 'cocuk_0_3'
            ];
            
            $currency = mysqli_real_escape_string($conn, $_POST['currency'] ?? 'TL');
            
            foreach($price_types as $type) {
                $cash = $_POST['price_' . $type . '_cash'] ?? 0;
                $card_single = $_POST['price_' . $type . '_card_single'] ?? 0;
                $card_installment = $_POST['price_' . $type . '_card_installment'] ?? 0;
                
                $conn->query("INSERT INTO tour_pricing 
                    (tour_id, price_type, cash_price, card_single, card_installment, currency) 
                    VALUES 
                    ('$tour_id', '$type', '$cash', '$card_single', '$card_installment', '$currency')");
            }
            
            // FİYAT NOTLARINI KAYDET
            if(isset($_POST['price_notes'])) {
                foreach($_POST['price_notes'] as $note) {
                    if(!empty(trim($note))) {
                        $note = mysqli_real_escape_string($conn, $note);
                        $conn->query("INSERT INTO tour_price_notes (tour_id, note_text) VALUES ('$tour_id', '$note')");
                    }
                }
            }
            
            // KALKIŞ NOKTALARINI KAYDET
            if(!empty($pickup_locations)) {
                foreach($pickup_locations as $pickup) {
                    $conn->query("INSERT INTO tour_pickup_locations (tour_id, location_name, departure_time, display_order) 
                                 VALUES ('$tour_id', '{$pickup['location']}', '{$pickup['time']}', 0)");
                }
            }
            
            // TURA DAHİL OLANLAR
            if(isset($_POST['included_item'])) {
                foreach($_POST['included_item'] as $key => $item) {
                    if(!empty($item)) {
                        $item = mysqli_real_escape_string($conn, $item);
                        $icon = mysqli_real_escape_string($conn, $_POST['included_icon'][$key]);
                        $conn->query("INSERT INTO tour_included (tour_id, item, icon) VALUES ('$tour_id', '$item', '$icon')");
                    }
                }
            }
            
            // TURA DAHİL OLMAYANLAR
            if(isset($_POST['excluded_item'])) {
                foreach($_POST['excluded_item'] as $key => $item) {
                    if(!empty($item)) {
                        $item = mysqli_real_escape_string($conn, $item);
                        $icon = mysqli_real_escape_string($conn, $_POST['excluded_icon'][$key]);
                        $conn->query("INSERT INTO tour_excluded (tour_id, item, icon) VALUES ('$tour_id', '$item', '$icon')");
                    }
                }
            }
            
            // EKSTRA HİZMETLER
            if(isset($_POST['extra_name'])) {
                foreach($_POST['extra_name'] as $key => $name) {
                    if(!empty($name)) {
                        $name = mysqli_real_escape_string($conn, $name);
                        $desc = mysqli_real_escape_string($conn, $_POST['extra_desc'][$key]);
                        $price = $_POST['extra_price'][$key];
                        $currency_extra = $_POST['extra_currency'][$key];
                        $conn->query("INSERT INTO tour_extras (tour_id, name, description, price, currency) 
                                     VALUES ('$tour_id', '$name', '$desc', '$price', '$currency_extra')");
                    }
                }
            }
            
            // ÖNEMLİ BİLGİLER
            if(isset($_POST['info_title'])) {
                foreach($_POST['info_title'] as $key => $title) {
                    if(!empty($title)) {
                        $title = mysqli_real_escape_string($conn, $title);
                        $content = mysqli_real_escape_string($conn, $_POST['info_content'][$key]);
                        $icon = mysqli_real_escape_string($conn, $_POST['info_icon'][$key]);
                        $conn->query("INSERT INTO tour_important_info (tour_id, title, content, icon) 
                                     VALUES ('$tour_id', '$title', '$content', '$icon')");
                    }
                }
            }
            
            // TUR PROGRAMI
            if(isset($_POST['itinerary_day'])) {
                foreach($_POST['itinerary_day'] as $key => $day) {
                    if(!empty($day)) {
                        $day_num = $day;
                        $title = mysqli_real_escape_string($conn, $_POST['itinerary_title'][$key]);
                        $desc = mysqli_real_escape_string($conn, $_POST['itinerary_desc'][$key]);
                        $accommodation = mysqli_real_escape_string($conn, $_POST['itinerary_accommodation'][$key]);
                        $meals = mysqli_real_escape_string($conn, $_POST['itinerary_meals'][$key]);
                        $conn->query("INSERT INTO tour_itinerary (tour_id, day_number, title, description, accommodation, meals) 
                                     VALUES ('$tour_id', '$day_num', '$title', '$desc', '$accommodation', '$meals')");
                    }
                }
            }
            
            // ÇOKLU GÖRSEL YÜKLEME
            if(!empty($_FILES['gallery_images']['name'][0])) {
                $target_dir = "../uploads/";
                $gallery_count = count($_FILES['gallery_images']['name']);
                
                for($i = 0; $i < $gallery_count; $i++) {
                    if($_FILES['gallery_images']['error'][$i] == 0) {
                        $file_type = $_FILES['gallery_images']['type'][$i];
                        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/webp'];
                        
                        if(in_array($file_type, $allowed_types)) {
                            $extension = pathinfo($_FILES['gallery_images']['name'][$i], PATHINFO_EXTENSION);
                            $image_name = "gallery_" . time() . "_" . uniqid() . "_$i." . $extension;
                            $target_file = $target_dir . $image_name;
                            
                            if(move_uploaded_file($_FILES['gallery_images']['tmp_name'][$i], $target_file)) {
                                $title = mysqli_real_escape_string($conn, $_POST['gallery_titles'][$i] ?? '');
                                $conn->query("INSERT INTO tour_images (tour_id, image_path, title, is_cover) 
                                             VALUES ('$tour_id', '$image_name', '$title', 0)");
                            }
                        }
                    }
                }
            }
            
            header("Location: index.php?msg=added");
            exit();
        } else {
            $error = "Veritabanı hatası: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tur Ekle - Artıyaşam Turizm</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #0f172a; color: white; padding: 20px 0; }
        .sidebar a { display: block; padding: 12px 20px; color: white; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #1e293b; }
        .main-content { flex: 1; padding: 20px; }
        .header-bar { background: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .form-container { background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-group textarea { resize: vertical; }
        .btn-save { background: #ff7b00; color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .btn-save:hover { background: #e66a00; }
        .btn-cancel { background: #6c757d; color: white; padding: 12px 30px; border: none; border-radius: 5px; text-decoration: none; display: inline-block; }
        .error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; }
        
        /* Tab Menü */
        .tab-nav { display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; background: white; position: sticky; top: 0; z-index: 100; }
        .tab-btn { padding: 12px 20px; background: #f5f7fa; border: none; border-radius: 8px 8px 0 0; cursor: pointer; font-weight: 600; transition: 0.3s; }
        .tab-btn:hover { background: #e9ecef; }
        .tab-btn.active { background: #ff7b00; color: white; }
        .tab-content { display: none; padding: 20px; background: white; border-radius: 0 0 8px 8px; border: 1px solid #ddd; border-top: none; }
        .tab-content.active { display: block; }
        
        .dynamic-item { background: #f8f9fa; padding: 20px; margin-bottom: 15px; border-radius: 8px; border-left: 4px solid #ff7b00; position: relative; }
        .remove-btn { position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; }
        
        /* Fiyat Tablosu */
        .price-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .price-table th { background: #ff7b00; color: white; padding: 12px; text-align: left; }
        .price-table td { padding: 8px; border: 1px solid #ddd; }
        .price-table input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        
        .gallery-preview { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px; }
        .gallery-item { width: 150px; border: 1px solid #ddd; border-radius: 8px; padding: 10px; background: #f9f9f9; }
        .gallery-item img { width: 100%; height: 100px; object-fit: cover; border-radius: 5px; }
        
        .pickup-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .pickup-location { flex: 2; }
        .pickup-time { flex: 1; }
        .pickup-remove { width: 30px; height: 30px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; }
        
        .info-box { background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; border-radius: 4px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div style="text-align: center; padding: 20px;">
                <img src="../uploads/<?php echo getSetting('site_logo', 'artiyasamlogo2.png'); ?>" alt="Artıyaşam" style="max-height: 50px;">
            </div>
            <a href="index.php">📊 Turlar</a>
            <a href="add_tour.php" class="active">➕ Tur Ekle</a>
            <a href="settings.php">⚙️ Ayarlar</a>
            <a href="logout.php">🚪 Çıkış Yap</a>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header-bar">
                <h2>➕ Yeni Tur Ekle</h2>
                <a href="index.php" class="btn-cancel">← Geri Dön</a>
            </div>
            
            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="" enctype="multipart/form-data">
                    
                    <!-- TEMEL BİLGİLER -->
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                        <h3 style="color: #ff7b00; margin-bottom: 20px;">📋 Temel Tur Bilgileri</h3>
                        
                        <div class="form-group">
                            <label>Tur Başlığı *</label>
                            <input type="text" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Tur Açıklaması</label>
                            <textarea name="description" style="width:100%; height:200px; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:inherit;"></textarea>
                            <small style="color:#666;">Düz metin giriniz. HTML etiketleri kullanmayınız.</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Kapak Görseli *</label>
                            <input type="file" name="cover_image" accept="image/*" required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div class="form-group">
                                <label>Kalkış Tarihi *</label>
                                <input type="date" name="departure_date" required>
                            </div>
                            <div class="form-group">
                                <label>Dönüş Tarihi *</label>
                                <input type="date" name="tour_date" required>
                            </div>
                            <div class="form-group">
                                <label>Kategori *</label>
                                <select name="category">
                                    <option value="yurtici">🏠 Yurtiçi Turlar</option>
                                    <option value="yurtdisi">✈️ Yurtdışı Turlar</option>
                                    <option value="gunubirlik">🌄 Günübirlik Turlar</option>
                                    <option value="ozel">🌟 Özel Butik Turlar</option>
                                    <option value="festival">🎪 Festival Turları</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Durum</label>
                                <select name="status">
                                    <option value="active">Aktif</option>
                                    <option value="passive">Pasif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- TAB MENÜ -->
                    <div class="tab-nav">
                        <button type="button" class="tab-btn active" onclick="openTab('details')">📋 Detaylar</button>
                        <button type="button" class="tab-btn" onclick="openTab('prices')">💰 Fiyatlar</button>
                        <button type="button" class="tab-btn" onclick="openTab('gallery')">📸 Galeri</button>
                        <button type="button" class="tab-btn" onclick="openTab('pickup')">🚌 Kalkış</button>
                        <button type="button" class="tab-btn" onclick="openTab('included')">✅ Dahil</button>
                        <button type="button" class="tab-btn" onclick="openTab('excluded')">❌ Hariç</button>
                        <button type="button" class="tab-btn" onclick="openTab('itinerary')">🗓️ Program</button>
                        <button type="button" class="tab-btn" onclick="openTab('info')">ℹ️ Bilgi</button>
                        <button type="button" class="tab-btn" onclick="openTab('extras')">➕ Ekstra</button>
                        <button type="button" class="tab-btn" onclick="openTab('whattobring')">🎒 Ne Getirmeli?</button>
                    </div>
                    
                    <!-- 1. DETAYLAR -->
                    <div id="tab-details" class="tab-content active">
                        <h4>📋 Tur Detayları</h4>
                        <div class="form-group"><label>Süre</label><input type="text" name="duration" placeholder="Örn: 4 Gece 5 Gün"></div>
                        <div class="form-group"><label>Ulaşım</label><input type="text" name="transportation" placeholder="Lüks otobüs"></div>
                        <div class="form-group"><label>Konaklama</label><input type="text" name="accommodation" placeholder="4* Otel"></div>
                        <div class="form-group"><label>Öğünler</label><input type="text" name="meals" placeholder="Sabah kahvaltısı"></div>
                        <div class="form-group"><label>Rehber</label><input type="text" name="guide" placeholder="Profesyonel rehber"></div>
                        <div class="form-group"><label>Sigorta</label><input type="text" name="insurance" placeholder="Seyahat sigortası"></div>
                        <div style="display: grid; grid-template-columns: repeat(3,1fr); gap:15px;">
                            <div class="form-group"><label>Min. Kişi</label><input type="number" name="min_participant" value="1"></div>
                            <div class="form-group"><label>Max. Kişi</label><input type="number" name="max_participant" value="50"></div>
                            <div class="form-group"><label>Zorluk</label>
                                <select name="difficulty">
                                    <option value="kolay">🌿 Kolay</option>
                                    <option value="orta" selected>⛰️ Orta</option>
                                    <option value="zor">🏔️ Zor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 2. FİYATLAR -->
                    <div id="tab-prices" class="tab-content">
                        <h4 style="color:#28a745;">💰 Detaylı Fiyat Tablosu</h4>
                        
                        <div class="info-box">
                            <strong>📌 Bilgi:</strong> Sadece rakam giriniz. Kullanılmayan alanları 0 bırakınız.
                        </div>
                        
                        <table class="price-table">
                            <thead>
                                <tr>
                                    <th>Fiyat Tipi</th>
                                    <th>Nakit</th>
                                    <th>Kredi Kartı Tek Çekim</th>
                                    <th>Kredi Kartı Taksit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td><b>Yetişkin</b></td><td><input type="number" name="price_yetiskin_cash" step="0.01" value="0"></td><td><input type="number" name="price_yetiskin_card_single" step="0.01" value="0"></td><td><input type="number" name="price_yetiskin_card_installment" step="0.01" value="0"></td></tr>
                                <tr><td><b>İki Kişilik Odada Kişi Başı</b></td><td><input type="number" name="price_iki_kisilik_cash" step="0.01" value="0"></td><td><input type="number" name="price_iki_kisilik_card_single" step="0.01" value="0"></td><td><input type="number" name="price_iki_kisilik_card_installment" step="0.01" value="0"></td></tr>
                                <tr><td><b>Üç Kişilik Odada Kişi Başı</b></td><td><input type="number" name="price_uc_kisilik_cash" step="0.01" value="0"></td><td><input type="number" name="price_uc_kisilik_card_single" step="0.01" value="0"></td><td><input type="number" name="price_uc_kisilik_card_installment" step="0.01" value="0"></td></tr>
                                <tr style="background:#fff3cd;"><td><b>Tek Kişilik Oda Farkı</b></td><td><input type="number" name="price_tek_kisilik_cash" step="0.01" value="0"></td><td><input type="number" name="price_tek_kisilik_card_single" step="0.01" value="0"></td><td><input type="number" name="price_tek_kisilik_card_installment" step="0.01" value="0"></td></tr>
                                <tr><td><b>Çocuk 7-12 Yaş</b></td><td><input type="number" name="price_cocuk_7_12_cash" step="0.01" value="0"></td><td><input type="number" name="price_cocuk_7_12_card_single" step="0.01" value="0"></td><td><input type="number" name="price_cocuk_7_12_card_installment" step="0.01" value="0"></td></tr>
                                <tr><td><b>Çocuk 3-12 Yaş</b></td><td><input type="number" name="price_cocuk_3_12_cash" step="0.01" value="0"></td><td><input type="number" name="price_cocuk_3_12_card_single" step="0.01" value="0"></td><td><input type="number" name="price_cocuk_3_12_card_installment" step="0.01" value="0"></td></tr>
                                <tr style="background:#d4edda;"><td><b>Çocuk 0-6 Yaş (Ücretsiz)</b></td><td><input type="number" name="price_cocuk_0_6_cash" step="0.01" value="0"></td><td><input type="number" name="price_cocuk_0_6_card_single" step="0.01" value="0"></td><td><input type="number" name="price_cocuk_0_6_card_installment" step="0.01" value="0"></td></tr>
                                <tr style="background:#d1ecf1;"><td><b>Çocuk 0-3 Yaş (Ücretsiz)</b></td><td><input type="number" name="price_cocuk_0_3_cash" step="0.01" value="0"></td><td><input type="number" name="price_cocuk_0_3_card_single" step="0.01" value="0"></td><td><input type="number" name="price_cocuk_0_3_card_installment" step="0.01" value="0"></td></tr>
                            </tbody>
                        </table>
                        
                        <div class="form-group" style="max-width:200px;">
                            <label>Para Birimi:</label>
                            <select name="currency">
                                <option value="TL">₺ TL</option>
                                <option value="EUR">€ EUR</option>
                                <option value="USD">$ USD</option>
                            </select>
                        </div>
                        
                        <!-- Fiyat Notları -->
                        <div style="margin-top:40px;">
                            <h4 style="color:#17a2b8;">📝 Fiyat Notları</h4>
                            <div id="price-notes">
                                <div class="dynamic-item" style="border-left-color:#17a2b8;">
                                    <textarea name="price_notes[]" style="width:100%; height:80px; padding:10px;" placeholder="Fiyat notu...">7-12 yaş ücretleri iki yetişkin yanında kalmak koşulu ile geçerlidir.</textarea>
                                    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
                                </div>
                                <div class="dynamic-item" style="border-left-color:#17a2b8;">
                                    <textarea name="price_notes[]" style="width:100%; height:80px; padding:10px;" placeholder="Fiyat notu...">3-12 yaş ücretleri iki yetişkin yanında kalmak koşulu ile geçerlidir.</textarea>
                                    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
                                </div>
                                <div class="dynamic-item" style="border-left-color:#17a2b8;">
                                    <textarea name="price_notes[]" style="width:100%; height:80px; padding:10px;" placeholder="Fiyat notu...">Aynı aileden 2. ve daha fazla çocuklar için çocuk başı TL ilave edilir.</textarea>
                                    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
                                </div>
                                <div class="dynamic-item" style="border-left-color:#17a2b8;">
                                    <textarea name="price_notes[]" style="width:100%; height:80px; padding:10px;" placeholder="Fiyat notu...">0-3 yaş çocuklar için araçta ayrı koltuk istenmesi halinde TL ilave edilir.</textarea>
                                    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
                                </div>
                                <div class="dynamic-item" style="border-left-color:#17a2b8;">
                                    <textarea name="price_notes[]" style="width:100%; height:80px; padding:10px;" placeholder="Fiyat notu...">0-6 yaş çocuklar için araçta ayrı koltuk istenmesi halinde TL ilave edilir.</textarea>
                                    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
                                </div>
                            </div>
                            <button type="button" onclick="addPriceNote()" class="btn-save" style="background:#17a2b8; margin-top:10px;">➕ Yeni Not Ekle</button>
                        </div>
                    </div>
                    
                    <!-- 3. GÖRSEL GALERİSİ -->
                    <div id="tab-gallery" class="tab-content">
                        <h4 style="color:#6f42c1;">📸 Görsel Galerisi</h4>
                        <div class="info-box">
                            <strong>📌 Bilgi:</strong> Birden fazla görsel seçmek için Ctrl tuşuna basılı tutun.
                        </div>
                        <div class="form-group">
                            <label>Galeri Görselleri</label>
                            <input type="file" name="gallery_images[]" accept="image/*" multiple id="galleryInput">
                        </div>
                        <div id="galleryPreview" class="gallery-preview"></div>
                        <button type="button" onclick="previewGallery()" class="btn-save" style="background:#6f42c1; margin-top:10px;">👁️ Seçilenleri Önizle</button>
                    </div>
                    
                    <!-- 4. KALKIŞ NOKTALARI -->
                    <div id="tab-pickup" class="tab-content">
                        <h4>🚌 Kalkış Noktaları</h4>
                        <div id="pickup-items">
                            <div class="dynamic-item">
                                <div class="pickup-row">
                                    <input type="text" name="pickup_location[]" class="pickup-location" placeholder="Kalkış noktası (Örn: Kızılay)">
                                    <input type="text" name="pickup_time[]" class="pickup-time" placeholder="Saat (Örn: 08:30)">
                                    <button type="button" class="pickup-remove" onclick="this.closest('.dynamic-item').remove()">✕</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="addPickup()" class="btn-save" style="background:#fd7e14;">➕ Yeni Nokta Ekle</button>
                    </div>
                    
                    <!-- 5. DAHİL OLANLAR -->
                    <div id="tab-included" class="tab-content">
                        <h4 style="color:#28a745;">✅ Tura Dahil Olanlar</h4>
                        <div id="included-items">
                            <div class="dynamic-item">
                                <input type="text" name="included_item[]" style="width:80%; padding:8px;" placeholder="Hizmet (Örn: Profesyonel rehber)">
                                <input type="text" name="included_icon[]" value="✅" style="width:60px; padding:8px;">
                                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
                            </div>
                        </div>
                        <button type="button" onclick="addIncluded()" class="btn-save" style="background:#28a745;">➕ Yeni Ekle</button>
                    </div>
                    
                    <!-- 6. DAHİL OLMAYANLAR -->
                    <div id="tab-excluded" class="tab-content">
                        <h4 style="color:#dc3545;">❌ Tura Dahil Olmayanlar</h4>
                        <div id="excluded-items">
                            <div class="dynamic-item">
                                <input type="text" name="excluded_item[]" style="width:80%; padding:8px;" placeholder="Hizmet (Örn: Kişisel harcamalar)">
                                <input type="text" name="excluded_icon[]" value="❌" style="width:60px; padding:8px;">
                                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
                            </div>
                        </div>
                        <button type="button" onclick="addExcluded()" class="btn-save" style="background:#dc3545;">➕ Yeni Ekle</button>
                    </div>
                    
                    <!-- 7. TUR PROGRAMI -->
                    <div id="tab-itinerary" class="tab-content">
                        <h4>🗓️ Günlük Program</h4>
                        <div id="itinerary-items">
                            <div class="dynamic-item">
                                <div style="display:flex; gap:10px; margin-bottom:10px;">
                                    <input type="number" name="itinerary_day[]" value="1" style="width:80px; padding:8px;" placeholder="Gün">
                                    <input type="text" name="itinerary_title[]" style="flex:1; padding:8px;" placeholder="Gün başlığı">
                                </div>
                                <textarea name="itinerary_desc[]" style="width:100%; height:100px; padding:8px; margin-bottom:10px;" placeholder="Günün detaylı açıklaması"></textarea>
                                <div style="display:flex; gap:10px;">
                                    <input type="text" name="itinerary_accommodation[]" style="flex:1; padding:8px;" placeholder="Konaklama">
                                    <input type="text" name="itinerary_meals[]" style="flex:1; padding:8px;" placeholder="Öğünler">
                                </div>
                                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
                            </div>
                        </div>
                        <button type="button" onclick="addItinerary()" class="btn-save" style="background:#6610f2;">➕ Yeni Gün Ekle</button>
                    </div>
                    
                    <!-- 8. ÖNEMLİ BİLGİLER -->
                    <div id="tab-info" class="tab-content">
                        <h4 style="color:#17a2b8;">ℹ️ Önemli Bilgiler</h4>
                        <div id="info-items">
                            <div class="dynamic-item">
                                <input type="text" name="info_title[]" style="width:100%; margin-bottom:10px; padding:8px;" placeholder="Başlık">
                                <textarea name="info_content[]" style="width:100%; height:80px; padding:8px;" placeholder="İçerik"></textarea>
                                <input type="text" name="info_icon[]" value="ℹ️" style="width:60px; margin-top:10px; padding:8px;">
                                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
                            </div>
                        </div>
                        <button type="button" onclick="addInfo()" class="btn-save" style="background:#17a2b8;">➕ Yeni Ekle</button>
                    </div>
                    
                    <!-- 9. EKSTRALAR -->
                    <div id="tab-extras" class="tab-content">
                        <h4>➕ Ekstra Hizmetler</h4>
                        <div id="extra-items">
                            <div class="dynamic-item">
                                <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:10px;">
                                    <input type="text" name="extra_name[]" placeholder="Hizmet adı">
                                    <input type="text" name="extra_desc[]" placeholder="Açıklama">
                                    <input type="number" name="extra_price[]" placeholder="Fiyat">
                                    <select name="extra_currency[]"><option value="TL">TL</option><option value="EUR">EUR</option></select>
                                    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="addExtra()" class="btn-save" style="background:#ffc107; color:#333;">➕ Yeni Ekle</button>
                    </div>
                    
                    <!-- 10. NE GETİRMELİ? -->
                    <div id="tab-whattobring" class="tab-content">
                        <h4>🎒 Yanınızda Bulundurmanız Gerekenler</h4>
                        <div class="form-group">
                            <textarea name="what_to_bring" rows="10" style="width:100%; padding:10px;" placeholder="Getirilmesi gereken malzemeler...">Uygun bir sırt çantası, kalın çorapla giyilmiş iyi bir yürüyüş ayakkabısı, yağmurluk, hava koşullarına uygun koruyucu aksesuar, şapka, kol ve bacakları kaptan giysiler, yedek çorap, çamaşır ve giysi, yedek ayakkabı, gece otelde kullanmak üzere kıyafet ve kişisel bakım ürünleri, havlu, mayo, terlik, fotoğraf makinası, kişisel ilaçlar, indirim kartlarınız, müze kartlarınız, sağlık karneniz, dudak nemlendirici, ani hava değişiklikleri için hırka, polar vb. ve denize girecekseniz mayo ve havlu almayı ihmal etmeyin...</textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-save" style="width:100%; font-size:18px; padding:15px; margin-top:30px;">💾 TURU KAYDET</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    function openTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tabName).classList.add('active');
        event.target.classList.add('active');
    }
    
    function addPriceNote() {
        let div = document.createElement('div');
        div.className = 'dynamic-item';
        div.style.borderLeftColor = '#17a2b8';
        div.innerHTML = '<textarea name="price_notes[]" style="width:100%; height:80px; padding:10px;" placeholder="Fiyat notu..."></textarea><button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>';
        document.getElementById('price-notes').appendChild(div);
    }
    
    function previewGallery() {
        let input = document.getElementById('galleryInput');
        let preview = document.getElementById('galleryPreview');
        preview.innerHTML = '';
        if (input.files) {
            for (let i = 0; i < input.files.length; i++) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let div = document.createElement('div');
                    div.className = 'gallery-item';
                    div.innerHTML = '<img src="' + e.target.result + '">';
                    preview.appendChild(div);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    }
    
    function addPickup() {
        let div = document.createElement('div');
        div.className = 'dynamic-item';
        div.innerHTML = '<div class="pickup-row"><input type="text" name="pickup_location[]" class="pickup-location" placeholder="Kalkış noktası"><input type="text" name="pickup_time[]" class="pickup-time" placeholder="Saat"><button type="button" class="pickup-remove" onclick="this.closest(\'.dynamic-item\').remove()">✕</button></div>';
        document.getElementById('pickup-items').appendChild(div);
    }
    
    function addIncluded() {
        let div = document.createElement('div');
        div.className = 'dynamic-item';
        div.innerHTML = '<input type="text" name="included_item[]" style="width:80%; padding:8px;" placeholder="Hizmet"><input type="text" name="included_icon[]" value="✅" style="width:60px; padding:8px;"><button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>';
        document.getElementById('included-items').appendChild(div);
    }
    
    function addExcluded() {
        let div = document.createElement('div');
        div.className = 'dynamic-item';
        div.innerHTML = '<input type="text" name="excluded_item[]" style="width:80%; padding:8px;" placeholder="Hizmet"><input type="text" name="excluded_icon[]" value="❌" style="width:60px; padding:8px;"><button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>';
        document.getElementById('excluded-items').appendChild(div);
    }
    
    function addItinerary() {
        let dayCount = document.getElementById('itinerary-items').children.length + 1;
        let div = document.createElement('div');
        div.className = 'dynamic-item';
        div.innerHTML = '<div style="display:flex; gap:10px; margin-bottom:10px;"><input type="number" name="itinerary_day[]" value="' + dayCount + '" style="width:80px; padding:8px;"><input type="text" name="itinerary_title[]" style="flex:1; padding:8px;" placeholder="Gün başlığı"></div><textarea name="itinerary_desc[]" style="width:100%; height:100px; padding:8px; margin-bottom:10px;" placeholder="Açıklama"></textarea><div style="display:flex; gap:10px;"><input type="text" name="itinerary_accommodation[]" style="flex:1; padding:8px;" placeholder="Konaklama"><input type="text" name="itinerary_meals[]" style="flex:1; padding:8px;" placeholder="Öğünler"></div><button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>';
        document.getElementById('itinerary-items').appendChild(div);
    }
    
    function addInfo() {
        let div = document.createElement('div');
        div.className = 'dynamic-item';
        div.innerHTML = '<input type="text" name="info_title[]" style="width:100%; margin-bottom:10px; padding:8px;" placeholder="Başlık"><textarea name="info_content[]" style="width:100%; height:80px; padding:8px;" placeholder="İçerik"></textarea><input type="text" name="info_icon[]" value="ℹ️" style="width:60px; margin-top:10px; padding:8px;"><button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>';
        document.getElementById('info-items').appendChild(div);
    }
    
    function addExtra() {
        let div = document.createElement('div');
        div.className = 'dynamic-item';
        div.innerHTML = '<div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:10px;"><input type="text" name="extra_name[]" placeholder="Hizmet adı"><input type="text" name="extra_desc[]" placeholder="Açıklama"><input type="number" name="extra_price[]" placeholder="Fiyat"><select name="extra_currency[]"><option value="TL">TL</option><option value="EUR">EUR</option></select><button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button></div>';
        document.getElementById('extra-items').appendChild(div);
    }
    </script>
</body>
</html>