<?php
/*
|--------------------------------------------------------------------------
| SET VARIABLES AND OPTION
|--------------------------------------------------------------------------
*/
$set_options = set_options2($db);
$date_md = date('md');
$date_Ymd = date('Ymd');
$date_plus_one_day = date('Ymd', strtotime('+1 day'));

/*
|--------------------------------------------------------------------------
| GET POOL DATA
|--------------------------------------------------------------------------
*/
// Set Pool Title
$pool_title =  ": 5 DAYS POOL FORECAST IN PROJECT NGVD29 STAGE FT AT 6AM";

// Get Ld24 Pool
$ld24_pool_forecast = get_ld24_pool_2($db);
$location_ld24_pool = $ld24_pool_forecast[0]->damlock;
$values_ld24_pool = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ld24_pool_forecast, 0, 5)));
$min_max_values_ld24_pool = number_format((float) min($ld24_pool_forecast[5]->value, $ld24_pool_forecast[6]->value), 1, '.', '') . ' - ' . number_format((float) max($ld24_pool_forecast[5]->value, $ld24_pool_forecast[6]->value), 1, '.', '');
$ld24_pool_data = ".ER {$location_ld24_pool} {$date_plus_one_day} Z DH1200/HPIF/DID1/{$values_ld24_pool}";

// Get Ld25 Pool
$ld25_pool_forecast = get_ld25_pool_2($db);
$location_ld25_pool = $ld25_pool_forecast[0]->damlock;
$values_ld25_pool = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ld25_pool_forecast, 0, 5)));
$min_max_values_ld25_pool = number_format((float) min($ld25_pool_forecast[5]->value, $ld25_pool_forecast[6]->value), 1, '.', '') . ' - ' . number_format((float) max($ld25_pool_forecast[5]->value, $ld25_pool_forecast[6]->value), 1, '.', '');
$ld25_pool_data = ".ER {$location_ld25_pool} {$date_plus_one_day} Z DH1200/HPIF/DID1/{$values_ld25_pool}";

// Get Ldmp Pool
$ldmp_pool_forecast = get_ldmp_pool_2($db);
$location_ldmp_pool = $ldmp_pool_forecast[0]->damlock;
$values_ldmp_pool = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ldmp_pool_forecast, 0, 5)));
$min_max_values_ldmp_pool = number_format((float) min($ldmp_pool_forecast[5]->value, $ldmp_pool_forecast[6]->value), 1, '.', '') . ' - ' . number_format((float) max($ldmp_pool_forecast[5]->value, $ldmp_pool_forecast[6]->value), 1, '.', '');
$ldmp_pool_data = ".ER {$location_ldmp_pool} {$date_plus_one_day} Z DH1200/HPIF/DID1/{$values_ldmp_pool}";


/*
|--------------------------------------------------------------------------
| GET TW DATA
|--------------------------------------------------------------------------
*/
// Set Tw Title
$tw_title =  ": 5 DAYS TW FORECAST IN STAGE FT AT 6AM";

// Get Ld24 Tw
$ld24_tw_forecast = get_ld24_tw($db);
$location_ld24_tw = $ld24_tw_forecast[0]->damlock;
$values_ld24_tw = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ld24_tw_forecast, 0, 6)));
$values_array_ld24_tw = explode('/', $values_ld24_tw); // Convert string to array
array_shift($values_array_ld24_tw); // Remove the first value
$values_forecast_ld24_tw = implode('/', $values_array_ld24_tw); // Convert back to string
$ld24_tw_data = ".ER {$location_ld24_tw} {$date_plus_one_day} Z DH1200/HTIF/DID1/{$values_forecast_ld24_tw}";

// Get Ld25 Tw
$ld25_tw_forecast = get_ld25_tw($db);
$location_ld25_tw = $ld25_tw_forecast[0]->damlock;
$values_ld25_tw = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ld25_tw_forecast, 0, 6)));
$values_array_ld25_tw = explode('/', $values_ld25_tw); // Convert string to array
array_shift($values_array_ld25_tw); // Remove the first value
$values_forecast_ld25_tw = implode('/', $values_array_ld25_tw); // Convert back to string
$ld25_tw_data = ".ER {$location_ld25_tw} {$date_plus_one_day} Z DH1200/HTIF/DID1/{$values_forecast_ld25_tw}";

// Get Ldmp Tw
$ldmp_tw_forecast = get_ldmp_tw($db);
$location_ldmp_tw = $ldmp_tw_forecast[0]->damlock;
$values_ldmp_tw = implode('/', array_map(function ($item) {
    return number_format((float) $item->value, 2, '.', '');
}, array_slice($ldmp_tw_forecast, 0, 6)));
$values_array_ldmp_tw = explode('/', $values_ldmp_tw); // Convert string to array
array_shift($values_array_ldmp_tw); // Remove the first value
$values_forecast_ldmp_tw = implode('/', $values_array_ldmp_tw); // Convert back to string
$ldmp_tw_data = ".ER {$location_ldmp_tw} {$date_plus_one_day} Z DH1200/HTIF/DID1/{$values_forecast_ldmp_tw}";

/*
|--------------------------------------------------------------------------
| GET LAKE FORECAST DATA
|--------------------------------------------------------------------------
*/
// Set Lake Title
$lake_today_title = ": TODAYS LAKE FLOW (6AM INSTANTANEOUS VALUE)";
$lake_forecast_title = ": 5 DAYS LAKE FORECAST (6AM INSTANTANEOUS FORECAST VALUE)";
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

$carlyle_today_outflow_value = $carlyle_outflow_values[0];
$carlyle_forecast_outflow_values = array_slice($carlyle_outflow_values, 1);
// Print all outflow values separated by forward slashes
$line_carlyle = ".ER " . $carlyle_station_value[0] . " " . $date_Ymd . " Z DH1200/QT/DID1/" . $carlyle_today_outflow_value;
$line_carlyle_2 = ".ER " . $carlyle_station_value[0] . " " . $date_plus_one_day . " Z DH1200/QTIF/DID1 " . implode('/', $carlyle_forecast_outflow_values);
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

$shelbyville_today_outflow_value = $shelbyville_outflow_values[0];
$shelbyville_forecast_outflow_values = array_slice($shelbyville_outflow_values, 1);
// Print all outflow values separated by forward slashes
$line_shelbyville = ".ER " . $shelbyville_station_value[0] . " " . $date_Ymd . " Z DH1200/QT/DID1/" . $shelbyville_today_outflow_value;
$line_shelbyville_2 = ".ER " . $shelbyville_station_value[0] . " " . $date_plus_one_day . " Z DH1200/QTIF/DID1 " . implode('/', $shelbyville_forecast_outflow_values);

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

$wappapello_today_outflow_value = $wappapello_outflow_values[0];
$wappapello_forecast_outflow_values = array_slice($wappapello_outflow_values, 1);
// Print all outflow values separated by forward slashes
$line_wappapello = ".ER " . $wappapello_station_value[0] . " " . $date_Ymd . " Z DH1200/QT/DID1/" . $wappapello_today_outflow_value;
$line_wappapello_2 = ".ER " . $wappapello_station_value[0] . " " . $date_plus_one_day . " Z DH1200/QTIF/DID1 " . implode('/', $wappapello_forecast_outflow_values);

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

$rend_today_outflow_value = $rend_outflow_values[0];
$rend_forecast_outflow_values = array_slice($rend_outflow_values, 1);
// Print all outflow values separated by forward slashes
$line_rend = ".ER " . $rend_station_value[0] . " " . $date_Ymd . " Z DH1200/QT/DID1/" . $rend_today_outflow_value;
$line_rend_2 = ".ER " . $rend_station_value[0] . " " . $date_plus_one_day . " Z DH1200/QTIF/DID1 " . implode('/', $rend_forecast_outflow_values);


/*
|--------------------------------------------------------------------------
| GET MARK TWAIN LAKE FORECAST DATA
|--------------------------------------------------------------------------
*/
// Get Mark Twain
$mark_twain_forecast = get_mark_twain_forecast_no_rounding($db);
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
// var_dump($line_mark_twain);

// Split the string into an array
$values = explode('/', $line_mark_twain);

// Remove the first value
array_shift($values);

// Reconstruct the string without the first value
$line_mark_twain_forecast = implode('/', array_map(fn($v) => $v / 1000, $values));

// Extract the first value
$line_mark_twain_forecast_today = array_shift($values);

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

// Get Norton Bridge Tw
$norton_bridge_forecast = get_norton_bridge($db);
// var_dump($norton_bridge_forecast);
$forecast_value = number_format($norton_bridge_forecast[0]->value / 1000, 2);
// var_dump($forecast_value);
// echo $forecast_value; // Outputs: 37.65

// Get Mark Twain Title
$lake_forecast_marktwain_title_1 = ": 5 DAYS FORECAST (DAILY AVERAGE FLOW FORECAST VALUE)";
$lake_forecast_marktwain_title_2 = ": TODAY (6AM INST FLOW (KCFS) VALUE AT NORTON BRIDGE)";
$lake_forecast_marktwain_title_3 = ": FLOW YESTERDAY (DAILY AVERAGE MIDNIGHT TO MIDNIGHT)";
$lake_forecast_marktwain_forecast  = ".ER CDAM7 " . $date_plus_one_day  . " Z DH1200/QTDF/DID1/" . $line_mark_twain_forecast;
$lake_forecast_marktwain_today  = ".ER CDAM7 " . $date_Ymd  . " Z DH1200/QT/DID1/" . $forecast_value;
$lake_forecast_marktwain_yesterday  = ".ER CDAM7 " . $date_Ymd . " Z DH0600/QTD/DID1/" . $line_marktwain_yesterday;

// Note Title
$lake_note_title = ": CEMVS RESERVOIR NOTES";
$ld_note_title = ": CEMVS LD NOTES";