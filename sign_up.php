<!DOCTYPE html>
<html>
    <head>
        <title>Household Account Book</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="common.css">
        <link rel="stylesheet" type="text/css" href="sign_up.css">
    </head>
    <body>
        <header>
            <a class="title" href="index.php">Household Account Book</a>
            <div class="buttons">
                <a class="sign" href="sign_in.php">sign in</a>
                <a class="sign" href="sign_up.php">sign up</a>
            </div>
        </header>
        <form class="apply" action="sign_up_comp.php" method="post">
            <div class="apply_item">
                <input type="text" name="userName" placeholder="name">
            </div>
            <div class="apply_item">
                <input type="password" name="password1" placeholder="passowrd">
            </div>
            <div class="apply_item">
                <input type="password" name="password2" placeholder="confirm">
            </div>
            <div class="button">
                <button type="submit">sign up</button>
            </div>
        </form>
    </body>
</html>