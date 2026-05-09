<?php
class PageController extends Controller {
    public function index($type = '') {
        $pageModel = $this->model('PageModel');
        $page = $pageModel->getPageByType($type);

        $titleMap = [
            'aboutus' => 'Giới thiệu',
            'privacy' => 'Chính sách bảo mật',
            'terms' => 'Điều khoản sử dụng',
            'contact' => 'Liên hệ',
        ];
        $titleText = $titleMap[$type] ?? ucfirst($type);

        $content = '';
        if ($page) {
            $content = $page->detail;
            // Chuan hoa cac the html cu
            $content = preg_replace('/<FONT[^>]*>/i', '', $content);
            $content = str_replace('</FONT>', '', $content);
            $content = preg_replace('/<P align=[^>]*>/i', '<p>', $content);
            $content = str_replace('</P>', '</p>', $content);
            $content = preg_replace('/<STRONG>/i', '<strong>', $content);
            $content = str_replace('</STRONG>', '</strong>', $content);
            
            // Loai bo script va thuoc tinh su kien de tranh xss
            // Xoa the script khong phan biet hoa thuong va ho tro noi dung nhieu dong
            $content = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/is', '', $content);
            $content = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $content);
            $content = preg_replace('/on\w+\s*=\s*[^\s>]*/i', '', $content);
        }

        $data = [
            'title' => $titleText,
            'content' => $content,
        ];

        $this->view('page/index', $data);
    }
}
