<?php
require_once('../../php_data_api/private/initialize.php');
require_once('morning_shef_data.php');
// require_login();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set("xdebug.var_display_max_children", '-1');
ini_set("xdebug.var_display_max_data", '-1');
ini_set("xdebug.var_display_max_depth", '-1');

date_default_timezone_set('America/Chicago');
if (date_default_timezone_get()) {
    //echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
}
if (ini_get('date.timezone')) {
    //echo 'date.timezone: ' . ini_get('date.timezone');
}

// preview
$preview = $_GET['preview'] ?? null;

/*
|--------------------------------------------------------------------------
| SHEF PREVIEW
|--------------------------------------------------------------------------
*/
if ($preview == "True") {
    print_r($gen_date);
    echo "<br>";
    print_r($pool_title);
    echo "<br>";
    print_r($tw_title);
    echo "<br>";
    echo "<br>";
    print_r($ld24_pool_data);
    echo "<br>";
    print_r($ld24_tw_data);
    echo "<br>";
    print_r($ld25_pool_data);
    echo "<br>";
    print_r($ld25_tw_data);
    echo "<br>";
    print_r($ldmp_pool_data);
    echo "<br>";
    print_r($ldmp_tw_data);
    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo "<br>";
    /*
|--------------------------------------------------------------------------
| PREVIEW LAKE
|--------------------------------------------------------------------------
*/
    print_r($lake_today_title);
    echo "<br>";
    print_r($lake_forecast_title);
    echo "<br>";
    echo "<br>";
    print_r($line_carlyle);
    echo "<br>";
    print_r($line_carlyle_2);
    echo "<br>";
    print_r($line_shelbyville);
    echo "<br>";
    print_r($line_shelbyville_2);
    echo "<br>";
    print_r($line_wappapello);
    echo "<br>";
    print_r($line_wappapello_2);
    echo "<br>";
    print_r($line_rend);
    echo "<br>";
    print_r($line_rend_2);
    echo "<br>";
    echo "<br>";
    print_r($lake_forecast_marktwain_title_1);
    echo "<br>";
    print_r($lake_forecast_marktwain_title_2);
    echo "<br>";
    print_r($lake_forecast_marktwain_title_3);
    echo "<br>";
    echo "<br>";
    print_r($lake_forecast_marktwain_forecast);
    echo "<br>";
    print_r($lake_forecast_marktwain_today);
    echo "<br>";
    print_r($lake_forecast_marktwain_yesterday);
    echo "<br>";
    echo "<br>";
}

/*
|--------------------------------------------------------------------------
| SHEF SENT
|--------------------------------------------------------------------------
*/
if ($preview == "False") {
    // Ld Pool
    $combinedData = $gen_date;
    $combinedData .= "\n" . $pool_title;
    $combinedData .= "\n" . $tw_title;
    $combinedData .= "\n" . $ld24_pool_data;
    $combinedData .= "\n" . $ld24_tw_data;
    $combinedData .= "\n" . $ld25_pool_data;
    $combinedData .=  "\n" . $ld25_tw_data;
    $combinedData .=  "\n" . $ldmp_pool_data;
    $combinedData .=  "\n" . $ldmp_tw_data;
    $combinedData .= "\n";

    // Lakes
    $combinedData .= "\n" . $lake_today_title;
    $combinedData .=  "\n" . $lake_forecast_title;
    $combinedData .=  "\n" . $line_carlyle;
    $combinedData .=  "\n" . $line_carlyle_2;
    $combinedData .=  "\n" . $line_shelbyville;
    $combinedData .=  "\n" . $line_shelbyville_2;
    $combinedData .=  "\n" . $line_wappapello;
    $combinedData .=  "\n" . $line_wappapello_2;
    $combinedData .=  "\n" . $line_rend;
    $combinedData .=  "\n" . $line_rend_2;
    $combinedData .= "\n";

    // Mark Twain
    $combinedData .=  "\n" . $lake_forecast_marktwain_title_1;
    $combinedData .=  "\n" . $lake_forecast_marktwain_title_2;
    $combinedData .=  "\n" . $lake_forecast_marktwain_title_3;
    $combinedData .=  "\n" . $lake_forecast_marktwain_forecast;
    $combinedData .=  "\n" . $lake_forecast_marktwain_today;
    $combinedData .=  "\n" . $lake_forecast_marktwain_yesterday;

    /*
    |--------------------------------------------------------------------------
    | EMAIL DOCUMENT
    |--------------------------------------------------------------------------
    */
    // Specify the file path where you want to export the data
    $format = ".shef";
    $name = "nws_morning_shef";
    // $name = "morning_shef.shef";
    $file = $name . $format; // for testing purposes

    // $filename_date_time = "morning_shef" . $date_Ymd . ".shef";
    $filename_date_time = $name . "_" . $date_Ymd . $format;

    // Open the file for writing
    $fileHandle = fopen($file, 'w');

    if ($fileHandle === false) {
        echo "Error opening the file.";
    } else {
        // Write the data to the file
        fwrite($fileHandle, $combinedData);

        // Close the file handle
        fclose($fileHandle);

        echo "<p><b>NWS Shef data has been saved as '" . $file . "'" .  "</b>" . "</p>";
    }

    // View Morning Shef
    echo "<p>" . "<a href='" . $file . "' target='_blank'>" . "View NWS Morning Shef" . "</a>" . "</p>";

    $to = "ivan.h.nguyen@usace.army.mil,allen.phillips@usace.army.mil,DLL-CEMVS-WATER-MANAGERS@usace.army.mil";
    // $to = "ivan.h.nguyen@usace.army.mil";
    $subject = "NWS Morning Shef Sent" .  " " . $date_Ymd;
    $message = $combinedData;

    // Additional headers
    $headers = "From: noreply@mvs.usace.army.mil"; // Replace with the sender's email address

    // Send the email
    $mailSent = mail($to, $subject, $message, $headers);

    if ($mailSent) {
        echo "<p><b>Email sent successfully!</b></p>";
        echo "<p>" . $to . "</p>";
    } else {
        echo "Email sending failed.";
    }

    echo "<br>";

    $filepathname = "/wmdata/DailyOps/morning_shef/" . $file . "";
    $content = $combinedData;

    // Save the file
    if (file_put_contents($filepathname, $content) !== false) {
        echo "<p>" . "File saved successfully! " . $filepathname . "</p>";
        echo "<p>" . "Z:\\DailyOps\\morning_shef\\" . $file . "</p>";
    } else {
        echo "Failed to save the file.";
    }

    $filepathname_date_time = "/wmdata/DailyOps/morning_shef/" . $filename_date_time . "";

    // Save the file
    if (file_put_contents($filepathname_date_time, $content) !== false) {
        echo "<p>" . "File saved successfully! " . $filepathname_date_time . "</p>";
        echo "<p>" . "Z:\\DailyOps\\morning_shef\\" . $filename_date_time . "</p>";
    } else {
        echo "<p>" . "Failed to save the file." . "</p>";
    }

    // PUBLIC VERIFY LINK
    echo "<p>";
    echo "<a href='https://www.mvs-wc.usace.army.mil/" . $file . ".txt' target='_blank'>";
    echo "View NWS Morning Shef on Public Web Link as Text File";
    echo "</a>";
    echo "</p>";
}