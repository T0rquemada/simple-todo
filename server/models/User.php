<?php

require_once './env.php';
require_once __DIR__ .  '/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class User {
    private $pdo;
    private $secretKey;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->secretKey = $_ENV['JWT'];
    }

    private function getUserId($email) {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE email=?;');
        try {
            $result = $stmt->execute([$email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['id'])) { return $row['id']; } 
            else { return null; }
        } catch (PDOException $e) {
            return null;
        }
    }

    private function generateJWT($email, $password, $user_id) {
        $issuedAt = time();

        $payload = [
            'iat' => $issuedAt,
            'email' => $email,
            'password' => $password,
            'user_id' => $user_id
        ];

        error_log('JWT Payload: ' . print_r($payload, true));

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    // Return userdata from JWT
    public function parseJWT($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key($this->secretKey, 'HS256'));
            $decoded_array = (array) $decoded; // Convert to array
    
            $email = $decoded_array['email'];
            $password = $decoded_array['password'];
    
            return ['email' => $email, 'password' => $password];
        } catch (Exception $e) {
            error_log('JWT decode error: ' . $e->getMessage());
            return null;
        }
    }
    

    private function validateUserdata($userdata) {
        # Check on spaces
        /*if (strpos($userdata['username'], ' ') === false) return false;
        if (strpos($userdata['email'], ' ') === false) return false;
        if (strpos($userdata['password'], ' ') === false) return false;

        # Check on lengths
        if (strlen($userdata['username'] < 3)) return false;
        if (strlen($userdata['email'] < 3)) return false;
        if (strlen($userdata['password'] < 8)) return false;


        if (strpos($userdata['email'], '@') === false) return false;*/

        return true;
    }

    # Return true, if user email and pass fits
    private function verify_user($user): bool {
        try {
            $email = $user['email'];
            $password = $user['password'];
        } catch (ValueError $e) {
            echo "ValueError: " . $e->getMessage();
            return false;
        }

        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user_from_db = $stmt->fetch();

        if ($user && password_verify($password, $user_from_db['password'])) {
            return true;
        }
        return false;
    }

    // Return true if user with given email/username exist, otherwise - false
    private function user_already_exist(string $type, string $user_field): bool {
        $stmt = null;
    
        if ($type === 'email') {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        } else if ($type === 'username') {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        }
    
        $stmt->execute([$user_field]);
        $result = $stmt->fetchColumn();
    
        return $result > 0;
    }

    public function create($userdata) {
        $correct_fields = isset($userdata['username'], $userdata['password'], $userdata['email']);
        if (!$this->validateUserdata($userdata) && $correct_fields) {
            http_response_code(400); 
            $response = [ 'code' => 400, 'message' => 'Invalid user data' ];
        } else {
            $name = strip_tags($userdata['username']);
            $hashed_pass = password_hash($userdata['password'], PASSWORD_DEFAULT); # Hash user password
            $email = strip_tags($userdata['email']);

            # Inserting user in db
            $stmt = $this->pdo->prepare('INSERT INTO users (username, password, email) VALUES (?, ?, ?)');
            try {
                $result = $stmt->execute([$name, $hashed_pass, $email]);
                $user_id = $this->getUserId($email);
                $token = $this->generateJWT($email, $userdata['password'], $user_id);
                
                http_response_code(200); 
                $response = [ 'code' => 200, 'message' => 'User inserted successfully', 'jwt' => $token ];
            } catch (PDOException $e) {
                http_response_code(500); 
                $response = [ 'code' => 500, 'message' => 'Database error: ' . $e->getMessage() ];
            }
        }

        echo json_encode($response);
    }

    public function login ($userdata) {
        $correct_fields = isset($userdata['password'], $userdata['email']);

        if (!$this->validateUserdata($userdata) && $correct_fields) {
            http_response_code(400); 
            $response = [ 'code' => 400, 'message' => 'Invalid user data' ];
        } else {
            $email = $userdata['email'];
            if ($this->user_already_exist('email', $email)) {
                if ($this->verify_user($userdata)) {
                    $user_id = $this->getUserId($email);
                    $token = $this->generateJWT($userdata['email'], $userdata['password'], $user_id);
                    
                    http_response_code(200); 
                    $response = [ 'code' => 200, 'message' => 'User logged successfully', 'jwt' => $token ];
                } else {
                    http_response_code(400); 
                    $response = [ 'code' => 400, 'message' => 'Incorrect data while sign in'];
                }
            } else {
                http_response_code(500); 
                $response = [ 'code' => 500, 'message' => 'User does not exist!'];
            }
        }

        echo json_encode($response);
    }

    public function autologin($jwt) {
        // Add jwt validation
        $userdata = $this->parseJWT($jwt);
        if (is_null($userdata)) { echo json_encode([ 'code' => 400, 'message' => 'Incorrect jwt']);} 
        else { $this->login($userdata); }
    }

    public function get_username($id) {
        $stmt = $this->pdo->prepare('SELECT username FROM users WHERE id=?;');
        try {
            $stmt->execute([$id]);
            $username = $stmt->fetch(PDO::FETCH_ASSOC);
            $username = $username['username'];
            
            http_response_code(200); 
            $response = ['code' => 200, 'message' => 'Username fetched succesfully!', 'username' => $username];
        } catch (PDOException $e) {
            http_response_code(400); 
            $response = ['code' => 400, 'message' => 'Username not finded!', 'username' => null];
        }

        echo json_encode($response);
    }
}