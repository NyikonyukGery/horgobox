var messageLimit = 3;

function LoginUser() {
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    if (email.trim().length != 0 && password.trim().length != 0) {
        document.getElementById("error-container").style.display = "none";
        document.getElementById("error-message").innerText = "";

        var request = new XMLHttpRequest();
        request.open("POST", "/app/validation/loginFormValidation.php");
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        var sendJson = JSON.stringify({ "method": "login", "user": { "email": encodeURI(email), "password": encodeURI(password) } });
        request.send(sendJson);

        request.onload = function() {
            console.log(request.response);
            var jsonResponse = JSON.parse(request.response);
            if (jsonResponse.response == "error") {
                document.getElementById("error-container").style.display = "block";
                document.getElementById("error-message").innerText = jsonResponse.error;
            } else if (jsonResponse.response == "success") {
                window.location.replace(jsonResponse.route);
            }
        }
    } else {
        document.getElementById("error-container").style.display = "block";
        document.getElementById("error-message").innerText = "Kitöltetlen mező!";
    }
}

function RegisterUser() {
    var familyName = document.getElementById("familyName").value;
    var firstName = document.getElementById("firstName").value;
    var username = document.getElementById("username").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var newsletter = document.getElementById("newsletterSub").checked;
    var acceptTerms = document.getElementById("acceptConditions").checked;

    if (acceptTerms && familyName.trim().length != 0 && firstName.trim().length != 0 && username.trim().length != 0 && email.trim().length != 0 && password.trim().length != 0) {
        document.getElementById("error-container").style.display = "none";
        document.getElementById("error-message").innerText = "";

        var request = new XMLHttpRequest();
        request.open("POST", "/app/validation/loginFormValidation.php");
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        var sendJson = JSON.stringify({ "method": "register", "user": { "familyName": familyName, "firstName": firstName, "username": username, "email": email, "password": password, "newsletter": newsletter, "acceptTerms": acceptTerms } });
        request.send(sendJson);

        request.onload = function() {
            console.log(request.response);
            var jsonResponse = JSON.parse(request.response);
            if (jsonResponse.response == "error") {
                document.getElementById("error-container").style.display = "block";
                document.getElementById("error-message").innerText = jsonResponse.error;
            } else if (jsonResponse.response == "success") {
                window.location.replace(jsonResponse.route);
            }

        }
    } else {
        document.getElementById("error-container").style.display = "block";
        if (familyName.trim().length == 0 && firstName.trim().length == 0 && username.trim().length == 0 && email.trim().length == 0 && password.trim().length == 0) {
            document.getElementById("error-message").innerText = "Kitöltetlen mező!";
        } else {
            document.getElementById("error-message").innerText = "Az ÁSZF elfogadása kötelező!";
        }
    }
}

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
            console.log(request.response);
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