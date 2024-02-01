function ResendEmail() {
    var request = new XMLHttpRequest();
    request.open("POST", "/app/endpoints/userData.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    var sendJson = JSON.stringify({ "method": "resendConformEmail" });
    request.send(sendJson);

    request.onload = function() {
        var jsonResponse = JSON.parse(request.response);
        if (jsonResponse.response == "error") {
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);
        } else if (jsonResponse.response == "success") {
            ShowPopupMessage("Sikeresen elküldtük!", "Ellenőrizd a megadott email címet!", "success", 10);
        }
    }
}

function SubmitCode() {
    var code = document.getElementById("code").value.trim();

    if (code.length > 5) {
        var request = new XMLHttpRequest();
        request.open("POST", "/app/endpoints/userData.php");
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        var sendJson = JSON.stringify({ "method": "checkEmailConfirmationCode", "code": code });
        request.send(sendJson);

        request.onload = function() {
            var jsonResponse = JSON.parse(request.response);
            if (jsonResponse.response == "error") {
                ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);
            } else if (jsonResponse.response == "success") {
                ShowPopupMessage("Sikeres megerősítés!", "Hamarosan átirányítunk!", "success", 10);
                setTimeout(() => {
                    window.location.replace(jsonResponse.route);
                }, 5000);
            }
        }
    } else {
        ShowPopupMessage("Hibás kód!", "Ismeretlen kódot adott meg!");
    }
}