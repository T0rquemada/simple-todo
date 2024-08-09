<?php

require_once './env.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Check suer's JWT, if it's not correct - end up proccess
class AuthMiddleware {
    private $pdo;
    private $secretKey;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->secretKey = $_ENV['JWT'];
    }

    public function handle($jwt) {
        if (!$jwt) {
            http_response_code(401);
            echo json_encode(['code' => 401, 'message' => 'Unauthorized: JWT not provided']);
            exit();
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->secretKey, 'HS256'));

            $email = $decoded->email;
            $password = $decoded->password;

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                http_response_code(401);
                echo json_encode(['code' => 401, 'message' => 'Unauthorized: Invalid credentials']);
                exit();
            }
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['code' => 401, 'message' => 'Unauthorized: Invalid JWT']);
            exit();
        }
    }
}
