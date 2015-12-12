<?php

function __autoload($class_name) {
    include $class_name . ".php";
}

class getClusterModel {

    private $getCluster;
    private $data;
    private $userid;
    private $part;
    private $json;

    function __construct() {
        $this->json = json_decode(json_decode(file_get_contents("php://input"), true)['GetClusterModelReq'], true);
        $this->getCluster = $this->json["param"];
        $this->userid = $this->json["userid"];
        $this->part = 'modelfile/' . $this->userid . '/';
        $this->getContent();
    }

    function loadDataByParam() {
        if ($this->getCluster == "head") {
            $this->getHead();
        } else if ($this->getCluster == "body") {
            $this->getBody();
        } else if ($this->getCluster == "footer") {
            $this->getFooter();
        } else if ($this->getCluster == "full_data") {
            echo json_encode($this->data);
        }
    }

    private function getContent() {
        $hfile = fopen($this->part . "temp_model.txt", "r");
        while (!feof($hfile)) {
            $this->data [] = fgets($hfile);
        }
        fclose($hfile);
    }

    private function getHead() {
        $json = array();
        $fLine = $this->getFirstLine(20, "Number of iterations");
        $endLine = ($this->getEndLine($fLine) - 1);
        for ($i = $fLine; $i <= $endLine; $i++) {
            $json [] = $this->data [$i];
        }
        echo json_encode($json);
    }

    private function getBody() {
        $fLine = ($this->getFirstLine(5, "=====") + 1);
        echo "[";
        foreach ($this->data as $key => $value) {
            if ($key >= $fLine) {
                if (strlen($value) <= 1)
                    break;
                else {
                    if ($key < count($this->data) - 1 && $key > $fLine) {
                        echo ",";
                    }
                }
                $mexp = explode(" ", $value);
                echo "[";
                foreach ($mexp as $k => $v) {
                    if ($v != "") {
                        echo json_encode($v);
                        if ($k < count($mexp) - 1)
                            echo ",";
                    }
                }
                echo "]";
            }
        }
        echo "]";
    }

    private function getFirstLine($glenght, $text) {
        $keyLine = 0;
        foreach ($this->data as $key => $value) {
            if (trim(substr($value, 0, $glenght)) == $text) {
                $keyLine = $key;
            }
        }
        return $keyLine;
    }

    private function getEndLine($fLine) {
        $keyLine = 0;
        foreach ($this->data as $key => $value) {
            if ($key >= $fLine && (trim(substr($value, 0, 1)) == "")) {
                $keyLine = $key;
                break;
            }
        }
        return $keyLine;
    }

    private function getFooter() {
        $json = array();
        foreach ($this->data as $key => $value) {
            if ($key >= $this->getFirstLine(19, "Clustered Instances")) {
                if (strlen($value) <= 1) {
                    break;
                }
                $json[] = $value;
            }
        }
        echo json_encode($json);
    }

}

$g = new getClusterModel ();
$g->loadDataByParam();
