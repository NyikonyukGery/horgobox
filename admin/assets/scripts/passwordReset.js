function SendPasswordReset() {
    var email = document.getElementById("email").value.trim();
    if (email.length != 0) {
        var request = new XMLHttpRequest();
        request.open("POST", "/app/endpoints/userData.php");
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        var sendJson = JSON.stringify({ "method": "passwordReset", "email": email });
        request.send(sendJson);

        request.onload = function() {
            console.log(request.response);
            var jsonResponse = JSON.parse(request.response);
            if (jsonResponse.response == "error") {
                ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);
            } else if (jsonResponse.response == "success") {
                ShowPopupMessage("Kód elküldve!", "Ellenőrizze emailjét! Előfordulhat, hogy a spam mappában találod!",
                    "success", 10);
            }
        }
    } else {
        ShowPopupMessage("Kitöltetlen mező!", "Email cím nincs megadva!");
    }
}

function SendNewPassword() {
    var password = document.getElementById("password").value.trim();
    if (password.length > 7) {
        var request = new XMLHttpRequest();
        request.open("POST", "/app/endpoints/userData.php");
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        var sendJson = JSON.stringify({ "method": "updatePassword", "token": token, "user": user, "password": password });
        request.send(sendJson);

        request.onload = function() {
            console.log(request.response);
            var jsonResponse = JSON.parse(request.response);
            if (jsonResponse.response == "error") {
                ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);
            } else if (jsonResponse.response == "success") {
                ShowPopupMessage("Jelszó frissítve!", "Hamarosan átirányítunk!", "success", 10);
                setTimeout(() => {
                    window.location.replace(jsonResponse.route);
                }, 5000);
            }
        }
    } else {
        ShowPopupMessage("Hibás jelszó!", "Tartalmaznia kell számot, kis- és nagybetűt, különleges karakter!\nLegalább 8 karakternek kell lennie!");
    }
}