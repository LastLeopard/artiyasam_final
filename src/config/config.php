<?php
$conn = new mysqli("localhost", "root", "", "artiyasam_db");

if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası");
}

// Türkçe karakter desteği
$conn->set_charset("utf8mb4");

/**
 * Site ayarlarını veritabanından çeker
 * @param string $key Ayar anahtarı
 * @param string $default Varsayılan değer
 * @return string
 */
function getSetting($key, $default = '') {
    global $conn;
    static $settings_cache = [];
    
    if (isset($settings_cache[$key])) {
        return $settings_cache[$key];
    }
    
    $key_escaped = mysqli_real_escape_string($conn, $key);
    $result = $conn->query("SELECT setting_value FROM site_settings WHERE setting_key = '$key_escaped'");
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $settings_cache[$key] = $row['setting_value'];
        return $row['setting_value'];
    } else {
        $settings_cache[$key] = $default;
        return $default;
    }
}
?>