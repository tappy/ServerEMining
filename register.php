<?php
function __autoload($class_name) {
	include $class_name . '.php';
}
class register extends database_manager {
        private $data;
        
        function __construct() {
            $this->data = json_decode(file_get_contents('php://input'), true)['RegisterReq'];
        }
                
	function getUser() {
		return json_decode($this->data, true)['user'];
	}
	function getPass() {
		return md5 ( json_decode($this->data, true)['password'] );
	}
	function getEmail() {
		return json_decode($this->data, true)['email'];
	}
	function saveData() {
		if ($this->connection ()) {
			$quser = mysql_query ( "select * from `customer_table` where cus_user='" . $this->getUser () . "' OR cus_email='".$this->getEmail()."' " );
			if (mysql_numrows ( $quser ) == 0) {
				$query = mysql_query ( "insert into customer_table values(NULL,'" . $this->getUser () . "','" . $this->getPass () . "','" . $this->getEmail () . "')" );
				if ($query) {
					$result ['status'] = "1";
				} else {
					$result ['status'] = "0";
				}
			} else {
                $row=mysql_fetch_array($quser);
                if($row['cus_user']==$this->getUser()&&$row['cus_email']==$this->getEmail()){
                    $result ['status'] = "4";
                }elseif($row['cus_user']==$this->getUser()){
                    $result ['status'] = "2";
                }elseif($row['cus_email']==$this->getEmail()){
                    $result ['status'] = "3";
                }else{
                    $result ['status'] = "0";
                }
			}
		} else {
			$result ['status'] = "0";
		}
		echo json_encode ( $result );
	}
}
$r = new register ();
$r->saveData ();
