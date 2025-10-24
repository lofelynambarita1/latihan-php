<?php
$page = $_GET['page'] ?? 'index';
require_once(__DIR__ . '/../controllers/TodoController.php');

$controller = new TodoController();

switch ($page) {
    case 'index': $controller->index(); break;
    case 'create': $controller->create(); break;
    case 'update': $controller->update(); break;
    case 'toggle': $controller->toggle(); break;            // khusus toggle status
    case 'delete': $controller->delete(); break;
    case 'detail': $controller->detail(); break;
    case 'reorder': $controller->reorder(); break;
    default: echo "404 - Halaman tidak ditemukan"; break;
}
