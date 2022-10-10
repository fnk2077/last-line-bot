<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LIFF - LINE Front-end Framework</title>
        <style>
            body { margin: 16px }
            button, img { display: none; width: 10% }
            button { padding: 16px }
        </style>
    </head>
    <body>
    <h1>LINE Login</h1>
    <img id="pictureUrl">
    <p id="displayName"><b>displayName:</b></p>
    <p id="userId"><b>userId:</b></p>
    <button id="btnLogIn" onclick="logIn()">Log In</button>
    <button id="btnLogOut" onclick="logOut()">Log Out</button>
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <script>
        function logOut() {
            liff.logout()
            window.location.reload()
        }
        function logIn() {
            liff.login({ redirectUri: window.location.href })
        }
        async function getUserProfile() {
            const profile = await liff.getProfile()
            document.getElementById("pictureUrl").style.display = "block"
            document.getElementById("pictureUrl").src = profile.pictureUrl
            document.getElementById("displayName").append(profile.displayName)
            document.getElementById("userId").append(profile.userId)
        }
        async function main() {
            await liff.init({ liffId: "1657544079-QAnAnMvD" })
            if (liff.isInClient()) {
                getUserProfile()
            } else {
                if (liff.isLoggedIn()) {
                    getUserProfile()
                    document.getElementById("btnLogIn").style.display = "none"
                    document.getElementById("btnLogOut").style.display = "block"
                    document.getElementById("displayName").style.display = "block"
                    document.getElementById("userId").style.display = "block"
                } else {
                    document.getElementById("btnLogIn").style.display = "block"
                    document.getElementById("btnLogOut").style.display = "none"
                    document.getElementById("displayName").style.display = "none"
                    document.getElementById("userId").style.display = "none"
                }
            }
        }
        main()
    </script>
    </body>
</html>
