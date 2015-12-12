<?php
function __autoload($class_name)
{
    include $class_name . ".php";
}

class updateTable extends database_manager
{
    private $userID;
    private $tableName;
    private $fileName;
    private $tempFile;
    private $FilePart;
    private $TempPart;

    function __construct()
    {
        $this->userID = trim(filter_input(INPUT_POST, "userID"));
        $this->tableName = trim(filter_input(INPUT_POST, "tableName"));
        $this->fileName = trim($_FILES["filUpload"]["name"]);
        $this->tempFile = trim($_FILES["filUpload"]["tmp_name"]);
        $this->FilePart = trim("datafile/" . $this->userID . "/");
        $this->TempPart = trim("temp/" . $this->userID . "/");

        $this->creatDir("datafile/" . $this->userID);
        $this->creatDir("temp/" . $this->userID);

    }

    function creatDir($part)
    {
        if (!file_exists(trim($part))) {
            @mkdir($part, 0777, true);
            @chmod($part, 0777);
        }
    }

    function upLoadFile()
    {
        if (move_uploaded_file($this->tempFile, $this->TempPart . $this->fileName)) {
            @chmod($this->TempPart . $this->fileName, 0777);
            $this->renameFile();
            $settresul = $this->settingTableARFF(); // create table
            $arr ["StatusID"] = "1";
            if ($settresul == "0")
                $arr ["Error"] = "";
            else $arr ["Error"] = $settresul;
        }else{
            $arr ["StatusID"] = "0";
            $arr ["Error"] = "Error upload file failed.";
        }
        echo json_encode($arr);
    }

    public function renameFile()
    { //rename file before move to datafile folder
        $tempname = explode('.', $this->fileName);
        $newname = "";
        foreach ($tempname as $k => $v) {
            if ($k < count($tempname) - 1) {
                $newname .= $v;
            }
        }
        $newname = str_replace("-", "_", $newname);
        $newname = $newname . date("ymdhis") . "." . $tempname [count($tempname) - 1]; //set new file name
        rename($this->TempPart . $this->fileName, $this->FilePart . $newname); // move file rename (old name , new name)
        $this->fileName = $newname;
    }

    public function settingTableARFF()
    { //setting table before create it
        $resultIns=0;
        if ($this->connection()) {
            $file = fopen($this->FilePart . $this->fileName, "r");
            if (!feof($file)) { //get atribute cout form file
                $idList=$this->addFileList($this->userID, $this->fileName, $this->tableName,$this->getTableType($this->tableName)); //serID , FileName , TableName , status(0=private,1=public)
                $resultIns = $this->insertFileData($this->tableName, $this->FilePart . $this->fileName,$idList); //add data in file to table
            }
            fclose($file);
        }
        return $resultIns;
    }

}

$up = new updateTable();
$up->upLoadFile();