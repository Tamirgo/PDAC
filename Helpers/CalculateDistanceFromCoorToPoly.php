<?php
require_once "ExternalFunctions.php";
$uploadPath = '../Uploads/';

if(isset($_FILES["FileToUpload"]))
{
    $splittedFileName = explode('.',$_FILES["FileToUpload"]['name']);
    if($splittedFileName && $splittedFileName[count($splittedFileName)-1] === 'kml')
    {
        $uploadPath.=$_FILES["FileToUpload"]["name"];
        if(!file_exists($uploadPath))
        {
            if(!move_uploaded_file($_FILES["FileToUpload"]["tmp_name"], $uploadPath))
            {
                exit("Could not upload the file.");
            }
        }
        $polygon = new Polygon([],$uploadPath);
        $coordinate = new Coordinates(floatval($_POST['Longtitude']),floatval($_POST['Latitude']));
        
        $distance = Distance_Between_Coordinate_Polygon($coordinate,$polygon);
        if($distance === 0)
        {
            echo "The distance is $distance since the point made by these coordinates are inside the polygon";
        }
        else
        {
            echo "The distance between the polygon made by the kml file is : $distance";
        }
    }
}
else
{
    echo "There seems to be an issue with the file you've uploaded. Please try again or try a different kml file.";
}



