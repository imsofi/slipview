<?php

function resettoken() {
  setcookie("token", "", time()-3600, "/");
  setcookie("user", "", time()-3600, "/");
  header('Location: login.php');
}

if (isset($_COOKIE["user"]) && isset($_COOKIE["token"])) {
  $realusersql = sprintf("SELECT userid FROM user WHERE username='%s';",
                          $auth->real_escape_string($_COOKIE["user"])
                        );
  $realuserdatabase = $auth->query($realusersql);
  if($realuserrow = mysqli_fetch_array($realuserdatabase)){
    $realchecksql = sprintf("SELECT userid, username, password, last_login, is_logged_in FROM user WHERE userid='%s';",
                            $auth->real_escape_string($realuserrow["userid"]));
    $realcheckdatabase = $auth->query($realchecksql);
    if($realcheckrow = mysqli_fetch_array($realcheckdatabase)){
      $hash_value = $realcheckrow["username"] . $realcheckrow["last_login"];
      $cookie_value = hash('sha256', $hash_value);
      if($_COOKIE["token"] === $cookie_value){
        if($realcheckrow["is_logged_in"] != 1){
          resettoken();
        }
        else {
          /* User has correct token, no reset */
        }
      }
      else {
        resettoken();
      }
    }
    else {
      resettoken();
    }
  }
  else {
    resettoken();
  }
}
else {
  resettoken();
}

?>
