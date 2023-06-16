<?php
error_reporting(0);
set_time_limit(0);

if (php_sapi_name() == "cli") $eol = "\n";
else  $eol = "<br>";

include "/var/www/d78236gbe27823/includes/connect.php";

$check_if_running = shell_exec("ps aux | grep extractPhoneNumber.php");
$runtimes = explode("extractPhoneNumber.php",$check_if_running);
$howmanyruns = count($runtimes);

$check_if_crawl_running = shell_exec("ps aux | grep crawl.php");
$crawl_runtimes = explode("crawl.php",$check_if_crawl_running);
$howmanyruns_of_crawl = count($crawl_runtimes);

if($howmanyruns<=5 AND $howmanyruns_of_crawl<=3) {

    $nfile = 1;

    header( 'Content-type: text/html; charset=utf-8' );

    $total_n = 0;

    $oldrs = mysqli_query($conn,"select max(Mobile_ID) as Last_Mobile_ID, count(1) as Total_No_Of_Old_Records from Mobile_Lists");
    $oldrow = mysqli_fetch_array($oldrs);

    //$fileslimit = 200;

    foreach (array_filter(glob("/var/www/d78236gbe27823/marketing/Whatsapp/images/*"), 'is_file') as $file) {

        $emergencyStopQ = "SELECT Emergency_STOP FROM Emergency WHERE Instruction_For = 'extractPhoneNumber.php'";
        $emergencyStopRS = mysqli_query($conn,$emergencyStopQ);
        $emergencyStopRow = mysqli_fetch_array($emergencyStopRS);

        if($emergencyStopRow['Emergency_STOP'] == "STOP"){

            mysqli_query($conn,"UPDATE Emergency SET Emergency_STOP = 'Keep Going' WHERE Instruction_For = 'extractPhoneNumber.php'");
            die($eol.$eol.date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . "EMERGENCY STOP CALLED".$eol.$eol);

        }


        //if($nfile>$fileslimit) break;

        echo $eol."=====================";
        $file_name = explode("/var/www/d78236gbe27823/marketing/Whatsapp/images/", $file)[1];
        echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."Parsing File ".$nfile.": ".$file_name;
        $nfile++;

        $filenameparts = explode(".",$file_name);
        $fileextension = $filenameparts[1];

        $ocrtext_path = "/var/www/d78236gbe27823/marketing/Whatsapp/images/ocrtexts/".str_replace(".".$fileextension,"",$file_name);

        $newspaper_name = explode("_", $file_name)[0];
        $newspaper_region = explode("_", $file_name)[1];
        $newspaper_operator_name = explode("_",$file_name)[4];

        if($newspaper_operator_name=='admin')
        {
            $newspaper_operator_name = NULL;
        }
        $check_operator_name = mysqli_query($conn,"select DISTINCT(Newspaper_Lang) as Languages from Mobile_Lists WHERE Newspaper_Lang='".$newspaper_operator_name."'");
        if($check_operator_name->num_rows>0)
        {
            $newspaper_operator_name = NULL;
        }
        $newspaper_full_name = $file_name;
        $newspaper_date_string = str_replace("_","-",substr(explode("_", $file_name,3)[2],0,10));

        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$newspaper_date_string)){
            $newspaper_date = $newspaper_date_string;
        }
        else{
            if(preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/",$newspaper_date_string)){
                $newspaper_date = substr($newspaper_date_string,6,4)."-".substr($newspaper_date_string,3,2)."-".substr($newspaper_date_string,0,2);
            }
            else{
                $newspaper_date = substr($newspaper_date_string,6,4)."-".substr($newspaper_date_string,0,2)."-".substr($newspaper_date_string,3,2);
            }
        }

        $newspaper_lang_temp = explode("_", explode(".",$file_name)[0]);
        $newspaper_lang = $newspaper_lang_temp[count($newspaper_lang_temp) - 1];
        
        echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."Starting OCR...";

        flush();
        ob_flush();

        if($newspaper_lang == "hin" || $newspaper_lang == "kan" || $newspaper_lang == "pan" || $newspaper_lang == "mar" || $newspaper_lang == "ben" || $newspaper_lang == "guj") $command = "tesseract ".$file." ".$ocrtext_path." -l ".$newspaper_lang."+eng";
        else $command = "tesseract ".$file." ".$ocrtext_path." -l eng";

        exec($command);
        $image_description_data = file_get_contents($ocrtext_path.".txt");
        unlink($file);

        if(strlen($image_description_data)==0){
            echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."OCR Found Nothing...";
            continue;
        }

        if((strpos(strtolower("--".$image_description_data),"obituary")>0 OR strpos(strtolower("--".$image_description_data),"demise")>0) AND $newspaper_name=="TOI"){
            echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."Obituary Image. Skipping file....";
            continue;
        }

        echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."Getting Phone Numbers...";

        // Proceed to find mobile Numbers

        $matches = array();
        preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $image_description_data, $matches);
        $matches = str_replace("+91","",str_replace($eol,"",str_replace("-","",str_replace(" ","",$matches[0]))));
        foreach ($matches as $match => $val) $matches[$match] = ltrim($val,"0");
        $n = count($matches);

        //print_r($matches);
        //die();

        if($n > 0){

            $total_n += $n;
            echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."Found ".$n." numbers";
            echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."Starting to add in the database...";

            $values = "";

            for($i = 0; $i < $n; $i++) {
                $blockcheck = "select * from Blocked_Numbers where Mobile_No = '".$matches[$i]."'";
                $bcrs = mysqli_query($conn,$blockcheck);
                if(!mysqli_num_rows($bcrs)) $values .= "('".$matches[$i]."','".$newspaper_name."','".$newspaper_region."','".$newspaper_date."','".$newspaper_lang."','".$newspaper_full_name."','".$newspaper_operator_name."'),";
                else echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."Skipping ".$matches[$i]." found in blocked numbers";
            }

            if(strlen($values)>0){
        
    	       $values = substr($values,0,strlen($values)-1)." ON DUPLICATE KEY UPDATE Newspaper_Name = concat(Newspaper_Name,' | ',VALUES(Newspaper_Name)), Newspaper_Region = concat(Newspaper_Region,' | ',VALUES(Newspaper_Region)), Newspaper_Date = concat(Newspaper_Date,' | ',VALUES(Newspaper_Date)), Newspaper_Lang = Newspaper_Lang;";

                $values_non_unique = substr($values,0,strlen($values)-1);

                $q_non_unqiue = "insert into Mobile_Lists (Mobile_Number,Newspaper_Name,Newspaper_Region,Newspaper_Date,Newspaper_Lang,Image_File_Name,Image_Operator) values ".$values_non_unique;

                $q = "insert into Mobile_Lists (Mobile_Number,Newspaper_Name,Newspaper_Region,Newspaper_Date,Newspaper_Lang,Image_File_Name,Image_Operator) values ".$values;

                if(!mysqli_query($conn,$q)) {
                    echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."Error in insert query... ABORTING!".$eol.$eol.$q."".$eol.$eol;
                    die();
                }

                mysqli_query($conn,$q_non_unqiue);
                echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."Insert query executed successfully......".$eol.$eol;

            }
            else echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."No numbers left to insert";
        }
        else echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."No numbers found";

        echo $eol.date('Y-m-d H:i:s',(time()+(5.5*3600)))."==> "."Done ".$file_name.$eol;
    }

    echo $eol.$eol."TOTAL NUMBERS: ".$total_n;

    $newrs = mysqli_query($conn,"Select * from Mobile_Lists where Mobile_ID>".$oldrow['Last_Mobile_ID']." Order by Mobile_ID");

    $i=0;
    $csv = "ID,Phone Number,Paper,City,Date,Country Code,Language".$eol;

    while($newrow = mysqli_fetch_array($newrs)){
    	$i++;
    	$unconistent_mobile_id = $newrow['Mobile_ID'];
    	$consistent_mobile_id = $oldrow['Last_Mobile_ID']+$i;
    	$idupdateqry = "UPDATE Mobile_Lists SET Mobile_ID = ".$consistent_mobile_id." Where Mobile_ID = ".$unconistent_mobile_id;
    	if(!mysqli_query($conn,$idupdateqry)) die(mysqli_error($conn));
    	$csv .= $consistent_mobile_id.",".$newrow['Mobile_Number'].",".$newrow['Newspaper_Name'].",".$newrow['Newspaper_Region'].",".$newrow['Newspaper_Date'].",91,".$newrow['Newspaper_Lang'].$eol;
    }

    mysqli_query($conn,"ALTER TABLE Mobile_Lists AUTO_INCREMENT = ".($consistent_mobile_id+1));

    echo $eol.$eol."TOTAL NEW NUMBERS: ".$i;

}
?>
