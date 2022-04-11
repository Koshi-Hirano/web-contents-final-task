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

            if(!empty($_POST["userName"]) && !empty($_POST["password1"]) && !empty($_POST["password2"])){
                $userName = $_POST["userName"];
                $password1 = $_POST["password1"];
                $password2 = $_POST["password2"];

                $sql = "select * from users where userName = '$userName'";
                $result = $mysqli->query($sql);
                if( $result->num_rows != 0){
                    echo "ユーザ名「${userName}」はすでに登録されているため使用できません。<br>";
                    exit();
                }

                if($password1 != $password2) {
                    echo "パスワードが一致しません<br>";
                    exit();
                }

                $enc_passwd = password_hash($password1,PASSWORD_DEFAULT);
                
                $sql = "insert into users (userName, password) values ('$userName','$enc_passwd')";
                $result = $mysqli->query($sql);
                if ($result) {
                    echo "<div class=\"box\">"
                            ."<p class=\"phrase\">"."Welcome to my page, ${userName}!"."</p>"
                            ."<a href=\"sign_in.php\">"."sign in >"."</a>"
                        ."</div>";
                } else {
                    echo "データの登録に登録に失敗しました <br>";
                    echo "SQL文：$sql <br>";
                    echo "エラー番号：$mysqli->errno <br>";
                    echo "エラーメッセージ：$mysqli->error <br>";
                    exit();
                }

                $mysqli->close();
            } else {
                echo "入力されていない項目があります。<br>";
            }
        ?>
    </body>
</html>