<?php
class UserController extends Controller {
    public function logout() {
        $userName = $_SESSION['login'] ?? 'Người dùng';
        $_SESSION['login'] = '';
        session_unset();
        session_destroy();
        session_start(); // Restart session để lưu message
        $_SESSION['msg'] = 'Đăng xuất thành công. Hẹn gặp lại!';
        header('location:' . BASE_URL);
        exit;
    }

    public function account() {
        if (strlen($_SESSION['login']) == 0) {
            header('location:' . BASE_URL);
            exit;
        }

        $userModel = $this->model('UserModel');
        $bookingModel = $this->model('BookingModel');
        $wishlistModel = $this->model('WishlistModel');
        
        $userEmail = $_SESSION['login'];
        $user = $userModel->getUserByEmail($userEmail);
        $bookings = $bookingModel->getBookingsByUserEmail($userEmail);
        $wishlistItems = $wishlistModel->getWishlistByUser($userEmail);

        $data = [
            'user' => $user,
            'bookings' => $bookings,
            'wishlistItems' => $wishlistItems,
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);

        $this->view('account/index', $data);
    }

    public function updateProfileExtended() {
        if (strlen($_SESSION['login']) == 0) {
            header('location:' . BASE_URL);
            exit;
        }

        if (isset($_POST['submit'])) {
            $name = trim($_POST['name'] ?? '');
            $mobileno = trim($_POST['mobileno'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $dateOfBirth = trim($_POST['dateofbirth'] ?? '');
            $gender = trim($_POST['gender'] ?? '');
            $email = $_SESSION['login'];

            // Kiem tra du lieu dau vao
            if (empty($name) || empty($mobileno)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ họ tên và số điện thoại";
                header('location:' . BASE_URL . 'user/account');
                exit;
            }

            if (!preg_match('/^[0-9]{10}$/', $mobileno)) {
                $_SESSION['error'] = "Số điện thoại phải có 10 chữ số";
                header('location:' . BASE_URL . 'user/account');
                exit;
            }

            // Xu ly tai anh dai dien
            $userModel = $this->model('UserModel');
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['avatar']['name'];
                $filetype = pathinfo($filename, PATHINFO_EXTENSION);
                
                if (!in_array(strtolower($filetype), $allowed)) {
                    $_SESSION['error'] = "Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif)";
                    header('location:' . BASE_URL . 'user/account');
                    exit;
                }

                // Tao thu muc upload neu chua co
                $uploadDir = ROOT . '/public/uploads/avatars/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Tao ten file duy nhat
                $newFilename = uniqid() . '_' . time() . '.' . $filetype;
                $uploadPath = $uploadDir . $newFilename;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
                    $avatarPath = 'public/uploads/avatars/' . $newFilename;
                    $userModel->updateAvatar($email, $avatarPath);
                }
            }

            if ($userModel->updateUserProfileExtended($email, $name, $mobileno, $address, $dateOfBirth, $gender)) {
                $_SESSION['msg'] = "Hồ sơ đã được cập nhật";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
            }
        }
        header('location:' . BASE_URL . 'user/account');
        exit;
    }

    public function profile() {
        if (strlen($_SESSION['login']) == 0) {
            header('location:' . BASE_URL);
            exit;
        }

        $userModel = $this->model('UserModel');
        $user = $userModel->getUserByEmail($_SESSION['login']);

        $data = [
            'user' => $user,
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);

        $this->view('user/profile', $data);
    }

    public function updateProfile() {
        if (strlen($_SESSION['login']) == 0) {
            header('location:' . BASE_URL);
            exit;
        }

        if (isset($_POST['submit'])) {
            $name = trim($_POST['name'] ?? '');
            $mobileno = trim($_POST['mobileno'] ?? '');
            $email = $_SESSION['login'];

            // Kiem tra du lieu dau vao
            if (empty($name) || empty($mobileno)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
                header('location:' . BASE_URL . 'user/profile');
                exit;
            }

            if (!preg_match('/^[0-9]{10}$/', $mobileno)) {
                $_SESSION['error'] = "Số điện thoại phải có 10 chữ số";
                header('location:' . BASE_URL . 'user/profile');
                exit;
            }

            $userModel = $this->model('UserModel');
            if ($userModel->updateUserProfile($email, $name, $mobileno)) {
                $_SESSION['msg'] = "Hồ sơ đã được cập nhật";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
            }
        }
        header('location:' . BASE_URL . 'user/profile');
        exit;
    }

    public function changePassword() {
        if (strlen($_SESSION['login']) == 0) {
            header('location:' . BASE_URL);
            exit;
        }

        $data = [
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);

        $this->view('user/change-password', $data);
    }

    public function updatePassword() {
        if (strlen($_SESSION['login']) == 0) {
            header('location:' . BASE_URL);
            exit;
        }

        if (isset($_POST['submit5'])) {
            $password = $_POST['password'] ?? '';
            $newpassword = $_POST['newpassword'] ?? '';
            $email = $_SESSION['login'];

            // Kiem tra mat khau
            if (empty($password) || empty($newpassword)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
                header('location:' . BASE_URL . 'user/change-password');
                exit;
            }

            if (strlen($newpassword) < 6) {
                $_SESSION['error'] = "Mật khẩu mới phải có ít nhất 6 ký tự";
                header('location:' . BASE_URL . 'user/change-password');
                exit;
            }

            $userModel = $this->model('UserModel');

            if ($userModel->checkPassword($email, $password)) {
                if ($userModel->updatePassword($email, $newpassword)) {
                    $_SESSION['msg'] = "Cập nhật mật khẩu thành công";
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
                }
            } else {
                $_SESSION['error'] = "Mật khẩu hiện tại không chính xác";
            }
        }
        header('location:' . BASE_URL . 'user/change-password');
        exit;
    }

    public function forgotPassword() {
        $data = [
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);

        $this->view('user/forgot-password', $data);
    }

    public function resetPassword() {
        if (isset($_POST['submit'])) {
            $contact = trim($_POST['mobile'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $newpassword = $_POST['newpassword'] ?? '';

            // Kiem tra du lieu dau vao
            if (empty($email) || empty($contact) || empty($newpassword)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
                header('location:' . BASE_URL . 'user/forgot-password');
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email không hợp lệ";
                header('location:' . BASE_URL . 'user/forgot-password');
                exit;
            }

            if (strlen($newpassword) < 6) {
                $_SESSION['error'] = "Mật khẩu mới phải có ít nhất 6 ký tự";
                header('location:' . BASE_URL . 'user/forgot-password');
                exit;
            }

            $userModel = $this->model('UserModel');

            if ($userModel->checkUserByEmailAndMobile($email, $contact)) {
                if ($userModel->resetPassword($email, $contact, $newpassword)) {
                    $_SESSION['msg'] = "Đặt lại mật khẩu thành công";
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
                }
            } else {
                $_SESSION['error'] = "Email hoặc số điện thoại không hợp lệ";
            }
        }
        header('location:' . BASE_URL . 'user/forgot-password');
        exit;
    }

    public function checkAvailability() {
        header('Content-Type: application/json');
        $response = ['available' => false, 'message' => ''];

        if (!empty($_POST["emailid"])) {
            $email = $_POST["emailid"];
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $response['message'] = "Lỗi: Bạn đã nhập email không hợp lệ.";
            } else {
                $userModel = $this->model('UserModel');
                if ($userModel->checkEmailAvailability($email)) {
                    $response['message'] = "Email đã tồn tại.";
                } else {
                    $response['available'] = true;
                    $response['message'] = "Email có thể được dùng để đăng ký.";
                }
            }
        }
        echo json_encode($response);
    }

    public function signup() {
        if (isset($_POST['submit'])) {
            $fname = trim($_POST['fname'] ?? '');
            $mnumber = trim($_POST['mobilenumber'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Kiem tra du lieu dau vao
            if (empty($fname) || empty($mnumber) || empty($email) || empty($password)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
                header('location:' . BASE_URL);
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email không hợp lệ";
                header('location:' . BASE_URL);
                exit;
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = "Mật khẩu phải có ít nhất 6 ký tự";
                header('location:' . BASE_URL);
                exit;
            }

            if (!preg_match('/^[0-9]{10}$/', $mnumber)) {
                $_SESSION['error'] = "Số điện thoại phải có 10 chữ số";
                header('location:' . BASE_URL);
                exit;
            }

            $userModel = $this->model('UserModel');
            
            // Kiem tra email da ton tai
            if ($userModel->checkEmailAvailability($email)) {
                $_SESSION['error'] = "Email này đã được sử dụng";
                header('location:' . BASE_URL);
                exit;
            }

            $lastInsertId = $userModel->createUser($fname, $mnumber, $email, $password);

            if ($lastInsertId) {
                $_SESSION['msg'] = "Bạn đã đăng ký thành công. Bây giờ bạn có thể đăng nhập.";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại.";
            }
            header('location:' . BASE_URL . 'thankyou');
            exit;
        }
        header('location:' . BASE_URL);
        exit;
    }

    public function login() {
        if (isset($_POST['signin'])) {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Kiem tra du lieu dau vao
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
                header('location:' . BASE_URL);
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email không hợp lệ";
                header('location:' . BASE_URL);
                exit;
            }

            $userModel = $this->model('UserModel');
            if ($userModel->checkPassword($email, $password)) {
                $_SESSION['login'] = $email;
                $_SESSION['msg'] = "Đăng nhập thành công! Chào mừng bạn trở lại.";
                header('location:' . BASE_URL);
                exit;
            } else {
                $_SESSION['error'] = "Email hoặc mật khẩu không chính xác";
                header('location:' . BASE_URL);
                exit;
            }
        }
        header('location:' . BASE_URL);
        exit;
    }
}
