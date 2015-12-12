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
        $this->uploadTable = filter_input(INPUT_POST, "uploadTable"); //0=POST 1=GET
        $this->fileName = filter_input(INPUT_POST, "fileName");
        $this->listID = filter_input(INPUT_POST, "id_upload");
        $this->userID= filter_input(INPUT_POST,"userid");
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
