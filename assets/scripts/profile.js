var originalUserJson;

function loadUser() {
    var request = new XMLHttpRequest();
    request.open("POST", "app/endpoints/userData.php");
    request.setRequestHeader("Content-type", 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    var sendJson = JSON.stringify({ "method": "getUserData" });
    request.send(sendJson);

    request.onload = function() {
        var jsonResponse = JSON.parse(request.response);
        if (jsonResponse.response == "error") {
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);

        } else if (jsonResponse.response == "success") {
            originalUserJson = jsonResponse.user;
            document.getElementById("familyName").value = jsonResponse.user.familyName;
            document.getElementById("firstName").value = jsonResponse.user.firstName;
            document.getElementById("username").value = jsonResponse.user.username;
            document.getElementById("email").innerText = jsonResponse.user.email;
            if (jsonResponse.user.newsletter == true) {
                document.getElementById("newsletter-action-container").innerHTML = "<p>Már feliratkozott!</p>";
            }
        }
    }
}

function UpdateUser() {
    var familyName = document.getElementById("familyName").value.trim();
    var firstName = document.getElementById("firstName").value.trim();
    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value;
    if (originalUserJson.familyName != familyName || originalUserJson.firstName != firstName || originalUserJson.username != username || password.length > 0) {
        var request = new XMLHttpRequest();
        request.open("POST", "app/endpoints/userData.php");
        request.setRequestHeader("Content-type", 'application/x-www-form-urlencoded');
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        var sendJson = JSON.stringify({ "method": "updateUser", "user": { "familyName": familyName, "firstName": firstName, "username": username, "password": password } });
        request.send(sendJson);

        request.onload = function() {
            var jsonResponse = JSON.parse(request.response);
            if (jsonResponse.response == "error") {
                ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);

            } else if (jsonResponse.response == "success") {
                originalUserJson = jsonResponse.user;
                document.getElementById("familyName").value = jsonResponse.user.familyName;
                document.getElementById("firstName").value = jsonResponse.user.firstName;
                document.getElementById("username").value = jsonResponse.user.username;
                document.getElementById("password").value = "";
                ShowPopupMessage("Sikeres módosítás!", "A kívánt módosítások mentésre kerültek!", "success", 5);
            }
        }
    } else {
        ShowPopupMessage("Nincs módosított mező!", "A mezők tartalma nem módosult!")
    }
}

function NewsletterSignUp() {
    var request = new XMLHttpRequest();
    request.open("POST", "app/endpoints/userData.php");
    request.setRequestHeader("Content-type", 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    var sendJson = JSON.stringify({ "method": "newsletterSignUp" });
    request.send(sendJson);

    request.onload = function() {
        var jsonResponse = JSON.parse(request.response);

        if (jsonResponse.response == "error") {
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description)
        } else if (jsonResponse.response == "success") {
            document.getElementById("newsletter-action-container").innerHTML = "<p>Sikeresen feliratkozott!</p>";
        }
    }
}