<?php

function __autoload($class_name) {
    include $class_name . '.php';
}

class checkUser extends database_manager {

    private $username;
    private $password;
    private $JsonVal;

    function __construct() {
        $data = json_decode(file_get_contents('php://input'), true)['LoginReq'];
        $this->username = json_decode($data,true)['username'];
        $this->password = md5(json_decode($data,true)['password']);
        if ($this->connection()) {
            $sql = "SELECT * FROM customer_table where cus_user= BINARY '" . mysql_real_escape_string($this->username) . "' AND cus_pass= BINARY '" . mysql_real_escape_string($this->password) . "' ";
            $query = mysql_query($sql);
            if ($row = mysql_fetch_array($query)) {
                $this->JsonVal["username"] = $row["cus_user"];
                $this->JsonVal["id"] = $row["cus_id"];
                $this->JsonVal["email"] = $row["cus_email"];
                $this->JsonVal["stulog"] = "true";
            } else {
                $this->JsonVal["stulog"] = "false";
            }
        }
        echo json_encode($this->JsonVal);
    }

}

new checkUser ();
