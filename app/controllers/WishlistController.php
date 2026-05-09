<?php
class WishlistController extends Controller {
    /**
     * hien thi trang danh sach yeu thich
     */
    public function index() {
        if (strlen($_SESSION['login']) == 0) {
            header('location:' . BASE_URL);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        $wishlistItems = $wishlistModel->getWishlistByUser($userEmail);

        $data = [
            'wishlistItems' => $wishlistItems,
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);

        $this->view('wishlist/index', $data);
    }

    /**
     * them tour vao yeu thich (ajax endpoint)
     */
    public function add($packageId = 0) {
        header('Content-Type: application/json');
        
        if (strlen($_SESSION['login']) == 0) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập', 'requireLogin' => true]);
            exit;
        }

        // Kiem tra package id hop le
        if ($packageId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tour không hợp lệ']);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        
        try {
            if ($wishlistModel->addToWishlist($userEmail, $packageId)) {
                echo json_encode(['success' => true, 'message' => 'Đã thêm vào danh sách yêu thích']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể thêm vào danh sách yêu thích. Vui lòng thử lại']);
            }
        } catch (Exception $e) {
            error_log("WishlistController::add Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra. Vui lòng thử lại']);
        }
        exit;
    }

    /**
     * xoa tour khoi yeu thich (ajax endpoint)
     */
    public function remove($packageId = 0) {
        header('Content-Type: application/json');
        
        if (strlen($_SESSION['login']) == 0) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập', 'requireLogin' => true]);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        
        if ($wishlistModel->removeFromWishlist($userEmail, $packageId)) {
            echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi danh sách yêu thích']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
        exit;
    }

    /**
     * dao trang thai yeu thich (ajax endpoint)
     */
    public function toggle($packageId = 0) {
        header('Content-Type: application/json');
        
        if (strlen($_SESSION['login']) == 0) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập', 'requireLogin' => true]);
            exit;
        }

        // Kiem tra package id hop le
        if ($packageId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tour không hợp lệ']);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        
        try {
            $isInWishlist = $wishlistModel->isInWishlist($userEmail, $packageId);
            
            if ($wishlistModel->toggleWishlist($userEmail, $packageId)) {
                $newStatus = !$isInWishlist;
                echo json_encode([
                    'success' => true, 
                    'inWishlist' => $newStatus,
                    'message' => $newStatus ? 'Đã thêm vào danh sách yêu thích' : 'Đã xóa khỏi danh sách yêu thích'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật danh sách yêu thích. Vui lòng thử lại']);
            }
        } catch (Exception $e) {
            error_log("WishlistController::toggle Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra. Vui lòng thử lại']);
        }
        exit;
    }

    /**
     * kiem tra package co trong yeu thich (ajax endpoint)
     */
    public function check($packageId = 0) {
        header('Content-Type: application/json');
        
        if (strlen($_SESSION['login']) == 0) {
            echo json_encode(['inWishlist' => false]);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        
        $inWishlist = $wishlistModel->isInWishlist($userEmail, $packageId);
        echo json_encode(['inWishlist' => $inWishlist]);
        exit;
    }

    /**
     * lay tat ca package id yeu thich cua nguoi dung hien tai (ajax endpoint)
     */
    public function getIds() {
        header('Content-Type: application/json');
        
        if (strlen($_SESSION['login']) == 0) {
            echo json_encode(['packageIds' => []]);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        
        $packageIds = $wishlistModel->getWishlistPackageIds($userEmail);
        echo json_encode(['packageIds' => $packageIds]);
        exit;
    }
}
