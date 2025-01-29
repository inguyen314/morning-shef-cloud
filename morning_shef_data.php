<?php
require_once('../../php_data_api/private/initialize.php');
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

// lock and dam
$userLD24Input = $_GET['userLD24Input'] ?? null;
$userLD25Input = $_GET['userLD25Input'] ?? null;
$userMelPriceInput = $_GET['userMelPriceInput'] ?? null;

// lakes
$userCarlyleInput = $_GET['userCarlyleInput'] ?? null;
$userShelbyvilleInput = $_GET['userShelbyvilleInput'] ?? null;
$userMarkTwainInput = $_GET['userMarkTwainInput'] ?? null;
$userWappapelloInput = $_GET['userWappapelloInput'] ?? null;
$userRendInput = $_GET['userRendInput'] ?? null;

/*
|--------------------------------------------------------------------------
| SET VARIABLES AND OPTION
|--------------------------------------------------------------------------
*/
$set_options = set_options2($db);
$date_md =  date('md');
$date_Ymd =  date('Ymd');

/*
|--------------------------------------------------------------------------
| GET POOL DATA
|--------------------------------------------------------------------------
*/
// Set Pool Title
$end = ".END";
$ld_forecast_title_1 =  ": 5 DAYS POOL FORECAST IN PROJECT STAGE AT 6AM";
$ld_forecast_title_2 =  ".B STL " . $date_md . " C DH0600/DC" . $date_md . "0700/DRD+1/HPIF/DRD+2/HPIF/DRD+3/HPIF/DRD+4/HPIF/DRD+5/HPIF";

// Get Ld24 Pool
$ld24_pool_forecast = get_ld24_pool_2($db);
$location_ld24_pool = $ld24_pool_forecast[0]->damlock;
$values_ld24_pool = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ld24_pool_forecast, 0, 5)));
$min_max_values_ld24_pool = number_format((float) min($ld24_pool_forecast[5]->value, $ld24_pool_forecast[6]->value), 1, '.', '') . ' - ' . number_format((float) max($ld24_pool_forecast[5]->value, $ld24_pool_forecast[6]->value), 1, '.', '');
$line_ld24_pool = "{$location_ld24_pool}  {$values_ld24_pool} : CLARKSVILLE LD 24 --> HINGE PT LOUSIANA {$min_max_values_ld24_pool} FT";

// Get Ld25 Pool
$ld25_pool_forecast = get_ld25_pool_2($db);
$location_ld25_pool = $ld25_pool_forecast[0]->damlock;
$values_ld25_pool = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ld25_pool_forecast, 0, 5)));
$min_max_values_ld25_pool = number_format((float) min($ld25_pool_forecast[5]->value, $ld25_pool_forecast[6]->value), 1, '.', '') . ' - ' . number_format((float) max($ld25_pool_forecast[5]->value, $ld25_pool_forecast[6]->value), 1, '.', '');
$line_ld25_pool = "{$location_ld25_pool}  {$values_ld25_pool} : WINFIELD LD 25 --> HINGE PT MOSIER LDG {$min_max_values_ld25_pool} FT";

// Get Ldmp Pool
$ldmp_pool_forecast = get_ldmp_pool_2($db);
$location_ldmp_pool = $ldmp_pool_forecast[0]->damlock;
$values_ldmp_pool = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ldmp_pool_forecast, 0, 5)));
$min_max_values_ldmp_pool = number_format((float) min($ldmp_pool_forecast[5]->value, $ldmp_pool_forecast[6]->value), 1, '.', '') . ' - ' . number_format((float) max($ldmp_pool_forecast[5]->value, $ldmp_pool_forecast[6]->value), 1, '.', '');
$line_ldmp_pool = "{$location_ldmp_pool}  {$values_ldmp_pool} :ALTON LD 26 --> HINGE PT GRAFTON {$min_max_values_ldmp_pool} FT";


/*
|--------------------------------------------------------------------------
| GET TW DATA
|--------------------------------------------------------------------------
*/
// Set Tw Title
$ld_tw_forecast_title_1 =  ": TODAYS OBSERVED TW AT 6AM AND 5 DAY FORECAST IN STAGE AT 6AM";
$ld_tw_forecast_title_2 =  ".B STL " . $date_md . " C DH0600/DC" . $date_md . "0700/HT/DRD+1/HTIF/DRD+2/HTIF/DRD+3/HTIF/DRD+4/HTIF/DRD+5/HTIF";

// Get Ld24 Tw
$ld24_tw_forecast = get_ld24_tw($db);
$location_ld24_tw = $ld24_tw_forecast[0]->damlock;
$values_ld24_tw = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ld24_tw_forecast, 0, 6)));
$line_ld24_tw = "{$location_ld24_tw}  {$values_ld24_tw}";

// Get Ld25 Tw
$ld25_tw_forecast = get_ld25_tw($db);
$location_ld25_tw = $ld25_tw_forecast[0]->damlock;
$values_ld25_tw = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ld25_tw_forecast, 0, 6)));
$line_ld25_tw = "{$location_ld25_tw}  {$values_ld25_tw}";

// Get Ldmp Tw
$ldmp_tw_forecast = get_ldmp_tw($db);
$location_ldmp_tw = $ldmp_tw_forecast[0]->damlock;
$values_ldmp_tw = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ldmp_tw_forecast, 0, 6)));
$line_ldmp_tw = "{$location_ldmp_tw}  {$values_ldmp_tw}";

/*
|--------------------------------------------------------------------------
| GET LAKE FORECAST DATA
|--------------------------------------------------------------------------
*/
// Set Lake Title
$lake_forecast_title_1 = ": TODAYS LAKE FLOW AND 5 DAY FORECAST (6AM INSTANTANEOUS FORECAST VALUE)";
$lake_forecast_title_2  = ".B STL " . $date_md . " C DH0600/DC" . $date_md . "0700/QT/DRD+1/QTIF/DRD+2/QTIF/DRD+3/QTIF/DRD+4/QTIF/DRD+5/QTIF";

// Get Carlyle Data
$carlyle_forecast = get_carlyle_forecast($db);
$allOutflowsNotNullCarlyle = true;

foreach ($carlyle_forecast as $forecast_shelby) {
    if (is_null($forecast_shelby->outflow)) {
        $allOutflowsNotNullCarlyle = false;
        break; // Exit the loop early if any outflow is null
    }
}

// Check the data being returned
if (count($carlyle_forecast) == 6 && $allOutflowsNotNullCarlyle) {
    // The object array has 6 elements and all outflows are not null
} else {
    // Handle the cases where the number of elements is not as expected or some outflows are null
    echo "<p class='monochrome-button-warning'><b>ERROR! Check Carlyle Lake Sheet</b></p><br>";
}

foreach ($carlyle_forecast as $carlyle) {
    if ($carlyle->station === 'CAYI2') {
        $carlyle_outflow_values[] = $carlyle->outflow;
        $carlyle_station_value[] = $carlyle->station;
        $carlyle_lake_value[] = $carlyle->lake;
    }
}
// Print all outflow values separated by forward slashes
$line_carlyle = $carlyle_station_value[0] . " " . implode('/', $carlyle_outflow_values) . " : " . $carlyle_lake_value[0];

// Get Shelbyville Data
$shelbyville_forecast = get_shelbyville_forecast($db);
$allOutflowsNotNullShelbyville = true;

foreach ($shelbyville_forecast as $forecast_shelby) {
    if (is_null($forecast_shelby->outflow)) {
        $allOutflowsNotNullShelbyville = false;
        break; // Exit the loop early if any outflow is null
    }
}

// Check the data being returned
if (count($shelbyville_forecast) == 6 && $allOutflowsNotNullShelbyville) {
    // The object array has 6 elements and all outflows are not null
} else {
    // Handle the cases where the number of elements is not as expected or some outflows are null
    echo "<p class='monochrome-button-warning'><b>ERROR! Check Shelbyville Lake Sheet</b></p><br>";
}


foreach ($shelbyville_forecast as $shelbyville) {
    if ($shelbyville->station === 'SBYI2') {
        $shelbyville_outflow_values[] = $shelbyville->outflow;
        $shelbyville_station_value[] = $shelbyville->station;
        $shelbyville_lake_value[] = $shelbyville->lake;
    }
}
// Print all outflow values separated by forward slashes
$line_shelbyville = $shelbyville_station_value[0] . " " . implode('/', $shelbyville_outflow_values) . " : " . $shelbyville_lake_value[0];

// Get Wappapello
$wappapello_forecast = get_wappapello_forecast($db);
$allOutflowsNotNullWappapello = true;

foreach ($wappapello_forecast as $forecast_shelby) {
    if (is_null($forecast_shelby->outflow)) {
        $allOutflowsNotNullWappapello = false;
        break; // Exit the loop early if any outflow is null
    }
}

if (count($wappapello_forecast) == 6 && $allOutflowsNotNullWappapello) {
    // The object array has 6 elements and all outflows are not null
} else {
    // Handle the cases where the number of elements is not as expected or some outflows are null
    echo "<p class='monochrome-button-warning'><b>ERROR! Check Wappapello Lake Sheet</b></p><br>";
}

foreach ($wappapello_forecast as $wappapello) {
    if ($wappapello->station === 'WPPM7') {
        $wappapello_outflow_values[] = $wappapello->outflow;
        $wappapello_station_value[] = $wappapello->station;
        $wappapello_lake_value[] = $wappapello->lake;
    }
}
// Print all outflow values separated by forward slashes
$line_wappapello = $wappapello_station_value[0] . " " . implode('/', $wappapello_outflow_values) . " : " . $wappapello_lake_value[0];

// Get Rend
$rend_forecast = get_rend_forecast($db);
$allOutflowsNotNullRend = true;

foreach ($rend_forecast as $forecast_shelby) {
    if (is_null($forecast_shelby->outflow)) {
        $allOutflowsNotNullRend = false;
        break; // Exit the loop early if any outflow is null
    }
}

if (count($rend_forecast) == 6 && $allOutflowsNotNullRend) {
    // The object array has 6 elements and all outflows are not null
} else {
    // Handle the cases where the number of elements is not as expected or some outflows are null
    echo "<p class='monochrome-button-warning'><b>ERROR! Check Rend Lake Sheet</b></p><br>";
}

foreach ($rend_forecast as $rend) {
    if ($rend->station === 'RNDI2') {
        $rend_outflow_values[] = $rend->outflow;
        $rend_station_value[] = $rend->station;
        $rend_lake_value[] = $rend->lake;
    }
}
// Print all outflow values separated by forward slashes
$line_rend = $rend_station_value[0] . " " . implode('/', $rend_outflow_values) . " : " . $rend_lake_value[0];


/*
|--------------------------------------------------------------------------
| GET MARK TWAIN LAKE FORECAST DATA
|--------------------------------------------------------------------------
*/
// Get Mark Twain
$mark_twain_forecast = get_mark_twain_forecast($db);
$allOutflowsNotNullMarkTwain = true;
foreach ($mark_twain_forecast as $forecast) {
    if (is_null($forecast->outflow)) {
        $allOutflowsNotNullMarkTwain = false;
        break;
    }
}

if (count($mark_twain_forecast) == 6 && $allOutflowsNotNullMarkTwain) {
} else {
    echo "<p class='monochrome-button-warning'><b>ERROR! Check MarkTwain Lake Sheet</b></p><br>";
}

foreach ($mark_twain_forecast as $mark_twain) {
    if ($mark_twain->station === 'CDAM7') {
        $mark_twain_outflow_values[] = $mark_twain->outflow;
        $mark_twain_station_value[] = $mark_twain->station;
        $mark_twain_lake_value[] = $mark_twain->lake;
    }
}
// Print all outflow values separated by forward slashes
$line_mark_twain = implode('/', $mark_twain_outflow_values);

// Get Mark Twain Yesterday
$marktwain_yesterday_forecast = get_mark_twain_yesterday_forecast($db);
$allOutflowsNotNullMarkTwainYesterday = true;

if (is_null($marktwain_yesterday_forecast->value)) {
    $allOutflowsNotNullMarkTwainYesterday = false;
}

if (!is_null($marktwain_yesterday_forecast) && $allOutflowsNotNullMarkTwainYesterday) {
} else {
    echo "<p class='monochrome-button-warning'><b>ERROR! Check MarkTwainYesterday Lake Sheet</b></p><br>";
}

if ($marktwain_yesterday_forecast->station === 'CDAM7') {
    // Format and print the line
    $line_marktwain_yesterday = number_format($marktwain_yesterday_forecast->value, 2);
}

// Get Mark Twain Title
$lake_forecast_marktwain_title_1 = ": MARK TWAIN FLOW AND 5 DAY FORECAST (6AM DAILY AVERAGE FORECAST VALUE)";
$lake_forecast_marktwain_title_2  = ".E CDAM7 " . $date_md  . " C DH0000/DC" . $date_md . "0700/QTDF/DID1/" . $line_mark_twain;

$lake_forecast_marktwain_yesterday_title_1 = ": MARK TWAIN LAKE FLOW YESTERDAY (MIDNIGHT DAILY AVERAGE FORECAST VALUE)";
$lake_forecast_marktwain_yesterday_title_2  = ".E CDAM7 " . $date_md . " C DH0000/DC" . $date_md . "0700/QTD/DID1/" . $line_marktwain_yesterday;


// Note Title
$lake_note_title = ": CEMVS RESERVOIR NOTES";
$ld_note_title = ": CEMVS LD NOTES";

/*
|--------------------------------------------------------------------------
| PREVIEW LD DATA
|--------------------------------------------------------------------------
*/
if ($preview == "True") {
    print_r($ld_forecast_title_1);
    echo "<br>";
    print_r($ld_forecast_title_2);
    echo "<br>";
    print_r($line_ld24_pool);
    echo "<br>";
    print_r($line_ld25_pool);
    echo "<br>";
    print_r($line_ldmp_pool);
    echo "<br>";
    print_r($end);
    echo "<br>";
    echo "<br>";
    print_r($ld_tw_forecast_title_1);
    echo "<br>";
    print_r($ld_tw_forecast_title_2);
    echo "<br>";
    print_r($line_ld24_tw);
    echo "<br>";
    print_r($line_ld25_tw);
    echo "<br>";
    print_r($line_ldmp_tw);
    echo "<br>";
    print_r($end);
    echo "<br>";
    echo "<br>";
}

/*
|--------------------------------------------------------------------------
| PREVIEW LAKE
|--------------------------------------------------------------------------
*/
if ($preview == "True") {
    print_r($lake_forecast_title_1);
    echo "<br>";
    print_r($lake_forecast_title_2);
    echo "<br>";
    print_r($line_carlyle);
    echo "<br>";
    print_r($line_shelbyville);
    echo "<br>";
    print_r($line_wappapello);
    echo "<br>";
    print_r($line_rend);
    echo "<br>";
    print_r($end);
    echo "<br>";
    echo "<br>";
    print_r($lake_forecast_marktwain_title_1);
    echo "<br>";
    print_r($lake_forecast_marktwain_title_2);
    echo "<br>";
    echo "<br>";
    print_r($lake_forecast_marktwain_yesterday_title_1);
    echo "<br>";
    print_r($lake_forecast_marktwain_yesterday_title_2);
    echo "<br>";
    echo "<br>";
    print_r($lake_note_title);
    echo "<br>";
}

/*
|--------------------------------------------------------------------------
| EMAIL
|--------------------------------------------------------------------------
*/
if ($preview == "False") {
    // Ld Pool
    $combinedData = $ld_forecast_title_1;
    $combinedData .= "\n" . $ld_forecast_title_2;
    $combinedData .= "\n" . $line_ld24_pool;
    $combinedData .= "\n" . $line_ld25_pool;
    $combinedData .= "\n" . $line_ldmp_pool;
    $combinedData .=  "\n" . $end;
    $combinedData .= "\n";

    // Ld Tw
    $combinedData .= "\n" . $ld_tw_forecast_title_1;
    $combinedData .= "\n" . $ld_tw_forecast_title_2;
    $combinedData .= "\n" . $line_ld24_tw;
    $combinedData .= "\n" . $line_ld25_tw;
    $combinedData .= "\n" . $line_ldmp_tw;
    $combinedData .=  "\n" . $end;
    $combinedData .= "\n";

    // Ld Notes
    $combinedData .= "\n" . $ld_note_title;
    $combinedData .= "\n" . ": LD24: " . $userLD24Input;
    $combinedData .= "\n" . ": LD25: " . $userLD25Input;
    $combinedData .= "\n" . ": MEL PRICE: " . $userMelPriceInput;
    $combinedData .= "\n";

    // Lakes
    $combinedData .= "\n" . $lake_forecast_title_1;
    $combinedData .=  "\n" . $lake_forecast_title_2;
    $combinedData .=  "\n" . $line_carlyle;
    $combinedData .=  "\n" . $line_shelbyville;
    $combinedData .=  "\n" . $line_wappapello;
    $combinedData .=  "\n" . $line_rend;
    $combinedData .=  "\n" . $end;

    // Mark Twain
    $combinedData .= "\n" . "\n" . $lake_forecast_marktwain_title_1;
    $combinedData .=  "\n" . $lake_forecast_marktwain_title_2;
    //$combinedData .=  "\n" . $end;

    // Mark Twain Yesterday
    $combinedData .= "\n" . "\n" . $lake_forecast_marktwain_yesterday_title_1;
    $combinedData .=  "\n" . $lake_forecast_marktwain_yesterday_title_2;

    // Lake Notes
    $combinedData .= "\n" . "\n" . $lake_note_title;
    $combinedData .= "\n" . ": CARLYLE: " . $userCarlyleInput . "\n" . ": SHELBYVILLE: " . $userShelbyvilleInput;
    $combinedData .= "\n" . ": MARK TWAIN: " . $userMarkTwainInput . "\n" . ": WAPPAPELLO: " . $userWappapelloInput;
    $combinedData .= "\n" . ": REND: " . $userRendInput;


    /*
    |--------------------------------------------------------------------------
    | EMAIL DOCUMENT
    |--------------------------------------------------------------------------
    */
    // Specify the file path where you want to export the data
    // $filename = "morning_shef.shef";
    $filename = "morning_shef_test.shef"; // for testing purposes

    // $filename_date_time = "morning_shef" . $date_Ymd . ".shef";
    $filename_date_time = "morning_shef_test".$date_Ymd.".shef";

    // Open the file for writing
    $fileHandle = fopen($filename, 'w');

    if ($fileHandle === false) {
        echo "Error opening the file.";
    } else {
        // Write the data to the file
        fwrite($fileHandle, $combinedData);

        // Close the file handle
        fclose($fileHandle);

        echo "<p><b>Data has been exported to " . $filename . "</b>" . "</p>";
    }

    // View Morning Shef
    echo "<p>" . "<a href='" . $filename . "' target='_blank'>" . "View Morning Shef" . "</a>" . "</p>";

    // $to = "ivan.h.nguyen@usace.army.mil,allen.phillips@usace.army.mil,DLL-CEMVS-WATER-MANAGERS@usace.army.mil"; // Replace with the recipient's email address
    $to = "ivan.h.nguyen@usace.army.mil";
    $subject = "MVS PHP Morning Shef Sent to NWS" .  " " . $date_Ymd;
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

    $filepathname = "/wmdata/DailyOps/morning_shef/" . $filename . "";
    $content = $combinedData;

    // Save the file
    if (file_put_contents($filepathname, $content) !== false) {
        echo "<p>" . "File saved successfully! " . $filepathname . "</p>";
    } else {
        echo "Failed to save the file.";
    }

    $filepathname_date_time = "/wmdata/DailyOps/morning_shef/" . $filename_date_time . "";

    // Save the file
    if (file_put_contents($filepathname_date_time, $content) !== false) {
        echo "<p>" . "File saved successfully! " . $filepathname_date_time . "</p>";
    } else {
        echo "<p>" . "Failed to save the file." . "</p>";
    }

    // PUBLIC VERIFY LINK
    echo "<p>";
    echo "<a href='https://www.mvs-wc.usace.army.mil/morning_shef_test.shef.txt' target='_blank'>";
    echo "View Morning Shef Public";
    echo "</a>";
    echo "</p>";

    // Cron
    echo "<p>cronjob push morning shef to public web every hour, /wm/mvs/wm_web/var/apache2/2.4/htdocs/bin/morning_shef.sh</p>";
}