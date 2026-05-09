<?php
class EnquiryController extends Controller {
    public function index() {
        $prefill = null;
        if (!empty($_SESSION['login'])) {
            $userModel = $this->model('UserModel');
            if ($userModel) {
                $prefill = $userModel->getUserByEmail($_SESSION['login']);
            }
        }
        $data = [
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null,
            'prefill' => $prefill,
        ];
        unset($_SESSION['error'], $_SESSION['msg']);
        $this->view('enquiry/index', $data);
    }

    public function inbox() {
        if (empty($_SESSION['login'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để xem tin nhắn liên hệ.';
            header('Location: ' . BASE_URL . 'enquiry');
            exit;
        }
        $enquiryModel = $this->model('EnquiryModel');
        $items = $enquiryModel ? $enquiryModel->getByEmail($_SESSION['login']) : [];
        $data = [
            'items' => $items,
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null,
        ];
        unset($_SESSION['error'], $_SESSION['msg']);
        $this->view('enquiry/inbox', $data);
    }

    public function submit() {
        if (isset($_POST['submit1'])) {
            $fname = trim($_POST['fname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $mobile = trim($_POST['mobileno'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Kiem tra du lieu dau vao
            if (empty($fname) || empty($email) || empty($mobile) || empty($subject) || empty($description)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin";
                header('location:' . BASE_URL . 'enquiry');
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email không hợp lệ";
                header('location:' . BASE_URL . 'enquiry');
                exit;
            }

            if (!preg_match('/^[0-9]{10}$/', $mobile)) {
                $_SESSION['error'] = "Số điện thoại phải có 10 chữ số";
                header('location:' . BASE_URL . 'enquiry');
                exit;
            }

            if (strlen($subject) > 100) {
                $_SESSION['error'] = "Tiêu đề không được vượt quá 100 ký tự";
                header('location:' . BASE_URL . 'enquiry');
                exit;
            }

            $enquiryModel = $this->model('EnquiryModel');
            $lastInsertId = $enquiryModel->createEnquiry($fname, $email, $mobile, $subject, $description);

            if ($lastInsertId) {
                $_SESSION['msg'] = "Đã gửi tin nhắn thành công. Bạn có thể xem phản hồi tại Hộp thư (sau khi đăng nhập).";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
            }
        }
        header('location:' . BASE_URL . 'enquiry');
        exit;
    }
}
