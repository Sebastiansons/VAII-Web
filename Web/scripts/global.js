//Functions with SessionID
function SetCookies(responseObj) {
    console.log(responseObj)
    const currentTime = new Date(responseObj.updated_at);
    const expires = new Date(currentTime.getTime() + 3600000); // 1h
    document.cookie = `sessionIDTimeout=${expires.toUTCString()}; path=/`;
    document.cookie = "sessionID=" + responseObj.session_id + "; path=/";
    document.cookie = "username=" + responseObj.name + "; path=/";
    document.cookie = "role=" + responseObj.role + "; path=/";
    document.cookie = "balance=" + responseObj.balance + "; path=/";
    window.location.href = "../index.html";
}

function IsSessionValid() {
    const sessionIDTimeout = new Date(document.cookie.replace(/(?:(?:^|.*;\s*)sessionIDTimeout\s*\=\s*([^;]*).*$)|^.*$/, "$1"));
    const currentTime = new Date();
    return (sessionIDTimeout.getTime() + 3600000) > currentTime.getTime(); // 1h
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
    const pastDate = "Thu, 01 Jan 1970 00:00:00 UTC";
    document.cookie = "sessionIDTimeout=; expires=" + pastDate + "; path=/;";
    document.cookie = "sessionID=; expires=" + pastDate + "; path=/;";
    document.cookie = "username=; expires=" + pastDate + "; path=/;";
    document.cookie = "role=" + pastDate + "; path=/";
    document.cookie = "balance=" + pastDate + "; path=/";
    window.location.href = "../../../VAII-Web/Web/index.html";
}

document.addEventListener('DOMContentLoaded', UpdateNavbar);