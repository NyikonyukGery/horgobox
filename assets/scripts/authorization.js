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
        var sendJson = JSON.stringify({ "method": "login", "user": { "email": email, "password": password } });
        request.send(sendJson);

        request.onload = function() {
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

function HidePopup() {
    document.getElementById("popup-container").remove();
}

function HidePopupForever() {
    var someDate = new Date();
    var numberOfDaysToAdd = 100;
    var result = someDate.setDate(someDate.getDate() + numberOfDaysToAdd);
    document.cookie = "HIDELOGINPOPUP=true, expires=" + new Date(result);
    document.getElementById("popup-container").remove();
}