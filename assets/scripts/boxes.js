var messageLimit = 3;
var boxId;

function GetBoxes() {

}

function GetBox() {
    var request = new XMLHttpRequest();
    request.open("POST", "/app/endpoints/boxes.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    var sendJson = JSON.stringify({ "method": "getBox", "boxName": boxName });
    request.send(sendJson);

    request.onload = function() {
        var jsonResponse = JSON.parse(request.response);
        console.log(jsonResponse);
        if (jsonResponse.response == "error") {
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description, "error", 10);
            setTimeout(() => {
                window.location.replace(jsonResponse.route);
            }, 5000);
        } else if (jsonResponse.response == "success") {
            if (jsonResponse.box_data != "no-cover") {
                document.getElementById("box-cover").src = jsonResponse.box_data.url;
                document.getElementById("box-cover").alt = jsonResponse.box_data.title;
            }
            document.getElementById("box-name").innerText = jsonResponse.box_data.name;
            document.getElementById("webshop-link").href = jsonResponse.box_data.webshop_url;
            boxId = parseInt(jsonResponse.box_data.id);
            document.title = jsonResponse.box_data.name + " - " + document.title;
        }
    }
}

function UnlockBox() {
    boxPassword = document.getElementById("boxPassword").value.trim();
    if (boxPassword.length > 2) {
        var request = new XMLHttpRequest();
        request.open("POST", "/app/endpoints/boxes.php");
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        var sendJson = JSON.stringify({ "method": "unlockBox", "password": boxPassword, "boxId": boxId });
        request.send(sendJson);

        request.onload = function() {
            console.log(request.response);
            var jsonResponse = JSON.parse(request.response);

            if (jsonResponse.response == "error") {
                ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);
            } else if (jsonResponse.response == "success") {
                ShowPopupMessage("Sikeres feloldás!", "Hamarosan átirányítunk!", "success", 10);
                setTimeout(() => {
                    window.location.replace(jsonResponse.route);
                }, 5000);
            }
        }
    } else {
        ShowPopupMessage("Hibás jelszó!", "A megadott jelszó nem található!");
    }
}

function ShowPopupMessage(title, message, type = "error", durationInSeconds = 3) {
    if (messageLimit > 0) {
        if (type == "error") {
            document.getElementById("message-popup-container").innerHTML += "<div class='error popup' id='message-popup'><p class='message-title' id='message-title'>" + title + "</p><p class='message-description' id='message-description'>" + message + "</p></div>";
        } else if (type == "success") {
            document.getElementById("message-popup-container").innerHTML += "<div class='success popup' id='message-popup'><p class='message-title' id='message-title'>" + title + "</p><p class='message-description' id='message-description'>" + message + "</p></div>";
        }
        messageLimit--;
        setTimeout(() => {
            document.getElementById("message-popup").remove();
            messageLimit++;
        }, durationInSeconds * 1000);
    }
}