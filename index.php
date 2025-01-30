<!DOCTYPE html>
<html lang="en">

<?php
require_once('private/initialize.php');
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

// Check if the form is submitted and redirected to a new url to process shef data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    redirect_to(('morning_shef_data.php?preview=False'));
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Morning Shef</title>
    <meta name="Description" content="U.S. Army Corps of Engineers St. Louis District Home Page" />
    <link rel="stylesheet" href="css/body.css" />
    <link rel="stylesheet" href="css/breadcrumbs.css" />
    <link rel="stylesheet" href="css/jumpMenu.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/style.css" />
    <script type="text/javascript" src="js/main.js"></script>
    <!-- Add sidebar.css IF NOT LOAD SIDEBAR TEMPLATE -->
    <link rel="stylesheet" href="css/sidebar.css" />
    <!-- Include Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <!-- Include the Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include the Moment.js adapter for Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
    <style>
        #spinner {
            display: none;
        }
    </style>
</head>

<body>
    <div id="page-container">
        <header id="header">
            <!--Header content populated here by JavaScript Tag at end of body -->
        </header>
        <div class="page-wrap">
            <div class="container-fluid">
                <div id="breadcrumbs">
                </div>
                <div class="page-content">
                    <sidebar id="sidebar">
                        <!--Side bar content populated here by JavaScript Tag at end of body -->
                    </sidebar>
                    <div id="topPane" class="col-md backend-cp-collapsible">
                        <div class="box-usace">
                            <h2 class="box-header-striped">
                                <span class="titleLabel title">NWS Morning Shef</span>
                                <span class="rss"></span>
                            </h2>
                            <div class="box-content" style="background-color:white;margin:auto">
                                <div class="content">
                                    <!-- PHP -->
                                    <div id="spinner"><img src="images/loading4.gif"
                                            style='height: 50px; width: 50px;' alt="Loading..." /></div>
                                    <div id="preview"></div>
                                    <form method="post" target="_blank" onsubmit="hideSubmitButton()">
                                        <input type="submit" value="Submit" id="submitButton">
                                    </form>
                                    <script src='morning_shef.js'></script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button id="returnTop" title="Return to Top of Page">Top</button>
        </div>
    </div>
    <footer id="footer">
        <!--Footer content populated here by script tag at end of body -->
    </footer>
    <script src="js/libraries/jQuery-3.3.6.min.js"></script>
    <script defer>
        // When the document has loaded pull in the page header and footer skins
        $(document).ready(function() {
            $('#header').load('templates/DISTRICT.header.html');
            $('#sidebar').load('templates/DISTRICT.sidebar.html');
            $('#footer').load('templates/DISTRICT.footer.html');
        })
    </script>
</body>

</html>

<?php db_disconnect($db); ?>