<?php
  if(!isset($_COOKIE["user"])){
    header('Location: login.php');
  }
?>

<?php /* XSS safemeasure */
  function _e($string) {
    echo htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
  }
  include ('src/database_connection.php');
  include ('src/check.php');
?>
<?php
  $categorysql = sprintf("SELECT * FROM categorylist;");
  $categorysql2 = sprintf("SELECT * FROM categorylist;");
  $categorydatabase = $auth->query($categorysql);
  $categorydatabase2 = $auth->query($categorysql);
?>
<?php
  $user2sql = sprintf("SELECT userid, username FROM user WHERE username='%s';",
                      $auth->real_escape_string($_COOKIE["user"])
                    );
  $user2database = $auth->query($user2sql);
  $user2row = mysqli_fetch_array($user2database);
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
  <header id="menu">
    <h1 id="logo"><a href="home.php"><span class="text-thin">SLIP</span><span class="text-bold">VIEW</span></a></h1>
    <div class="">
      <form action="search.php">
        <input class="input" type="text" name="s" placeholder="Search" autocomplete="off" />
      </form>
    </div>
    <nav id="category-list">
      <ul>
        <?php while($categorysql2 = mysqli_fetch_array($categorydatabase2)) { ?>
        <li><a href="#<?php _e($categorysql2["categoryname"]) ?>"><?php _e($categorysql2["categoryname"]) ?></a></li>
        <?php } ?>
      </ul>
    </nav>
    <div class="user-container">
      <img src="userdata/profilepicture/<?php _e($user2row["userid"]) ?>.jpg" alt="Avatar" class="img-avatar"/>
      <p class="flex-grow"><?php _e($_COOKIE["user"]); ?></p>
      <p><a href="src/auth.php?uname=<?php _e($_COOKIE["user"]); ?>&logout=1"><i class="fas fa-sign-out-alt"></i></a></p>
    </div>
  </header>
  <main class="fade-in">
    <?php
      while($categorysql = mysqli_fetch_array($categorydatabase)) {
      $sql = sprintf("SELECT categorylist.category, categorylist.categoryname, series.seriesid, series.name, series.desc, series.popularity
                      FROM categorylist
                      INNER JOIN categories ON categorylist.category=categories.category
                      INNER JOIN series ON categories.inseries=series.seriesid
                      WHERE categorylist.category='%s';",
                      $auth->real_escape_string($categorysql["category"])
                    );
      $database = $auth->query($sql);
    ?>
    <h4 class="recent tab" id="<?php _e($categorysql["categoryname"]) ?>"><?php _e($categorysql["categoryname"])?></h4>
    <article class="list-container">
      <div class="list">
        <?php while($row = mysqli_fetch_array($database)) { ?>
          <a href="play.php?v=<?php _e($row["seriesid"])?>" class="list-item" style="background-image: url(userdata/preview/<?php _e($row["seriesid"]) ?>.jpg);">
            <div class="list-content">
              <h5><?php _e($row["name"]) ?></h5>
              <div class="more">
                <p><?php _e($row["desc"]) ?></p>
              </div>
            </div>
          </a>
        <?php } ?>
      </div>
    </article>
    <?php } ?>
  </main>
</body>
</html>
