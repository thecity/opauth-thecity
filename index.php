<html>
<head>
</head>
<body>
  <a href="strategy.php">go go gadget</a>
  <?php
    if(isset($_GET['code'])) {
      header('location: strategy.php?code=' . $_GET['code']);
    }
  ?>
</body>
</html>