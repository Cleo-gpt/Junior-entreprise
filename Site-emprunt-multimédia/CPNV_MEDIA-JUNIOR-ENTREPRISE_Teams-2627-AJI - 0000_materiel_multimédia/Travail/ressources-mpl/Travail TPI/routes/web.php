<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\DashboardController;
use App\Controllers\MaterialController;
use App\Controllers\ReservationController;

$authController = AuthController::class;
$dashboardController = DashboardController::class;
$materialController = MaterialController::class;
$reservationController = ReservationController::class;
$adminController = AdminController::class;
$cartController = CartController::class;

$router->get('/', [$dashboardController, 'index']);

$router->get('/login', [$authController, 'showLogin']);
$router->post('/login', [$authController, 'login']);

$router->get('/register', [$authController, 'showRegister']);
$router->post('/register', [$authController, 'register']);

$router->post('/logout', [$authController, 'logout']);

$router->get('/materials', [$materialController, 'index']);
$router->post('/reservations', [$reservationController, 'store']);
$router->post('/reservations/extend', [$reservationController, 'extend']);

$router->get('/cart', [$cartController, 'index']);
$router->post('/cart/add', [$cartController, 'add']);
$router->post('/cart/update', [$cartController, 'update']);
$router->post('/cart/remove', [$cartController, 'remove']);
$router->post('/cart/clear', [$cartController, 'clear']);
$router->post('/cart/checkout', [$cartController, 'checkout']);

$router->get('/admin/tools', [$adminController, 'tools']);
$router->get('/admin/materials/edit', [$adminController, 'editMaterial']);
$router->get('/admin/reservations', [$adminController, 'reservations']);
$router->get('/admin/incidents', [$adminController, 'incidents']);
$router->get('/admin/trash', [$adminController, 'trash']);
$router->get('/admin/users', [$adminController, 'users']);
$router->post('/admin/materials', [$adminController, 'storeMaterial']);
$router->post('/admin/materials/update', [$adminController, 'updateMaterial']);
$router->post('/admin/reservations/update', [$adminController, 'updateReservation']);
$router->post('/admin/reservations/delete', [$adminController, 'deleteReservation']);
$router->post('/admin/trash/restore', [$adminController, 'restoreTrash']);
$router->post('/admin/trash/delete', [$adminController, 'destroyTrash']);
$router->post('/admin/users/create', [$adminController, 'storeUser']);
$router->post('/admin/users/update', [$adminController, 'updateUser']);
$router->post('/admin/users/delete', [$adminController, 'deleteUser']);
$router->post('/admin/whitelist/add', [$adminController, 'addWhitelist']);
$router->post('/admin/whitelist/import', [$adminController, 'importWhitelist']);
$router->post('/admin/whitelist/remove', [$adminController, 'removeWhitelist']);
$router->post('/admin/preview-email', [$adminController, 'previewEmail']);

