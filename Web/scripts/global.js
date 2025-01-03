//Functions with SessionID
function SetSessionIDTimeout() {
    const currentTime = new Date();
    const expires = new Date(currentTime.getTime() + 3600000); // 1h
    document.cookie = `sessionIDTimeout=${expires.toUTCString()}; path=/`;
    location.reload();
}

function IsSessionValid() {
    const sessionIDTimeout = new Date(document.cookie.replace(/(?:(?:^|.*;\s*)sessionIDTimeout\s*\=\s*([^;]*).*$)|^.*$/, "$1"));
    const currentTime = new Date();
    return (sessionIDTimeout.getTime() + 3600000) > currentTime.getTime(); // 1h
}

function UpdateNavbar() {
    const navbarContent = document.getElementById('navbarContent');
    if (IsSessionValid()) {
        navbarContent.innerHTML += `
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#">Settings</a></li>
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
    }//onclick="SetSessionIDTimeout();"
}

function Logout() {
    document.cookie = "sessionIDTimeout=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    location.reload();
}

document.addEventListener('DOMContentLoaded', UpdateNavbar);