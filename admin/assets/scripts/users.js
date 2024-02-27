currentPage = 1;

function GetUsers() {
    //felhaszálók kiürítése
    document.getElementById("users").innerHTML = "";

    //kérés létrehozása
    var request = new XMLHttpRequest();
    request.open("POST", "/admin/app/endpoints/users.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    var from = (currentPage - 1) * 50;

    var sendJson = JSON.stringify({ "method": "getUsers", "from": from });
    request.send(sendJson);

    //kérés lefutásának esetén történő kód
    request.onload = function() {
        var jsonResponse = JSON.parse(request.response);
        if (jsonResponse.response == "error") { //hibakezelés
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description, "error", 5);
        } else if (jsonResponse.response == "success") { //adatok feltöltése html kód formájában
            document.getElementById("pages").innerHTML = "";
            for (let i = 0; i < Math.ceil(jsonResponse.userCount / 50) && i < 15; i++) {
                if (currentPage == i + 1) {
                    document.getElementById("pages").innerHTML += '<p onclick="" class="selected">' + (i + 1) + '</p>';
                } else {
                    document.getElementById("pages").innerHTML += '<p onclick="">' + (i + 1) + '</p>';
                }
            }
            var counter = (currentPage - 1) * 50 + 1;
            jsonResponse.users.forEach(user => {
                document.getElementById('users').innerHTML += "<tr onclick='location.href=\"" + jsonResponse.base_url + "felhasznalo?id=" + user.id + "\"'><td>" + counter + "</td><td>" + user.familyName + " " + user.firstName + "</td><td>" + user.username + "</td><td>" + user.email + "</td><td>" + user.registrationDate + "</td></tr>";
                counter++;
            });

            if (currentPage > 1) {
                document.getElementById("left-arrow").onclick = "modifyPage(" + (currentPage - 1) + ")";
                document.getElementById("left-arrow").classList.remove("inactive");
            } else {
                document.getElementById("left-arrow").onclick = "";
                document.getElementById("left-arrow").classList.add("inactive");
            }

            if (currentPage < Math.ceil(jsonResponse.userCount / 50)) {
                document.getElementById("right-arrow").onclick = "modifyPage(" + (currentPage + 1) + ")";
                document.getElementById("right-arrow").classList.remove("inactive");
            } else {
                document.getElementById("right-arrow").onclick = "";
                document.getElementById("right-arrow").classList.add("inactive");
            }
        }
    }
}

function SearchUsers() {
    //felhaszálók kiürítése
    document.getElementById("users").innerHTML = "";

    //kérés létrehozása
    var request = new XMLHttpRequest();
    request.open("POST", "/admin/app/endpoints/users.php");
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    currentPage = 1;

    var from = (currentPage - 1) * 50;
    var phrase = document.getElementById("search").value;

    var sendJson = JSON.stringify({ "method": "searchUsers", "from": from, "phrase": phrase });
    request.send(sendJson);

    //kérés lefutásának esetén történő kód
    request.onload = function() {
        var jsonResponse = JSON.parse(request.response);
        if (jsonResponse.response == "error") { //hibakezelés
            ShowPopupMessage(jsonResponse.error_title, jsonResponse.error_description, "error", 5);
        } else if (jsonResponse.response == "success") { //adatok feltöltése html kód formájában
            document.getElementById("pages").innerHTML = "";
            for (let i = 0; i < Math.ceil(jsonResponse.userCount / 50); i++) {
                if (currentPage == i + 1) {
                    document.getElementById("pages").innerHTML += '<p onclick="" class="selected">' + (i + 1) + '</p>';
                } else {
                    document.getElementById("pages").innerHTML += '<p onclick="">' + (i + 1) + '</p>';
                }
            }
            var counter = (currentPage - 1) * 50 + 1;
            jsonResponse.users.forEach(user => {
                document.getElementById('users').innerHTML += "<tr onclick='location.href=\"" + jsonResponse.base_url + "felhasznalo?id=" + user.id + "\"'><td>" + counter + "</td><td>" + user.familyName + " " + user.firstName + "</td><td>" + user.username + "</td><td>" + user.email + "</td><td>" + user.registrationDate + "</td></tr>";
                counter++;
            });

            if (currentPage > 1) {
                document.getElementById("left-arrow").onclick = "modifyPage(" + (currentPage - 1) + ")";
                document.getElementById("left-arrow").classList.remove("inactive");
            } else {
                document.getElementById("left-arrow").onclick = "";
                document.getElementById("left-arrow").classList.add("inactive");
            }

            if (currentPage < Math.ceil(jsonResponse.userCount / 50)) {
                document.getElementById("right-arrow").onclick = "modifyPage(" + (currentPage + 1) + ")";
                document.getElementById("right-arrow").classList.remove("inactive");
            } else {
                document.getElementById("right-arrow").onclick = "";
                document.getElementById("right-arrow").classList.add("inactive");
            }
        }
    }
}


//ha ki van jelölve a kereső mező és entert ütünk, kód futtatása
addEventListener("keyup", function(e) {
    if (e.code === "Enter" && document.getElementById("search") == document.activeElement) {
        if (document.getElementById("search").length == 0) {
            GetUsers();
        } else {
            SearchUsers();
        }
    }
});


//váltás oldalak között
function modifyPage(pageNumber) {
    currentPage = pageNumber;
    GetUsers();
}


//felhasználók azonnali betöltése
GetUsers();