<?php
class PackageController extends Controller {
    public function index() {
        $packageModel = $this->model('PackageModel');

        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $locationFilter = isset($_GET['location']) ? trim($_GET['location']) : '';
        $priceFilter = isset($_GET['price']) ? $_GET['price'] : '';

        $packages = $packageModel->getFilteredPackages($keyword, $locationFilter, $priceFilter);
        $locations = $packageModel->getDistinctLocations();

        $data = [
            'packages' => $packages,
            'locations' => $locations,
            'keyword' => $keyword,
            'locationFilter' => $locationFilter,
            'priceFilter' => $priceFilter
        ];

        $this->view('package/index', $data);
    }

    public function details($id = 0) {
        $packageModel = $this->model('PackageModel');
        $package = $packageModel->getPackageById($id);

        $itineraryModel = $this->model('ItineraryModel');
        $itineraries = $itineraryModel->getByPackageId($id);

        $reviewStats = (object) ['avg_rating' => null, 'review_count' => 0];
        $reviews = [];
        $userHasReviewed = false;
        $userReview = null;
        if ($package) {
            $reviewModel = $this->model('ReviewModel');
            if ($reviewModel) {
                $pid = (int) $package->PackageId;
                $reviewStats = $reviewModel->getStatsByPackageId($pid);
                $reviews = $reviewModel->getReviewsByPackageId($pid, 50);
                $loginEmail = isset($_SESSION['login']) && strlen($_SESSION['login']) > 0 ? $_SESSION['login'] : '';
                $userHasReviewed = $loginEmail ? $reviewModel->userHasReviewed($pid, $loginEmail) : false;
                $userReview = $userHasReviewed ? $reviewModel->getUserReview($pid, $loginEmail) : null;
            }
        }

        $data = [
            'package' => $package,
            'itineraries' => $itineraries,
            'reviewStats' => $reviewStats,
            'reviews' => $reviews,
            'userHasReviewed' => $userHasReviewed,
            'userReview' => $userReview,
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);


        $this->view('package/details', $data);
    }

    public function submitReview($id = 0) {
        $pid = (int) $id;
        $reviewAnchorUrl = BASE_URL . 'package/details/' . $pid . '#danh-gia';
        if ($pid <= 0) {
            header('Location: ' . BASE_URL . 'package');
            exit;
        }

        if (empty($_SESSION['login']) || strlen($_SESSION['login']) == 0) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để gửi đánh giá.';
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }

        if (!isset($_POST['submit_review'])) {
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }

        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
        $note = isset($_POST['note']) ? trim((string) $_POST['note']) : '';

        if ($rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Vui lòng chọn số sao từ 1 đến 5.';
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }

        if (mb_strlen($note, 'UTF-8') > 1000) {
            $_SESSION['error'] = 'Ghi chú đánh giá tối đa 1000 ký tự.';
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }

        $reviewModel = $this->model('ReviewModel');
        if (!$reviewModel) {
            $_SESSION['error'] = 'Không thể gửi đánh giá lúc này.';
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }

        if ($reviewModel->userHasReviewed($pid, $_SESSION['login'])) {
            $_SESSION['error'] = 'Bạn đã đánh giá gói tour này rồi.';
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }

        if ($reviewModel->addReview($pid, $_SESSION['login'], $rating, $note)) {
            $_SESSION['msg'] = 'Cảm ơn bạn đã đánh giá tour.';
        } else {
            $_SESSION['error'] = 'Không thể lưu đánh giá. Vui lòng thử lại.';
        }
        header('Location: ' . $reviewAnchorUrl);
        exit;
    }

    public function updateReview($id = 0) {
        $pid = (int) $id;
        $reviewAnchorUrl = BASE_URL . 'package/details/' . $pid . '#danh-gia';
        if ($pid <= 0) {
            header('Location: ' . BASE_URL . 'package');
            exit;
        }
        if (empty($_SESSION['login']) || strlen($_SESSION['login']) == 0) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để sửa đánh giá.';
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }
        if (!isset($_POST['update_review'])) {
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }
        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
        $note = isset($_POST['note']) ? trim((string) $_POST['note']) : '';
        if ($rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Vui lòng chọn số sao từ 1 đến 5.';
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }
        if (mb_strlen($note, 'UTF-8') > 1000) {
            $_SESSION['error'] = 'Ghi chú đánh giá tối đa 1000 ký tự.';
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }
        $reviewModel = $this->model('ReviewModel');
        if (!$reviewModel || !$reviewModel->userHasReviewed($pid, $_SESSION['login'])) {
            $_SESSION['error'] = 'Không tìm thấy đánh giá của bạn để cập nhật.';
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }
        if ($reviewModel->updateReview($pid, $_SESSION['login'], $rating, $note)) {
            $_SESSION['msg'] = 'Đã cập nhật đánh giá của bạn.';
        } else {
            $_SESSION['error'] = 'Không thể cập nhật đánh giá.';
        }
        header('Location: ' . $reviewAnchorUrl);
        exit;
    }

    public function deleteReview($id = 0) {
        $pid = (int) $id;
        $reviewAnchorUrl = BASE_URL . 'package/details/' . $pid . '#danh-gia';
        if ($pid <= 0) {
            header('Location: ' . BASE_URL . 'package');
            exit;
        }
        if (empty($_SESSION['login']) || strlen($_SESSION['login']) == 0) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để xóa đánh giá.';
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }
        if (!isset($_POST['delete_review'])) {
            header('Location: ' . $reviewAnchorUrl);
            exit;
        }
        $reviewModel = $this->model('ReviewModel');
        if ($reviewModel && $reviewModel->deleteReview($pid, $_SESSION['login'])) {
            $_SESSION['msg'] = 'Đã xóa đánh giá của bạn.';
        } else {
            $_SESSION['error'] = 'Không thể xóa đánh giá.';
        }
        header('Location: ' . $reviewAnchorUrl);
        exit;
    }

    public function book($id = 0) {
        if (strlen($_SESSION['login']) == 0) {
            $_SESSION['error'] = "Vui lòng đăng nhập để đặt tour";
            header('Location: ' . BASE_URL);
            exit;
        }

        if (isset($_POST['submit2'])) {
            $pid = intval($id);
            $useremail = $_SESSION['login'];
            $departureDate = trim($_POST['departuredate'] ?? '');
            $numberofpeople = intval($_POST['numberofpeople'] ?? 1);
            $comment = trim($_POST['comment'] ?? '');

            // Kiem tra du lieu dat tour
            if (empty($departureDate)) {
                $_SESSION['error'] = "Vui lòng chọn ngày khởi hành";
                header('Location: ' . BASE_URL . 'package/details/' . $pid);
                exit;
            }

            if ($numberofpeople < 1 || $numberofpeople > 100) {
                $_SESSION['error'] = "Số người phải từ 1 đến 100";
                header('Location: ' . BASE_URL . 'package/details/' . $pid);
                exit;
            }

            // Kiem tra ngay khoi hanh hop le
            $departureTimestamp = strtotime($departureDate);
            $todayTimestamp = strtotime('today');

            if ($departureTimestamp === false) {
                $_SESSION['error'] = "Ngày không hợp lệ";
                header('Location: ' . BASE_URL . 'package/details/' . $pid);
                exit;
            }

            if ($departureTimestamp < $todayTimestamp) {
                $_SESSION['error'] = "Ngày khởi hành không thể là ngày trong quá khứ";
                header('Location: ' . BASE_URL . 'package/details/' . $pid);
                exit;
            }

            if ($pid <= 0) {
                $_SESSION['error'] = "Gói tour không hợp lệ";
                header('Location: ' . BASE_URL . 'package');
                exit;
            }

            // Lay gia goi tour va tinh tong tien
            $packageModel = $this->model('PackageModel');
            $package = $packageModel->getPackageById($pid);
            $totalprice = $package->PackagePrice * $numberofpeople;

            // Tam dung cung 1 ngay cho fromdate va todate de phu hop cau truc db
            $bookingModel = $this->model('BookingModel');
            $lastInsertId = $bookingModel->createBooking($pid, $useremail, $departureDate, $departureDate, $comment, $numberofpeople, $totalprice);

            if ($lastInsertId) {
                $_SESSION['msg'] = "Đặt tour thành công.";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
            }
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }
        header('Location: ' . BASE_URL . 'package');
        exit;
    }
}
