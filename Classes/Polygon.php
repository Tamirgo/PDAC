<?php

class Polygon
{
    private $edges;
    private $coordinates;
    private $maxLongtitude,$maxLatitude,$minLatitude,$minLongtitude;

    public
        /**
         * @PARAM $coordinates - array of coordinates
         * @PARAM $kmlFile - string - path to a kml file.
         * @DESC - The constructor gets either an array of coordinates or a path to a kml file. depending on which one of them is empty it will build accordingly.
         * If the coordinates array is not empty - It will build the polygon by calling the build function by coordinates. 
         * If the coordinates array is empty and the kml file is not an empty string - it will build the polygon by the file.
         * The constructor does prioritize the coordinates array on the kml file - if both are non empty it will go for the coordinates array. if both empty - it will just create an empty array.
         */
        function __construct(array $coordinates = [],string $kmlFile = "")
        {
            if($coordinates)
            {
                $this->coordinates = $coordinates;
                $this->Build_Polygon_By_Coordinates($this->coordinates);
            }
            else if($kmlFile)
            {
                $this->Build_Polygon_By_Kml_File($kmlFile);
            }
            else
            {
                $edges = [];
            }
        }

        function GetEdges()
        {
            return $this->edges;
        }

        function GetCoordinates()
        {
            return $this->coordinates;
        }

        /**
         * @DESC - Calculating the Exteremes coordinates in the polygon - This will help us determining if a point is inside the polygon or not.
         */
        function GetExtremes():array
        {
            return array("maxLongtitude"=>$this->maxLongtitude,"maxLatitude"=>$this->maxLatitude,"minLatitude"=>$this->minLatitude,"minLongtitude"=>$this->minLongtitude);
        }

        /**
         * @DESC - Checking if the coordinate given is already in the coordinates array.
         */
        function IsCoordinatePartOfPolygon(Coordinates $coordinate):bool
        {
            foreach($this->coordinates as $current_coor)
            {
                if($coordinate->GetLongtitude() === $current_coor->GetLongtitude() && $coordinate->GetLatitude() === $current_coor->GetLatitude())
                {
                    return true;
                }
            }
            return false;
        }
        

    private
        /**
         * @DESC - Building a polygon out of the given coordinates array. - connecting the coordinates one by one and in the end we are connecting the last one with the first one.
         */
        function Build_Polygon_By_Coordinates(array $coordinates):void
        {
            $this->edges = array();
            $i;
            for($i = 0;$i<count($coordinates)-1;$i++)
            {
                $this->edges[] = new Line($coordinates[$i],$coordinates[$i+1]);
            }
            $this->edges[] = new Line($coordinates[$i],$coordinates[0]);
            $this->SetExtremeCoordinates($coordinates);

        }
        /**
         * Gets simpleXMLFile and search for <Polygon> tag. returns null if it didn't find and the simpleXMLFile polygon tag if it did.
         */
        function SearchForPolygonInKmlFile($kmlFileContent)
        {
            if(!$kmlFileContent || !$kmlFileContent->children())
            {
                return null;
            }
            foreach($kmlFileContent->children() as $child)
            {
                if($child->Polygon)
                {
                    return $child->Polygon;
                }
                $temp_res = $this->SearchForPolygonInKmlFile($child);
            }
            return $temp_res;
        }
        /**
         * @DESC - Getting a string with a path for a kml file - gets the data out of it into a simplexmlelement object.
         * finding a Polygon tag inside of it with SearchForPolygonInKmlFile.
         * creating the coordinates out of the large coordinates string we got out of the polygon tag.
         * When we have an array of coordinates - we send it the Build_Polygon_By_Kml_File.
         */
        function Build_Polygon_By_Kml_File(string $kmlFile):void
        {
            $this->coordinates = array();
            if(file_exists($kmlFile))
            {
                $fileContent = simplexml_load_file($kmlFile);
                $polygonTag = $this->SearchForPolygonInKmlFile($fileContent);
                if($polygonTag)
                {
                    $xmlCoordinatesString = $polygonTag->outerBoundaryIs->LinearRing->coordinates;
                    $xmlCoordinatesArray = explode(' ',trim($xmlCoordinatesString));
                    foreach($xmlCoordinatesArray as $coordinates)
                    {
                        $current_coordinate = explode(',',$coordinates);
                        if(is_numeric($current_coordinate[0]))
                        {
                            $new_coor = new Coordinates(floatval($current_coordinate[0]),floatval($current_coordinate[1]));
                            if(!$this->IsCoordinatePartOfPolygon($new_coor))//if we have the same point showing up again there is no need to insert it.
                            {
                                $this->coordinates[] = $new_coor;
                            }
                        }
                    }
                    $this->Build_Polygon_By_Coordinates($this->coordinates);
                }
                else{
                    exit("No Polygon tag in this file.");
                }

            }
            else{
                exit("File $kmlFile wasn't found.");
            }
        }
        /**
         * Setting the extreme coordinates so we can use them later to determine if a point is inside a polygon.
         */
        function SetExtremeCoordinates(array $coordinates)
        {
            $this->minLongtitude = $this->maxLongtitude = $coordinates[0]->GetLongtitude();
            $this->minLatitude = $this->maxLatitude = $coordinates[0]->GetLatitude();
            foreach($coordinates as $coordinate)
            {
                if($coordinate->GetLongtitude() <=  $this->minLongtitude)
                {
                    $this->minLongtitude = $coordinate->GetLongtitude();
                }
                else if($coordinate->GetLongtitude() > $this->maxLongtitude)
                {
                    $this->maxLongtitude = $coordinate->GetLongtitude();
                }
                if($coordinate->GetLatitude() <= $this->minLatitude)
                {
                    $this->minLatitude = $coordinate->GetLatitude();
                }
                else if($coordinate->GetLatitude() > $this->maxLatitude)
                {
                    $this->maxLatitude = $coordinate->GetLatitude();
                }
            }
        }

}