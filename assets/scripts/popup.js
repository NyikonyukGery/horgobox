var messageLimit = 3;

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