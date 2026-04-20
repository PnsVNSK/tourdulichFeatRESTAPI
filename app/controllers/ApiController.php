<?php

class ApiController extends Controller
{
    private $tourModel;
    private $userModel;
    private $bookingModel;
    private $wishlistModel;
    private $itineraryModel;
    private $reviewModel;

    public function __construct()
    {
        $this->tourModel = $this->model('ApiTourModel');
        $this->userModel = $this->model('ApiUserModel');
        $this->bookingModel = $this->model('ApiBookingModel');
        $this->wishlistModel = $this->model('WishlistModel');
        $this->itineraryModel = $this->model('ItineraryModel');
        $this->reviewModel = $this->model('ApiReviewModel');
    }

    public function index()
    {
        $this->sendJson([
            'message' => 'API hoat dong',
            'endpoints' => [
                'GET /api/tours',
                'GET /api/tours/{id}',
                'POST /api/tours',
                'PUT/PATCH /api/tours/{id}',
                'DELETE /api/tours/{id}',
                'POST /api/auth/register',
                'POST /api/auth/login',
                'GET /api/users/{email}',
                'PUT/PATCH /api/users/{email}',
                'POST /api/bookings',
                'GET /api/bookings/{email}',
                'PATCH /api/bookings/{id}',
                'GET /api/wishlist/{email}',
                'POST /api/wishlist',
                'DELETE /api/wishlist/{email}/{packageId}',
                'GET /api/tours/{id}/reviews',
                'POST /api/tours/{id}/reviews',
                'PATCH /api/tours/{id}/reviews/me',
                'DELETE /api/tours/{id}/reviews/me',
            ],
        ]);
    }

    public function tours($id = null)
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'OPTIONS') {
            return $this->sendJson([]);
        }

        if ($method === 'GET') {
            if ($id === null) {
                return $this->sendJson(['data' => $this->tourModel->getAll($_GET)]);
            }
            $tour = $this->tourModel->getById((int)$id);
            if (!$tour) {
                return $this->sendJson(['error' => 'Khong tim thay tour'], 404);
            }
            $tour['itinerary'] = $this->itineraryModel->getByPackageId((int)$id);
            return $this->sendJson(['data' => $tour]);
        }

        if ($method === 'POST') {
            $payload = $this->getJsonInput();
            if ($payload === null) {
                return $this->sendJson(['error' => 'JSON khong hop le'], 400);
            }
            $errors = $this->validateTourPayload($payload);
            if (!empty($errors)) {
                return $this->sendJson(['error' => 'Du lieu khong hop le', 'details' => $errors], 422);
            }
            $newId = $this->tourModel->create($payload);
            return $this->sendJson(['message' => 'Tao tour thanh cong', 'data' => $this->tourModel->getById($newId)], 201);
        }

        if ($method === 'PUT' || $method === 'PATCH') {
            if ($id === null) {
                return $this->sendJson(['error' => 'Thieu id tour'], 400);
            }
            $existing = $this->tourModel->getById((int)$id);
            if (!$existing) {
                return $this->sendJson(['error' => 'Khong tim thay tour'], 404);
            }
            $payload = $this->getJsonInput();
            if ($payload === null) {
                return $this->sendJson(['error' => 'JSON khong hop le'], 400);
            }
            $merged = array_merge($existing, $payload);
            $errors = $this->validateTourPayload($merged);
            if (!empty($errors)) {
                return $this->sendJson(['error' => 'Du lieu khong hop le', 'details' => $errors], 422);
            }
            $this->tourModel->update((int)$id, $merged);
            return $this->sendJson(['message' => 'Cap nhat tour thanh cong', 'data' => $this->tourModel->getById((int)$id)]);
        }

        if ($method === 'DELETE') {
            if ($id === null) {
                return $this->sendJson(['error' => 'Thieu id tour'], 400);
            }
            if (!$this->tourModel->getById((int)$id)) {
                return $this->sendJson(['error' => 'Khong tim thay tour'], 404);
            }
            $this->tourModel->delete((int)$id);
            return $this->sendJson(['message' => 'Xoa tour thanh cong']);
        }

        return $this->sendJson(['error' => 'Method khong ho tro'], 405);
    }

    public function auth($action = null)
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'OPTIONS') {
            return $this->sendJson([]);
        }
        if ($method !== 'POST') {
            return $this->sendJson(['error' => 'Method khong ho tro'], 405);
        }

        $payload = $this->getJsonInput();
        if ($payload === null) {
            return $this->sendJson(['error' => 'JSON khong hop le'], 400);
        }

        if ($action === 'register') {
            return $this->register($payload);
        }
        if ($action === 'login') {
            return $this->login($payload);
        }
        return $this->sendJson(['error' => 'Action khong hop le'], 400);
    }

    public function users($email = null)
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'OPTIONS') {
            return $this->sendJson([]);
        }

        if ($email === null) {
            return $this->sendJson(['error' => 'Thieu email'], 400);
        }
        $decodedEmail = urldecode($email);

        if ($method === 'GET') {
            $user = $this->userModel->getByEmail($decodedEmail);
            if (!$user) {
                return $this->sendJson(['error' => 'Khong tim thay user'], 404);
            }
            return $this->sendJson(['data' => $user]);
        }

        if ($method === 'PUT' || $method === 'PATCH') {
            $payload = $this->getJsonInput();
            if ($payload === null) {
                return $this->sendJson(['error' => 'JSON khong hop le'], 400);
            }
            $existing = $this->userModel->getByEmail($decodedEmail);
            if (!$existing) {
                return $this->sendJson(['error' => 'Khong tim thay user'], 404);
            }
            $merged = array_merge($existing, $payload);
            if (empty($merged['FullName']) || empty($merged['MobileNumber'])) {
                return $this->sendJson(['error' => 'FullName va MobileNumber la bat buoc'], 422);
            }
            $this->userModel->updateProfile($decodedEmail, $merged);
            return $this->sendJson(['message' => 'Cap nhat profile thanh cong', 'data' => $this->userModel->getByEmail($decodedEmail)]);
        }

        return $this->sendJson(['error' => 'Method khong ho tro'], 405);
    }

    public function bookings($idOrEmail = null)
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'OPTIONS') {
            return $this->sendJson([]);
        }

        if ($method === 'POST') {
            $payload = $this->getJsonInput();
            if ($payload === null) {
                return $this->sendJson(['error' => 'JSON khong hop le'], 400);
            }
            return $this->createBooking($payload);
        }

        if ($method === 'GET') {
            if ($idOrEmail === null) {
                return $this->sendJson(['error' => 'Thieu email'], 400);
            }
            $email = urldecode($idOrEmail);
            return $this->sendJson(['data' => $this->bookingModel->getByUserEmail($email)]);
        }

        if ($method === 'PATCH') {
            if ($idOrEmail === null) {
                return $this->sendJson(['error' => 'Thieu id booking'], 400);
            }
            $payload = $this->getJsonInput();
            if ($payload === null || empty($payload['action'])) {
                return $this->sendJson(['error' => 'Can action trong body'], 400);
            }
            if ($payload['action'] !== 'cancel') {
                return $this->sendJson(['error' => 'Chi ho tro action=cancel'], 400);
            }
            if (empty($payload['userEmail'])) {
                return $this->sendJson(['error' => 'Can userEmail de huy booking'], 400);
            }
            $booking = $this->bookingModel->getById((int)$idOrEmail);
            if (!$booking) {
                return $this->sendJson(['error' => 'Khong tim thay booking'], 404);
            }
            $this->bookingModel->cancelByUser((int)$idOrEmail, $payload['userEmail']);
            return $this->sendJson(['message' => 'Huy booking thanh cong']);
        }

        return $this->sendJson(['error' => 'Method khong ho tro'], 405);
    }

    public function wishlist($email = null, $packageId = null)
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'OPTIONS') {
            return $this->sendJson([]);
        }

        if ($method === 'GET') {
            if ($email === null) {
                return $this->sendJson(['error' => 'Thieu email'], 400);
            }
            return $this->sendJson(['data' => $this->wishlistModel->getWishlistByUser(urldecode($email))]);
        }

        if ($method === 'POST') {
            $payload = $this->getJsonInput();
            if ($payload === null) {
                return $this->sendJson(['error' => 'JSON khong hop le'], 400);
            }
            if (empty($payload['userEmail']) || empty($payload['packageId'])) {
                return $this->sendJson(['error' => 'Can userEmail va packageId'], 422);
            }
            $this->wishlistModel->addToWishlist($payload['userEmail'], (int)$payload['packageId']);
            return $this->sendJson(['message' => 'Them wishlist thanh cong'], 201);
        }

        if ($method === 'DELETE') {
            if ($email === null || $packageId === null) {
                return $this->sendJson(['error' => 'Can email va packageId tren url'], 400);
            }
            $this->wishlistModel->removeFromWishlist(urldecode($email), (int)$packageId);
            return $this->sendJson(['message' => 'Xoa wishlist thanh cong']);
        }

        return $this->sendJson(['error' => 'Method khong ho tro'], 405);
    }

    public function reviews($tourId = null, $scope = null)
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'OPTIONS') {
            return $this->sendJson([]);
        }
        if ($tourId === null) {
            return $this->sendJson(['error' => 'Thieu id tour'], 400);
        }

        $tour = $this->tourModel->getById((int)$tourId);
        if (!$tour) {
            return $this->sendJson(['error' => 'Khong tim thay tour'], 404);
        }

        if ($method === 'GET' && $scope === null) {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            return $this->sendJson([
                'data' => [
                    'stats' => $this->reviewModel->getStatsByTourId((int)$tourId),
                    'items' => $this->reviewModel->getByTourId((int)$tourId, $limit, $offset),
                ],
            ]);
        }

        if ($method === 'POST' && $scope === null) {
            $payload = $this->getJsonInput();
            if ($payload === null) {
                return $this->sendJson(['error' => 'JSON khong hop le'], 400);
            }
            if (empty($payload['userEmail'])) {
                return $this->sendJson(['error' => 'Can userEmail'], 422);
            }
            $rating = isset($payload['rating']) ? (int)$payload['rating'] : 0;
            if ($rating < 1 || $rating > 5) {
                return $this->sendJson(['error' => 'rating phai tu 1 den 5'], 422);
            }
            $note = isset($payload['note']) ? trim((string)$payload['note']) : '';
            if (mb_strlen($note, 'UTF-8') > 1000) {
                return $this->sendJson(['error' => 'note toi da 1000 ky tu'], 422);
            }
            if ($this->reviewModel->existsUserReview((int)$tourId, $payload['userEmail'])) {
                return $this->sendJson(['error' => 'User da danh gia tour nay'], 409);
            }
            $this->reviewModel->create((int)$tourId, $payload['userEmail'], $rating, $note);
            return $this->sendJson([
                'message' => 'Gui danh gia thanh cong',
                'data' => $this->reviewModel->getUserReview((int)$tourId, $payload['userEmail']),
            ], 201);
        }

        if ($method === 'PATCH' && $scope === 'me') {
            $payload = $this->getJsonInput();
            if ($payload === null) {
                return $this->sendJson(['error' => 'JSON khong hop le'], 400);
            }
            if (empty($payload['userEmail'])) {
                return $this->sendJson(['error' => 'Can userEmail'], 422);
            }
            if (!$this->reviewModel->existsUserReview((int)$tourId, $payload['userEmail'])) {
                return $this->sendJson(['error' => 'Khong tim thay danh gia cua user'], 404);
            }
            $rating = isset($payload['rating']) ? (int)$payload['rating'] : 0;
            if ($rating < 1 || $rating > 5) {
                return $this->sendJson(['error' => 'rating phai tu 1 den 5'], 422);
            }
            $note = isset($payload['note']) ? trim((string)$payload['note']) : '';
            if (mb_strlen($note, 'UTF-8') > 1000) {
                return $this->sendJson(['error' => 'note toi da 1000 ky tu'], 422);
            }
            $this->reviewModel->updateByUser((int)$tourId, $payload['userEmail'], $rating, $note);
            return $this->sendJson([
                'message' => 'Cap nhat danh gia thanh cong',
                'data' => $this->reviewModel->getUserReview((int)$tourId, $payload['userEmail']),
            ]);
        }

        if ($method === 'DELETE' && $scope === 'me') {
            $payload = $this->getJsonInput();
            if ($payload === null) {
                $payload = [];
            }
            $userEmail = '';
            if (!empty($payload['userEmail'])) {
                $userEmail = $payload['userEmail'];
            } elseif (!empty($_GET['userEmail'])) {
                $userEmail = $_GET['userEmail'];
            }
            if ($userEmail === '') {
                return $this->sendJson(['error' => 'Can userEmail (body hoac query)'], 422);
            }
            if (!$this->reviewModel->existsUserReview((int)$tourId, $userEmail)) {
                return $this->sendJson(['error' => 'Khong tim thay danh gia cua user'], 404);
            }
            $this->reviewModel->deleteByUser((int)$tourId, $userEmail);
            return $this->sendJson(['message' => 'Xoa danh gia thanh cong']);
        }

        return $this->sendJson(['error' => 'Method khong ho tro'], 405);
    }

    private function register($payload)
    {
        $required = ['fullName', 'mobileNumber', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($payload[$field])) {
                return $this->sendJson(['error' => $field . ' la bat buoc'], 422);
            }
        }
        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->sendJson(['error' => 'Email khong hop le'], 422);
        }
        if ($this->userModel->emailExists($payload['email'])) {
            return $this->sendJson(['error' => 'Email da ton tai'], 409);
        }

        $newId = $this->userModel->createUser(
            $payload['fullName'],
            $payload['mobileNumber'],
            $payload['email'],
            $payload['password']
        );
        return $this->sendJson(['message' => 'Dang ky thanh cong', 'data' => ['id' => $newId]], 201);
    }

    private function login($payload)
    {
        if (empty($payload['email']) || empty($payload['password'])) {
            return $this->sendJson(['error' => 'Email va password la bat buoc'], 422);
        }
        $user = $this->userModel->verifyCredentials($payload['email'], $payload['password']);
        if (!$user) {
            return $this->sendJson(['error' => 'Sai thong tin dang nhap'], 401);
        }

        $_SESSION['login'] = $payload['email'];
        return $this->sendJson(['message' => 'Dang nhap thanh cong', 'data' => $user]);
    }

    private function createBooking($payload)
    {
        $required = ['packageId', 'userEmail', 'fromDate', 'numberOfPeople'];
        foreach ($required as $field) {
            if (empty($payload[$field])) {
                return $this->sendJson(['error' => $field . ' la bat buoc'], 422);
            }
        }

        $price = $this->bookingModel->getPackagePrice((int)$payload['packageId']);
        if ($price === null) {
            return $this->sendJson(['error' => 'Khong tim thay package'], 404);
        }

        $toDate = !empty($payload['toDate']) ? $payload['toDate'] : $payload['fromDate'];
        $comment = $payload['comment'] ?? '';
        $number = (int)$payload['numberOfPeople'];
        $total = $price * $number;

        $newId = $this->bookingModel->create(
            (int)$payload['packageId'],
            $payload['userEmail'],
            $payload['fromDate'],
            $toDate,
            $comment,
            $number,
            $total
        );

        return $this->sendJson(['message' => 'Dat tour thanh cong', 'data' => $this->bookingModel->getById($newId)], 201);
    }

    private function validateTourPayload($data)
    {
        $errors = [];
        $requiredFields = ['PackageName', 'PackageType', 'TourDuration', 'PackageLocation', 'PackagePrice', 'PackageFetures', 'PackageDetails', 'PackageImage'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                $errors[$field] = 'Truong bat buoc';
            }
        }
        if (isset($data['PackagePrice']) && (!is_numeric($data['PackagePrice']) || (int)$data['PackagePrice'] < 0)) {
            $errors['PackagePrice'] = 'PackagePrice phai >= 0';
        }
        return $errors;
    }

    private function getJsonInput()
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return $decoded;
    }

    private function sendJson($payload, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
