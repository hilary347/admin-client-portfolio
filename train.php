

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="train.php" method="post">
        <label for="user">username</label>
        <input type="text" name="username">
        <input type="submit" name="login">
    </form>
</body>
</html>

<?php 
  session_start(); 
?>