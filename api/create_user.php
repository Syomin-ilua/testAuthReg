<?php

header("Access-Control-Allow-Origin: http://get/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// подключение к БД
include_once "config/database.php";
include_once "objects/user.php";


// соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта "User"
$user = new User($db);

// отправляемые данные
$data = json_decode(file_get_contents("php://input"));

// установление значений
$user->firstname = $data->firstname;
$user->lastname = $data->lastname;
$user->patronymic = $data->patronymic;
$user->email = $data->email;
$user->password = $data->password;

// создание пользователя
if(
    !empty($user->firstname) &&
    !empty($user->email) &&
    !empty($user->password) &&
    $user->create()
) {
    // код ответа
    http_response_code(200);

    // сообщение о том, что пользователь был создан
    echo json_encode(array("message" => "Пользователь был создан."));
}
// если не удаётся создать пользователя 
else {
    // код ответа
    http_response_code(400);

    // сообщение о том, что создать пользователя не удалось 
    echo json_encode(array("message" => "Невозможно создать пользователя."));
}
?>