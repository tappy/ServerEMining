<?php
class setUpModel extends database_manager
{
    private $data;
    private $row;
    private $FileName;
    private $path;

    function loadClsterDataFormTable($table, $algorithm, $userID, $class_count, $iteria, $seed, $missing)
    {
        $this->setCurrentFile($table, $userID);
        $re = new getmodelweka ();
        $datareturn = $re->dogetClusterModel(
            $algorithm,
            $this->FileName,
            $this->path,
            $class_count,
            $iteria,
            $seed,
            $missing);
        return $datareturn;
    }

    function loadTreeDataFormTable($table,
                                   $algorithm,
                                   $userID,
                                   $binarySplit,
                                   $confidentFactor,
                                   $minNumObj,
                                   $numFolds,
                                   $reduceErrorPuning,
                                   $treeSeed,
                                   $subTree,
                                   $unPruned,
                                   $useLaplace)
    {

        $this->setCurrentFile($table, $userID);
        $re = new getmodelweka ();
        $datareturn = $re->dogetTreeModel(
            $algorithm,
            $this->FileName,
            $this->path,
            $binarySplit,
            $confidentFactor,
            $minNumObj,
            $numFolds,
            $reduceErrorPuning,
            $treeSeed,
            $subTree,
            $unPruned,
            $useLaplace);

        return $datareturn;
    }

    function loadAprioriDataFormTable($table,
                                      $algorithm,
                                      $userID,
                                      $classindex,
                                      $delta,
                                      $lowerBoundMinSupport,
                                      $minMetric,
                                      $numRules,
                                      $significanceLevel,
                                      $upperBoundMinSupport)
    {
        $this->setCurrentFile($table, $userID);
        $re = new getmodelweka ();
        $datareturn = $re->dogetArioriModel(
            $algorithm,
            $this->FileName,
            $this->path,
            $classindex,
            $delta,
            $lowerBoundMinSupport,
            $minMetric,
            $numRules,
            $significanceLevel,
            $upperBoundMinSupport);

        return $datareturn;
    }

    function setCurrentFile($table, $userID)
    {
        $this->creatDir("modelfile");
        $this->path = 'modelfile/' . $userID . '/';
        $this->FileName = $this->getFileName($table);
        $this->putDataToFile($table, $this->FileName);
    }

    function creatDir($part)
    {
        if (!file_exists($part)) {
            @mkdir($part, 0777, true);
            @chmod($part, 0777);
        }
    }

    function getFileName($table)
    {
        if ($this->row = mysql_fetch_array($this->selecUploadtList($table))) {
            return $this->row ["file_name"];
        } else {
            return null;
        }
    }

    function putDataToFile($table)
    {
        if ($this->getTableType($table) == 0) {
            $this->getAttrValARFF($table);
        }else{
            $this->getAttrValCSV($table);
        }
        $this->getDataContent($table);
    }

    function getAttrValARFF($table)
    {
        $this->data .= "\n@relation " . $table . "\n\n";
        $i = 0;
        $val = mysql_query("SELECT * FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='" . $table . "';");
        while ($row = mysql_fetch_assoc($val)) {
            if ($i > 1)
                $this->data .= $row["COLUMN_COMMENT"] . "\n";
            $i++;
        }
    }

    function getAttrValCSV($table)
    {
        $i = 0;
        $val = mysql_query("SELECT * FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='" . $table . "';");
        $colCount=$this->getColCount($table);
        while ($row = mysql_fetch_assoc($val)) {
            if ($i > 1){
                $this->data .= trim($row["COLUMN_COMMENT"]);
            if($i<$colCount-1){
                $this->data.=",";
            }
            }
            $i++;
        }
        $this->data.="\n";
    }


    function getDataContent($table)
    {
        $this->creatDir($this->path);
        $pathFile = $this->path . $this->FileName;
        $f = fopen($pathFile, "a");
        if($this->getTableType($table)==0) {
            fputs($f, $this->data);
            fputs($f, "\n@data\n\n");
        }else{
            fputs($f, $this->data);
        }
        $loadFile = mysql_query("select * from " . $table);
        $col = mysql_num_fields($loadFile);
        while ($this->row = mysql_fetch_array($loadFile)) {
            for ($i = 2; $i < $col; $i++) {
                fputs($f,trim($this->row [$i]));
                if ($i < $col - 1) {
                    fputs($f, ",");
                }
            }
            fputs($f, "\n");
        }
        fclose($f);
        @chmod($pathFile, 0777);
    }
}