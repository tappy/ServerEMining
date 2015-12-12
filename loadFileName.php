<?php
function __autoload($class_name)
{
    include $class_name . '.php';
}

class loadFileName extends database_manager
{
    private $tableName;
    private $userName;
    function __construct()
    {
        $data = json_decode(file_get_contents("php://input"), true)['LoadFilesNameReq'];
        $this->userName= json_decode($data, true)["userID"];
        $this->tableName= json_decode($data, true)["tableName"];
        if($this->connection()){
            $this->loadNow();
        }
    }

    function loadNow()
    {
        $sql="SELECT file_name,id_upload FROM uploadList WHERE user_id='".$this->userName."' AND table_name='".$this->tableName."'";
        $q=mysql_query($sql);
        $i=0;
        $val=array();
        while($row=mysql_fetch_assoc($q)){
            $val[$i]=$row;
            $i++;
        }
    echo json_encode($val);
    }

}
new loadFileName();