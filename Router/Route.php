<?php

// Health check
$router->get('/api', 'api@index');

// Tours
$router->get('/api/tours', 'api@tours');
$router->get('/api/tours/:id', 'api@tours');
$router->post('/api/tours', 'api@tours');
$router->put('/api/tours/:id', 'api@tours');
$router->patch('/api/tours/:id', 'api@tours');
$router->delete('/api/tours/:id', 'api@tours');

// Auth
$router->post('/api/auth/register', function () {
    (new ApiController())->auth('register');
});
$router->post('/api/auth/login', function () {
    (new ApiController())->auth('login');
});

// Users
$router->get('/api/users/:email', 'api@users');
$router->put('/api/users/:email', 'api@users');
$router->patch('/api/users/:email', 'api@users');

// Bookings
$router->post('/api/bookings', 'api@bookings');
$router->get('/api/bookings/:email', 'api@bookings');
$router->patch('/api/bookings/:id', 'api@bookings');

// Wishlist
$router->get('/api/wishlist/:email', 'api@wishlist');
$router->post('/api/wishlist', 'api@wishlist');
$router->delete('/api/wishlist/:email/:packageId', 'api@wishlist');

// Reviews
$router->get('/api/tours/:id/reviews', 'api@reviews');
$router->post('/api/tours/:id/reviews', 'api@reviews');
$router->patch('/api/tours/:id/reviews/me', 'api@reviews');
$router->delete('/api/tours/:id/reviews/me', 'api@reviews');

// OPTIONS for browser preflight
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
