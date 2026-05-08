<?php
include "config.php";

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

// Aktif kategori
$active_category = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Turları çek
if($active_category && array_key_exists($active_category, $categories)) {
    $tours = $conn->query("SELECT * FROM tours WHERE status='active' AND category='$active_category' ORDER BY tour_date ASC");
} else {
    $tours = $conn->query("SELECT * FROM tours WHERE status='active' ORDER BY tour_date ASC LIMIT " . getSetting('tours_per_page', '12'));
}

// Öne çıkan turlar
$featured_tours = $conn->query("SELECT * FROM tours WHERE status='active' ORDER BY created_at DESC LIMIT 3");

// Kategori sayıları
$category_counts = [];
foreach(array_keys($categories) as $cat) {
    $result = $conn->query("SELECT COUNT(*) as count FROM tours WHERE status='active' AND category='$cat'");
    $count = $result->fetch_assoc();
    $category_counts[$cat] = $count['count'];
}

// Hero için tur resimleri
$tours_data = [];
$bg_result = $conn->query("SELECT id, title, image FROM tours WHERE status='active' AND image != '' ORDER BY RAND()");
while($bg_row = $bg_result->fetch_assoc()) {
    $tours_data[] = [
        'id' => $bg_row['id'],
        'title' => $bg_row['title'],
        'image' => 'uploads/' . $bg_row['image']
    ];
}
?>
<!DOCTYPE html>
<html lang="<?php echo getSetting('site_language', 'tr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getSetting('site_title'); ?></title>
    <meta name="description" content="<?php echo getSetting('site_description'); ?>">
    <meta name="keywords" content="<?php echo getSetting('site_keywords'); ?>">
    <meta name="author" content="<?php echo getSetting('meta_author'); ?>">
    
    <meta property="og:title" content="<?php echo getSetting('og_title'); ?>">
    <meta property="og:description" content="<?php echo getSetting('og_description'); ?>">
    
    <link rel="icon" type="image/png" href="uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
    <link rel="shortcut icon" type="image/png" href="uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="uploads/<?php echo getSetting('site_favicon', 'favicon.ico'); ?>">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        header {
            background: <?php echo getSetting('secondary_color', '#0f172a'); ?>;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
        }

        .btn-admin {
            background: <?php echo getSetting('admin_button_color', '#ff7b00'); ?>;
            color: white;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-left: auto;
            letter-spacing: 0.5px;
        }

        .btn-admin:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Kategori Navigasyonu */
        .category-nav {
            background: #f8f9fa;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 70px;
            z-index: 999;
        }

        .category-menu {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .category-item {
            position: relative;
        }

        .category-link {
            display: inline-block;
            padding: 8px 20px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            border-radius: 30px;
            transition: all 0.3s;
        }

        .category-link:hover,
        .category-link.active {
            background: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            color: white;
        }

        .category-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            color: white;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 20px;
            font-weight: bold;
        }

        /* Hero */
        .hero {
            background: linear-gradient(rgba(0,0,0,<?php echo getSetting('hero_overlay_opacity', '0.5'); ?>), rgba(0,0,0,<?php echo getSetting('hero_overlay_opacity', '0.7'); ?>)),
                        url('<?php echo !empty(getSetting('hero_background_image')) ? 'uploads/'.getSetting('hero_background_image') : 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800'; ?>') center/cover no-repeat;
            transition: background-image 0.5s ease;
            cursor: pointer;
            height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .hero-tur-buton {
            display: inline-block;
            background: <?php echo getSetting('hero_button_color', '#ff7b00'); ?>;
            color: <?php echo getSetting('hero_button_text_color', '#ffffff'); ?>;
            padding: 15px 50px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 20px;
            letter-spacing: 1px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .hero-tur-buton:hover {
            background: transparent;
            color: white;
            border: 2px solid white;
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }

        .hero-tour-title {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 10px;
            color: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        /* Turlar */
        .section-title {
            text-align: center;
            margin: 60px 0 30px;
            font-size: 36px;
            color: <?php echo getSetting('secondary_color', '#0f172a'); ?>;
            position: relative;
        }

        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            margin: 15px auto 0;
            border-radius: 2px;
        }

        .tour-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .tour-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: relative;
        }

        .tour-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .tour-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .tour-card:hover img {
            transform: scale(1.05);
        }

        .featured-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: bold;
            z-index: 2;
        }

        .tour-content {
            padding: 25px;
        }

        .tour-content h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: <?php echo getSetting('secondary_color', '#0f172a'); ?>;
        }

        .tour-dates {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            font-size: 13px;
        }

        .tour-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .tour-category {
            font-size: 12px;
            background: #f0f0f0;
            padding: 4px 12px;
            border-radius: 20px;
            color: #666;
        }

        .price {
            font-size: 24px;
            font-weight: 700;
            color: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
        }

        .btn-detail {
            display: inline-block;
            background: transparent;
            border: 2px solid <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            color: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            padding: 10px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 15px;
        }

        .btn-detail:hover {
            background: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            color: white;
        }

        /* Özellikler */
        .features {
            background: #f9f9f9;
            padding: 80px 0;
            margin: 60px 0;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            text-align: center;
        }

        .feature-item h3 {
            color: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            margin-bottom: 15px;
        }

        /* İletişim */
        .contact-section {
            padding: 60px 0;
            background: #f8f9fa;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .contact-info {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .contact-info h2 {
            color: <?php echo getSetting('secondary_color', '#0f172a'); ?>;
            margin-bottom: 30px;
            border-bottom: 2px solid <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            padding-bottom: 10px;
        }

        .contact-info p {
            margin: 20px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .contact-info i {
            font-size: 24px;
            color: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            width: 30px;
        }

        .contact-info .phone {
            font-size: 24px;
            font-weight: bold;
            color: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
        }

        .map-container {
            width: 100%;
            min-height: 400px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* SOSYAL MEDYA BÖLÜMÜ */
        .social-media-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            text-align: center;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .social-icon {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 20px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            min-width: 120px;
        }

        .social-icon:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .social-icon img {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }

        .social-icon span {
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .social-icon.instagram:hover { background: #f09433; }
        .social-icon.instagram:hover span { color: white; }
        .social-icon.facebook:hover { background: #1877f2; }
        .social-icon.facebook:hover span { color: white; }
        .social-icon.twitter:hover { background: #1da1f2; }
        .social-icon.twitter:hover span { color: white; }
        .social-icon.youtube:hover { background: #ff0000; }
        .social-icon.youtube:hover span { color: white; }

        /* Footer */
        footer {
            background: <?php echo getSetting('secondary_color', '#0f172a'); ?>;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }

        .footer-section h4 {
            color: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
            margin-bottom: 20px;
        }

        .footer-section p, .footer-section a {
            color: #aaa;
            text-decoration: none;
            margin-bottom: 10px;
            display: block;
        }

        .footer-section a:hover {
            color: <?php echo getSetting('theme_color', '#ff7b00'); ?>;
        }

        .copyright {
            text-align: center;
            padding-top: 40px;
            margin-top: 40px;
            border-top: 1px solid #333;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 { font-size: 36px; }
            .nav { flex-direction: column; gap: 15px; }
            .contact-container { grid-template-columns: 1fr; }
            .map-container { min-height: 300px; }
            .social-icons { gap: 15px; }
            .social-icon { min-width: 100px; padding: 15px; }
            .social-icon img { width: 40px; height: 40px; }
        }
    </style>
    
    <?php if(getSetting('custom_head_code')): ?>
        <?php echo getSetting('custom_head_code'); ?>
    <?php endif; ?>
    
    <?php if(getSetting('custom_css')): ?>
    <style>
        <?php echo getSetting('custom_css'); ?>
    </style>
    <?php endif; ?>
    
    <?php if(getSetting('google_analytics')): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo getSetting('google_analytics'); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo getSetting('google_analytics'); ?>');
    </script>
    <?php endif; ?>
</head>
<body>

<header>
    <div class="container nav">
        <div class="logo">
            <img src="uploads/<?php echo getSetting('site_logo', 'artiyasamlogo2.png'); ?>" alt="<?php echo getSetting('site_title'); ?>" style="height: 50px;">
        </div>
        <nav>
            <a href="index.php">Anasayfa</a>
            <a href="#turlar">Turlar</a>
            <a href="#hakkimizda">Hakkımızda</a>
            <a href="#iletisim">İletişim</a>
        </nav>
        <a href="admin/login.php" class="btn-admin" style="font-size: <?php echo getSetting('admin_button_size', 'small') == 'small' ? '14px' : (getSetting('admin_button_size', 'small') == 'medium' ? '16px' : '18px'); ?>; padding: <?php echo getSetting('admin_button_size', 'small') == 'small' ? '6px 16px' : (getSetting('admin_button_size', 'small') == 'medium' ? '8px 20px' : '10px 24px'); ?>;"><?php echo getSetting('admin_button_text', '🔐 ADMIN'); ?></a>
    </div>
</header>

<?php if(getSetting('show_category_counts') == 1): ?>
<div class="category-nav">
    <div class="container">
        <div class="category-menu">
            <a href="index.php" class="category-link <?php echo !$active_category ? 'active' : ''; ?>">Tüm Turlar</a>
            <?php foreach($categories as $key => $label): ?>
                <?php if($category_counts[$key] > 0): ?>
                <div class="category-item">
                    <a href="?kategori=<?php echo $key; ?>" class="category-link <?php echo ($active_category == $key) ? 'active' : ''; ?>">
                        <?php echo $cat_icons[$key] . ' ' . $label; ?>
                    </a>
                    <span class="category-count"><?php echo $category_counts[$key]; ?></span>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if(!$active_category): ?>
<section class="hero" id="hero">
    <div class="hero-content">
        <h1 id="hero-main-title"><?php echo getSetting('hero_default_title', '%100 Size Özel Butik Turlar'); ?></h1>
        <div id="hero-tour-info" style="display: none;">
            <div class="hero-tour-title" id="current-tour-title"></div>
            <p>Hemen inceleyin, unutulmaz bir deneyim sizi bekliyor!</p>
        </div>
        <p><?php echo getSetting('hero_subtitle', 'Ankara çıkışlı yurtiçi, yurtdışı ve günübirlik turlar'); ?></p>
        <a href="#turlar" class="hero-tur-buton"><?php echo getSetting('hero_button_text', 'Turları İncele'); ?></a>
    </div>
</section>

<?php if(getSetting('show_featured_tours') == 1 && $featured_tours->num_rows > 0): ?>
<section class="tours container">
    <h2 class="section-title">Öne Çıkan Turlar</h2>
    <div class="tour-grid">
        <?php while($row = $featured_tours->fetch_assoc()): ?>
        <div class="tour-card">
            <div class="featured-badge">⭐ Öne Çıkan</div>
            <img src="uploads/<?php echo $row['image']; ?>" onerror="this.src='https://images.unsplash.com/photo-1506905925346-21bda4d32df4'">
            <div class="tour-content">
                <h3><?php echo $row['title']; ?></h3>
                <p><?php echo substr(strip_tags($row['description']),0,100); ?>...</p>
                <div class="tour-dates">
                    <span>📅 Kalkış: <?php echo date('d.m.Y', strtotime($row['departure_date'])); ?></span>
                    <span>🏁 Dönüş: <?php echo date('d.m.Y', strtotime($row['tour_date'])); ?></span>
                </div>
                <div class="tour-footer">
                    <span class="tour-category"><?php echo $cat_icons[$row['category']] . ' ' . $categories[$row['category']]; ?></span>
                    <span class="price">₺<?php echo number_format($row['price'],0,',','.'); ?></span>
                </div>
                <a href="tour-detay.php?id=<?php echo $row['id']; ?>" class="btn-detail" style="width: 100%; text-align: center;">Detaylı Bilgi</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>

<section class="tours container" id="turlar">
    <h2 class="section-title">
        <?php echo $active_category ? $categories[$active_category] : 'Tüm Turlarımız'; ?>
    </h2>
    
    <div class="tour-grid">
        <?php if($tours->num_rows > 0): ?>
            <?php while($row = $tours->fetch_assoc()): ?>
            <div class="tour-card">
                <img src="uploads/<?php echo $row['image']; ?>" onerror="this.src='https://images.unsplash.com/photo-1506905925346-21bda4d32df4'">
                <div class="tour-content">
                    <h3><?php echo $row['title']; ?></h3>
                    <p><?php echo substr(strip_tags($row['description']),0,120); ?>...</p>
                    <div class="tour-dates">
                        <span>🚌 Kalkış: <?php echo date('d.m.Y', strtotime($row['departure_date'])); ?></span>
                        <span>🎯 Dönüş: <?php echo date('d.m.Y', strtotime($row['tour_date'])); ?></span>
                    </div>
                    <div class="tour-footer">
                        <span class="tour-category"><?php echo $cat_icons[$row['category']] . ' ' . $categories[$row['category']]; ?></span>
                        <div class="price">₺<?php echo number_format($row['price'],0,',','.'); ?></div>
                    </div>
                    <a href="tour-detay.php?id=<?php echo $row['id']; ?>" class="btn-detail" style="width: 100%; text-align: center;">Detaylı Bilgi</a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>Bu kategoride henüz tur bulunmuyor.</p>
                <a href="index.php" style="color: <?php echo getSetting('theme_color', '#ff7b00'); ?>; text-decoration: none; font-weight: 600;">Diğer kategorilere göz atın →</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="features" id="hakkimizda">
    <div class="container">
        <h2 class="section-title">Neden Artıyaşam?</h2>
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">🧠</div>
                <h3>Kafamız yeniliklere çalışır</h3>
                <p>Siz ne istiyorsanız bizde onu istiyoruz. Yeni rotalar, yeni destinasyonlar.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">💰</div>
                <h3>Ücretlerimiz uygundur</h3>
                <p>Fiyatları abartmayız. Standart kar marjımız var. Herkesin katılabileceği etkinlikler.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">🎯</div>
                <h3>%95'i bize özel</h3>
                <p>Kendi rotalarımızı yazıyoruz. Gidilmeyen rotalarda farkındalığı yaşayın.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">👨‍👩‍👧‍👦</div>
                <h3>Çocuklarımızı önemsiyoruz</h3>
                <p>Baba-Çocuk ve Anne-Çocuk kamplarımız var. Haydi çocuklar doğaya!</p>
            </div>
        </div>
    </div>
</section>

<section class="contact-section" id="iletisim">
    <div class="contact-container">
        <div class="contact-info">
            <h2>📞 Bize Ulaşın</h2>
            <p><i>📍</i> <span><?php echo getSetting('contact_address'); ?></span></p>
            <p><i>📞</i> <span class="phone"><?php echo getSetting('contact_phone'); ?></span></p>
            <?php if(getSetting('contact_phone2')): ?>
            <p><i>📱</i> <span><?php echo getSetting('contact_phone2'); ?></span></p>
            <?php endif; ?>
            <p><i>✉️</i> <span><?php echo getSetting('contact_email'); ?></span></p>
            <p><i>🕒</i> <span><?php echo getSetting('working_hours'); ?></span></p>
        </div>
        <div class="map-container">
            <?php echo getSetting('contact_map'); ?>
        </div>
    </div>
</section>

<!-- SOSYAL MEDYA BÖLÜMÜ -->
<section class="social-media-section">
    <div class="container">
        <h2 class="section-title">Bizi Takip Edin</h2>
        <div class="social-icons">
            <a href="https://instagram.com/artiyasam" target="_blank" class="social-icon instagram">
                <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram">
                <span>Instagram</span>
            </a>
            
            <a href="https://facebook.com/artiyasam" target="_blank" class="social-icon facebook">
                <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook">
                <span>Facebook</span>
            </a>
            
            <a href="https://twitter.com/artiyasam" target="_blank" class="social-icon twitter">
                <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter">
                <span>Twitter</span>
            </a>
            
            <a href="https://youtube.com/artiyasam" target="_blank" class="social-icon youtube">
                <img src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" alt="YouTube">
                <span>YouTube</span>
            </a>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Artıyaşam Turizm</h4>
                <p><?php echo getSetting('footer_about'); ?></p>
            </div>
            <div class="footer-section">
                <h4>Hızlı Bağlantılar</h4>
                <a href="index.php">Anasayfa</a>
                <a href="#turlar">Turlar</a>
                <a href="#hakkimizda">Hakkımızda</a>
                <a href="#iletisim">İletişim</a>
            </div>
            <div class="footer-section">
                <h4>Bülten</h4>
                <p><?php echo getSetting('footer_newsletter_text'); ?></p>
            </div>
        </div>
        <div class="copyright">
            <p><?php echo getSetting('footer_copyright'); ?></p>
            <p><a href="admin/login.php" style="color: <?php echo getSetting('theme_color', '#ff7b00'); ?>;">Admin Giriş</a></p>
        </div>
    </div>
</footer>

<script>
const tours = <?php echo json_encode($tours_data); ?>;

if(document.querySelector('.hero')) {
    const heroSection = document.querySelector('.hero');
    const heroMainTitle = document.getElementById('hero-main-title');
    const heroTourInfo = document.getElementById('hero-tour-info');
    const currentTourTitle = document.getElementById('current-tour-title');
    const currentTourLink = document.getElementById('current-tour-link');
    
    if(tours.length > 0) {
        heroSection.style.backgroundImage = `linear-gradient(rgba(0,0,0,<?php echo getSetting('hero_overlay_opacity', '0.5'); ?>), rgba(0,0,0,<?php echo getSetting('hero_overlay_opacity', '0.7'); ?>)), url('${tours[0].image}')`;
        heroMainTitle.style.display = 'none';
        heroTourInfo.style.display = 'block';
        currentTourTitle.textContent = tours[0].title;
        currentTourLink.href = tours[0].id > 0 ? 'tour-detay.php?id=' + tours[0].id : '#';
    }
    
    let currentIndex = 0;
    const autoChange = <?php echo getSetting('hero_auto_change', '1'); ?>;
    const interval = <?php echo (int)getSetting('hero_change_interval', '10'); ?> * 1000;
    
    function changeHero() {
        if(tours.length <= 1) return;
        currentIndex = (currentIndex + 1) % tours.length;
        heroSection.style.backgroundImage = `linear-gradient(rgba(0,0,0,<?php echo getSetting('hero_overlay_opacity', '0.5'); ?>), rgba(0,0,0,<?php echo getSetting('hero_overlay_opacity', '0.7'); ?>)), url('${tours[currentIndex].image}')`;
        currentTourTitle.textContent = tours[currentIndex].title;
        currentTourLink.href = tours[currentIndex].id > 0 ? 'tour-detay.php?id=' + tours[currentIndex].id : '#';
    }
    
    heroSection.addEventListener('click', function(e) {
        if(e.target.tagName !== 'A') changeHero();
    });
    
    if(autoChange && tours.length > 1) setInterval(changeHero, interval);
}
</script>

<?php if(getSetting('custom_body_code')): ?>
    <?php echo getSetting('custom_body_code'); ?>
<?php endif; ?>

</body>
</html>