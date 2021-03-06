<?php

function __autoload($className)
{
    include $className . ".php";
}

class updateAccount extends database_manager
{

    private $updateType;
    private $userID;
    private $resultVal;

    function __construct()
    {
        $this->updateType = filter_input(INPUT_POST, "updateType");
        $this->userID = filter_input(INPUT_POST, "userID");
        $this->updateNow($this->updateType);
    }

    function updateNow($type)
    {
        if($this->connection()) {

            switch ($type) {

                case 0: {
                    $newUserEmail = filter_input(INPUT_POST, "userEmail");
                    if ($this->updateEmail($newUserEmail)) {
                        $this->resultVal["result"] = 1;
                        $this->resultVal["type"] = 0;
                        $this->resultVal["userEmail"] = $newUserEmail;
                    } else {
                        $this->resultVal["result"] = 0;
                        $this->resultVal["type"] = 0;
                    }
                };
                    break;
                case 1: {
                    $newUserName = filter_input(INPUT_POST, "userName");
                    if ($this->updateUserName($newUserName)) {
                        $this->resultVal["result"] = 1;
                        $this->resultVal["type"] = 1;
                        $this->resultVal["userName"] = $newUserName;
                    } else {
                        $this->resultVal["result"] = 0;
                        $this->resultVal["type"] = 1;
                    }
                };
                    break;
                case 2: {
                    $oldUserPassword = filter_input(INPUT_POST, "oldUserPassword");
                    $newUserPassword = filter_input(INPUT_POST, "newUserPassword");
                    if ($this->updatePassword($oldUserPassword, $newUserPassword)) {
                        $this->resultVal["result"] = 1;
                        $this->resultVal["type"] = 2;
                        $this->resultVal["newUserPassword"] = $newUserPassword;
                    } else {
                        $this->resultVal["result"] = 0;
                        $this->resultVal["type"] = 2;
                    }
                };
                    break;
            }
            echo json_encode($this->resultVal);
        }
    }

    function checkExist($attr,$value){
        $quser = mysql_query ( "select * from `customer_table` where ".$attr."='" . $value . "'" );
        $numrow=mysql_num_rows($quser);
        if($numrow>0){
        return true;
        }else{
        return false;
        }
    }

    function updateEmail($newUserEmail)
    {
        if(!$this->checkExist("cus_email",$newUserEmail)){
            $str = "UPDATE `EMining`.`customer_table` SET `cus_email` = '".$newUserEmail."' WHERE `customer_table`.`cus_id` = ".$this->userID.";";
            $result = mysql_query($str);
            return $result;
        }else{
            return false;
        }
    }

    function updateUserName($newUserName)
    {
        if(!$this->checkExist("cus_user",$newUserName)) {
            $str = "UPDATE `EMining`.`customer_table` SET `cus_user` = '" . $newUserName . "' WHERE `customer_table`.`cus_id` = " . $this->userID . " ;";
            $result = mysql_query($str);
            return $result;
        }else{
            return false;
        }
    }

    function updatePassword($oldUserPassword, $newUserPassword)
    {
        if ($this->checkExistUserByID($this->userID, $oldUserPassword)) {
            $oldpass = md5(mysql_real_escape_string($oldUserPassword));
            $npass = md5(mysql_real_escape_string($newUserPassword));
            $str = "UPDATE `EMining`.`customer_table` SET `cus_pass` = '".$npass."' WHERE `customer_table`.`cus_pass` ='".$oldpass."'   AND `customer_table`.`cus_id` = ".$this->userID." ;";
            $result = mysql_query($str);
            return $result;
        } else {
            return false;
        }
    }

}

new updateAccount();