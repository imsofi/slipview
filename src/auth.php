<?php /* Login System */
error_reporting(0); /* Hide Notices over unset variables */
include ('database_connection.php');

if(isset($_POST["uname"]) && isset($_POST["psw"]) && $_POST["pswr"] && $_GET["forgotten"] == 1){
  $checksql = sprintf("SELECT userid, username FROM user WHERE username='%s'",
                        $auth->real_escape_string($_POST["uname"])
                      );
  $checkdatabase = $auth->query($checksql);
  if(!mysqli_fetch_array($checkdatabase)) {
    echo "No account with that name";
  }
  else {
    if ($_POST["psw"] === $_POST["pswr"] && $_POST["psw"] != "") {

      $user4sql = sprintf("SELECT userid FROM user WHERE username='%s';",
                            $auth->real_escape_string($_POST["uname"])
                          );
      $user4database = $auth->query($user4sql);


      while ($user4row = mysqli_fetch_array($user4database)) {
      $reg2sql = sprintf("UPDATE user SET password='%s' WHERE userid='%s';",
                            $auth->real_escape_string(password_hash($_POST["psw"],PASSWORD_BCRYPT)),
                            $auth->real_escape_string($user4row["userid"])
                          );
        $auth->query($reg2sql);
        echo "Password changed successfully!";
      }
    }
    else { echo "You must write a vaild password!"; }
  }
}

if(isset($_POST["uname"]) && isset($_POST["psw"]) && isset($_POST["pswr"]) && !isset($_GET["forgotten"])){
  $checksql = sprintf("SELECT username FROM user WHERE username='%s'",
                        $auth->real_escape_string($_POST["uname"])
                      );
  $checkdatabase = $auth->query($checksql);
  if(mysqli_fetch_array($checkdatabase)) {
    echo "Username already taken";
  }
  else {
    if ($_POST["psw"] === $_POST["pswr"] && $_POST["psw"] != "") {
      $regsql = sprintf("INSERT INTO user (username, password) VALUES ('%s', '%s')",
                          $auth->real_escape_string($_POST["uname"]),
                          $auth->real_escape_string(password_hash($_POST["psw"],PASSWORD_BCRYPT))
                        );
      $auth->query($regsql);
      echo "Register Successfull";
      header('Location: ../login.php');
    }
    else { echo "Passwords do not match!"; }
  }
}

$error = "Your username and/or password may be wrong";

if (isset($_POST["uname"]) && isset($_POST["psw"]) && !isset($_POST["pswr"]) && !isset($_GET["forgotten"])){
  $loginsql = sprintf("SELECT password
                  FROM user
                  WHERE username='%s'",
                  $auth->real_escape_string($_POST["uname"])
                );
  $logindatabase = $auth->query($loginsql);

  if($row = mysqli_fetch_array($logindatabase)) {
    if(password_verify($_POST["psw"],$row["password"])){
      $currentdate = date("Y-m-d H:i:s");
      $passsql = sprintf("UPDATE user
                          SET last_login = '%s', is_logged_in = '%s'
                          WHERE username='%s'",
                    $auth->real_escape_string($currentdate),
                    $auth->real_escape_string(1),
                    $auth->real_escape_string($_POST["uname"])
                  );
      $auth->query($passsql);

      $hash_value = $_POST["uname"] . $currentdate; 

      $cookie_name = "token";
      $cookie_value = hash('sha256', $hash_value);

      setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
      setcookie("user", $_POST["uname"], time() + (86400 * 30), "/");
      header('Location: ../home.php');
    }
    else{
      echo $error;
    }
  }
  else{
    echo $error;
  }
}
?>

<?php /* Logout system */
  if($_GET["uname"] && $_GET["logout"] == 1){
    if(!isset($_COOKIE["token"])) {
      echo "You are already logged out!";
  } else{

    $logoutsql = sprintf("SELECT last_login, is_logged_in
                    FROM user
                    WHERE username='%s'",
                    $auth->real_escape_string($_GET["uname"])
                  );
    $logoutdatabase = $auth->query($logoutsql);

    if($row = mysqli_fetch_array($logoutdatabase)) {
      if($row["is_logged_in"] == 0){
        echo "You are already logged out!";
        header('Location: ../home.php');
      } else{
      $hash_value = $_GET["uname"] . $row["last_login"];
      $cookie_value = hash('sha256', $hash_value);
      if($cookie_value === $_COOKIE["token"]){
        $outsql = sprintf("UPDATE user
                            SET is_logged_in = '%s'
                            WHERE username='%s'",
                            $auth->real_escape_string(0),
                            $auth->real_escape_string($_GET["uname"])
                          );
        $auth->query($outsql);
        setcookie("token", "", time()-3600, "/");
        setcookie("user", "", time()-3600, "/");
        echo "Logged out!";
        header('Location: ../home.php');
      }else{
        echo "Logout failed!";
        }
      }
    }
  }
}
?>
