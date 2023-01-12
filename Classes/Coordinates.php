<?php


class Coordinates
{
    private $longtitude,$latitude;

    public

    function __construct($longtitude,$latitude)
    {
        $this->longtitude = $longtitude;
        $this->latitude = $latitude;
    }

    function GetLongtitude()
    {
        return $this->longtitude;
    }
    function GetLatitude()
    {
        return $this->latitude;
    }
}