<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

require_once './routers/UserRouter.php';
require_once './routers/TaskRouter.php';

function get_route($uri) {
    $path = parse_url($uri, PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    return $segments[0] ?? '';
}

$route = get_route($_SERVER['REQUEST_URI']);

switch ($route) {
    case 'users':
        require_once './routers/UserRouter.php';
        $userRouter = new UserRouter();
        $userRouter->route();
        break;
    case 'tasks':
        require_once './routers/TaskRouter.php';
        $taskRouter = new TaskRouter();
        $taskRouter->route();
        break;
    default:
        http_response_code(404);
        echo json_encode(['code' => 404, 'message' => 'Route not found']);
        break;
}