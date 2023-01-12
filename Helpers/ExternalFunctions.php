<?php
require_once "../Classes/Coordinates.php";
require_once "../Classes/Polygon.php";
require_once "../Classes/Line.php";

/**
 * @Param $coordinate1
 * @Param $coordinate2
 * @Return value float
 * @DESC square root of x1^2 - x2^2 + y1^2 - y2^2
*/
function Distance_Between_Two_Coordinates(Coordinates $coordinate1, Coordinates $coordinate2):float
{
    return sqrt(pow(($coordinate1->GetLongtitude() - $coordinate2->GetLongtitude()),2) + pow(($coordinate1->GetLatitude() - $coordinate2->GetLatitude()),2));
}

/**
 * @Param $coordinate
 * @Param $line
 * @Return value float
 * @DESC - returns the absolute value of the distance between the given line and point - using the formula of absolute value of the insertion of the point in the line
 * divided by a^2 + b^2 where a,b are the multipliers of x and y. since in this task i use the formula y = mx + c => b is always 1.
 */
function Distance_Between_Coordinate_Line(Coordinates $coordinate,Line $line):float
{
    return abs($line->InsertCoordinateInLine($coordinate))/sqrt(($line->GetSlope()*$line->GetSlope()) + 1);//1 since we always represent a line with y=mx+b
}

/**
 * @Param $coordinate
 * @Param $polygon
 * @Return value - float
 * @DESC Returns the distance between a given point and a polygon. the way its being calculated is by a few steps.
 * 1) First checking if the given point is inside the polygon - if it is we immediatly return 0.
 * 2) Getting both the edges and the points that are creating the polygon for later use.
 * 3) finding the closest point to the given point in the polygon - this point has a good chance being the closest part of the polygon to the given point.
 * 4) Finding the closest segment of a line to our given point. it is being done with the usage of the closest point we found earlier. we are getting the only two lines that are connecing 
 * the mindistance point - these two are candidate segments. the closest segment is the one with the closer point to our given point so its the second closest point to our given point THAT IS
 * ON ONE OF THE CANDIDATE SEGMENTS (not the second closest in general). the two points we found are the endpoints of the closest line segment to our given point so now we can check.
 * 6) if the given point is in between our two founded points - then we need to check if the segment is closer than the closest point. else it has to be the point which is closest to us which is 
 * the actual distance.
 */
function Distance_Between_Coordinate_Polygon(Coordinates $coordinate, Polygon $polygon):float
{
    if(!IsPoint_Inside_Polygon($polygon, $coordinate))
    {
        $edges = $polygon->GetEdges();
        $polyCoordinates = $polygon->GetCoordinates();
        if($edges && $polyCoordinates)
        {
            //$min_distance = Distance_Between_Coordinate_Line($coordinate,$edges[0]);
            //First step: finding the closest coordinate in the polygon to the given coordinate.
            $minCoorDistance = Distance_Between_Two_Coordinates($coordinate,$polyCoordinates[0]);//setting the first distance and coordinate.
            $minDistanceCoor = $polyCoordinates[0];
            foreach($polyCoordinates as $polyCoordinate)
            {
                $currDistance = Distance_Between_Two_Coordinates($coordinate,$polyCoordinate);
                if($currDistance < $minCoorDistance)
                {
                    $minCoorDistance = $currDistance;
                    $minDistanceCoor = $polyCoordinate;
                }
            }
            $minDistanceCoorIndex = array_search($minDistanceCoor,$polyCoordinates);//finding the index of this point -> we can find the two segments it connects -> the second closest endpoint will give us the closest segment.
            
            //Second step: find the closest Segment to the given coordinate.
            if($minDistanceCoorIndex === count($polyCoordinates)-1)//the closest point in the last point in the array -> it connects to index-1 and index - 0
            {
                $secondMinDistanceCoor = Distance_Between_Two_Coordinates($coordinate,$polyCoordinates[0]) > Distance_Between_Two_Coordinates($coordinate,$polyCoordinates[$minDistanceCoorIndex-1])? $polyCoordinates[$minDistanceCoorIndex-1]:$polyCoordinates[0];
            }
            else if($minDistanceCoorIndex === 0)
            {
                $secondMinDistanceCoor = Distance_Between_Two_Coordinates($coordinate,$polyCoordinates[1]) > Distance_Between_Two_Coordinates($coordinate,$polyCoordinates[count($polyCoordinates)-1])? $polyCoordinates[count($polyCoordinates)-1]:$polyCoordinates[1];
            }
            else
            {
                $secondMinDistanceCoor = Distance_Between_Two_Coordinates($coordinate,$polyCoordinates[$minDistanceCoorIndex+1]) > Distance_Between_Two_Coordinates($coordinate,$polyCoordinates[$minDistanceCoorIndex-1])? $polyCoordinates[$minDistanceCoorIndex-1]:$polyCoordinates[$minDistanceCoorIndex+1];
            }
            $segment = new Line($minDistanceCoor,$secondMinDistanceCoor);
            //inside the segment => minimum between the line that containts the segment and the minimum point.
            if(IsPoint_Between_TwoGivenPoints($coordinate,$minDistanceCoor,$secondMinDistanceCoor))
            {
                return abs(min($minCoorDistance,Distance_Between_Coordinate_Line($coordinate,$segment)));
            }
            return abs($minCoorDistance);
        }
    }
    return 0;
}

function Intersaction_Between_Two_Lines(Line $line1, Line $line2)
{
    if($line1->GetSlope() === $line2->GetSlope())
    {
        return null;
    }
    $temp_slope = $line1->GetSlope() - $line2->GetSlope();
    $temp_c = $line1->GetC() - $line2->GetC();
    $coor_long = $temp_c/(-$temp_slope);//temp_slope = 5 for example => 5x.. temp_c = 10 for example => x = 10/5
    $coor_lat = $coor_long*$line1->GetSlope() + $line1->GetC();
    return New Coordinates($coor_long,$coor_lat);
}

function IsPoint_Between_TwoGivenPoints(Coordinates $myCoor,Coordinates $coordinate1, Coordinates $coordinate2):bool
{
    
    $maxLongtitude = max(array($coordinate1->GetLongtitude() , $coordinate2->GetLongtitude()));
    $maxLatitude = max(array($coordinate1->GetLatitude() , $coordinate2->GetLatitude()));
    $minLongtitude = min(array($coordinate1->GetLongtitude() , $coordinate2->GetLongtitude()));
    $minLatitude = min(array($coordinate1->GetLatitude() , $coordinate2->GetLatitude()));

    return ((abs($myCoor->GetLongtitude())<= abs($maxLongtitude) && abs($myCoor->GetLongtitude()>= $minLongtitude)) || (abs($myCoor->GetLatitude())<= abs($maxLatitude) && abs($myCoor->GetLatitude())>= abs($minLatitude)));
}


/**
 * @Param $polygon
 * @Param $coordinate
 * @Return value bool
 * @DESC This function returns true if we find that a point given is inside a polygon given. it is divided to steps:
 * 1) check if the point is outside of our extremes range - if the x or y values (longtitude and latitude) are too big or too small it has to be outside.
 * 2) We check if the Coordinate given is a part of the coordinates that are building this polygon.
 * 3) Usign a well known method to determine of a point is inside or not - we draw a horizontal line from the point to the right and counting how many intersactions it has with our Polygon
 * Also making sure we only count intersections that are relevant (meaning - if the intersaction occurs outside of the polygon we do not count it and it is possible because the polygon is built out of infinite lines.)
 * If the amount of intersactions(that count) is even - then the point is OUTSIDE the polygon. else its inside.
 * 
 */
function IsPoint_Inside_Polygon(Polygon $polygon, Coordinates $coordinate):bool
{
    $counter = 0;
    $polygonExtremes = $polygon->GetExtremes();
    $coor_long = $coordinate->GetLongtitude();
    $coor_lat = $coordinate->GetLatitude();
    // First test - if the coordinates are outside the extremes than the point is for sure NOT in the polygon.
    if(
        $coor_long < $polygonExtremes["minLongtitude"] ||
        $coor_long > $polygonExtremes["maxLongtitude"] ||
        $coor_lat < $polygonExtremes["minLatitude"] || 
        $coor_lat > $polygonExtremes["maxLatitude"]
    )
    {
        return false;
    }
    // Second test - the coordinate might be one of the coordinates which built this polygon.
    if($polygon->IsCoordinatePartOfPolygon($coordinate))
    {
        return true;
    }
    // Third and final test - Draw a horizontal line from the point to the right. if the amount of times the line hits the polygon is odd the point is inside, else its outside.
    $lineFromCoordinate = new Line($coordinate,new Coordinates($polygonExtremes["maxLongtitude"],$coordinate->GetLatitude()));//setting a line that is horizontal to the x axis (the two points have the same latitude value.)
    foreach($polygon->GetEdges() as $edge)
    {
        $intersactionCoordinate = Intersaction_Between_Two_Lines($lineFromCoordinate,$edge);
        if($intersactionCoordinate !== null && $intersactionCoordinate->GetLongtitude() >= $coordinate->GetLongtitude() && $intersactionCoordinate->GetLongtitude() <= $polygonExtremes["maxLongtitude"])
        {
            $counter++;
        }
    }
    return !($counter%2 === 0);
}