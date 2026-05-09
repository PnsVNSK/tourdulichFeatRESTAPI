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

// Nhom nguoi dung
$router->get('/api/users/:email', 'api@users');
$router->put('/api/users/:email', 'api@users');
$router->patch('/api/users/:email', 'api@users');

// Nhom dat tour
$router->post('/api/bookings', 'api@bookings');
$router->get('/api/bookings/:email', 'api@bookings');
$router->patch('/api/bookings/:id', 'api@bookings');

// Nhom yeu thich
$router->get('/api/wishlist/:email', 'api@wishlist');
$router->post('/api/wishlist', 'api@wishlist');
$router->delete('/api/wishlist/:email/:packageId', 'api@wishlist');

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
$router->options('/api/users/:email', 'api@users');
$router->options('/api/bookings', 'api@bookings');
$router->options('/api/bookings/:id', 'api@bookings');
$router->options('/api/bookings/:email', 'api@bookings');
$router->options('/api/wishlist', 'api@wishlist');
$router->options('/api/wishlist/:email', 'api@wishlist');
$router->options('/api/wishlist/:email/:packageId', 'api@wishlist');
$router->options('/api/tours/:id/reviews', 'api@reviews');
$router->options('/api/tours/:id/reviews/me', 'api@reviews');
