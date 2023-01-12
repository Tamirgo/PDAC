<?php

class Line
{
    private $slope,$c;

    public

        function __construct(Coordinates $coordinate1,Coordinates $coordinate2)
        {
            // We don't want to divide by zero.
            try{
                if($coordinate1->GetLongtitude() !== $coordinate2->GetLongtitude())
                {
                    $this->slope = ($coordinate1->GetLatitude() - $coordinate2->GetLatitude())/($coordinate1->GetLongtitude() - $coordinate2->GetLongtitude());
                    $this->c = $coordinate1->GetLatitude() - $this->slope*$coordinate1->GetLongtitude();
                }
                else
                {
                    $givenCoor1Longtitude = $coordinate1->GetLongtitude();
                    $givenCoor1Latitude = $coordinate1->GetLatitude();
                    $givenCoor2Longtitude = $coordinate2->GetLongtitude();
                    $givenCoor2Latitude = $coordinate2->GetLatitude();
                    throw new Exception("An error occured while creating the line with the points:($givenCoor1Longtitude,$givenCoor1Latitude), ($givenCoor2Longtitude,$givenCoor2Latitude)");
                }
                
            }
            catch(Exception $ex)
            {

                exit($ex->getMessage());
            }
            
        }
        function Getslope()
        {
            return $this->slope;
        }

        function GetC()
        {
            return $this->c;
        }

        function InsertCoordinateInLine(Coordinates $coordinate):float
        {
            return $coordinate->GetLongtitude() * $this->slope - $coordinate->GetLatitude() + $this->c;
        }
        /**
         * @DESC - we insert the point in the line - if we get 0 it means that the point is on the line - otherwise its not.
         * Problem is - php rounds up the numbers and sometimes the coordinates are being rouded too much and then when we insert them in the line the result is very small but still not 0.
         * hence there is a threshold, if the value of the insertion is smaller than that epsilon - we assume its in the line.
         */
        function IsCoordinateOnLine(Coordinates $coordinate):bool
        {
            return abs($this->InsertCoordinateInLine($coordinate)) < 0.000000001;//threshold to see the actual distance from the line.
        }
}