<?php

include './vendor/autoload.php';

use \Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../.env" );
$dotenv->safeLoad(); 

class AuthController
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function create(string $method, array $data): void
    {
        if ($method != "POST") {
       
            echo json_encode([
                'status' => 0,
                'message' => 'Access Denied',
            ]);
           
            exit;
        }

        $coop_exist = $this->verify_user($data["email"], $data["phone"]);

        if ($coop_exist) {
            echo json_encode([
                'status' => 0,
                'message' => 'User Already Exist',
            ]);
            exit;
        }

        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, email, phone, account_number, how_did_your_hear, password)
                VALUES (:first_name, :last_name, :email, :phone, :account_number, :how_did_your_hear, :password)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":first_name", $data["first_name"], PDO::PARAM_STR);
        $stmt->bindValue(":last_name", $data["last_name"], PDO::PARAM_STR);
        $stmt->bindValue(":email", $data["email"], PDO::PARAM_STR);
        $stmt->bindValue(":phone", $data["phone"], PDO::PARAM_STR);
        $stmt->bindValue(":account_number", $data["phone"], PDO::PARAM_STR);
        $stmt->bindValue(":how_did_your_hear", $data["how_did_your_hear"] ?? "", PDO::PARAM_STR);
        $stmt->bindValue(":password", $password, PDO::PARAM_STR);

        $stmt->execute();

        echo json_encode([
            'status' => 1,
            'id' => $this->conn->lastInsertId(),
            'message' => 'Account Created Successfully',
        ]);
        exit;
    }


    public function verify_user(string $email, string $phone = null): array | false
    {
        $sql = "SELECT *
                FROM users
                WHERE email = :email OR phone = :phone";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":phone", $phone, PDO::PARAM_STR);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data == false) {
            return false;
        }

        return $data;
    }


    public function login(string $method, array $data): void
    {

        if ($method == "POST") {

            $email = htmlentities($data["email"]);
            $password = htmlentities($data["password"]);


            $result = $this->verify_user($email);

            if (!$result) {

                echo json_encode([
                    'status' => 0,
                    'message' => 'Invalid Credential',
                ]);
                exit;
            }

            if (!password_verify($password, $result['password'])) {

                echo json_encode([
                    'status' => 0,
                    'message' => 'Invalid Credential',
                ]);
                exit;
            }

            $payload = [
                'iss' => "localhost",
                'aud' => 'localhost',
                'exp' => time() + 24 * 60 * 60, //24 hour 
                'data' => [
                    'id' => $result["id"],
                    'name' => $result["account_number"],
                    'email' => $result["email"],
                ],
            ];

            $secret_key = $_ENV["JWT_SECRET_KEY"];
            $token = JWT::encode($payload, $secret_key, 'HS256');

            echo json_encode([
                'status' => 1,
                'token' => $token,
                'user_id' => $result["id"],
                'result' => $result,
                'message' => 'Login Successfully',
            ]);
            exit;
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Access Denied',
            ]);
            // http_response_code(405);
            // header("Allow: POST");
            exit;
        }
    }
}
