<?php /* XSS safemeasure */
  function _e($string) {
    echo htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
  }
?>
<?php /* Check incomming variables */
  if (isset($_GET["v"])) {
    if(filter_var($_GET["v"], FILTER_VALIDATE_INT) === false) { /* Check if videoid variable is an integer */
      header('Location: error.php?e=videoid');
    }
  } else {
    header('Location: index.php');
  }
?>
<?php /* Database connection */
  $auth = mysqli_connect("localhost","webgrensesnitt","drossap","slipview");

  $sql = sprintf("SELECT videoid, episodename, episodedesc, episode, season, views, inseries
          FROM video
          WHERE videoid=%s",
          $auth->real_escape_string($_GET["v"])
        );
  $database = $auth->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<?php if($row = mysqli_fetch_array($database)) { ?>
<? /* Database to get username */
  $usersql = sprintf("SELECT seriesid, creator
                          FROM series
                          WHERE seriesid=%s",
                          $auth->real_escape_string($row["inseries"])
                        );
  $userdatabase = $auth->query($usersql);
  if($userrow = mysqli_fetch_array($userdatabase)) {
    $usernamesql = sprintf("SELECT userid, username
                            FROM user
                            WHERE userid=%s",
                            $auth->real_escape_string($userrow["creator"])
                          );
    $usernamedatabase = $auth->query($usernamesql);
  }

?>
<head>
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Slipview - <?php _e($row["episodename"]) ?></title>
</head>
<body>
<main class="fade-in">
  <video controls>
    <source src="userdata/series/<?php _e($row["inseries"])?>/<?php _e($row["videoid"])?>.mp4" type="video/mp4">
    Video could not be played.
  </video>
  <div id="content">
    <div id="about">
    <h1><?php _e($row["episodename"]) ?></h1>
    <p><?php _e($row["episodedesc"]) ?></p>
    </div>
    <div id="stats">
      <div class="user-container">
        <img src="userdata/1/avatar.jpg" alt="Avatar" class="img-avatar"/>
        <?php if($usernamerow = mysqli_fetch_array($usernamedatabase)) { ?>
        <p class="flex-grow"><?php _e($usernamerow["username"])?></p>
        <?php } ?>
      </div>
      <p id="views"><?php _e($row["views"]) ?> views</p>
    </div>
  <?php } ?>
  </div>
</main>
</body>
</html>
