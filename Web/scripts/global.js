//Functions with SessionID
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
   // window.location.href = "../index.html";
}

function UpdateSession(sessionId, expirTime) {
    const date = new Date(expirTime * 1000); // Convert Unix timestamp to milliseconds
    document.cookie = `sessionID=${sessionId}; expires=${date.toUTCString()}; path=/`;
    console.log("sessionID cookie set:", document.cookie); // Log all cookies
}

function IsSessionValid() {
    const sessionID = GetCookieValue("username");
    console.log(sessionID);
    if (!sessionID) {
        
        return false;
    }

    const sessionIDExpires = GetCookieExpiration("sessionID");
    const currentTime = new Date();
    console.log(sessionIDExpires > currentTime);
    return sessionIDExpires > currentTime;
}

function GetCookieValue(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function GetCookieExpiration(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        const cookiePart = parts.pop().split(';').shift();
        const expiresPart = parts.pop().split(';').find(part => part.trim().startsWith('expires='));
        if (expiresPart) {
            return new Date(expiresPart.split('=')[1]);
        }
    }
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
    const pastDate = "Thu, 01 Jan 1970 00:00:00 UTC";
    document.cookie = "sessionIDTimeout=; expires=" + pastDate + "; path=/;";
    document.cookie = "sessionID=; expires=" + pastDate + "; path=/;";
    document.cookie = "username=; expires=" + pastDate + "; path=/;";
    document.cookie = "role=" + pastDate + "; path=/";
    document.cookie = "balance=" + pastDate + "; path=/";
    window.location.href = "../../../VAII-Web/Web/index.html";
}

document.addEventListener('DOMContentLoaded', UpdateNavbar);