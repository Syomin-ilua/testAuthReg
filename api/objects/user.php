<?php

    class User {
        // подключение к БД таблице "users"
        private $conn;
        private $table_name = "users";

        // свойство объекта
        public $id;
        public $firstname;
        public $lastname;
        public $patronymic;
        public $email;
        public $password;

        // конструктор класса User
        public function __construct($db) {
            $this->conn = $db;
        }

        // создание нового пользователя
        function create() {

            // запрос
            $query = "INSERT INTO " . $this->table_name . "
                SET
                    firstname = :firstname,
                    lastname = :lastname,
                    patronymic = :patronymic,
                    email = :email,
                    password = :password";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->firstname=htmlspecialchars(strip_tags($this->firstname)); 
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->patronymic=htmlspecialchars(strip_tags($this->patronymic));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));

        // привязывание значений
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":patronymic", $this->patronymic);
        $stmt->bindParam(":email", $this->email);

        // защита пароля(хеш пароля)
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);

        // выполнение запроса(если всё успешно, то инфа о пользователе будет сохранена в БД)
        if($stmt->execute()) {
            return true;
        }
        return false;

    }
    // метод emailExists
    function emailExists() {
        // запрос на проверку существует ли такая эл. почта
        $query = "SELECT id, firstname, lastname, patronymic, password 
        FROM " . $this->table_name . "
        WHERE email = ? 
        LIMIT 0,1";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция
        $this->email=htmlspecialchars(strip_tags($this->email));

        // привязывание значение e-mail
        $stmt->bindParam(1, $this->email);

        // выполнение запроса
        $stmt->execute();

        // получение кол-во строк
        $num = $stmt->rowCount();

        // если эл. почта существует, присвою значения свойствам объекта для легкого доступа и использования для php сессий 
        if($num > 0) {
            // получение значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // присвоение значения свойствам объекта
            $this->id = $row["id"];
            $this->firstname = $row["firstname"];
            $this->lastname = $row["lastname"];
            $this->patronymic = $row["patronymic"];
            $this->password = $row["password"];

            // вернётся true, потому что в базе данных существует эл. почта
            return true;
        }
        // вернётся false, потому что в эл. почты не существует в бд
        return false;
   
    }

    // Метод update()
    public function update() {
        // если в html-форме был введён пароль(необходимо обновить пароль)
        $password_set=!empty($this->password) ? ", password = :password" : "";


        // если не введён пароль - не обновлять пароль
        $query = "UPDATE " . $this->table_name . " 
            SET
                firstname = :firstname,
                lastname = :lastname,
                patronymic = :patronymic,
                email = :email
                {$password_set}
                WHERE id = :id";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // инъекция (очистка)
        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->patronymic=htmlspecialchars(strip_tags($this->patronymic));
        $this->email=htmlspecialchars(strip_tags($this->email));

        // привязываем значение с HTML формы
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":patronymic", $this->patronymic);
        $stmt->bindParam(":email", $this->email);

        // метод password_hash () для защиты пароля пользователя в бд
        if(!empty($this->password)) {
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $password_hash);
        }

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(":id", $this->id);

        // если выполнение успешно, то информация о пользователе будет сохранена в бд
        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>