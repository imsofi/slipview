<?php
  if(isset($_COOKIE["user"])) {
    header('Location: home.php');
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Slipview</title>
</head>
<body>
  <div style="width:20em; margin: 15% auto -10% auto;">
    <h1 id="logo"><span class="text-thin">SLIP</span><span class="text-bold">VIEW</span></h1>
    <p style="background-color:var(--main-accent);padding-top:1em;">Please log in or <a href="register.php" style="color: lightblue;">register</a></p>
    <form class="user-container" style="background-color:var(--main-accent)" action="src/auth.php"method="post">
        <div>
          <label for="uname"><b style="padding-right: 2.5em;">Username</b></label>
          <input type="text" placeholder="Enter Username" name="uname" required>
          <br>
          <label for="psw"><b style="padding-right: 2.8em;">Password</b></label>
          <input type="password" placeholder="Enter Password" name="psw" required>
          <br>
          <button type="submit">Login</button>
          <p style="background-color:var(--main-accent);padding-top:1em;"><a href="forgotten.php" style="color: lightblue;">Forgotten password?</a></p>
        </div>
    </form>
  </div>
</body>
</html>
