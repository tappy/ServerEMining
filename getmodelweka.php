<?php

class getmodelweka
{
    private $model;
    private $output;
    private $cmd;

    function dogetClusterModel($algorithm, $temp_file, $temp_part, $class_count, $iteria, $seed, $missing)
    { // getmodelweka cluster
        try {
            if ($algorithm == 0) {
                $this->cmd = "java -cp weka.jar weka.clusterers.SimpleKMeans -t " . $temp_part . $temp_file . " " . $missing . " -N " . $class_count . " -I " . $iteria . " -S " . $seed . "";
            }

            exec($this->cmd, $this->output);
            if (sizeof($this->output) > 0) {

                for ($i = 0; $i < sizeof($this->output); $i++) {
                    trim($this->output [$i]);
                    $this->model .= $this->output [$i] . "\n";
                }

                $this->putModelToFile($this->model, $temp_part);
                @unlink($temp_part . $temp_file);
                return "1";

            } else if (sizeof($this->output) <= 0) {
                @unlink($temp_part . $temp_file);
                return "2";
            }

        } catch (Exception $e) {
            @unlink($temp_part . $temp_file);
            return "0";
        }
    }

    function dogetTreeModel($algorithm,
                            $temp_file,
                            $temp_part,
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
        try {
            if ($algorithm == 1) {
                $this->cmd = "java -cp weka.jar weka.classifiers.trees.J48 -t " . $temp_part . $temp_file . " "
                    . $binarySplit . ""
                    . $confidentFactor . ""
                    . $minNumObj . ""
                    . $numFolds . ""
                    . $reduceErrorPuning . ""
                    . $treeSeed . ""
                    . $subTree . ""
                    . $unPruned . ""
                    . $useLaplace;
            }

            exec($this->cmd, $this->output);
            if (sizeof($this->output) > 0) {

                for ($i = 0; $i < sizeof($this->output); $i++) {
                    trim($this->output [$i]);
                    $this->model .= $this->output [$i] . "\n";
                }

                $this->putModelToFile($this->model, $temp_part);
                @unlink($temp_part . $temp_file);
                return "1";

            } else if (sizeof($this->output) <= 0) {
                @unlink($temp_part . $temp_file);
                return "2";
            }
        } catch (Exception $ex) {
            @unlink($temp_part . $temp_file);
            return "0";
        }
    }

    function dogetArioriModel($algorithm, $temp_file, $temp_part, $classindex, $delta, $lowerBoundMinSupport, $minMetric, $numRules, $significanceLevel, $upperBoundMinSupport)
    {
        try {
            if ($algorithm == 2) {
                $this->cmd = "java -cp weka.jar weka.associations.Apriori -t " . $temp_part . $temp_file .
                    " -c " . $classindex .
                    " -D " . $delta .
                    " -M " . $lowerBoundMinSupport .
                    " -C " . $minMetric .
                    " -N " . $numRules .
                    " -S " . $significanceLevel .
                    " -U " . $upperBoundMinSupport;
                //-N 10 -T 0 -C 0.9 -D 0.06 -U 1.0 -M 0.1 -S -1.0 -c -1
            }
            exec($this->cmd, $this->output);
            if (sizeof($this->output) > 0) {


                for ($i = 0; $i < sizeof($this->output); $i++) {
                    trim($this->output [$i]);
                    $this->model .= $this->output [$i] . "\n";
                }

                $this->putModelToFile($this->model, $temp_part);
                @unlink($temp_part . $temp_file);
                return "1";

            } else if (sizeof($this->output) <= 0) {
                @unlink($temp_part . $temp_file);
                return "2";
            }
        } catch (Exception $ex) {
            @unlink($temp_part . $temp_file);
            return "0";
        }
    }

    function putModelToFile($model, $part)
    {
        $fmodel = fopen($part . "temp_model.txt", "w");
        fputs($fmodel, $model);
        fclose($fmodel);
        @chmod($part . "temp_model.txt", 0777);
    }
}