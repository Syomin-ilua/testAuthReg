<?php 

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once "config/core.php";
include_once "../api/config/libs/php-jwt-master/BeforeValidException.php";
include_once "../api/config/libs/php-jwt-master/ExpiredException.php";
include_once "../api/config/libs/php-jwt-master/SignatureInvalidException.php";
include_once "../api/config/libs/php-jwt-master/JWT.php";
include_once "../api/config/libs/php-jwt-master/Key.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

include_once "./config/database.php";
include_once "./objects/user.php";

// получаю соединение с БД
$database = new Database();
$db = $database -> getConnection();

// создание объекта User
$user = new User($db); 
// получение данных
$data = json_decode(file_get_contents("php://input"));

// получение jwt
$jwt = isset($data->jwt) ? $data->jwt : "";

// если jwt не пуст
if($jwt) {
    // если декодирование выполнено успешно, показать данные пользователя
    try {
        // декодирование jwt
        $decoded = JWT::decode($jwt, new Key($key, "HS256"));

        // нужно установить отправленные данные (через форму HTML) в свойствах объекта пользователя
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->patronymic = $data->patronymic;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->id = $decoded->data->id;

        // создание пользваотеля 
        if ($user->update()) {
            // сгенирировать заново JWT здесь
            $token = array(
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "data" => array(
                    "id" => $user->id,
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "patronymic" => $user->patronymic,
                    "email" => $user->email
                )
                );
                $jwt = JWT::encode($token, $key, "HS256");

                // код ответа
                http_response_code(200);

                // ответ в формате JSON
                echo json_encode(
                    array(
                        "message" => "Пользователь был обновлён",
                        "jwt" => $jwt
                    )
                    );
        }
        // сообщение, если не удаётся обновить пользователя
        else {
            // код ответа
            http_response_code(401);

            // показать сообщение об ошибке
            echo json_encode(array("message" => "Невозможно обновить пользователя"));
        }
    }

    // если декодирование не удалось, это означает, что JWT является недействительным
    catch(Exception $e) {
        // Код ответа
        http_response_code(401);

        // сообщение об ошибке 
        echo json_encode(array(
            "message" => "Доступ закрыт",
            "error" => $e -> getMessage()
        ));
    }
}




?>