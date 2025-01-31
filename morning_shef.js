window.onload = function () {
    getDataLD24();
    getDataLD25();
    getDataLDMP();
    getDataCarlyle();
    getDataShelbyville();
    getDataMarkTwain();
    getDataWappapello();
    getDataRend();
};

console.log("Calling getPreviewShef");
getPreviewShef();

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

function getPreviewShef() {
    showSpinner();
    var target = document.getElementById("preview");
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'morning_shef.php?preview=True', true);
    xhr.onreadystatechange = function () {
        // console.log('readyState: ' + xhr.readyState);
        if (xhr.readyState == 4) {
            hideSpinner(); // Hide spinner when the request is complete

            if (xhr.status == 200) {
                target.innerHTML = xhr.responseText;
            } else {
                // Handle error cases here if needed
                console.error('Error loading data. Status: ' + xhr.status);
            }
        }
    }
    xhr.send();
}

function getDataLD24() {
    // Make an AJAX request to a PHP file that fetches the data
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
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
                    console.log("value = ", typeof (value));

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
    xhr.onreadystatechange = function () {
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
                    console.log("value = ", typeof (value));

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
    xhr.onreadystatechange = function () {
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
                    console.log("value = ", typeof (value));

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
    xhr.onreadystatechange = function () {
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
    xhr.onreadystatechange = function () {
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
    xhr.onreadystatechange = function () {
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
    xhr.onreadystatechange = function () {
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
    xhr.onreadystatechange = function () {
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