<?php

// Kiem tra trang thai api
$router->get('/api', 'api@index');

// Nhom api tour
$router->get('/api/tours', 'api@tours');
$router->get('/api/tours/:id', 'api@tours');
$router->post('/api/tours', 'api@tours');
$router->put('/api/tours/:id', 'api@tours');
$router->patch('/api/tours/:id', 'api@tours');
$router->delete('/api/tours/:id', 'api@tours');

// Nhom xac thuc
$router->post('/api/auth/register', function () {
    (new ApiController())->auth('register');
});
$router->post('/api/auth/login', function () {
    (new ApiController())->auth('login');
});

// Nhom nguoi dung (resource theo id so)
$router->get('/api/users/:id', 'api@users');
$router->put('/api/users/:id', 'api@users');
$router->patch('/api/users/:id', 'api@users');

// Nhom dat tour: danh sach loc bang query ?userEmail=, chi tiet /{id}
$router->get('/api/bookings', 'api@bookings');
$router->get('/api/bookings/:id', 'api@bookings');
$router->post('/api/bookings', 'api@bookings');
$router->patch('/api/bookings/:id', 'api@bookings');

// Nhom yeu thich: loc bang ?userEmail=, xoa muc bang DELETE /{packageId}?userEmail=
$router->get('/api/wishlist', 'api@wishlist');
$router->post('/api/wishlist', 'api@wishlist');
$router->delete('/api/wishlist/:packageId', 'api@wishlist');

// Nhom danh gia
$router->get('/api/tours/:id/reviews', 'api@reviews');
$router->post('/api/tours/:id/reviews', 'api@reviews');
$router->patch('/api/tours/:id/reviews/me', 'api@reviews');
$router->delete('/api/tours/:id/reviews/me', 'api@reviews');

// Khai bao options cho preflight cors
$router->options('/api', 'api@index');
$router->options('/api/tours', 'api@tours');
$router->options('/api/tours/:id', 'api@tours');
$router->options('/api/auth/register', function () {
    (new ApiController())->auth('register');
});
$router->options('/api/auth/login', function () {
    (new ApiController())->auth('login');
});
$router->options('/api/users/:id', 'api@users');
$router->options('/api/bookings', 'api@bookings');
$router->options('/api/bookings/:id', 'api@bookings');
$router->options('/api/wishlist', 'api@wishlist');
$router->options('/api/wishlist/:packageId', 'api@wishlist');
$router->options('/api/tours/:id/reviews', 'api@reviews');
$router->options('/api/tours/:id/reviews/me', 'api@reviews');
