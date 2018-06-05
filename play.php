<?php /* XSS safemeasure */
  include ('src/database_connection.php');
  include ('src/check.php');
  function _e($string) {
    echo htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
  }
?>
<?php if(isset($_POST["submit"])) {
  $username2sql = sprintf("SELECT userid FROM user WHERE username='%s'",
                          $auth->real_escape_string($_COOKIE["user"])
                        );
  $username2database = $auth->query($username2sql);
  if ($username2row = mysqli_fetch_array($username2database)) {
    $uploadcommentsql = sprintf("INSERT INTO comment(date, text, videoid, userid) VALUES(NOW(), '%s', '%s', '%s')",
                    $auth->real_escape_string($_POST["txtTekst"]),
                    $auth->real_escape_string($_GET["v"]),
                    $auth->real_escape_string($username2row["userid"])
                    );
    $auth->query($uploadcommentsql);
  }
}

if(isset($_GET["deletecommentID"]))
{
  $deletecommentsql = sprintf("DELETE FROM comment WHERE idcomment='%s'",
                $auth->real_escape_string($_GET["deletecommentID"]) /* TODO: Bad way to delete. */
                );
  $auth->query($deletecommentsql);
}
?>
<?php /* Check incomming variables */
if (isset($_GET["v"])) {
  $v = $_GET["v"];
  if(filter_var($_GET["v"], FILTER_VALIDATE_INT) === false) {
    header('Location: error.php?e=v');
  }
  if (isset($_GET["s"])) {
    $s = $_GET["s"];
    if(filter_var($_GET["s"], FILTER_VALIDATE_INT) === false) {
      header('Location: error.php?e=s');
    }
  } else {
    $s = 1;
  }
  if (isset($_GET["e"])) {
    $e = $_GET["e"];
    if(filter_var($_GET["e"], FILTER_VALIDATE_INT) === false) {
      header('Location: error.php?e=e');
    }
  } else {
    $e = 1;
  }
} else {
  header('Location: home.php');
}
?>
<?php /* Database connection */
  $sql = sprintf("SELECT videoid, episodename, episodedesc, episode, season, views, inseries
                  FROM video
                  WHERE %s = inseries AND %s = season AND %s = episode",
                  $auth->real_escape_string($v),
                  $auth->real_escape_string($s),
                  $auth->real_escape_string($e)
                );
  $database = $auth->query($sql);
?>
<?php  /* Database to get username */
if($row = mysqli_fetch_array($database)) {

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
<?php
  $seriessql = sprintf("SELECT season, episode
                  FROM video
                  WHERE %s = inseries AND 1 = episode",
                  $auth->real_escape_string($v)
                );
  $seriesdatabase = $auth->query($seriessql);
?>
<?php /* Views counter */
  $viewssql = sprintf("UPDATE video
                       SET views='%s'
                       WHERE inseries=%s AND season=%s AND episode=%s",
                        $auth->real_escape_string($row["views"]+1),
                        $auth->real_escape_string($row["inseries"]),
                        $auth->real_escape_string($row["season"]),
                        $auth->real_escape_string($row["episode"])
                      );
  $viewsdatabase = $auth->query($viewssql);
?>
<?php
  $user2sql = sprintf("SELECT userid, username FROM user WHERE username='%s';",
                      $auth->real_escape_string($_COOKIE["user"])
                    );
  $user2database = $auth->query($user2sql);
  $user2row = mysqli_fetch_array($user2database);
?>
<?php
  $categorysql = sprintf("SELECT * FROM categorylist
                          INNER JOIN categories ON categorylist.category=categories.category
                          INNER JOIN series ON categories.inseries=series.seriesid
                          WHERE inseries='%s';",
                          $auth->real_escape_string($v));
  $categorydatabase = $auth->query($categorysql);
?>
<?php
$commentsql = sprintf("SELECT comment.idcomment, comment.videoid, user.userid, user.username, comment.text, comment.date
                        FROM comment
                        INNER JOIN user ON comment.userid=user.userid
                        INNER JOIN video ON comment.videoid=video.videoid
                        WHERE comment.videoid='%s'
                        ORDER BY comment.date DESC;",
                        $auth->real_escape_string($row["videoid"])
                      );

$commentdatabase = $auth->query($commentsql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Slipview - <?php _e($row["episodename"]) ?></title>
</head>
<body>
  <header id="menu">
    <h1 id="logo"><a href="home.php"><span class="text-thin">SLIP</span><span class="text-bold">VIEW</span></a></h1>
    <div class="">
      <form action="search.php">
        <input class="input" type="text" name="s" placeholder="Search" autocomplete="off" />
      </form>
    </div>
    <nav id="category-list">
      <?php while($seriesrow = mysqli_fetch_array($seriesdatabase)) { ?>
      <h5>Season <?php _e($seriesrow["season"])?></h5>
      <?php
        $epssql = sprintf("SELECT episode, episodename
                        FROM video
                        WHERE %s = inseries AND %s = season",
                        $auth->real_escape_string($v),
                        $auth->real_escape_string($seriesrow["season"])
                      );
        $epdatabase = $auth->query($epssql);
        ?>
        <ul>
        <?php while($eprow = mysqli_fetch_array($epdatabase)){ ?>
          <li><a href="play.php?v=<?php _e($v) ?>&s=<?php _e($seriesrow["season"]) ?>&e=<?php _e($eprow["episode"]) ?>">#<?php _e($eprow["episode"])?> - <?php _e($eprow["episodename"])?></a></li>
        <?php } ?>
        </ul>
      <?php } ?>
    </nav>
    <div class="user-container">
      <img src="userdata/profilepicture/<?php _e($user2row["userid"]) ?>.jpg" alt="Avatar" class="img-avatar"/>
      <p class="flex-grow"><?php _e($_COOKIE["user"]); ?></p>
      <p><a href="src/auth.php?uname=<?php _e($_COOKIE["user"]); ?>&logout=1"><i class="fas fa-sign-out-alt"></i></a></p>
    </div>
  </header>
  <main class="fade-in">
    <video controls>
      <source src="userdata/series/<?php _e($row["inseries"])?>/<?php _e($row["videoid"])?>.mp4" type="video/mp4">
      Video could not be played.
    </video>
    <div id="content">
      <div id="about">
      <h1><span id="seasonepisode">S<?php _e($row["season"])?>E<?php _e($row["episode"])?></span> <?php _e($row["episodename"]) ?></h1> <?php while($categorysql = mysqli_fetch_array($categorydatabase)) { ?>
        <a href="home.php#<?php _e($categorysql["categoryname"]); ?>" class="small tag">• <?php _e($categorysql["categoryname"]); ?></a>
      <?php } ?>
      <p><?php _e($row["episodedesc"]) ?></p>
      </div>
      <div id="stats">
        <div class="user-container">
          <?php if($usernamerow = mysqli_fetch_array($usernamedatabase)) { ?>
          <img src="userdata/profilepicture/<?php _e($usernamerow["userid"]); ?>.jpg" alt="Avatar" class="img-avatar"/>
          <p class="flex-grow"><?php _e($usernamerow["username"])?></p>
          <?php } ?>
        </div>
        <p id="views"><?php _e($row["views"]) ?> views</p>
      </div>
    </div>
    <div class="container">
      <form method="post">
        <div class="user-container container">
          <img src="userdata/profilepicture/<?php _e($user2row["userid"])?>.jpg" alt="Avatar" class="img-avatar"/>
          <p class="flex-grow">Jeexzee</p>
          <p><button type="submit" name="submit">Comment</button></p>
        </div>
        <textarea name="txtTekst" cols="50" rows="5"></textarea>
        <br />
      </form>

      <?php while($commentrow = mysqli_fetch_array($commentdatabase)) { ?>
      <div class="container comment-list">
        <div class="user-container">
          <img src="userdata/profilepicture/<?php _e($commentrow["userid"]) ?>.jpg" alt="Avatar" class="img-avatar"/>
          <p class="flex-grow"><?php _e($commentrow["username"]) ?></p>
          <?php if ($commentrow["userid"] === $user2row["userid"]) {?><p><a href="play.php?v=<?php _e($_GET['v'])?>&deletecommentID=<?php _e($commentrow["idcomment"]) ?>"><i class="fas fa-trash"></i></a></p> <?php } ?>
        </div>
        <div class="comment">
          <p><?php _e($commentrow["text"]) ?></p>
          <p class="small date">Written on <?php _e($commentrow["date"]) ?></p>
        </div>
      </div>
    <?php } ?>
  <?php } ?>
    </div>
  </main>
</body>
</html>
