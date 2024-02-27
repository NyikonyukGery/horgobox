function GetUserLogs() {
    //kérés létrehozása
    var request = new XMLHttpRequest();
    request.open("POST", "/admin/app/endpoints/users.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    var sendJson = JSON.stringify({ "method": "getUserLogs", "userId": userId });
    request.send(sendJson);

    //kérés lefutása esetén történő kód
    request.onload = function() {
        var jsonResponse = JSON.parse(request.response);
        if (jsonResponse.response == "success") {
            document.getElementById("logs").innerHTML = "";
            jsonResponse.logs.forEach(log => {
                document.getElementById("logs").innerHTML += "<p>" + log.timestamp + " | IP: " + log.ip + " | " + log.type + "</p>";
            });
        } else {
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);
        }
    }
}


function SendNewPasswordEmail() {
    document.getElementById("newPassword").disabled = true;
    //kérés létrehozása
    var request = new XMLHttpRequest();
    request.open("POST", "/admin/app/endpoints/users.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    var sendJson = JSON.stringify({ "method": "sendNewPasswordEmail", "userId": userId });
    request.send(sendJson);

    //kérés lefutása esetén történő kód
    request.onload = function() {
        var jsonResponse = JSON.parse(request.response);
        if (jsonResponse.response == "success") {
            GetUserLogs();
            ShowPopupMessage("Sikeres művelet!", "Új jelszó email kiküldve a felhasználónak!", "success");
        } else {
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);
        }
    }
    document.getElementById("newPassword").disabled = false;
}

function ToggleNewsletterSub() {
    document.getElementById("newsletterSub").disabled = true;
    //kérés létrehozása
    var request = new XMLHttpRequest();
    request.open("POST", "/admin/app/endpoints/users.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    if (originalUserData.newsletter_sub == 0) {
        newStatus = 1;
    } else {
        newStatus = 0;
    }

    var sendJson = JSON.stringify({ "method": "toggleNewsletterSub", "userId": userId, "newStatus": newStatus });
    request.send(sendJson);

    //kérés lefutása esetén történő kód
    request.onload = function() {
        var jsonResponse = JSON.parse(request.response)
        if (jsonResponse.response == "success") {
            GetUserLogs();
            if (newStatus == 1) {
                document.getElementById("newsletterSub").innerHTML = "Leiratkoztatás a hírlevélről";
            } else {
                document.getElementById("newsletterSub").innerHTML = "Feliratkoztatás a hírlevélre";
            }
            ShowPopupMessage("Sikeres hírlevélmódosítás", "A művelet sikeresen lezajlott!", "success");
        } else {
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);
        }
        document.getElementById("newsletterSub").disabled = false;
    }
}

function SaveChanges() {
    document.getElementById("saveChanges").disabled = true;
    var familyName = document.getElementById("familyName").value.trim();
    var firstName = document.getElementById("firstName").value.trim();
    var username = document.getElementById("username").value.trim();
    var email = document.getElementById("email").value.trim();
    if (originalUserData.familyName != familyName || originalUserData.firstName != firstName || originalUserData.username != username || originalUserData.email != email) {
        var request = new XMLHttpRequest();
        request.open("POST", "app/endpoints/users.php");
        request.setRequestHeader("Content-type", 'application/x-www-form-urlencoded');
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        var sendJson = JSON.stringify({ "method": "updateUser", "user": { "familyName": familyName, "firstName": firstName, "username": username, "email": email, "userId": userId } });
        request.send(sendJson);

        request.onload = function() {
            var jsonResponse = JSON.parse(request.response);
            if (jsonResponse.response == "error") {
                ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);

            } else if (jsonResponse.response == "success") {
                GetUserLogs();
                originalUserJson = jsonResponse.user;
                document.getElementById("familyName").value = jsonResponse.user.familyName;
                document.getElementById("firstName").value = jsonResponse.user.firstName;
                document.getElementById("username").value = jsonResponse.user.username;
                document.getElementById("email").value = jsonResponse.user.email;
                ShowPopupMessage("Sikeres módosítás!", "A kívánt módosítások mentésre kerültek!", "success", 5);
            }
        }
    } else {
        ShowPopupMessage("Nincs módosított mező!", "A mezők tartalma nem módosult!")
    }
    document.getElementById("saveChanges").disabled = false;
}

function ConfirmEmail() {
    document.getElementById("confirmEmail").disabled = true;
    //kérés létrehozása
    var request = new XMLHttpRequest();
    request.open("POST", "/admin/app/endpoints/users.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    var sendJson = JSON.stringify({ "method": "confirmEmail", "userId": userId });
    request.send(sendJson);

    //kérés lefutása esetén történő kód
    request.onload = function() {
        var jsonResponse = JSON.parse(request.response)
        if (jsonResponse.response == "success") {
            GetUserLogs();
            document.getElementById("confirmEmail").remove();
            document.getElementById("sendNewEmailCode").remove();
            ShowPopupMessage("Sikeres megerősítés", "Sikeresen megerősítette a felhasználó email címét!", "success");
        } else {
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);
            document.getElementById("confirmEmail").disabled = false;
        }
    }
}


function SendNewEmailCode() {
    document.getElementById("sendNewEmailCode").disabled = true;
    //kérés létrehozása
    var request = new XMLHttpRequest();
    request.open("POST", "/admin/app/endpoints/users.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    var sendJson = JSON.stringify({ "method": "sendNewEmailCode", "userId": userId });
    request.send(sendJson);

    //kérés lefutása esetén történő kód
    request.onload = function() {
        var jsonResponse = JSON.parse(request.response)
        if (jsonResponse.response == "success") {
            GetUserLogs();
            ShowPopupMessage("Sikeres kiküldés", "Sikeresen elküldte a felhasználónak az emailt!", "success");
        } else {
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description);
        }
    }
    document.getElementById("sendNewEmailCode").disabled = false;
}