<?php
/**
 * lop helper - cac ham ho tro cho ung dung
 * cung cap cac ham bao mat, kiem tra va dinh dang
 */
class Helper {
    
    /**
     * tao csrf token
     * @return string
     */
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * xac thuc csrf token
     * @param string $token
     * @return bool
     */
    public static function verifyCSRFToken($token) {
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * tao o input html cho csrf token
     * @return string
     */
    public static function csrfField() {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * lam sach chuoi dau vao
     * @param string $input
     * @return string
     */
    public static function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * kiem tra email hop le
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * kiem tra so dien thoai 10 chu so
     * @param string $phone
     * @return bool
     */
    public static function validatePhone($phone) {
        return preg_match('/^[0-9]{10}$/', $phone);
    }
    
    /**
     * dinh dang gia theo vnd
     * @param int|float $price
     * @return string
     */
    public static function formatVND($price) {
        return number_format($price, 0, ',', '.') . ' đ';
    }
    
    /**
     * kiem tra va lam sach ten file
     * @param string $filename
     * @return string
     */
    public static function sanitizeFilename($filename) {
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        return $filename;
    }
    
    /**
     * kiem tra file anh
     * @param array $file $_FILES array element
     * @param int $maxSize Maximum file size in bytes (default 5MB)
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validateImage($file, $maxSize = 5242880) {
        $result = ['valid' => false, 'error' => ''];
        
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $result['error'] = 'Vui lòng chọn hình ảnh';
            return $result;
        }
        
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $file['type'];
        
        // Kiem tra mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($fileType, $allowedTypes) || !in_array($mimeType, $allowedTypes)) {
            $result['error'] = 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WEBP)';
            return $result;
        }
        
        if ($file['size'] > $maxSize) {
            $result['error'] = 'Kích thước file không được vượt quá ' . ($maxSize / 1024 / 1024) . 'MB';
            return $result;
        }
        
        // Kiem tra day la file anh hop le
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $result['error'] = 'File không phải là hình ảnh hợp lệ';
            return $result;
        }
        
        $result['valid'] = true;
        return $result;
    }
    
    /**
     * chuyen huong kem thong bao session
     * @param string $url
     * @param string $message
     * @param string $type 'error' or 'msg'
     */
    public static function redirect($url, $message = '', $type = 'msg') {
        if ($message) {
            $_SESSION[$type] = $message;
        }
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * kiem tra nguoi dung da dang nhap
     * @return bool
     */
    public static function isLoggedIn() {
        return !empty($_SESSION['login']);
    }
    
    /**
     * kiem tra admin da dang nhap
     * @return bool
     */
    public static function isAdminLoggedIn() {
        return !empty($_SESSION['alogin']);
    }
    
    /**
     * yeu cau dang nhap nguoi dung
     * @param string $redirectUrl
     */
    public static function requireLogin($redirectUrl = '') {
        if (!self::isLoggedIn()) {
            $url = $redirectUrl ?: BASE_URL;
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục';
            header('Location: ' . $url);
            exit;
        }
    }
    
    /**
     * yeu cau dang nhap admin
     * @param string $redirectUrl
     */
    public static function requireAdminLogin($redirectUrl = 'index.php') {
        if (!self::isAdminLoggedIn()) {
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * kiem tra dinh dang va gia tri ngay
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * ma hoa output an toan cho html
     * @param string $string
     * @return string
     */
    public static function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * tao chuoi ngau nhien
     * @param int $length
     * @return string
     */
    public static function randomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * chuyen chu viet khong dau thanh co dau
     * dung cho du lieu mau cu hien thi tren giao dien
     * @param string $text
     * @return string
     */
    public static function vi($text) {
        $value = trim((string)$text);
        if ($value === '') {
            return $value;
        }

        static $map = [
            'kham pha' => 'Khám phá',
            'nghi duong' => 'Nghỉ dưỡng',
            'van hoa' => 'Văn hóa',
            'bien dao' => 'Biển đảo',
            'cao cap' => 'Cao cấp',
            'da nang' => 'Đà Nẵng',
            'hue' => 'Huế',
            'hoi an' => 'Hội An',
            'da lat' => 'Đà Lạt',
            'nha trang' => 'Nha Trang',
            'phu quoc' => 'Phú Quốc',
            'quy nhon' => 'Quy Nhơn',
            'ha noi' => 'Hà Nội',
            'hai phong' => 'Hải Phòng',
            'can tho' => 'Cần Thơ',
            '3 ngay 2 dem' => '3 ngày 2 đêm',
            '2 ngay 1 dem' => '2 ngày 1 đêm',
            '4 ngay 3 dem' => '4 ngày 3 đêm',
        ];

        $normalized = preg_replace('/\s+/', ' ', mb_strtolower($value, 'UTF-8'));
        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        return $value;
    }
}
