<!DOCTYPE html>
<html>
<head>
    <title>Places within Distance</title>
    <style>
        label {
            font-weight: bold;
            color: #333;
            display: block; /* Add this to make labels appear on separate lines */
            margin-bottom: 10px; /* Add some spacing between labels and inputs */
        }

        /* Internal CSS for input styling */
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px; /* Add some spacing between input fields */
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        /* Style the submit button */
        button[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Places within Distance</h1>
    <form method="post">
        <label for="latitude">Latitude:</label>
        <input type="text" id="latitude" name="latitude" required><br><br>

        <label for="longitude">Longitude:</label>
        <input type="text" id="longitude" name="longitude" required><br><br>

        <button type="submit">Submit</button>
    </form>

    <ul>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "bbdms";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        mysqli_set_charset($conn, "utf8");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // User input latitude and longitude
        $userLat = $_POST["latitude"];
        $userLon = $_POST["longitude"];
        $distance = 50;

        $userLat = mysqli_real_escape_string($conn, $userLat);
        $userLon = mysqli_real_escape_string($conn, $userLon);
        $distance = mysqli_real_escape_string($conn, $distance);

        $query = <<<EOF
        SELECT * FROM (
            SELECT *, 
                (
                    (
                        (
                            acos(
                                sin(( $userLat * pi() / 180))
                                *
                                sin(( `lat` * pi() / 180)) + cos(( $userLat * pi() /180 ))
                                *
                                cos(( `lat` * pi() / 180)) * cos((( $userLon - `lng`) * pi()/180)))
                        ) * 180/pi()
                    ) * 60 * 1.1515 * 1.609344
                )
            as distance FROM `markers`
        ) markers
        WHERE distance <= $distance
        LIMIT 15;
EOF;

        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . $row["name"] . " - Distance: " . round($row["distance"], 2) . " km</li>";
            }
        } else {
            echo "No results found.";
        }

        $conn->close();
    }
    ?>
    </ul>
</body>
</html>
