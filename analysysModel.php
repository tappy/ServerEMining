<?php

function __autoload($class_name) {
    include $class_name . '.php';
}

class analysysModel extends database_manager {

    private $tableName;
    private $returnValue;
    private $algorithm;
    private $userID;
    private $json;

    function __construct() {
        $this->json = json_decode(json_decode(file_get_contents("php://input"), true)["AnalysysLoaderReq"], true);
        $this->algorithm = $this->json["algorithm"];
        $this->tableName = $this->json["tableName"];
        $this->userID = $this->json["userid"];
    }

    function analysysNow() {
        if ($this->connection()) {
            switch ($this->algorithm) {
                case 0 :
                    $cluster_class_count = $this->json["class_count"];
                    $max_itetia = $this->json["max_iteria"];
                    $seed = $this->json["seed"];
                    $missing_value = $this->json["missing_value"];

                    $clus = new setUpModel();
                    $result = $clus->loadClsterDataFormTable($this->tableName, $this->algorithm, $this->userID, $cluster_class_count, $max_itetia, $seed, $missing_value);
                    $this->returnValue ['model'] = $result;
                    $this->returnValue ['algorithm'] = 0;
                    $this->returnValue ['count'] = $cluster_class_count;
                    break;
                case 1 :
                    $clus = new setUpModel();
                    $binarySplit = $this->json["binarySplit"];
                    $confidentFactor = $this->json["confidentFactor"];
                    $minNumObj = $this->json["minNumObj"];
                    $numFolds = $this->json["numFolds"];
                    $reduceErrorPuning = $this->json["reduceErrorPuning"];
                    $treeSeed = $this->json["treeSeed"];
                    $subTree = $this->json["subTree"];
                    $unPruned = $this->json["unPruned"];
                    $useLaplace = $this->json["useLaplace"];


                    $result = $clus->loadTreeDataFormTable(
                            $this->tableName, $this->algorithm, $this->userID, $binarySplit, $confidentFactor, $minNumObj, $numFolds, $reduceErrorPuning, $treeSeed, $subTree, $unPruned, $useLaplace);


                    $this->returnValue ['model'] = $result;
                    $this->returnValue ['algorithm'] = 1;
                    break;
                case 2 :
                    $classindex = $this->json["classindex"];
                    $delta = $this->json["delta"];
                    $lowerBoundMinSupport = $this->json["lowerBoundMinSupport"];
                    $minMetric = $this->json["minMetric"];
                    $numRules = $this->json["numRules"];
                    $significanceLevel = $this->json["significanceLevel"];
                    $upperBoundMinSupport = $this->json["upperBoundMinSupport"];

                    $clus = new setUpModel();
                    $result = $clus->loadAprioriDataFormTable(
                            $this->tableName, $this->algorithm, $this->userID, $classindex, $delta, $lowerBoundMinSupport, $minMetric, $numRules, $significanceLevel, $upperBoundMinSupport);

                    $this->returnValue ['model'] = $result;
                    $this->returnValue ['algorithm'] = 2;
                    break;

                default :
                    break;
            }
        }
        echo json_encode($this->returnValue);
    }

}

$ana = new analysysModel ();
$ana->analysysNow();
