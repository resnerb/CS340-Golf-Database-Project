<?php
    $servername = "oniddb.cws.oregonstate.edu";
    $username = "resnerb-db";
    $password = "7qKnFUFXqMYOmsTZ";
    $database = "resnerb-db";
    //$servername = "localhost";
    //$username = "root";
    //$password = "resnerb";
    //$database = "golfHDCP";
    
    $conn = new mysqli($servername, $username, $password);
    //Check if connection works
    if ($conn->connect_error)
    {
        die ("Connection failed: " . $conn->connect_error);
    }
    
    // Select the resnerb-db as the default database
    mysqli_select_db($conn, $database);
    
    // Check if the playerScores table exists in the golfHDCP database
    // If playerScores table does not exist then we know we must create both the
    // playerScores and golfCourses tables
    $sql = "SHOW TABLES IN `". $database . "` WHERE `Tables_in_" . $database . "` = 'playerScores'";
    
    // Perform the query and store the result
    $result = $conn->query($sql);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>CurrentHDCP</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h1 id="header">Add Player to Database</h1><br><br>

<form align='left' action='PGA_Database.php' method='POST'>
<input type='text' name='fname' value
<input type='submit' style='font-family: Courier New, monospace; font-size: 30px;' value='Enter New Score'>
</form><br><br>

<?php
    if (!isset($_SESSION['user']))
    {
        $username = $_POST["username"];
        
        if ($username === '' || !isset($username))
        {
            echo 'A username must be entered. Click <a href="loginHDCP.php">here</a> to return to the login screen.';
        }
        else
        {
            $_SESSION['user'] = $username;
        }
    }
    
    $username = $_SESSION['user'];
    
    
    $sql = "SELECT * FROM playerScores WHERE playerName='$username' ORDER BY datePlayed DESC LIMIT 20";
    $playerResult = $conn->query($sql);
    
    if ($playerResult->num_rows > 0) {
        // Create an array to hold the handicap differentials
        $hdArray = array();
        
        echo "<table id='table1'><tr><th>Date Played</th><th>Score</th><th>Par</th><th>Slope</th><th>Rating</th><th>Course</th><th>Handicap Differential</th></tr>";
        // output data of each row
        while($p_data = mysqli_fetch_array($playerResult)) {
            
            $courseName = $p_data['golfCourseName'];
            $sql = "SELECT * FROM golfCourses WHERE name='$courseName'";
            $courseResult = $conn->query($sql);
            
            if ($courseResult->num_rows > 0) {
                $gc_data = mysqli_fetch_array($courseResult);
                
                $hd = ($p_data['score'] - $gc_data['rating']) * 113 / $gc_data['slope'];
                $hdArray[] = $hd;
                
                echo "<tr><td>".$p_data["datePlayed"]."</td><td>".$p_data["score"]."</td><td>".$gc_data["par"]."</td><td>".$gc_data["slope"]."</td><td>".$gc_data["rating"]."</td><td>".$gc_data["name"]."</td><td>". round($hd,1)."</td></tr>";
            }
            
        }
        echo "</table>";
        
    $conn->close();
    ?>

</body>
</html>

