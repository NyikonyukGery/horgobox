function LoginUser() {
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    if (email.trim().length != 0 && password.trim().length != 0) {
        document.getElementById("error-container").style.display = "none";
        document.getElementById("error-message").innerText = "";

        var request = new XMLHttpRequest();
        request.open("POST", "/admin/app/validation/loginFormValidation.php");
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        var sendJson = JSON.stringify({ "method": "login", "user": { "email": encodeURI(email), "password": encodeURI(password) } });
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