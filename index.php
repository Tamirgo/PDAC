<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>P.D.A.C</h1>
    <p>Please forgive me for not styling this page - its just to play a little with geometry and linear algebra.</p>

    <form action="Helpers/CalculateDistanceFromCoorToPoly.php" method="POST" enctype="multipart/form-data">
            <input type="text" placeholder="Longtitude" name="Longtitude">
            <input type="text" placeholder="Latitude" name="Latitude">
            <br/>
            <p>Please upload a kml file - we will create a polygon out of the coordinates and measure the distance between the coordinates you gave us and the polygon.</p>
            <input type="file" name="FileToUpload">
            <button type="submit">Calculate</button>
    </form>
</body>
</html>