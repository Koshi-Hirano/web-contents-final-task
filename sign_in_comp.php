<!DOCTYPE html>
<html>
    <head>
        <title>Household Account Book</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="common.css">
    </head>
    <body>
        <header>
            <a class="title" href="index.php">Household Account Book</a>
            <div class="buttons">
                <a class="sign" href="sign_in.php">sign in</a>
                <a class="sign" href="sign_up.php">sign up</a>
            </div>
        </header>
        <?php
            $host = 'localhost';
            $user = 'www';
            $pass = '4510491210umr';
            $dbname = 'household';
                
            $mysqli = new mysqli($host,$user,$pass,$dbname);
            if ($mysqli->connect_error) {
                echo $mysqli->connect_error;
                exit();
            } else {
                $mysqli->set_charset("utf8");
            }
                
            if(!empty($_POST["userName"]) && !empty($_POST["password"])){
                $userName = $_POST["userName"];
                $password = $_POST["password"];

                $sql = "select uid, password from users where userName = '$userName'";
                $result = $mysqli->query($sql);
                if( $result->num_rows == 0){
                    echo "ユーザ名「${userName}」は登録されていません。<br>";
                    exit();
                }
                    
                $row = $result->fetch_assoc();
                $db_enc_passwd = $row["password"];
                $uid = $row["uid"];
                if(password_verify($password, $db_enc_passwd)) {
                    echo "<div class=\"box\">"
                            ."<p class=\"phrase\">"."Hello, ${userName}!"."</p>"
                            ."<a href=\"index.php\">"."HOME >"."</a>"
                        ."</div>";
                    
                    session_start();
                    $_SESSION['uid'] = $uid;
                } else {
                    echo "ユーザ「${userName}」を認証できませんでした。パスワードが一致しません。<br>";
                    exit();
                }
                $result->close();
                $mysqli->close();
            } else {
                echo "入力されていない項目があります。<br>";
            }
        ?>
    </body>
</html>