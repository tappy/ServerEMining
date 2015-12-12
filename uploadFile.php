<?php

//error_reporting ( 0 );
function __autoload($class_name)
{
    include $class_name . '.php';
}

class uploadFile extends database_manager
{

    private $fileName;
    private $tempName;
    private $filePart;
    private $tempPart;
    private $userID;
    private $data;
    private $json;
    //Getter
    private function getTempName()
    {
        return $this->tempName;
    }

    private function getFileName()
    {
        return $this->fileName;
    }

    private function getFilePart()
    {
        return $this->filePart;
    }

    private function getTempPart()
    {
        return $this->tempPart;
    }

    private function getUserID()
    {
        return $this->userID;
    }

    //setter
    private function setFileName($name)
    {
        $this->fileName = $name;
    }

    private function setTempName($tempname)
    {
        $this->tempName = $tempname;
    }

    private function setFilePart($part)
    {
        $this->filePart = $part;
    }

    private function setTempPart($part)
    {
        $this->tempPart = $part;
    }

    private function setUserID($part)
    {
        $this->userID = $part;
    }

    function __construct()
    {
        $this->setFileName(trim($_FILES ["filUpload"] ["name"]));
        $this->setTempName(trim($_FILES ["filUpload"] ["tmp_name"]));
        $this->setUserID(trim(filter_input(INPUT_POST, "userId")));
        $this->setFilePart("datafile/" . trim($this->getUserID()) . "/");
        $this->setTempPart("temp/" . trim($this->getUserID()) . "/");

        $this->creatDir("datafile/" . $this->getUserID());
        $this->creatDir("temp/" . $this->getUserID());
    }


    function creatDir($part)
    {
        if (!file_exists(trim($part))) {
            @mkdir($part, 0777, true);
            chmod($part, 0777);
        }
    }

    public function upLoadFileNow()
    { //main function upload file
        if (move_uploaded_file($this->getTempName(), $this->getTempPart() . $this->getFileName())) { //upload file to temp folder
            chmod($this->getTempPart() . $this->getFileName(), 0777);
            $this->renameFile();
            $settresul = $this->settingTableARFF(); // create table
            if ($settresul == "0"){
                $arr ["StatusID"] = "1";
                $arr ["Error"] = "";
            }else {
                $arr ["StatusID"] = "0";
                $arr ["Error"] = $settresul;
            }
        } else {
            $arr ["StatusID"] = "0";
            $arr ["Error"] = "Error upload file failed.";
        }
        echo json_encode($arr);
    }

    public function renameFile()
    { //rename file before move to datafile folder
        $tempname = explode('.', $this->getFileName());
        $newname = "";
        foreach ($tempname as $k => $v) {
            if ($k < count($tempname) - 1) {
                $newname .= $v;
            }
        }
        $newname = str_replace("-", "_", $newname);
        $newname = $newname . date("ymdhis") . "." . $tempname [count($tempname) - 1]; //set new file name
        rename($this->getTempPart() . $this->getFileName(), $this->getFilePart() . $newname); // move file rename (old name , new name)
        $this->setFileName($newname);
    }

    public function settingTableARFF()
    { //setting table before create
        if ($this->connection()) {
            $file = fopen($this->getFilePart() . $this->getFileName(), "r");
            if (!feof($file)) { //get atribute cout form file
                $this->data = explode(',', fgets($file));
                $fname = explode('.', $this->getFileName()); //split filename
                if (strtolower($fname[1]) == "arff") {
                    $idList = $this->addFileList($this->getUserID(), $this->getFileName(), $fname [0],0); //0=arff
                    $value = $this->addField(strtolower($fname[1]));
                    $comment = $this->addComment();
                    $this->creatTable($fname [0], $value, $comment); //create table(tablename,attribute,$comment)
                } else if (strtolower($fname[1]) == "csv") {
                    $idList = $this->addFileList($this->getUserID(), $this->getFileName(), $fname [0],1);//1=csv
                    $value = $this->addFieldCSV(strtolower($fname[1]));
                    $comment = $this->addCommentCSV();
                    $this->creatTable($fname [0], $value ,$comment);
                }
                $resultIns = $this->insertFileData($fname [0], $this->getFilePart() . $this->getFileName(), $idList); //add data in file to table
                return $resultIns;
            } else {
                return false;
            }
            fclose($file);
        }
    }

    function addComment()
    {
        $comment = null;
        $gcomm = fopen($this->getFilePart() . $this->getFileName(), "r");
        $line = null;
        while (!feof($gcomm)) {
            $line = trim(fgets($gcomm));
                if (strtolower(str_split($line, 10)[0]) == "@attribute") {
                    $comment[] = mysql_real_escape_string($line);
                }
        }
        return $comment;
    }

    function addCommentCSV()
    {
        $comment = null;
        $gcomm = fopen($this->getFilePart() . $this->getFileName(), "r");
        $line = null;
        while (!feof($gcomm)) {
            $line = trim(fgets($gcomm));
            if($line!=""){
                if (trim(str_split($line, 10)[0]) != "@" && trim(str_split($line, 10)[0]) != "%") {
                    $comment = explode(",",$line);
                    break;
                }
            }
        }
        return $comment;
    }

    function addField($filetype)
    {
        $value = array();
        $line = null;
        if ($filetype == "arff") {
            $value = $this->addfieldARFF();
        }
        elseif ($filetype == "csv") {
            $value = $this->addfieldCSV();
        }
        return $value;
    }

    function addfieldARFF()
    {
        $gcomm = fopen($this->getFilePart() . $this->getFileName(), "r");
        $value = array();
        $i = 0;
        while (!feof($gcomm)) {
            $line = fgets($gcomm);
            if(trim($line)!=""){
                if (strtolower(str_split($line, 10)[0]) == "@attribute") {
                    $value[] = "attribute" . $i;
                    $i++;
                }
            }
        }
        fclose($gcomm);
        return $value;
    }

    function addfieldCSV()
    {
        $gcomm = fopen($this->getFilePart() . $this->getFileName(), "r");
        $value = array();
        while (!feof($gcomm)) {
            $line = fgets($gcomm);
            if (trim($line) != "") {
                foreach (explode(",", $line) as $k => $v) {
                    $value[] = "attribute" . $k;
                }
                break;
            }
        }
        fclose($gcomm);
        return $value;
    }

}

$up = new uploadFile();
$up->upLoadFileNow();
