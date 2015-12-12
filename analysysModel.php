<?php
function __autoload($class_name)
{
    include $class_name . '.php';
}

class analysysModel extends database_manager
{
    private $tableName;
    private $returnValue;
    private $algorithm;
    private $userID;

    function __construct()
    {
        $this->algorithm = filter_input(INPUT_POST, "algorithm");
        $this->tableName = filter_input(INPUT_POST, "tableName");
        $this->userID = filter_input(INPUT_POST, "userid");
    }

    function analysysNow()
    {
        if ($this->connection()) {
            switch ($this->algorithm) {
                case 0 :
                    $cluster_class_count=filter_input(INPUT_POST,"class_count");
                    $max_itetia=filter_input(INPUT_POST,"max_iteria");
                    $seed=filter_input(INPUT_POST,"seed");
                    $missing_value=filter_input(INPUT_POST,"missing_value");

                    $clus = new setUpModel();
                    $result = $clus->loadClsterDataFormTable($this->tableName, $this->algorithm,$this->userID,$cluster_class_count,$max_itetia,$seed,$missing_value);
                    $this->returnValue ['model'] = $result;
                    $this->returnValue ['algorithm']= 0;
                    $this->returnValue ['count']= $cluster_class_count;
                    break;
                case 1 :
                    $clus = new setUpModel();
                    $binarySplit=filter_input(INPUT_POST,"binarySplit");
                    $confidentFactor=filter_input(INPUT_POST,"confidentFactor");
                    $minNumObj=filter_input(INPUT_POST,"minNumObj");
                    $numFolds=filter_input(INPUT_POST,"numFolds");
                    $reduceErrorPuning=filter_input(INPUT_POST,"reduceErrorPuning");
                    $treeSeed=filter_input(INPUT_POST,"treeSeed");
                    $subTree=filter_input(INPUT_POST,"subTree");
                    $unPruned=filter_input(INPUT_POST,"unPruned");
                    $useLaplace=filter_input(INPUT_POST,"useLaplace");


                    $result = $clus->loadTreeDataFormTable(
                        $this->tableName,
                        $this->algorithm,
                        $this->userID,
                        $binarySplit,
                        $confidentFactor,
                        $minNumObj,
                        $numFolds,
                        $reduceErrorPuning,
                        $treeSeed,
                        $subTree,
                        $unPruned,
                        $useLaplace);


                    $this->returnValue ['model'] = $result;
                    $this->returnValue ['algorithm']= 1;
                    break;
                case 2 :
                    $classindex=filter_input(INPUT_POST,"classindex");
                    $delta=filter_input(INPUT_POST,"delta");
                    $lowerBoundMinSupport=filter_input(INPUT_POST,"lowerBoundMinSupport");
                    $minMetric=filter_input(INPUT_POST,"minMetric");
                    $numRules=filter_input(INPUT_POST,"numRules");
                    $significanceLevel=filter_input(INPUT_POST,"significanceLevel");
                    $upperBoundMinSupport=filter_input(INPUT_POST,"upperBoundMinSupport");

                    $clus = new setUpModel();
                    $result = $clus->loadAprioriDataFormTable(
                        $this->tableName,
                        $this->algorithm,
                        $this->userID,
                        $classindex,
                        $delta,
                        $lowerBoundMinSupport,
                        $minMetric,
                        $numRules,
                        $significanceLevel,
                        $upperBoundMinSupport);

                    $this->returnValue ['model'] = $result;
                    $this->returnValue ['algorithm']= 2;
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
