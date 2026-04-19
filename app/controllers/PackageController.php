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
        if ($package) {
            $reviewModel = $this->model('ReviewModel');
            if ($reviewModel) {
                $pid = (int) $package->PackageId;
                $reviewStats = $reviewModel->getStatsByPackageId($pid);
                $reviews = $reviewModel->getReviewsByPackageId($pid, 50);
                $loginEmail = isset($_SESSION['login']) && strlen($_SESSION['login']) > 0 ? $_SESSION['login'] : '';
                $userHasReviewed = $loginEmail ? $reviewModel->userHasReviewed($pid, $loginEmail) : false;
            }
        }

        $data = [
            'package' => $package,
            'itineraries' => $itineraries,
            'reviewStats' => $reviewStats,
            'reviews' => $reviews,
            'userHasReviewed' => $userHasReviewed,
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);


        $this->view('package/details', $data);
    }

    public function submitReview($id = 0) {
        $pid = (int) $id;
        if ($pid <= 0) {
            header('Location: ' . BASE_URL . 'package');
            exit;
        }

        if (empty($_SESSION['login']) || strlen($_SESSION['login']) == 0) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để gửi đánh giá.';
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }

        if (!isset($_POST['submit_review'])) {
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }

        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
        $note = isset($_POST['note']) ? trim((string) $_POST['note']) : '';

        if ($rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Vui lòng chọn số sao từ 1 đến 5.';
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }

        if (mb_strlen($note, 'UTF-8') > 1000) {
            $_SESSION['error'] = 'Ghi chú đánh giá tối đa 1000 ký tự.';
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }

        $reviewModel = $this->model('ReviewModel');
        if (!$reviewModel) {
            $_SESSION['error'] = 'Không thể gửi đánh giá lúc này.';
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }

        if ($reviewModel->userHasReviewed($pid, $_SESSION['login'])) {
            $_SESSION['error'] = 'Bạn đã đánh giá gói tour này rồi.';
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }

        if ($reviewModel->addReview($pid, $_SESSION['login'], $rating, $note)) {
            $_SESSION['msg'] = 'Cảm ơn bạn đã đánh giá tour.';
        } else {
            $_SESSION['error'] = 'Không thể lưu đánh giá. Vui lòng thử lại.';
        }
        header('Location: ' . BASE_URL . 'package/details/' . $pid);
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

            // Loc
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

            // Validate date
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

            // Get package price and calculate total
            $packageModel = $this->model('PackageModel');
            $package = $packageModel->getPackageById($pid);
            $totalprice = $package->PackagePrice * $numberofpeople;

            // Use same date for both fromdate and todate for database compatibility
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
