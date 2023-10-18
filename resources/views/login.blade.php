<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CS442 200 Laravel</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://unpkg.com/flowbite@1.5.1/dist/flowbite.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
</head>

<body>
<button id="btn" type="button" onclick="login()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center" >Login with Line
</button>
<div id="content">
    <p id="userId"></p>
    <p id="displayName"></p>
    <img id="pictureUrl" width="400">
</div>
<br>
<br>
<button id="btnl" type="button" onclick="logout()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Logout
</button>
</body>

<script type="text/javascript">

    liff.init({
        liffId: '1657567656-d8PxP0VE',
    });

    const btn = document.getElementById('btn');
    const btnl = document.getElementById('btnl');
    const content = document.getElementById('content');
    btnl.style.visibility = 'hidden';
    btn.addEventListener('click', () => {
        btn.style.visibility = 'hidden';
        btnl.style.visibility = 'visible';
    });
    async function login(){
        if (!liff.isLoggedIn()) {
            liff.login({ redirectUri: "https://fat-moose-51.loca.lt/api/movie/pushMessage" });
        }

        var profile = await liff.getProfile();
        console.log(profile)
        profile.then(
            document.getElementById("userId").innerHTML = profile.userId,
            document.getElementById("displayName").innerHTML = profile.displayName,
            document.getElementById("pictureUrl").src = profile.pictureUrl,

        );
    }

    function logout(){
        if (liff.isLoggedIn()) {
            liff.logout();
            content.style.display = 'none';
            btn.style.visibility = 'visible';
            btnl.style.display = 'none';
        }
    }

</script>
