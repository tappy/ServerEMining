<?php
error_reporting(0);
function __autoload($class_name) {
    include $class_name . '.php';
}

class loadListTable extends database_manager {

    private $userID;

    function loadData() {
        $data = file_get_contents(json_decode("php://input", true))['TableLoaderReq'];
        $this->userID = json_decode(data, true)['userId'];
        //$this->userID =9;
        $val = null;
        if ($this->connection()) {
            $q = mysql_query("SELECT DISTINCT table_name FROM `uploadList` WHERE user_id=" . $this->userID . " ORDER BY id_upload desc ");
            $rowCount = mysql_num_rows($q);
            if ($rowCount > 0) {
                $i=0;
                echo "[";
                while ($row = mysql_fetch_assoc($q)) {
                    $val["table_name"] = $row["table_name"];
                    $m = mysql_query("SELECT * FROM `uploadList` WHERE table_name='" . $row["table_name"] . "'");
                    $num = mysql_num_rows($m);
                    $val["num_row"] = $num;
                    echo json_encode($val);
                    if($i<$rowCount-1){
                        echo",";
                         $i++;
                    }
                }
                echo "]";
            }else{
              echo"false";
            }
        }
    }

}

$llt = new loadListTable();
$llt->loadData();
