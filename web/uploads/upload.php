<?php

$config = parse_ini_file('../config.ini'); 
$studydir=$config['study_dir'];
$target_dir = "../".$studydir."/";
$name = $_POST['name'];
//print_r($_FILES);
$target_file = $target_dir . basename($_FILES["file"]["name"]);

if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file))
    {
        echo "File: ".$_FILES["file"]["name"]." correcly uploaded";
    }
else
    {
        die("ERROR: File not Uploaded");
    }

?>