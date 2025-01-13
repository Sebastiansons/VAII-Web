//Functions with SessionID
//CheckSessionID();

function CheckSessionID() {
    if (!IsSessionValid()) {
        alert("Session has expired! Please log in again.");
        window.location.href = "../../../VAII-Web/Web/pages/login.html";
    }
}

function SetCookies(responseObj) {
    UpdateSession(responseObj.session_id, responseObj.sessionIdExpirationDate);
    document.cookie = "username=" + responseObj.name + "; path=/";
    document.cookie = "role=" + responseObj.role + "; path=/";
    document.cookie = "balance=" + responseObj.balance + "; path=/";
    window.location.href = "../index.html";
}

function UpdateSession(sessionId, expirTime) {
    const date = new Date(expirTime * 1000);
    document.cookie = `session_ID=${sessionId}; expires=${date.toUTCString()}; path=/`;
}

function IsSessionValid() {
    const sessionID = GetCookieValue("session_ID");
    console.log(sessionID);
    if (!sessionID) {
        return false;
    }
    return true;
}

function GetCookieValue(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function UpdateNavbar() {
    const navbarContent = document.getElementById('navbarContent');
    if (navbarContent) {
        if (IsSessionValid()) {
            navbarContent.innerHTML += `
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user"></i>${GetCookieValue('username')}
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><span class="dropdown-item-text">Balance: <b>${GetCookieValue('balance')}&#8364;</b></span></li>
                    <li><span class="dropdown-item-text">Role: <b class="${GetCookieValue('role')}">${GetCookieValue('role')}</b></span></li>
                    <li><a class="dropdown-item" href="../../../VAII-Web/Web/pages/profile.html">My profile</a></li>
                    <li><a class="dropdown-item" href="#" onclick="Logout();">Logout</a></li>
                </ul>
            </li>
        `;
        } else {
            navbarContent.innerHTML += `
            <li class="nav-item">
                <a class="nav-link" href="../../../VAII-Web/Web/pages/login.html">Login</a>
            </li>
        `;
        }
    }
}

function Logout() {
    fetch('../../../VAII-Web/Web/back-end/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const pastDate = "Thu, 01 Jan 1970 00:00:00 UTC";
            document.cookie = "session_ID=; expires=" + pastDate + "; path=/;";
            document.cookie = "username=; expires=" + pastDate + "; path=/;";
            document.cookie = "role=; expires=" + pastDate + "; path=/;";
            document.cookie = "balance=; expires=" + pastDate + "; path=/;";
            window.location.href = "../../../VAII-Web/Web/index.html";
        } else {
            alert('Logout failed: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', UpdateNavbar);