<?php
class TourController extends Controller {
    public function history() {
        if (strlen($_SESSION['login']) == 0) {
            header('location:' . BASE_URL);
            exit;
        }

        $bookingModel = $this->model('BookingModel');
        $userEmail = $_SESSION['login'];
        $bookings = $bookingModel->getBookingsByUserEmail($userEmail);

        $data = [
            'bookings' => $bookings,
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);

        $this->view('tour/history', $data);
    }

    public function cancel($bookingId = 0) {
        if (strlen($_SESSION['login']) == 0) {
            header('location:' . BASE_URL);
            exit;
        }

        $bookingModel = $this->model('BookingModel');
        $userEmail = $_SESSION['login'];
        $booking = $bookingModel->getBookingForCancellation($bookingId, $userEmail);

        if ($booking) {
            $fdate = $booking->FromDate;
            $a = explode("/", $fdate);
            $val = array_reverse($a);
            $mydate = implode("/", $val);
            $cdate = date('Y/m/d');
            $date1 = date_create($cdate);
            $date2 = date_create($fdate);
            $diff = date_diff($date1, $date2);
            $df = $diff->format("%a");

            if ($df > 1) {
                if ($bookingModel->cancelBooking($bookingId, $userEmail)) {
                    $_SESSION['msg'] = "Hủy đặt tour thành công";
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
                }
            } else {
                $_SESSION['error'] = "Bạn không thể hủy đặt tour trước 24 giờ";
            }
        } else {
            $_SESSION['error'] = "Không tìm thấy đặt tour";
        }

        // Quay lai trang vua truy cap
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'user/account') !== false) {
            header('location:' . BASE_URL . 'user/account');
        } else {
            header('location:' . BASE_URL . 'tour/history');
        }
        exit;
    }
}
