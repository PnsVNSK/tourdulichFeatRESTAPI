<?php

class Controller {
    /**
     * nap file model
     * @param string $model ten model
     * @return object doi tuong model
     */
    public function model($model) {
        if (file_exists(APP . '/models/' . $model . '.php')) {
            require_once APP . '/models/' . $model . '.php';
            return new $model();
        }
        return null;
    }

    /**
     * nap file view
     * @param string $view ten file view
     * @param array $data du lieu truyen vao view
     */
    public function view($view, $data = []) {
        if (file_exists(APP . '/views/' . $view . '.php')) {
            // Tach du lieu de su dung trong view
            extract($data);
            
            require_once APP . '/views/' . $view . '.php';
        } else {
            // Neu view khong ton tai thi bao loi
            die('View does not exist: ' . $view);
        }
    }

    /**
     * Format giá VNĐ với dấu phân cách nghìn
     * @param int|float $vndPrice Giá gốc (VNĐ)
     * @return string Giá đã định dạng kèm đơn vị
     */
    public static function formatVND($vndPrice) {
        return number_format($vndPrice, 0, ',', '.') . ' vnđ';
    }
}
