<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>Household Account Book</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="common.css">
        <link rel="stylesheet" type="text/css" href="edit.css">
    </head>
    <body>
        <header>
            <a class="title" href="index.php">Household Account Book</a>
            <div class="buttons">
                <a class="sign" href="sign_in.php">sign in</a>
                <a class="sign" href="sign_up.php">sign up</a>
            </div>
        </header>
        <!--現在時刻、最大・最小日付 -->
        <?php
            date_default_timezone_set('Asia/Tokyo');
            $current_date = date("Y-m-d");
            $ymd = explode("-", $current_date);
            $current_day = (int)$ymd[0];
            $current_month = (int)$ymd[1];
            $current_year = (int)$ymd[2];
            $max_date = (new DateTimeImmutable)->modify('last day of')->format('Y-m-d'); 
            $min_date = (new DateTimeImmutable)->modify('last day of last year')->format('Y-m-d');
        ?>
        <!--アクセス確認、DBアクセス、登録、削除 -->
        <?php
            session_start();
            if(isset($_SESSION['uid'])){
                $uid = $_SESSION['uid'];
            } else {
                echo "ログインしていないので、アクセスできません。";
                exit();
            }

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

            $id = $_POST["id"];
            $sql = "select expense, content, amount, date from account where uid = '$uid' and id = '$id'";
            $result = $mysqli->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) { 
                    $expense = $row["expense"];
                    $content = $row["content"];
                    $amount = $row["amount"];
                    $date = $row["date"];
                    $pieces = explode("-", $row["date"]);
                    $year = $pieces[0];
                    $month = $pieces[1];
                    $day = $pieces[2];

                    echo "<div class=\"info\">"
                            ."<div class=\"item\">".$year."/".$month."/".$day."</div>"
                            ."<div class=\"item\">".$expense."</div>"
                            ."<div class=\"item\">".$content."</div>"
                            ."<div class=\"item\">￥".$amount."</div>"
                        ."</div>";
                }
            }
        ?>
        <form class="apply" action="index.php" method="post">
            <input type="hidden" name="id" value="<?php echo $id;?>">
            <div class="inline-radio">
                <?php
                    if($expense == "収入"){
                        echo "<input type=\"radio\" id=\"income\" name=\"expense\" value=\"収入\" checked required>"
                            ."<label for=\"income\" class=\"income\">income</label>"
                            ."<input type=\"radio\" id=\"payment\" name=\"expense\" value=\"支出\" required>"
                            ."<label for=\"payment\" class=\"payment\">payment</label>";
                    }else if($expense == "支出"){
                        echo "<input type=\"radio\" id=\"income\" name=\"expense\" value=\"収入\" required>"
                            ."<label for=\"income\" class=\"income\">income</label>"
                            ."<input type=\"radio\" id=\"payment\" name=\"expense\" value=\"支出\" checked required>"
                            ."<label for=\"payment\" class=\"payment\">payment</label>";
                    }
                ?>
            </div>
            <div class="apply_item">
                <input type="text" id="content" name="content" value="<?php echo $content?>" placeholder="content" required>
            </div>
            <div class="apply_item">
                <input type="number" id="amount" name="amount" value="<?php echo $amount?>" placeholder="$amount" required>
            </div>
            <div class="apply_item">
                <input type="date" id="date" name="date" max="<?php echo $max_date;?>" min="<?php echo $min_date?>" value="<?php echo $date;?>" required>
            </div>
            <div class="button">
                <button type="submit" class="submit">Edit</button>
            </div>
        </form>
    </body>
</html>