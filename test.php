<?php
function __autoload($class_name)
{
    include $class_name . ".php";
}
class test extends database_manager{

    function __construct(){
        if($this->connection())
       echo md5("tlxjwtbzozdq");
    }
}

new test();