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