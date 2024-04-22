<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <title>ログイン</title>
</head>

<body>

  <h4>ログイン</h4>
  <form id="login-form" action="login_act.php" method="post">
    <label for="email">メールアドレス</label> <br />
    <input type="email" name="email" id="email" /> <br />
    <label for="password">パスワード</label> <br />
    <input type="password" name="password" id="pass" /> <br />
    <input class="action-button" type="submit" value="ログイン" />
  </form>



  <nav class="bottom-nav">
    <a href="index.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>ホーム</span>
    </a>
    <a href="works.php" class="nav-item">
      <i class="fas fa-film"></i>
      <span>作品</span>
    </a>
    <a href="spot.php" class="nav-item">
      <i class="fas fa-map-marker-alt"></i>
      <span>スポット</span>
    </a>
    <a href="mylist.php" class="nav-item">
      <i class="fas fa-heart"></i>
      <span>マイリスト</span>
    </a>
  </nav>


</body>

</html>