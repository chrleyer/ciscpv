<?php
 
 // Set png size for cisco phone idle background
    $width = 295;
    $height = 140;

 // Set PVoutput.org portlet URL // Replace XXXXX with your own SID here
    $url = "https://pvoutput.org/portlet/r1/getstatus.jsp?sid=XXXXX";

function curl_download($Url){
 
    // check if cURL is installed?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
 
    // create a new cURL resource handle
    $ch = curl_init();
 
    // Now set some options (most are optional)
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
 
    // Set a referer for PVoutput.org, enter this URL at your PVoutput API profile (Settings), if not set the result will contain an emtpy sting
    curl_setopt($ch, CURLOPT_REFERER, "http://FQDN/cisco/idle/createpng.php");
 
    // User agent (we simulate a Linux Firefox here)
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux i586; rv:49.0) Gecko/20100101 Firefox/49.0");
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
    return $output;
}

// download the data from PVoutput.org
$lines = curl_download($url); 

// format the result string, remove HTML tags and insert line breaks
$common_words = array("document.write", "('", "');('", "');" );
$output = str_replace($common_words, "", $lines);

$output = strip_tags($output);

$output = str_replace("Energy", "\nEnergy", $output);
$output = str_replace("Efficiency", "\nEfficiency", $output);
$output = str_replace("Power", "\nPower", $output);
$output = str_replace("Temperature", "\nTemperature", $output);
$output = str_replace("V", "V\n", $output);
$output = str_replace("V\noltage", "\nVoltage ", $output);

$arrText=explode("\n",wordwrap($output,30,"\n"));
 
$im = @imagecreate($width, $height); //creates the foreground image
$bgcolor = imagecolorallocate($im, 255, 255, 255); //sets image background color
$y=5; //vertical position of text

// Transparent background
    imagecolortransparent($im, $bgcolor);

foreach($arrText as $arr)
{
  $textcolor=imagecolorallocate($im,0,0,0); //sets text color
  imagestring($im,5,15,$y,trim($arr),$textcolor); //create the text string for image,added trim() to remove unwanted chars
  $y=$y+15;
 
}

// read background image (optional, enable nexts two commands if you want a background image)
// $bg = imagecreatefrompng('background.png');
// imagecopymerge($bg, $im, 0, 0, 0, 0, 295, 140, 100);

// Image output
header("Content-type: image/png");
imagepng($im);                           // change $im to $bg if you want a background image
imagedestroy($im);                       // change $im to $bg if you want a background image
?> 
