<?php

declare(strict_types=1);

header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");


spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});


include './vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad(); 


set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");


$parts = explode("/", $_SERVER["REQUEST_URI"]);

$part = $parts[3];


if ($part != "login" && $part != "signup") {
    try {
        $allheaders = getallheaders();
        $jwt = $allheaders['Authorization'] ?? $allheaders['authorization'] ?? null;
        $secret_key = $_ENV["JWT_SECRET_KEY"];
        $coop_data = JWT::decode($jwt, new Key($secret_key, 'HS256'));

        $coop_id = (int) $coop_data->data->id;
    } catch (Throwable $th) {
        echo json_encode([
            'status' => 0,
            'type' => 'JWT',
            'message' => $th->getMessage(),
        ]);

        exit;
    }
}

$id = $parts[4] ?? null;

$database = new Database("localhost", $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PWD"]);

$auth_controller = new AuthController($database);


switch ($part) {
    case 'signup':
        $data =  (array) json_decode(file_get_contents("php://input"), true);
        $auth_controller->create($_SERVER["REQUEST_METHOD"], $data);
        break;
    case 'login':
        $data =  (array) json_decode(file_get_contents("php://input"), true);
        $auth_controller->login($_SERVER["REQUEST_METHOD"], $data);
        exit;
        break;
    case 'users':
        $gateway = new MemberGateway($database);
        $method = $_SERVER["REQUEST_METHOD"];
        switch ($id) {
            case 'credit':
                $data =  (array) json_decode(file_get_contents("php://input"), true);
                $gateway->credit_member($method, $data, $coop_id);
                exit;
                break;
            case 'debit':
                $data =  (array) json_decode(file_get_contents("php://input"), true);
                $gateway->debit_member($method, $data, $coop_id);
                exit;
                break;
            case 'history':
                $data =  (array) json_decode(file_get_contents("php://input"), true);
                $gateway->history($method, $coop_id);
                exit;
                break;
            case 'image_upload':
                $data =  (array) json_decode(file_get_contents("php://input"), true);
                $gateway->image_upload($method, $_POST, $_FILES);
                exit;
                break;
            default:
                $controller = new MemberController($gateway);
                $controller->processRequest($method, $coop_id);
                exit;
                break;
        }
   

    default:
        http_response_code(404);
        exit;
        break;
}
