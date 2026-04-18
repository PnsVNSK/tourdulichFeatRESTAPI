# Huong dan REST API (style PHP MVC REST API)

## 1) Cau truc router

Project dang dung router theo kieu khai bao route giong repo mau:

- File router class: `Router/Router.php`
- File khai bao route: `Router/Route.php`
- Bootstrap route: `public/index.php`

Moi request duoc router phan tich theo:

- URI (`/api/...`)
- HTTP method (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`)

Neu khong match route API, he thong fallback ve MVC cu qua `core/App.php`.

## 2) Cach them route moi

Mo file `Router/Route.php` va them route:

```php
$router->get('/api/demo', 'api@index');
$router->post('/api/demo', 'api@tours');
```

Tuong tu project mau, neu route co khoang trang thi se tu dong doi sang dau `-`:

```php
$router->get('/home index', 'home@index'); // truy cap bang /home-index
```

Co the dung tham so URL:

```php
$router->get('/api/tours/:id', 'api@tours');
```

Co the dung closure:

```php
$router->get('/:name', function($param) {
    echo 'Welcome ' . $param['name'];
});
```

## 3) Quy uoc handler

Handler theo mau: `controller@method`

- `api@tours` -> `ApiController::tours()`
- `api@users` -> `ApiController::users()`

Router tu dong doi `api` thanh `ApiController`.

## 4) Base URL

`http://localhost/tourdulich/public/api`

## 5) Danh sach endpoint

### Tours

- `GET /api/tours`
- `GET /api/tours/{id}`
- `POST /api/tours`
- `PUT /api/tours/{id}`
- `PATCH /api/tours/{id}`
- `DELETE /api/tours/{id}`

Bo loc cho `GET /api/tours`:

- `q`
- `location`
- `min_price`
- `max_price`
- `limit`
- `offset`

### Auth

- `POST /api/auth/register`
- `POST /api/auth/login`

Body `register`:

```json
{
  "fullName": "Nguyen Van A",
  "mobileNumber": "0909123456",
  "email": "a@gmail.com",
  "password": "123456"
}
```

Body `login`:

```json
{
  "email": "a@gmail.com",
  "password": "123456"
}
```

### Users

- `GET /api/users/{email}`
- `PUT /api/users/{email}`
- `PATCH /api/users/{email}`

Body update:

```json
{
  "FullName": "Nguyen Van A",
  "MobileNumber": "0909123456",
  "Address": "Da Nang",
  "DateOfBirth": "2001-01-01",
  "Gender": "Nam"
}
```

### Bookings

- `POST /api/bookings`
- `GET /api/bookings/{email}`
- `PATCH /api/bookings/{bookingId}`

Body tao booking:

```json
{
  "packageId": 1,
  "userEmail": "a@gmail.com",
  "fromDate": "2026-05-01",
  "toDate": "2026-05-01",
  "numberOfPeople": 2,
  "comment": "Dat som"
}
```

Body huy booking:

```json
{
  "action": "cancel",
  "userEmail": "a@gmail.com"
}
```

### Wishlist

- `GET /api/wishlist/{email}`
- `POST /api/wishlist`
- `DELETE /api/wishlist/{email}/{packageId}`

Body them wishlist:

```json
{
  "userEmail": "a@gmail.com",
  "packageId": 1
}
```
