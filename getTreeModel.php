<?php
error_reporting(0);
class getTreeModel {

    private $data;
    private $userid;
    private $param;
    private $part;

    function __construct()
    {
        $this->userid = filter_input(INPUT_POST, "userid");
        $this->param = filter_input(INPUT_POST, "param");
        $this->part = 'modelfile/' . $this->userid . '/';
        $this->loadDataByParam($this->param);
    }

    function loadDataByParam($myparam)
    {
        $this->getContent();
        if ($myparam == "summary") {
            $this->getSummary();
        } else if ($myparam == "body") {
            $this->getBody();
        }else if ($myparam == 'leave'){

        }else if($myparam == 'full_data'){
           $this->getFullData();
        }
    }

    function getFullData()
    {
        echo json_encode($this->data);
    }

    function getLeaveVal(){
        $val = array();
        for ($i = $this->getLast($this->data); $i < sizeof($this->data); $i++) {
            if (trim(split(":",$this->data[$i])[0]) == "Number of Leaves") {
                $val[] = $this->data[$i];
            }
        }
        echo json_encode($val);
    }

    private function getContent()
    {
        $hfile = fopen($this->part . "temp_model.txt", "r");
        while (!feof($hfile)) {
            $this->data [] = fgets($hfile);
        }
        fclose($hfile);
    }

    function getSummary()
    {
        $val = array();
        for ($i = $this->getLast($this->data); $i < sizeof($this->data); $i++) {
            if (trim($this->data[$i]) != "") {
                $val[] = $this->data[$i];
            }
        }
        echo json_encode($val);
    }

    private function getBody(){
        $val = array();
        for ($i = 6; $i < $this->getLast($this->data); $i++) {
            if (trim($this->data[$i]) != "") {
                $val[] = $this->data[$i];
            }
        }
        echo json_encode($val);
    }

    function getLast($sdata)
    {
        $last = 0;
        foreach ($sdata as $key => $value) {
            if (str_split(trim($value),16)[0]=="Number of Leaves") {
                $last = $key;
            }
        }
        return $last;
    }

}
$tree=new getTreeModel();