<?php

class database_manager
{

    public $host = "localhost"; // กำหนดชื่อ host
    public $dbusr = "root"; // กำหนดชื่อผู้ใช้
    public $dbpass = "1234"; // กำหนดรหัสผ่าน
    public $charset = "utf-8";
    public $dbname = "EMining"; // กำหนดชื่อ Database
    public $File;
    public $Query;
    public $con;

    public function connection()
    {
        $this->con = mysql_connect($this->host, $this->dbusr, $this->dbpass);
        mysql_select_db($this->dbname);
        mysql_set_charset($this->charset);
        return $this->con;
    }

    function close_mysql()
    {
        mysql_close($this->con);
    }

    public function addFileList($userID, $FileName, $TableName, $table_type)
    {
        $idList = 0;
        if ($this->connection()) {
            $StringQuery = "INSERT INTO `uploadList`(`id_upload`, `user_id`, `table_name`, `file_name`,`table_type`) ";
            $StringQuery .= "VALUES (NULL,'" . $userID . "','" . $TableName . "','" . $FileName . "'," . $table_type . ")"; //table_type 0=arff 1=csv
            if (mysql_query($StringQuery)) {
                $strQuery = "SELECT `id_upload` FROM `uploadList` WHERE `file_name` = '" . $FileName . "' AND `table_name`='" . $TableName . "' ;";
                $q = mysql_query($strQuery);
                if ($row = mysql_fetch_array($q)) {
                    $idList = $row['id_upload'];
                }
            }
            return $idList;
        } else {
            return $idList;
        }
    }

    public function creatTable($tableName, $fieldName, $comment = null)
    { // creat table form file
        if ($this->connection()) {
            $strQuery = " CREATE TABLE " . $tableName . " (
			tableID INT(6)  AUTO_INCREMENT PRIMARY KEY ,
			uploadID INT(6) , ";
            if ($comment != null) {
                $strQuery .= $this->loopARFF($fieldName, $comment);
            } else {
                $strQuery .= $this->loopCSV($fieldName, $comment);
            }
            $strQuery .= ")";
            return mysql_query($strQuery);
        } else {
            return false;
        }
    }

    function loopARFF($fieldName, $comment)
    {
        $strQuery = "";
        foreach ($fieldName as $key => $value) {
            $strQuery .= $value . " VARCHAR(255) COMMENT ' " . $comment[$key] . " ' ";
            if ($key < count($fieldName) - 1) {
                $strQuery .= " , ";
            }
        }
        return $strQuery;
    }

    function loopCSV($fieldName, $comment)
    {
        $strQuery = "";
        foreach ($fieldName as $key => $value) {
            $strQuery .= $value . " VARCHAR(255) COMMENT ' " . mysql_real_escape_string($comment[$key]) . " ' ";
            if ($key < count($fieldName) - 1) {
                $strQuery .= " , ";
            }
        }
        return $strQuery;
    }


    function deleteData($TableName, $ARG, $ARGVAL)
    {
        $command = "DELETE FROM " . $TableName . " WHERE " . $ARG . "='" . $ARGVAL . "'";
        if (mysql_query($command)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getTableType($table)
    {
        if ($row = mysql_fetch_array($this->selecUploadtList($table))) {
            return $row ["table_type"];
        } else {
            return 0;
        }
    }

    function selecUploadtList($table)
    {
        $query = mysql_query("select * from uploadList where table_name='" . $table . "'");
        return $query;
    }

    public function insertFileData($tableName, $partFile, $uploadID)
    {
        try {
            $mydata = $this->streamFile($partFile);
            $sql = "INSERT INTO " . $tableName . " VALUES ";
            foreach ($mydata as $k => $v) {
                $sql .= "(NULL," . $uploadID . ",";
                $val = explode(",", $v);
                foreach ($val as $i => $j) {
                    $sql .= "'" . mysql_real_escape_string($j) . "'";
                    if ($i < count($val) - 1) $sql .= ",";
                }
                $sql .= ")";
                if ($k < count($mydata) - 1) {
                    $sql .= ",";
                }
            }
            $sql .= ";";
            if (mysql_query($sql)) {
                return "0";
            }else{
                return mysql_error();
            }
        } catch (Exception $e) {
            return "Error :".$e;
        }
    }

    function streamFile($partFile)
    {
        $data = array();
        $this->File = fopen($partFile, "r");
        $filetype = explode(".", basename($partFile));
        if (strtolower($filetype[1]) == "arff") {
            $data = $this->streamARFF();
        } else if (strtolower($filetype[1]) == "csv")
            $data = $this->streamCSV();
        fclose($this->File);
        return $data;
    }

    function streamARFF()
    {
        $data = array();
        while (!feof($this->File)) {
            $read = trim(fgets($this->File));
            if($read!=""){
                if ($read[0] != "@" && $read[0] != "%") {
                    $data[] = $read;
                }
            }
        }
        return $data;
    }

    function streamCSV()
    {
        $i = 0;
        $data = array();
        while (!feof($this->File)) {
            $read = trim(fgets($this->File));
            if ($read != "") {
                if ($i > 0) $data[] = $read;
                $i++;
            }
        }
        return $data;
    }

    function getRowCount($table)
    {
        $numrow = 0;
        if ($this->connection()) {
            $q = mysql_query("SELECT * FROM " . $table);
            $numrow = mysql_num_rows($q);
        }
        return $numrow;
    }

    function getColCount($table)
    {
        $numrow = 0;
        if ($this->connection()) {
            $q = mysql_query("SELECT * FROM " . $table);
            $numrow = mysql_num_fields($q);
        }
        return $numrow;
    }

    function  getTableCountList($table)
    {
        $numrow = 0;
        if ($this->connection()) {
            $q = mysql_query("SELECT * FROM uploadList WHERE table_name='" . $table . "'");
            $numrow = mysql_num_rows($q);
        }
        return $numrow;
    }

    function dropTable($tableName)
    {
        $result = mysql_query("SHOW TABLES LIKE '" . $tableName . "'");
        if (mysql_num_rows($result) > 0) {
            $drop = mysql_query("DROP TABLE " . $tableName);
            if ($drop) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }

    function checkExistUserByID($userID, $oldUserPass)
    {
        $str = "SELECT * FROM customer_table WHERE cus_id=" . $userID . " AND cus_pass='" . md5(mysql_real_escape_string($oldUserPass)) . "' ";
        $q = mysql_query($str);
        if (mysql_num_rows($q) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function updatePass($email, $newPass)
    {
        $str = "UPDATE `customer_table` SET `cus_pass`='".mysql_real_escape_string(md5($newPass))."' WHERE `cus_email`='".$email."'";
        $q = mysql_query($str);
        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    function find($table,$selectfield,$whereCouse,$whereCouseVal){
            $val=array();
            $str="SELECT ".$selectfield." FROM ".$table." WHERE ".$whereCouse." = ".$whereCouseVal;
            $query=mysql_query($str);
                while($row=mysql_fetch_array($query)){
                    $val[]=$row;
                }
                return $val;
        }

}
