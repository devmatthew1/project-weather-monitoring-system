<!--
  Rui Santos
  Complete project details at https://RandomNerdTutorials.com/cloud-weather-station-esp32-esp8266/

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
-->
<?php

   
    include_once('fetch.php');
    include_once('update.php');
    include_once('config.php');
    include_once('esp-database.php');
    $readings_count = 10;
    /*
    if ($_GET["readingsCount"]){
      $data = $_GET["readingsCount"];
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      $readings_count = $_GET["readingsCount"];
    }
    // default readings count set to 20
    else {
      $readings_count = 20;
    }
    */
    
    $last_reading = getLastReadings();
    $last_reading_moisture = $last_reading["value1"];
    $last_reading_humi = $last_reading["value2"];
    $last_reading_temp = $last_reading["value3"];
    
    $last_reading_pressure = $last_reading["value4"];
    $last_reading_lightntensity = $last_reading["value5"];
    $last_reading_time = $last_reading["reading_time"];

    // Uncomment to set timezone to - 1 hour (you can change 1 to any number)
    //$last_reading_time = date("Y-m-d H:i:s", strtotime("$last_reading_time - 1 hours"));
    // Uncomment to set timezone to + 7 hours (you can change 7 to any number)
    $last_reading_time = date("Y-m-d H:i:s", strtotime("$last_reading_time + 1 hours"));

    $min_moisture = minReading($readings_count, 'value1');
    $max_moisture = maxReading($readings_count, 'value1');
    $avg_moisture = avgReading($readings_count, 'value1');

    $min_humi = minReading($readings_count, 'value2');
    $max_humi = maxReading($readings_count, 'value2');
    $avg_humi = avgReading($readings_count, 'value2');

    $min_temp = minReading($readings_count, 'value3');
    $max_temp = maxReading($readings_count, 'value3');
    $avg_temp = avgReading($readings_count, 'value3');

    $min_pressure = minReading($readings_count, 'value4');
    $max_pressure = maxReading($readings_count, 'value4');
    $avg_pressure = avgReading($readings_count, 'value4');

    $min_lightntensity = minReading($readings_count, 'value5');
    $max_lightntensity = maxReading($readings_count, 'value5');
    $avg_lightntensity = avgReading($readings_count, 'value5');

?>

<!DOCTYPE html>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <link rel="stylesheet" type="text/css" href="esp-style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    <header class="header">
        <h1 style="margin:10px 0px;"> Farm Weather Monitoring System</h1>

    </header>
<body>
    <p>Last reading: <?php echo $last_reading_time; ?></p>
    <div style="display: flex;">
        <div style="">
        <div style="display: flex;flex-wrap: nowrap;align-items: center;">
            <p style="width: 10px;margin: 0px; height: 10px;background-color: black;" id="temp-icon"></p>
            <p style="margin: 0px; padding: 0px 5px;font-size: small;" id="temp-txt">....</p>
        </div>
    
        <div style="display: flex;flex-wrap: nowrap;align-items: center;">
            <p style="width: 10px;margin: 0px; height: 10px;background-color: black;" id="humid-icon"></p>
            <p style="margin: 0px; padding: 0px 5px;font-size: small;" id="humid-txt">.....</p>
        </div>
    
        <div style="display: flex;flex-wrap: nowrap;align-items: center;">
            <p style="width: 10px;margin: 0px; height: 10px;background-color: black;" id="moisture-icon"></p>
            <p style="margin: 0px; padding: 0px 5px;font-size: small;" id="moisture-txt">.....</p>
        </div>
        </div>
    </div>
    
    <div>
        <p id="demo">...</p>
        <form method="POST" action="#">
        Day*<input type="number" name="date" value="<?php echo $date;?>">
        Hour* <input type="number" name="h" value="0">
        Minuite* <input type="number" name="m" value="0">
        Second*<input type="number" name="s" value="0">
        <button type="submit" name="update">Update</button>
        </form>
    </div>
    <div class="container">
        <section class="content">
            <div class="box gauge--1">
                <h3>MOISTURE</h3>
                <div class="mask">
                  <div class="semi-circle"></div>
                  <div class="semi-circle--mask"></div>
                </div>
                <p style="font-size: 30px;" id="moisture">--</p>
                <table cellspacing="5" cellpadding="5">
                  
                    <tr>
                        <td>Min</td>
                        <td>Max</td>
                        <td>Average</td>
                    </tr>
                    <tr>
                        <td><?php echo $min_moisture['min_amount']; ?> &deg;C</td>
                        <td><?php echo $max_moisture['max_amount']; ?> &deg;C</td>
                        <td><?php echo round($avg_moisture['avg_amount'], 2); ?> &deg;C</td>
                    </tr>
                </table>
            </div>
            <div class="box gauge--2">
                <h3>HUMIDITY</h3>
                <div class="mask">
                    <div class="semi-circle"></div>
                    <div class="semi-circle--mask"></div>
                </div>
                <p style="font-size: 30px;" id="humi">--</p>
                <table cellspacing="5" cellpadding="5">
                  
                    <tr>
                        <td>Min</td>
                        <td>Max</td>
                        <td>Average</td>
                    </tr>
                    <tr>
                        <td><?php echo $min_humi['min_amount']; ?> %</td>
                        <td><?php echo $max_humi['max_amount']; ?> %</td>
                        <td><?php echo round($avg_humi['avg_amount'], 2); ?> %</td>
                    </tr>
                </table>
            </div>
            <div class="box gauge--3">
                <h3>Temperature</h3>
                <div class="mask">
                  <div class="semi-circle"></div>
                  <div class="semi-circle--mask"></div>
                </div>
                <p style="font-size: 30px;" id="temp">--</p>
                <table cellspacing="5" cellpadding="5">
                  
                    <tr>
                        <td>Min</td>
                        <td>Max</td>
                        <td>Average</td>
                    </tr>
                    <tr>
                        <td><?php echo $min_temp['min_amount']; ?> &deg;C</td>
                        <td><?php echo $max_temp['max_amount']; ?> &deg;C</td>
                        <td><?php echo round($avg_temp['avg_amount'], 2); ?> &deg;C</td>
                    </tr>
                </table>
            </div>
            <div class="box gauge--4">
                <h3>Pressure</h3>
                <div class="mask">
                  <div class="semi-circle"></div>
                  <div class="semi-circle--mask"></div>
                </div>
                <p style="font-size: 30px;" id="pressure">--</p>
                <table cellspacing="5" cellpadding="5">
                  
                    <tr>
                        <td>Min</td>
                        <td>Max</td>
                        <td>Average</td>
                    </tr>
                    <tr>
                        <td><?php echo $min_pressure['min_amount']; ?> &deg;C</td>
                        <td><?php echo $max_pressure['max_amount']; ?> &deg;C</td>
                        <td><?php echo round($avg_pressure['avg_amount'], 2); ?> &deg;C</td>
                    </tr>
                </table>
            </div>
            <div class="box gauge--5">
                <h3>light Intensity</h3>
                <div class="mask">
                  <div class="semi-circle"></div>
                  <div class="semi-circle--mask"></div>
                </div>
                <p style="font-size: 30px;" id="intensity">--</p>
                <table cellspacing="5" cellpadding="5">
                  
                    <tr>
                        <td>Min</td>
                        <td>Max</td>
                        <td>Average</td>
                    </tr>
                    <tr>
                        <td><?php echo $min_lightntensity['min_amount']; ?></td>
                        <td><?php echo $max_lightntensity['max_amount']; ?></td>
                        <td><?php echo round($avg_lightntensity['avg_amount'], 2); ?></td>
                    </tr>
                </table>
            </div>
        </section>
    </div>
    <h3 style="margin:30px 0px 5px 0px;">Table for last <?php echo $readings_count; ?> sensor readings</h3>
    <div class="table">
        
                <?php
            echo   '
                    <table cellspacing="5" cellpadding="5" id="tableReadings">
                        <tr>
                            <th>ID</th>
                          
                           
                            <th>Moisture</th>
                            <th>Humidity</th>
                            <th>Temperature</th>
                            <th>Pressure</th>
                            <th>Light intensity</th>
                            <th>Timestamp</th>
                        </tr>';
        
            $result = getAllReadings($readings_count);
                if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $row_id = $row["id"];
                   
                   
                    $row_value1 = $row["value1"];
                    $row_value2 = $row["value2"];
                    $row_value3 = $row["value3"];
                    $row_value4 = $row["value4"];
                    $row_value5 = $row["value5"];
                    $row_reading_time = $row["reading_time"];
                    // Uncomment to set timezone to - 1 hour (you can change 1 to any number)
                    //$row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time - 1 hours"));
                    // Uncomment to set timezone to + 7 hours (you can change 7 to any number)
                    $row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time + 1 hours"));
        
                    echo '<tr>
                            <td>' . $row_id . '</td>
                          
                            
                            <td>' . $row_value1 . '</td>
                            <td>' . $row_value2 . '</td>
                            <td>' . $row_value3 . '</td>
                            <td>' . $row_value4 . '</td>
                            <td>' . $row_value5 . '</td>
                            <td>' . $row_reading_time . '</td>
                          </tr>';
                }
                echo '</table>';
                $result->free();
            }
        ?>        
    </div>


<script>
    var value1 = <?php echo $last_reading_moisture; ?>;
    var value2 = <?php echo $last_reading_humi; ?>;
    var value3 = <?php echo $last_reading_temp; ?>;
    var value4 = <?php echo $last_reading_pressure; ?>;
    var value5 = <?php echo $last_reading_lightntensity; ?>;
   

    setMoisture(value1);
    setHumidity(value2);
    setTemperature(value3);
    setPressure(value4);
    setIntensity(value5);

    function setTemperature(curVal){
        //set range for Temperature in Celsius -5 Celsius to 38 Celsius
        var minTemp = -5.0;
        var maxTemp = 38.0;
        //set range for Temperature in Fahrenheit 23 Fahrenheit to 100 Fahrenheit
        //var minTemp = 23;
        //var maxTemp = 100;
        
                if (value5 < 700) {
                if (value2 >= 17 && value2 <= 18) {
                  $("#temp-txt").text(" It's night time and the Tomato is at a healthy temperature level ");
                  $("#temp-icon").css("background-color" , "green");
                } 
               
                else {
                  $("#temp-txt").text("It's night time tomato is not at a healthy temperature level");
                   $("#temp-icon").css("background-color" , "red");
                }
        } 

           
        if (value5 > 700) {
                if (value3 >= 21 && value3 <= 28) {
                  $("#temp-txt").text(" It's day time and the Tomato is at a healthy temperature level ");
                   $("#temp-icon").css("background-color" , "green");
                } 
                
                else {
                  $("#temp-txt").text("It's day time and the Tomato is not at a healthy temperature level");
                  $("#temp-icon").css("background-color" , "red");
                }
        }

        var newVal = scaleValue(curVal, [minTemp, maxTemp], [0, 180]);
        $('.gauge--1 .semi-circle--mask').attr({
            style: '-webkit-transform: rotate(' + newVal + 'deg);' +
            '-moz-transform: rotate(' + newVal + 'deg);' +
            'transform: rotate(' + newVal + 'deg);'
        });
        $("#temp").text(curVal + ' ºC');
    }


    function setHumidity(curVal){
        //set range for Humidity percentage 0 % to 100 %
        var minHumi = 0;
        var maxHumi = 100;
        
         if (value5 < 700) {
                if (value3 >= 65 && value3 <= 75) {
                  $("#humid-txt").text(" It's night time and the Tomato is at a healthy humidity level ");
                  $("#humid-icon").css("background-color" , "green");
                } 
                
                else {
                  $("#humid-txt").text("It's night time tomato is not at a healthy humidity level");
                   $("#humid-icon").css("background-color" , "red");
                }
        } 

           
        if (value5 > 700) {
                if (value2 >= 80 && value2 <= 90) {
                  $("#humid-txt").text(" It's day time and the Tomato is at a healthy humidity level ");
                  $("#humid-icon").css("background-color" , "green");
                } 
                
                else {
                  $("#humid-txt").text("It's day time and the Tomato is not at a healthy humidity level");
                  $("#humid-icon").css("background-color" , "red");
                }
        }

        var newVal = scaleValue(curVal, [minHumi, maxHumi], [0, 180]);
        $('.gauge--2 .semi-circle--mask').attr({
            style: '-webkit-transform: rotate(' + newVal + 'deg);' +
            '-moz-transform: rotate(' + newVal + 'deg);' +
            'transform: rotate(' + newVal + 'deg);'
        });
        $("#humi").text(curVal + ' %');
    }

    function setPressure(curVal){
        //set range for Temperature in Celsius -5 Celsius to 38 Celsius
        var minPressure = 0;
        var maxPressure = 10000;
        //set range for Temperature in Fahrenheit 23 Fahrenheit to 100 Fahrenheit
        //var minTemp = 23;
        //var maxTemp = 100;

        var newVal = scaleValue(curVal, [minPressure, maxPressure], [0, 180]);
        $('.gauge--1 .semi-circle--mask').attr({
            style: '-webkit-transform: rotate(' + newVal + 'deg);' +
            '-moz-transform: rotate(' + newVal + 'deg);' +
            'transform: rotate(' + newVal + 'deg);'
        });
        $("#pressure").text(curVal + ' mb');
    }


    function setIntensity(curVal){
        //set range for Temperature in Celsius -5 Celsius to 38 Celsius
        var minIntensity = -1.0;
        var maxIntensity = 1023.0;
        //set range for Temperature in Fahrenheit 23 Fahrenheit to 100 Fahrenheit
        //var minTemp = 23;
        //var maxTemp = 100;

        var newVal = scaleValue(curVal, [minIntensity, maxIntensity], [0, 180]);
        $('.gauge--1 .semi-circle--mask').attr({
            style: '-webkit-transform: rotate(' + newVal + 'deg);' +
            '-moz-transform: rotate(' + newVal + 'deg);' +
            'transform: rotate(' + newVal + 'deg);'
        });
        $("#intensity").text(curVal);
    }


    function setMoisture(curVal){
        //set range for Temperature in Celsius -5 Celsius to 38 Celsius
        var minMoisture = 0;
        var maxMoisture = 100;
        //set range for Temperature in Fahrenheit 23 Fahrenheit to 100 Fahrenheit
        //var minTemp = 23;
        //var maxTemp = 100;
        
        
        if (value2 >= 80) {
          $("#moisture-txt").text(" Soil is wet ");
          $("#moisture-icon").css("background-color" , "green");
        } else {
          $("#moisture-txt").text(" Soil is dry ");
          $("#moisture-icon").css("background-color" , "red");
        }

        var newVal = scaleValue(curVal, [minMoisture, maxMoisture], [0, 180]);
        $('.gauge--1 .semi-circle--mask').attr({
            style: '-webkit-transform: rotate(' + newVal + 'deg);' +
            '-moz-transform: rotate(' + newVal + 'deg);' +
            'transform: rotate(' + newVal + 'deg);'
        });
        $("#moisture").text(curVal + ' %');
    }


    function scaleValue(value, from, to) {
        var scale = (to[1] - to[0]) / (from[1] - from[0]);
        var capped = Math.min(from[1], Math.max(from[0], value)) - from[0];
        return ~~(capped * scale + to[0]);
    }
</script>
<script>
    var countDownDate = <?php 
    echo strtotime("$date $h:$m:$s" ) ?> * 1000;
    var now = <?php echo time() ?> * 1000;

    // Update the count down every 1 second
    var x = setInterval(function() {
    now = now + 1000;
    // Find the distance between now an the count down date
    var distance = countDownDate - now;
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    // Output the result in an element with id="demo"
    document.getElementById("demo").innerHTML = days + "d " + hours + "h " +
    minutes + "m " + seconds + "s ";
    // If the count down is over, write some text 
    if (distance < 0) {
    clearInterval(x);
     document.getElementById("demo").innerHTML = "EXPIRED";
    }
        
    }, 1000);

</script>
</body>
</html>