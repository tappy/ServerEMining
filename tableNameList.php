<?php

function __autoload($class_name) {
    include $class_name . '.php';
}

class tableNameList extends database_manager {

    private $result, $numRow, $row, $value;
    private $user_id;

    function __construct() {
        $this->user_id = filter_input(0, "user_id");
        //$this->user_id = 9;
    }

    function doList() {
        if ($this->connection()) {
            $this->result = mysql_query("SELECT DISTINCT table_name FROM `uploadList` WHERE user_id='" . $this->user_id."' ORDER BY id_upload desc ");
            $this->numRow = mysql_num_rows($this->result);
            if ($this->numRow >0) {
                while ($this->row = mysql_fetch_array($this->result)) {
                    $this->value[] = $this->row;
                }
                echo json_encode($this->value);
            } else {
                $this->value["table_name"]="0";
                echo "[".json_encode($this->value)."]";
            }
        }
    }

}

$excute = new tableNameList();
$excute->doList();
