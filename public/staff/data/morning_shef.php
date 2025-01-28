<?php
    require_once('../../../private/initialize.php');
    require_login();
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
        $userLD24Input = $_POST["ld24_user_input"];
        $userLD25Input = $_POST["ld25_user_input"];
        $userMelPriceInput = $_POST["mel_price_user_input"];
        $userCarlyleInput = $_POST["carlyle_user_input"];
        $userShelbyvilleInput = $_POST["shelbyville_user_input"];
        $userMarkTwainInput = $_POST["marktwain_user_input"];
        $userWappapelloInput = $_POST["wappapello_user_input"];
        $userRendInput = $_POST["rend_user_input"];
        redirect_to(url_for('staff/data/morning_shef_data.php?preview=False&userLD24Input='.$userLD24Input.'&userLD25Input='.$userLD25Input.'&userMelPriceInput='.$userMelPriceInput.'&userCarlyleInput='.$userCarlyleInput.'&userShelbyvilleInput='.$userShelbyvilleInput.'&userMarkTwainInput='.$userMarkTwainInput.'&userWappapelloInput='.$userWappapelloInput.'&userRendInput='.$userRendInput.''));
    }
?>

<!-- style for spinning wheel while data being load -->
<style>
    #spinner {
        display: none;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>St. Louis District Home Page</title>
        <meta name="Description" content="U.S. Army Corps of Engineers St. Louis District Home Page" />
        <link rel="stylesheet" href="../../../../../css/body.css" />
        <link rel="stylesheet" href="../../../../../css/breadcrumbs.css" />
        <link rel="stylesheet" href="../../../../../css/jumpMenu.css" />
        <script type="text/javascript" src="../../../../../js/main.js"></script>
        <!-- Additional CSS -->
        <link rel="stylesheet" href="../../../../../css/rebuild.css" />
        <!-- Include Moment.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <!-- Include the Chart.js library -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- Include the Moment.js adapter for Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
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
                                    <span class="titleLabel title">Morning Shef Preview</span>
                                    <span class="rss"></span>
                                </h2>
                                <div class="box-content" style="background-color:white;margin:auto">
                                    <div class="content"></div>
                                        <!-- Spinner image or animation goes here -->
                                        <div id="spinner"><img src="spinner.gif" width="100" height="100" /></div>
                                        <!-- Preview Shef Data Goes Here -->
                                        <div id="preview"></div>

                                        <!-- LD Data Form Data Goes Here -->
                                        <form method="post" target="_blank" onsubmit="hideSubmitButton()">
                                            <label for="ld24_text_input">: LD24: </label>
                                            <input type="text" id="ld24_text_input" name="ld24_user_input" value="Nothing to Report">
                                            <br>
                                            <br>
                                            <label for="ld25_text_input">: LD25: </label>
                                            <input type="text" id="ld25_text_input" name="ld25_user_input" value="Nothing to Report">
                                            <br>
                                            <br>
                                            <label for="mel_price_text_input">: MEL PRICE: </label>
                                            <input type="text" id="mel_price_text_input" name="mel_price_user_input" value="Nothing to Report">
                                            <br>
                                            <br>
                                            <label for="carlyle_text_input">: CARLYLE: </label>
                                            <input type="text" id="carlyle_text_input" name="carlyle_user_input" value="Nothing to Report">
                                            <br>
                                            <br>
                                            <label for="shelbyville_text_input">: SHELBYVILLE: </label>
                                            <input type="text" id="shelbyville_text_input" name="shelbyville_user_input" value="Nothing to Report">
                                            <br>
                                            <br>
                                            <label for="marktwain_text_input">: MARK TWAIN: </label>
                                            <input type="text" id="marktwain_text_input" name="marktwain_user_input" value="Nothing to Report">
                                            <br>
                                            <br>
                                            <label for="wappapello_text_input">: WAPPAPELLO: </label>
                                            <input type="text" id="wappapello_text_input" name="wappapello_user_input" value="Nothing to Report">
                                            <br>
                                            <br>
                                            <label for="rend_text_input">: REND: </label>
                                            <input type="text" id="rend_text_input" name="rend_user_input" value="Nothing to Report">
                                            <br>
                                            <br>
                                            <input type="submit" value="Submit" id="submitButton">
                                        </form>
                                    </div>
                                </div>

                                <!-- This script control(hide and unhide) the wheel spinner -->
                                <script>
                                    function showSpinner() {
                                        var spinner = document.getElementById("spinner");
                                        spinner.style.display = 'block';
                                    }

                                    function hideSpinner() {
                                        var spinner = document.getElementById("spinner");
                                        spinner.style.display = 'none';
                                    }

                                    function hideSubmitButton() {
                                        // Get the submit button by its ID
                                        var submitButton = document.getElementById("submitButton");

                                        // Hide the submit button
                                        submitButton.style.display = "none";
                                    }

                                    window.onload = function() {
                                        getDataLD24();
                                        getDataLD25();
                                        getDataLDMP();
                                        getDataCarlyle();
                                        getDataShelbyville();
                                        getDataMarkTwain();
                                        getDataWappapello();
                                        getDataRend();
                                    };

                                    function getDataLD24() {
                                        // Make an AJAX request to a PHP file that fetches the data
                                        var xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function() {
                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                if (xhr.status === 200) {
                                                    // Parse the JSON response
                                                    var responseData = JSON.parse(xhr.responseText);

                                                    // Check if the response contains data
                                                    if (Object.keys(responseData).length === 0) {
                                                        // If no data is returned, set input field value to "Nothing to Report"
                                                        document.getElementById("ld24_text_input").value = "Nothing to Report";
                                                    } else {
                                                        // Extract the values from the response
                                                        var value = responseData.value;
                                                        console.log("value = ", typeof(value));

                                                        if (parseFloat(value) > 900) {
                                                            document.getElementById("ld24_text_input").value = "Open River";
                                                        } else {
                                                            // Populate the input field with the desired value
                                                            document.getElementById("ld24_text_input").value = "Nothing to Report";
                                                        }
                                                    }

                                                    // Log the data received
                                                    console.log("Data received:", responseData);
                                                } else {
                                                    console.error('Request failed: ' + xhr.status);
                                                }
                                            }
                                        };
                                        xhr.open('GET', 'get_ld24.php', true);
                                        xhr.send();
                                    }

                                    function getDataLD25() {
                                        // Make an AJAX request to a PHP file that fetches the data
                                        var xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function() {
                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                if (xhr.status === 200) {
                                                    // Parse the JSON response
                                                    var responseData = JSON.parse(xhr.responseText);

                                                    // Check if the response contains data
                                                    if (Object.keys(responseData).length === 0) {
                                                        // If no data is returned, set input field value to "Nothing to Report"
                                                        document.getElementById("ld25_text_input").value = "Nothing to Report";
                                                    } else {
                                                        // Extract the values from the response
                                                        var value = responseData.value;
                                                        console.log("value = ", typeof(value));

                                                        if (parseFloat(value) > 900) {
                                                            document.getElementById("ld25_text_input").value = "Open River";
                                                        } else {
                                                            // Populate the input field with the desired value
                                                            document.getElementById("ld25_text_input").value = "Nothing to Report";
                                                        }
                                                    }

                                                    // Log the data received
                                                    console.log("Data received:", responseData);
                                                } else {
                                                    console.error('Request failed: ' + xhr.status);
                                                }
                                            }
                                        };
                                        xhr.open('GET', 'get_ld25.php', true);
                                        xhr.send();
                                    }

                                    function getDataLDMP() {
                                        // Make an AJAX request to a PHP file that fetches the data
                                        var xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function() {
                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                if (xhr.status === 200) {
                                                    // Parse the JSON response
                                                    var responseData = JSON.parse(xhr.responseText);

                                                    // Check if the response contains data
                                                    if (Object.keys(responseData).length === 0) {
                                                        // If no data is returned, set input field value to "Nothing to Report"
                                                        document.getElementById("mel_price_text_input").value = "Nothing to Report";
                                                    } else {
                                                        // Extract the values from the response
                                                        var value = responseData.value;
                                                        console.log("value = ", typeof(value));

                                                        if (parseFloat(value) > 900) {
                                                            document.getElementById("mel_price_text_input").value = "Open River";
                                                        } else {
                                                            // Populate the input field with the desired value
                                                            document.getElementById("mel_price_text_input").value = "Nothing to Report";
                                                        }
                                                    }

                                                    // Log the data received
                                                    console.log("Data received:", responseData);
                                                } else {
                                                    console.error('Request failed: ' + xhr.status);
                                                }
                                            }
                                        };
                                        xhr.open('GET', 'get_mp.php', true);
                                        xhr.send();
                                    }

                                    function getDataCarlyle() {
                                        // Make an AJAX request to a PHP file that fetches the data
                                        var xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function() {
                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                if (xhr.status === 200) {
                                                    // Parse the JSON response
                                                    var responseData = JSON.parse(xhr.responseText);

                                                    // Check if the response contains data
                                                    if (Object.keys(responseData).length === 0) {
                                                        // If no data is returned, set input field value to "Nothing to Report"
                                                        document.getElementById("carlyle_text_input").value = "Nothing to Report";
                                                    } else {
                                                        // Extract the values from the response
                                                        var project_id = responseData.project_id;
                                                        var crest = responseData.crest;
                                                        var crst_dt = responseData.crst_dt;
                                                        var data_entry_dt = responseData.data_entry_dt;
                                                        var opt = responseData.opt;

                                                        // Populate the input field with the desired value
                                                        if (opt === "CG") {
                                                            document.getElementById("carlyle_text_input").value = 'cresting';
                                                        } else {
                                                            document.getElementById("carlyle_text_input").value = 'crest' + ' ' + opt + ' ' + crest + ' - ' + crst_dt;
                                                        }
                                                    }

                                                    // Log the data received
                                                    console.log("Data received:", responseData);
                                                } else {
                                                    console.error('Request failed: ' + xhr.status);
                                                }
                                            }
                                        };
                                        xhr.open('GET', 'get_carlyle.php', true);
                                        xhr.send();
                                    }

                                    function getDataShelbyville() {
                                        // Make an AJAX request to a PHP file that fetches the data
                                        var xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function() {
                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                if (xhr.status === 200) {
                                                    // Parse the JSON response
                                                    var responseData = JSON.parse(xhr.responseText);

                                                    // Check if the response contains data
                                                    if (Object.keys(responseData).length === 0) {
                                                        // If no data is returned, set input field value to "Nothing to Report"
                                                        document.getElementById("shelbyville_text_input").value = "Nothing to Report";
                                                    } else {
                                                        // Extract the values from the response
                                                        var project_id = responseData.project_id;
                                                        var crest = responseData.crest;
                                                        var crst_dt = responseData.crst_dt;
                                                        var data_entry_dt = responseData.data_entry_dt;
                                                        var opt = responseData.opt;

                                                        // Populate the input field with the desired value
                                                        if (opt === "CG") {
                                                            document.getElementById("shelbyville_text_input").value = 'cresting';
                                                        } else {
                                                            document.getElementById("shelbyville_text_input").value = 'crest' + ' ' + opt + ' ' + crest + ' - ' + crst_dt;
                                                        }
                                                    }

                                                    // Log the data received
                                                    console.log("Data received:", responseData);
                                                } else {
                                                    console.error('Request failed: ' + xhr.status);
                                                }
                                            }
                                        };
                                        xhr.open('GET', 'get_shelbyville.php', true);
                                        xhr.send();
                                    }

                                    function getDataMarkTwain() {
                                        // Make an AJAX request to a PHP file that fetches the data
                                        var xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function() {
                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                if (xhr.status === 200) {
                                                    // Parse the JSON response
                                                    var responseData = JSON.parse(xhr.responseText);

                                                    // Check if the response contains data
                                                    if (Object.keys(responseData).length === 0) {
                                                        // If no data is returned, set input field value to "Nothing to Report"
                                                        document.getElementById("marktwain_text_input").value = "Nothing to Report";
                                                    } else {
                                                        // Extract the values from the response
                                                        var project_id = responseData.project_id;
                                                        var crest = responseData.crest;
                                                        var crst_dt = responseData.crst_dt;
                                                        var data_entry_dt = responseData.data_entry_dt;
                                                        var opt = responseData.opt;

                                                        // Populate the input field with the desired value
                                                        if (opt === "CG") {
                                                            document.getElementById("marktwain_text_input").value = 'cresting';
                                                        } else {
                                                            document.getElementById("marktwain_text_input").value = 'crest' + ' ' + opt + ' ' + crest + ' - ' + crst_dt;
                                                        }
                                                    }

                                                    // Log the data received
                                                    console.log("Data received:", responseData);
                                                } else {
                                                    console.error('Request failed: ' + xhr.status);
                                                }
                                            }
                                        };
                                        xhr.open('GET', 'get_mark_twain.php', true);
                                        xhr.send();
                                    }

                                    function getDataWappapello() {
                                        // Make an AJAX request to a PHP file that fetches the data
                                        var xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function() {
                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                if (xhr.status === 200) {
                                                    // Parse the JSON response
                                                    var responseData = JSON.parse(xhr.responseText);

                                                    // Check if the response contains data
                                                    if (Object.keys(responseData).length === 0) {
                                                        // If no data is returned, set input field value to "Nothing to Report"
                                                        document.getElementById("wappapello_text_input").value = "Nothing to Report";
                                                    } else {
                                                        // Extract the values from the response
                                                        var project_id = responseData.project_id;
                                                        var crest = responseData.crest;
                                                        var crst_dt = responseData.crst_dt;
                                                        var data_entry_dt = responseData.data_entry_dt;
                                                        var opt = responseData.opt;

                                                        // Populate the input field with the desired value
                                                        if (opt === "CG") {
                                                            document.getElementById("wappapello_text_input").value = 'cresting';
                                                        } else {
                                                            document.getElementById("wappapello_text_input").value = 'crest' + ' ' + opt + ' ' + crest + ' - ' + crst_dt;
                                                        }
                                                    }

                                                    // Log the data received
                                                    console.log("Data received:", responseData);
                                                } else {
                                                    console.error('Request failed: ' + xhr.status);
                                                }
                                            }
                                        };
                                        xhr.open('GET', 'get_wappapello.php', true);
                                        xhr.send();
                                    }

                                    function getDataRend() {
                                        // Make an AJAX request to a PHP file that fetches the data
                                        var xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function() {
                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                if (xhr.status === 200) {
                                                    // Parse the JSON response
                                                    var responseData = JSON.parse(xhr.responseText);

                                                    // Check if the response contains data
                                                    if (Object.keys(responseData).length === 0) {
                                                        // If no data is returned, set input field value to "Nothing to Report"
                                                        document.getElementById("rend_text_input").value = "Nothing to Report";
                                                    } else {
                                                        // Extract the values from the response
                                                        var project_id = responseData.project_id;
                                                        var crest = responseData.crest;
                                                        var crst_dt = responseData.crst_dt;
                                                        var data_entry_dt = responseData.data_entry_dt;
                                                        var opt = responseData.opt;

                                                        // Populate the input field with the desired value
                                                        if (opt === "CG") {
                                                            document.getElementById("rend_text_input").value = 'cresting';
                                                        } else {
                                                            document.getElementById("rend_text_input").value = 'crest' + ' ' + opt + ' ' + crest + ' - ' + crst_dt;
                                                        }
                                                    }

                                                    // Log the data received
                                                    console.log("Data received:", responseData);
                                                } else {
                                                    console.error('Request failed: ' + xhr.status);
                                                }
                                            }
                                        };
                                        xhr.open('GET', 'get_rend.php', true);
                                        xhr.send();
                                    }

                                    // Function to generate preview shef data for the div named "preview"
                                    function replaceText() {
                                        showSpinner();
                                        var target = document.getElementById("preview");
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('GET', 'morning_shef_data.php?preview=True', true);
                                        xhr.onreadystatechange = function () {
                                            console.log('readyState: ' + xhr.readyState);
                                            if(xhr.readyState == 4) {
                                                hideSpinner(); // Hide spinner when the request is complete

                                                if(xhr.status == 200) {
                                                    target.innerHTML = xhr.responseText;
                                                } else {
                                                    // Handle error cases here if needed
                                                    console.error('Error loading data. Status: ' + xhr.status);
                                                }
                                            }
                                        }
                                        xhr.send();
                                    }

                                    // Call replaceText function immediately
                                    replaceText();
                                </script>
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
        <script src="../../../../../js/libraries/jQuery-3.3.6.min.js"></script>
        <script defer>
            // When the document has loaded pull in the page header and footer skins
            $(document).ready(function () {
                // Change the v= to a different number to force clearing the cached version on the client browser
                $('#header').load('../../../../../templates/DISTRICT.header.php?app=True');
				$('#sidebar').load('../../../../../templates/INTERNAL.sidebar.php?app=True');
                $('#footer').load('../../../../../templates/INTERNAL.footer.php?app=True');
            })
        </script>
    </body>
</html>
<?php db_disconnect($db); ?>