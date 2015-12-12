<?php
error_reporting(0);
function __autoload($class_name) {
    include $class_name . '.php';
}

class deleteUploadList extends database_manager {

    private $uploadTable;
    private $fileName;
    private $listID;
    private $filepart;
    private $userID;
    public function __construct() {
        $data = json_decode(file_get_contents("php://input"), true)['DeleteTableReq'];
        $this->uploadTable = json_decode($data, true)["uploadTable"]; //0=POST 1=GET
        $this->fileName = json_decode($data, true)["fileName"];
        $this->listID = json_decode($data, true)["idUpload"];
        $this->userID= json_decode($data, true)["userid"];
        $this->filepart="datafile/".$this->userID."/";
    }

    function deleteNow() {
        //echo "1";
        $value=array();
            if ($this->connection()) {
                if($this->getTableCountList($this->uploadTable)>1){
                    if ($this->removeUpdate()) {
                        mysql_query("DELETE FROM uploadList WHERE id_upload=".$this->listID." ");
                        $value["status"] = "1";
                        $value["drop"]="0";
                        $this->dropFile();
                    } else {
                        $value["status"] = "0";
                        $value["drop"]="0";
                    }
                }else{
                    if($this->dropTable($this->uploadTable)){
                        mysql_query("DELETE FROM uploadList WHERE id_upload=".$this->listID." ");
                        $value["status"] = "1";
                        $value["drop"]="1";
                        $this->dropFile();
                    }else{
                        $value["status"] = "0";
                        $value["drop"]="0";
                    }
                }
            }

        echo json_encode($value);
    }

    function removeUpdate(){
            if($this->deleteData($this->uploadTable,"uploadID",$this->listID)){
             if($this->getRowCount($this->uploadTable)==0){
                 $this->dropTable($this->uploadTable);
             }
                return TRUE;
            }else{
                return FALSE;
            }
    }

    function dropFile() {
        if (@unlink($this->filepart . $this->fileName)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

$delList = new deleteUploadList();
$delList->deleteNow();
