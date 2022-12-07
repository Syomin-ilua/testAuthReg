<?php

// заголовок
header("Access-Control-Allow-Origin: http://get/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// требуется для декодирования 
include_once "config/core.php";
include_once "../api/config/libs/php-jwt-master/BeforeValidException.php";
include_once "../api/config/libs/php-jwt-master/ExpiredException.php";
include_once "../api/config/libs/php-jwt-master/SignatureInvalidException.php";
include_once "../api/config/libs/php-jwt-master/JWT.php";
include_once "../api/config/libs/php-jwt-master/Key.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key; 

// получение значения веб-токена JSON
$data = json_decode(file_get_contents("php://input"));

// получение JWT 
$jwt = isset($data->jwt) ? $data->jwt : "";

// если JWT не пуст
if($jwt) {
    
    // если декодирование выполнено успешно, показать данные пользователя
    try {
        // декодирование jwt
        $decoded = JWT::decode($jwt, new Key($key, "HS256"));

        // код ответа
        http_response_code(200);

        // показать детали
        echo json_encode(array(
            "message" => "Доступ разрешён.",
            "data" => $decoded->data
        ));
    }

    // если декодирование не удалось, это означает, что JWT является недействительным 
    catch (Exception $e) {
        // код ответа
        http_response_code(401);

        // сообщить пользователю отказано в доступе и показать сообщение об ошибке
        echo json_encode(array(
            "message" => "Доступ закрыт.",
            "error" => $e -> getMessage()
        ));
    }
}
// показать сообщение об ошибке, если jwt пуст
else {
    // код ответа 
    http_response_code(401);

    // сообщить пользователю что доступ запрещён
    echo json_encode(array("message" => "Доступ запрещён."));
}
?>