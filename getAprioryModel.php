<?php

class getAprioryModel {

    private $data;
    private $userid;
    private $param;
    private $part;
    private $json;

    function __construct() {
        $this->json = json_decode(json_decode(file_get_contents("php://input"), true)['GetApioriModelReq'], true);
        $this->userid = $this->json["userid"];
        $this->param = $this->json["param"];
        $this->part = 'modelfile/' . $this->userid . '/';
        $this->loadDataByParam($this->param);
    }

    function loadDataByParam($myparam) {
        $this->getContent();
        if ($myparam == "summary") {
            $this->getSummary();
        } else if ($myparam == "body") {
            $this->getBody();
        } else if ($myparam == "full_data") {
            $this->getFullData();
        }
    }

    function getFullData() {
        echo json_encode($this->data);
    }

    function getSummary() {
        $val = array();
        for ($i = 4; $i < $this->getLast($this->data); $i++) {
            if (trim($this->data[$i]) != "") {
                $val[] = $this->data[$i];
            }
        }
        echo json_encode($val);
    }

    function getBody() {
        $val = array();
        for ($i = ($this->getLast($this->data) + 1); $i < (sizeof($this->data) - 5); $i++) {
            if (trim($this->data[$i]) != "") {
                $val[] = $this->data[$i];
            } elseif (trim($this->data[$i]) == "=== Evaluation ===")
                break;
        }
        echo json_encode($val);
    }

    function getLast($sdata) {
        $last = 0;
        foreach ($sdata as $key => $value) {
            if (trim($value) == "Best rules found:") {
                $last = $key;
            }
        }
        return $last;
    }

    private function getContent() {
        $hfile = fopen($this->part . "temp_model.txt", "r");
        while (!feof($hfile)) {
            $this->data [] = fgets($hfile);
        }
        fclose($hfile);
    }

}

$getM = new getAprioryModel();