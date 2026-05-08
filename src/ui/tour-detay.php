<?php
include "config.php";

// ID kontrolü
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id == 0) {
    header("Location: index.php");
    exit();
}

// Tur bilgilerini çek
$result = $conn->query("SELECT * FROM tours WHERE id = $id AND status='active'");

if($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$tour = $result->fetch_assoc();

// Tur detaylarını çek
$details = $conn->query("SELECT * FROM tour_details WHERE tour_id = $id")->fetch_assoc();

// Dahil olanları çek
$included = $conn->query("SELECT * FROM tour_included WHERE tour_id = $id");

// Dahil olmayanları çek
$excluded = $conn->query("SELECT * FROM tour_excluded WHERE tour_id = $id");

// Ekstraları çek
$extras = $conn->query("SELECT * FROM tour_extras WHERE tour_id = $id");

// Önemli bilgileri çek
$important_info = $conn->query("SELECT * FROM tour_important_info WHERE tour_id = $id");

// Günlük programı çek
$itinerary = $conn->query("SELECT * FROM tour_itinerary WHERE tour_id = $id ORDER BY day_number");

// Takvimi çek
$calendar = $conn->query("SELECT * FROM tour_calendar WHERE tour_id = $id ORDER BY start_date");

// Fiyat bilgilerini çek
$prices = $conn->query("SELECT * FROM tour_prices WHERE tour_id = $id")->fetch_assoc();

// Kalkış noktalarını çek
$pickup_locations = $conn->query("SELECT * FROM tour_pickup_locations WHERE tour_id = $id ORDER BY display_order");

// Görselleri çek
$images = $conn->query("SELECT * FROM tour_images WHERE tour_id = $id ORDER BY is_cover DESC");

// Kapak görselini bul
$cover_image = $tour['image'];
if($images && $images->num_rows > 0) {
    while($img = $images->fetch_assoc()) {
        if($img['is_cover'] == 1) {
            $cover_image = $img['image_path'];
            break;
        }
    }
    $images->data_seek(0);
}

// Kategoriler
$categories = [
    'yurtici' => 'Yurtiçi Turlar',
    'yurtdisi' => 'Yurtdışı Turlar',
    'gunubirlik' => 'Günübirlik Turlar',
    'ozel' => 'Özel Butik Turlar',
    'festival' => 'Festival Turları'
];

$cat_icons = [
    'yurtici' => '🏠',
    'yurtdisi' => '✈️',
    'gunubirlik' => '🌄',
    'ozel' => '🌟',
    'festival' => '🎪'
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tour['title']; ?> - Artıyaşam Turizm</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
<link rel="shortcut icon" type="image/png" href="uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
<link rel="apple-touch-icon" href="uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        
        /* Header */
        header {
            background: #0f172a;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo img { height: 50px; }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
        }
        .btn-admin {
            color: white;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-left: auto;
            letter-spacing: 0.5px;
        }
        
        /* Tur Detay */
        .tour-detail { padding: 40px 0; }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #666;
            text-decoration: none;
        }
        .back-link:hover { color: #ff7b00; }
        
        /* Üst Grid */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .main-image {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
        }
        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .thumbnail-gallery {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            overflow-x: auto;
            padding-bottom: 5px;
        }
        .thumbnail {
            width: 80px;
            height: 60px;
            border-radius: 5px;
            overflow: hidden;
            cursor: pointer;
            opacity: 0.7;
            transition: 0.3s;
            flex-shrink: 0;
        }
        .thumbnail:hover { opacity: 1; }
        .thumbnail img { width: 100%; height: 100%; object-fit: cover; }
        
        .detail-info h1 {
            font-size: 32px;
            color: #0f172a;
            margin-bottom: 20px;
        }
        
        .detail-meta {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .detail-meta p {
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-price {
            font-size: 36px;
            color: #ff7b00;
            font-weight: bold;
            margin: 20px 0;
        }
        
        /* Bölüm başlıkları */
        .section-box {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }
        
        .section-title {
            color: #0f172a;
            margin-bottom: 20px;
            font-size: 28px;
            border-bottom: 2px solid #ff7b00;
            padding-bottom: 10px;
        }
        
        /* Listeler */
        .list-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .list-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .included-item { border-left: 4px solid #28a745; }
        .excluded-item { border-left: 4px solid #dc3545; }
        .extra-item { border-left: 4px solid #ffc107; }
        
        /* Günlük Program */
        .day-card {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 4px solid #ff7b00;
        }
        .day-card h4 {
            color: #0f172a;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .day-card p { line-height: 1.6; }
        
        /* Kalkış noktaları tablosu */
        .pickup-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .pickup-table th {
            background: #0f172a;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .pickup-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .pickup-table tr:last-child td { border-bottom: none; }
        
        /* Fiyat Tablosu */
        .price-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .price-table th {
            background: #ff7b00;
            color: white;
            padding: 12px;
            text-align: center;
        }
        .price-table td {
            padding: 12px;
            border: 1px solid #eee;
            text-align: center;
        }
        
        /* Önemli bilgiler */
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-card h4 {
            color: #ff7b00;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        /* Ne getirmeli */
        .what-to-bring {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            line-height: 1.8;
            white-space: pre-line;
        }
        
        /* Rezervasyon */
        .reservation-box {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            margin-top: 40px;
        }
        .btn-reserve {
            background: #ff7b00;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            font-weight: 600;
        }
        .btn-reserve:hover {
            background: #e66a00;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,123,0,0.3);
        }
        
        @media (max-width: 768px) {
            .detail-grid { grid-template-columns: 1fr; }
            .main-image { height: 300px; }
            .section-title { font-size: 24px; }
        }
        /* SOSYAL MEDYA PAYLAŞIM KUTUSU */
.social-share-box {
    background: white;
    border-radius: 15px;
    padding: 40px 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 40px;
    text-align: center;
    border: 1px solid #eee;
}

.social-share-box h3 {
    color: #0f172a;
    margin-bottom: 15px;
    font-size: 26px;
    font-weight: 600;
}

.social-share-box h3:after {
    content: '';
    display: block;
    width: 60px;
    height: 3px;
    background: #ff7b00;
    margin: 15px auto 0;
    border-radius: 2px;
}

.share-icons {
    display: flex;
    justify-content: center;
    gap: 25px;
    flex-wrap: wrap;
    margin: 30px 0 20px;
}

.share-icon {
    display: inline-block;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    text-decoration: none;
    position: relative;
}

.share-icon img {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 3px solid transparent;
}

.share-icon:hover {
    transform: translateY(-10px) scale(1.1);
}

.share-icon:hover img {
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.share-icon.facebook:hover img { border-color: #1877f2; }
.share-icon.twitter:hover img { border-color: #1da1f2; }
.share-icon.whatsapp:hover img { border-color: #25D366; }
.share-icon.instagram:hover img { border-color: #e1306c; }

.share-icon[title]:hover:after {
    content: attr(title);
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 10;
}

@media (max-width: 768px) {
    .social-share-box { padding: 30px 20px; }
    .social-share-box h3 { font-size: 22px; }
    .share-icons { gap: 15px; }
    .share-icon img { width: 55px; height: 55px; }
}
    </style>
</head>
<body>

<header>
    <div class="container nav">
        <div class="logo">
            <img src="uploads/<?php echo getSetting('site_logo', 'artiyasamlogo2.png'); ?>" alt="Artıyaşam">
        </div>
        <nav>
            <a href="index.php">Anasayfa</a>
            <a href="index.php#turlar">Turlar</a>
            <a href="index.php#hakkimizda">Hakkımızda</a>
            <a href="index.php#iletisim">İletişim</a>
        </nav>
        <a href="admin/login.php" class="btn-admin" style="background: <?php echo getSetting('admin_button_color', '#ff7b00'); ?>; font-size: <?php echo getSetting('admin_button_size', 'small') == 'small' ? '14px' : (getSetting('admin_button_size', 'small') == 'medium' ? '16px' : '18px'); ?>; padding: <?php echo getSetting('admin_button_size', 'small') == 'small' ? '6px 16px' : (getSetting('admin_button_size', 'small') == 'medium' ? '8px 20px' : '10px 24px'); ?>;"><?php echo getSetting('admin_button_text', '🔐 ADMIN'); ?></a>
    </div>
</header>

<div class="container tour-detail">
    <a href="javascript:history.back()" class="back-link">← Geri Dön</a>
    
    <!-- 1. ÜST BİLGİLER -->
    <div class="detail-grid">
        <div class="detail-image">
            <div class="main-image">
                <img id="mainImage" src="uploads/<?php echo $cover_image; ?>" alt="<?php echo $tour['title']; ?>">
            </div>
            
            <?php if($images && $images->num_rows > 1): ?>
            <div class="thumbnail-gallery">
                <?php while($img = $images->fetch_assoc()): ?>
                <div class="thumbnail" onclick="document.getElementById('mainImage').src='uploads/<?php echo $img['image_path']; ?>'">
                    <img src="uploads/<?php echo $img['image_path']; ?>" alt="">
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="detail-info">
            <h1><?php echo $tour['title']; ?></h1>
            
            <div class="detail-meta">
                <p><strong>📌 Kategori:</strong> <?php echo $cat_icons[$tour['category']] . ' ' . $categories[$tour['category']]; ?></p>
                <?php if($details['duration']): ?><p><strong>⏱️ Süre:</strong> <?php echo $details['duration']; ?></p><?php endif; ?>
                <p><strong>🚌 Kalkış:</strong> <?php echo date('d.m.Y', strtotime($tour['departure_date'])); ?></p>
                <p><strong>🏁 Dönüş:</strong> <?php echo date('d.m.Y', strtotime($tour['tour_date'])); ?></p>
                <?php if($details['transportation']): ?><p><strong>🚍 Ulaşım:</strong> <?php echo $details['transportation']; ?></p><?php endif; ?>
                <?php if($details['accommodation']): ?><p><strong>🏨 Konaklama:</strong> <?php echo $details['accommodation']; ?></p><?php endif; ?>
                <?php if($details['meals']): ?><p><strong>🍽️ Öğünler:</strong> <?php echo $details['meals']; ?></p><?php endif; ?>
                <?php if($details['guide']): ?><p><strong>👤 Rehber:</strong> <?php echo $details['guide']; ?></p><?php endif; ?>
            </div>
            
            <div class="detail-price">
                <?php 
                $tour_price = $tour['price'];
                if($tour_price > 0) {
                    echo '₺' . number_format($tour_price, 0, ',', '.');
                } elseif($calendar && $calendar->num_rows > 0) {
                    $prices_arr = [];
                    while($cal = $calendar->fetch_assoc()) {
                        if($cal['price_adult'] > 0) {
                            $prices_arr[] = $cal['price_adult'];
                        }
                    }
                    if(count($prices_arr) > 0) {
                        $min_price = min($prices_arr);
                        echo '₺' . number_format($min_price, 0, ',', '.');
                    } else {
                        echo 'Fiyat sorunuz';
                    }
                    $calendar->data_seek(0);
                } else {
                    echo 'Fiyat sorunuz';
                }
                ?>
                <small style="font-size: 16px; color: #666; font-weight: normal;"> kişi başı</small>
            </div>
        </div>
    </div>
    
    <!-- 2. TUR AÇIKLAMASI -->
    <?php if(!empty($tour['description'])): ?>
    <div class="section-box">
        <h2 class="section-title">Tur Açıklaması</h2>
        <div style="line-height: 1.8;">
            <?php echo nl2br($tour['description']); ?>
        </div>
    </div>
    <?php endif; ?>

<!-- FİYAT TABLOSU - SADECE DOLU OLANLAR -->
<?php
// Fiyatları çek
$prices_query = $conn->query("SELECT * FROM tour_pricing WHERE tour_id = $id ORDER BY FIELD(price_type, 
    'yetiskin', 'iki_kisilik', 'uc_kisilik', 'tek_kisilik', 'cocuk_7_12', 'cocuk_3_12', 'cocuk_0_6', 'cocuk_0_3')");
$price_notes_query = $conn->query("SELECT * FROM tour_price_notes WHERE tour_id = $id");

$price_labels = [
    'yetiskin' => 'Yetişkin',
    'iki_kisilik' => 'İki Kişilik Odada Kişi Başı',
    'uc_kisilik' => 'Üç Kişilik Odada Kişi Başı',
    'tek_kisilik' => 'Tek Kişilik Oda Farkı',
    'cocuk_7_12' => 'Çocuk 7-12 Yaş',
    'cocuk_3_12' => 'Çocuk 3-12 Yaş',
    'cocuk_0_6' => 'Çocuk 0-6 Yaş (Ücretsiz)',
    'cocuk_0_3' => 'Çocuk 0-3 Yaş (Ücretsiz)'
];

// Hiç fiyat var mı kontrol et (tablonun tamamen boş olup olmadığını anlamak için)
$has_any_price = false;
if($prices_query && $prices_query->num_rows > 0) {
    $prices_query->data_seek(0); // Sorguyu başa sar
    while($price = $prices_query->fetch_assoc()) {
        if($price['cash_price'] > 0 || $price['card_single'] > 0 || $price['card_installment'] > 0) {
            $has_any_price = true;
            break;
        }
    }
    $prices_query->data_seek(0); // Tekrar başa sar
}
?>

<?php if($prices_query && $prices_query->num_rows > 0 && $has_any_price): ?>
<div class="section-box" style="overflow-x: auto;">
    <h2 class="section-title">💰 Fiyat Tablosu</h2>
    
    <table style="width:100%; border-collapse:collapse; margin-top:20px;">
        <thead>
            <tr style="background:#ff7b00; color:white;">
                <th style="padding:12px; text-align:left;">Fiyat Tipi</th>
                <th style="padding:12px; text-align:center;">Nakit</th>
                <th style="padding:12px; text-align:center;">Kredi Kartı Tek Çekim</th>
                <th style="padding:12px; text-align:center;">Kredi Kartı Taksit</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $currency = 'TL';
            while($price = $prices_query->fetch_assoc()): 
                $currency = $price['currency'];
                // Sadece en az bir fiyatı dolu olan satırları göster
                if($price['cash_price'] > 0 || $price['card_single'] > 0 || $price['card_installment'] > 0):
            ?>
            <tr>
                <td style="padding:12px; border:1px solid #ddd; font-weight:500;"><?php echo $price_labels[$price['price_type']] ?? $price['price_type']; ?></td>
                <td style="padding:12px; border:1px solid #ddd; text-align:center;">
                    <?php 
                    if($price['cash_price'] > 0) {
                        echo number_format($price['cash_price'], 0, ',', '.') . ' ' . $currency;
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td style="padding:12px; border:1px solid #ddd; text-align:center;">
                    <?php 
                    if($price['card_single'] > 0) {
                        echo number_format($price['card_single'], 0, ',', '.') . ' ' . $currency;
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td style="padding:12px; border:1px solid #ddd; text-align:center;">
                    <?php 
                    if($price['card_installment'] > 0) {
                        echo number_format($price['card_installment'], 0, ',', '.') . ' ' . $currency;
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
            </tr>
            <?php endif; endwhile; ?>
        </tbody>
    </table>
    
    <!-- Fiyat Notları - Sadece not varsa göster -->
    <?php if($price_notes_query && $price_notes_query->num_rows > 0): ?>
    <div style="margin-top:30px; background:#f8f9fa; padding:20px; border-radius:8px;">
        <h4 style="color:#17a2b8; margin-bottom:15px;">📝 Fiyatlandırma Notları</h4>
        <ul style="list-style:none; padding:0;">
            <?php while($note = $price_notes_query->fetch_assoc()): ?>
            <?php if(!empty(trim($note['note_text']))): ?>
            <li style="padding:8px 0; border-bottom:1px dashed #ddd; display:flex; gap:10px;">
                <span style="color:#17a2b8; font-size:18px;">•</span> 
                <span><?php echo nl2br(htmlspecialchars($note['note_text'])); ?></span>
            </li>
            <?php endif; endwhile; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

    <!-- 3. KALKIŞ NOKTALARI -->
    <?php if($pickup_locations && $pickup_locations->num_rows > 0): ?>
    <div class="section-box">
        <h2 class="section-title">🚌 Sizi Nereden Alalım?</h2>
        
        <table class="pickup-table">
            <thead>
                <tr>
                    <th>Kalkış Noktası</th>
                    <th>Saat</th>
                </tr>
            </thead>
            <tbody>
                <?php while($pickup = $pickup_locations->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $pickup['location_name']; ?></td>
                    <td><strong><?php echo $pickup['departure_time']; ?></strong></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- 4. DAHİL OLANLAR -->
    <?php if($included && $included->num_rows > 0): ?>
    <div class="section-box">
        <h2 class="section-title">✅ Tura Dahil Olanlar</h2>
        
        <div class="list-grid">
            <?php while($item = $included->fetch_assoc()): ?>
            <div class="list-item included-item">
                <span style="font-size: 20px;"><?php echo $item['icon']; ?></span>
                <span><?php echo $item['item']; ?></span>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- 5. DAHİL OLMAYANLAR -->
    <?php if($excluded && $excluded->num_rows > 0): ?>
    <div class="section-box">
        <h2 class="section-title">❌ Tura Dahil Olmayanlar</h2>
        
        <div class="list-grid">
            <?php while($item = $excluded->fetch_assoc()): ?>
            <div class="list-item excluded-item">
                <span style="font-size: 20px;"><?php echo $item['icon']; ?></span>
                <span><?php echo $item['item']; ?></span>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- 6. TUR PROGRAMI -->
    <?php if($itinerary && $itinerary->num_rows > 0): ?>
    <div class="section-box">
        <h2 class="section-title">🗓️ Tur Programı</h2>
        
        <?php while($day = $itinerary->fetch_assoc()): ?>
        <div class="day-card">
            <h4><?php echo $day['day_number']; ?>. Gün: <?php echo $day['title']; ?></h4>
            <p><?php echo nl2br($day['description']); ?></p>
            <?php if($day['accommodation'] || $day['meals']): ?>
            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ddd;">
                <?php if($day['accommodation']): ?>
                <span style="background: #e9ecef; padding: 3px 10px; border-radius: 20px; margin-right: 10px;">🏨 <?php echo $day['accommodation']; ?></span>
                <?php endif; ?>
                <?php if($day['meals']): ?>
                <span style="background: #e9ecef; padding: 3px 10px; border-radius: 20px;">🍽️ <?php echo $day['meals']; ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
    
    <!-- 7. ÖNEMLİ BİLGİLER -->
    <?php if($important_info && $important_info->num_rows > 0): ?>
    <div class="section-box">
        <h2 class="section-title">ℹ️ Önemli Bilgiler</h2>
        
        <?php while($info = $important_info->fetch_assoc()): ?>
        <div class="info-card">
            <h4><?php echo $info['icon']; ?> <?php echo $info['title']; ?></h4>
            <p style="line-height: 1.6;"><?php echo nl2br($info['content']); ?></p>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
    
    <!-- 8. NE GETİRMELİ? -->
    <?php if(!empty($details['what_to_bring'])): ?>
    <div class="section-box">
        <h2 class="section-title">🎒 Yanınızda Bulundurmanız Gerekenler</h2>
        <div class="what-to-bring">
            <?php echo nl2br($details['what_to_bring']); ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- 9. REZERVASYON -->
    <div class="reservation-box">
        <h2 style="color: white; margin-bottom: 20px;">Bu Tura Katılmak İster misiniz?</h2>
        <p style="margin: 20px 0; font-size: 18px;">Hemen rezervasyon yapın, unutulmaz bir deneyim yaşayın!</p>
        <a href="https://wa.me/<?php echo getSetting('whatsapp_number', '905304898700'); ?>?text=<?php echo urlencode(getSetting('whatsapp_message', 'Merhaba, tur hakkında bilgi almak istiyorum.') . ' ' . $tour['title']); ?>" class="btn-reserve" target="_blank"><?php echo getSetting('whatsapp_button_text', '📱 WHATSAPP\'TAN YAZ'); ?></a>
        <p style="margin-top: 15px; opacity: 0.9;">veya <strong><?php echo getSetting('contact_phone', '0530 489 87 00'); ?></strong> numaralı telefonu arayın</p>
    </div>
    <!-- SOSYAL MEDYA PAYLAŞIM -->
<div class="social-share-box">
    <h3>📱 Bu Turu Paylaş</h3>
    <div class="share-icons">
        <?php 
        $current_url = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $tour_title = urlencode($tour['title'].' - Artıyaşam Turizm');
        ?>
        
        <!-- Facebook Paylaş -->
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $current_url; ?>" 
           target="_blank" 
           class="share-icon facebook"
           title="Facebook'ta Paylaş">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook">
        </a>
        
        <!-- Twitter Paylaş -->
        <a href="https://twitter.com/intent/tweet?text=<?php echo $tour_title; ?>&url=<?php echo $current_url; ?>" 
           target="_blank" 
           class="share-icon twitter"
           title="Twitter'da Paylaş">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter">
        </a>
        
        <!-- WhatsApp Paylaş -->
        <a href="https://wa.me/?text=<?php echo $tour_title.' - '.$current_url; ?>" 
           target="_blank" 
           class="share-icon whatsapp"
           title="WhatsApp'ta Paylaş">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp">
        </a>
        
        <!-- Instagram Profil -->
        <?php 
        $instagram_url = getSetting('instagram_url', 'https://instagram.com/artiyasam');
        $instagram_username = str_replace(['https://instagram.com/', 'https://www.instagram.com/', '/'], '', $instagram_url);
        ?>
        <a href="https://instagram.com/<?php echo $instagram_username; ?>" 
           target="_blank" 
           class="share-icon instagram"
           title="Instagram'da Takip Et">
            <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram">
        </a>
    </div>
    <p style="margin-top: 20px; color: #666; font-size: 14px;">
        👆 Arkadaşlarınla paylaş, onlar da katılsın!
    </p>
</div>
</div>

<footer style="background: #0f172a; color: white; padding: 40px 0; margin-top: 60px;">
    <div class="container">
        <div style="text-align: center;">
            <p><?php echo getSetting('footer_copyright', '© 2026 Artıyaşam Turizm | Tüm Hakları Saklıdır'); ?></p>
        </div>
    </div>
</footer>

</body>
</html>