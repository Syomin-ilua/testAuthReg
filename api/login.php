<?php
header("Access-Control-Allow-Origin: http://get/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// соединение с БД
include_once "config/database.php";
include_once "objects/user.php";

// получение соединения с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта "User"
$user = new User($db);

// получение данных
$data = json_decode(file_get_contents("php://input"));

$user->email = $data->email;
$email_exists = $user->emailExists();


// подключение файлов jwt
include_once "config/core.php";
include_once "../api/config/libs/php-jwt-master/BeforeValidException.php";
include_once "../api/config/libs/php-jwt-master/ExpiredException.php";
include_once "../api/config/libs/php-jwt-master/SignatureInvalidException.php";
include_once "../api/config/libs/php-jwt-master/JWT.php";

use \Firebase\JWT\JWT;

// существует ли электронная почта и соответствует ли пароль тому, что находтся в базе данных

if($email_exists && password_verify($data->password, $user->password)) {
    $token = array(
       "iss" => $iss,
       "aud" => $aud,
       "iat" => $iat,
       "nbf" => $nbf,
       "data" => array(
        "id"=>$user->id,
        "firstname" => $user->firstname,
        "lastname" => $user->lastname,
        "patronymic" => $user->patronymic,
        "email" => $user->email
       )
       );

    //    код ответа 
    http_response_code(200);

    // создание jwt
    $jwt = JWT::encode($token, $key, "HS256");
    echo json_encode(
        array(
            "message" => "Успешный вход в систему.",
            "jwt" => $jwt
        )
        );
} 
// Если электронная почта не существует или пароль не совпадает,
// сообщим пользователю, что он не может войти в систему
else {
    // код ответа
    http_response_code(401);

    // сказать пользователю что войти не удалось
    echo json_encode(array("message" => "Ошибка входа."));
}

?>