<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>Household Account Book</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="main.css">
        <link rel="stylesheet" type="text/css" href="common.css">
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.0/chart.min.js"
            integrity="sha512-VMsZqo0ar06BMtg0tPsdgRADvl0kDHpTbugCBBrL55KmucH6hP9zWdLIWY//OTfMnzz6xWQRxQqsUFefwHuHyg=="
            crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@next/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    </head>
    <body> 
        <!--コンソール表示-->
        <?php
            function console_log($data){
                echo '<script>';
                echo 'console.log('.json_encode($data).')';
                echo '</script>';
            }
        ?>
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
            if($_SERVER['REQUEST_METHOD']==='POST'){
                header('Location:http://localhost/myWeb/index.php');   
            }

            session_start();
            if(isset($_SESSION['uid'])){
                $uid = $_SESSION['uid'];
            } else {
                echo "<header>"
                        ."<a class=\"title\" href=\"index.php\">Household Account Book</a>"
                        ."<div class=\"buttons\">"
                            ."<a class=\"sign\" href=\"sign_in.php\">sign in</a>"
                            ."<a class=\"sign\" href=\"sign_up.php\">sign up</a>"
                        ."</div>"
                    ."</header>";
                echo "<div class=\"box\">"
                        ."<a class=\"link\" href=\"sign_in.php\">"."Please sign in >"."</a>"
                    ."</div>";
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

            if(!empty($_POST["expense"]) && !empty($_POST["content"]) && !empty($_POST["amount"]) && !empty($_POST["date"]) && !empty($_POST["id"])){
                $id = $_POST["id"];
                $expense = $_POST["expense"];
                $content = $_POST["content"];
                $amount = $_POST["amount"];
                $date = $_POST["date"];

                $sql = "update account set expense = '$expense', amount = '$amount', content = '$content', date = '$date' where date = '$id' and uid = '$uid'";
                $result = $mysqli->query($sql);
                if ($result) {
                    echo "データの編集に成功しました <br>";
                } else {
                    echo "データの編集に失敗しました <br>";
                    echo "SQL文：$sql <br>";
                    echo "エラー番号：$mysqli->errno <br>";
                    echo "エラーメッセージ：$mysqli->error <br>";
                    exit();
                }
            }

            if(!empty($_POST["expense"]) && !empty($_POST["content"]) && !empty($_POST["amount"]) && !empty($_POST["date"])){
                unset( $_SESSION["key"] );
            
                $expense = $_POST["expense"];
                $content = $_POST["content"];
                $amount = $_POST["amount"];
                $date = $_POST["date"];

                $sql = "insert into account (expense, content, amount, date, uid) values ('$expense', '$content', '$amount', '$date', '$uid')";
                $result = $mysqli->query($sql);
                if ($result) {                
                } else {
                    echo "データの登録に登録に失敗しました";
                    echo "SQL文：$sql";
                    echo "エラー番号：$mysqli->errno";
                    echo "エラーメッセージ：$mysql->error";
                    exit();
                }
            }

            if(!empty($_POST["id"])){
                $id = $_POST["id"];
                $sql = "delete from account where id = ${id}";
                $result = $mysqli->query($sql);
                if ($result) {
    
                } else {
                    echo "データの削除に失敗しました <br>";
                    echo "SQL文：$sql <br>";
                    echo "エラー番号：$mysqli->errno <br>";
                    echo "エラーメッセージ：$mysqli->error <br>";
                    exit();
                }
            }
        ?>
        <!--グラフデータ作成-->
        <?php
            $incomes = [];
            $payments = [];
            for($i = 1; $i <= 12; $i++){
                $incomes[$i] = [];
                $payments[$i] = [];
            }
            $sql = "select expense, content, amount, date, id from account where uid = '$uid' order by date desc";
            $result = $mysqli->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $date = new DateTime($row["date"]);
                    $max = new DateTime($max_date);
                    $min = new DateTime($min_date);

                    if($min < $date && $date <= $max){
                        if($row["expense"] == "収入"){
                            $pieces = explode("-", $row["date"]);
                            $data = [
                                "content" => $row['content'],
                                "amount" => (int)$row['amount'],
                                "year" => (int)$pieces[0],
                                "month" => (int)$pieces[1],
                                "day" => (int)$pieces[2],
                                "ymd" => $row["date"],
                                "id" => (int)$row['id']
                            ];
                            $incomes[(int)$pieces[1]][] = $data;
                        } else if ($row["expense"] == "支出"){
                            $pieces = explode("-", $row["date"]);
                            $data = [
                                "content" => $row['content'],
                                "amount" => (int)$row['amount'],
                                "year" => (int)$pieces[0],
                                "month" => (int)$pieces[1],
                                "day" => (int)$pieces[2],
                                "ymd" => $row["date"],
                                "id" => (int)$row['id']
                            ];
                            $payments[(int)$pieces[1]][] = $data;
                        }
                    }
                }
            }
            console_log($incomes);
            console_log($payments);
            $json_incomes = json_encode($incomes);
            $json_payments = json_encode($payments);
            $json_current_month = json_encode($current_month);
        ?>
        <!--残高-->
        <?php
            $sql = "select sum(amount) from account where uid = '$uid' and expense = '収入'";
            $result = $mysqli->query($sql);
            $income = 0;
            if($result){
                $row = $result->fetch_assoc();
                $income = $row["sum(amount)"];
            }

            $sql = "select sum(amount) from account where uid = '$uid' and expense = '支出'";
            $result = $mysqli->query($sql);
            $payment = 0;
            if($result){
                $row = $result->fetch_assoc();
                $payment = $row["sum(amount)"];
            }    
            $balance = $income - $payment;

            $income_month = 0;
            foreach ($incomes[$current_month] as $income){
                $income_month += $income["amount"];
            }

            $payment_month = 0;
            foreach ($payments[$current_month] as $payment){
                $payment_month += $payment["amount"];
            }

            $balance_month = $income_month - $payment_month;
        ?>
        <header>
            <a class="title" href="index.php">Household Account Book</a>
            <div class="buttons">
                <a class="sign" href="sign_in.php">sign in</a>
                <a class="sign" href="sign_up.php">sign up</a>
            </div>
        </header>
        <div class="container">
            <!--サイドバー-->
            <div class="side">
                <div class="balance">
                    <p class="balance_title">Balance</p>
                    <div class="content_box">
                        <p class="balance_content"><?php echo "￥".$balance?></p>
                    </div>
                </div>
                <form class="apply" action="index.php" method="post">
                    <div class="inline-radio">
                        <input type="radio" id="income" name="expense" value="収入" checked required>
                        <label for="income" class="income">income</label>
                        <input type="radio" id="payment" name="expense" value="支出" required>
                        <label for="payment" class="payment">payment</label>
                    </div>
                    <div class="apply_item">
                        <input type="text" id="content" name="content" placeholder="content" required>
                    </div>
                    <div class="apply_item">
                        <input type="number" id="amount" name="amount" placeholder="amount" required>
                    </div>
                    <div class="apply_item">
                        <input type="date" id="date" name="date" max="<?php echo $max_date;?>" min="<?php echo $min_date?>"value="<?php echo $current_date;?>" required>
                    </div>
                    <div class="button">
                        <button type="submit" class="submit">Add</button>
                    </div>
                </form>
            </div>
            <!--メイン表示-->
            <div class="board">
                <canvas id="myChart"></canvas>
                <div class="table">
                    <table class="income">
                        <tr><th colspan="5">INCOME</th></tr>
                        <?php
                            for($i = 0; $i < 12; $i++){
                                $month = $current_month - $i;
                                if($month <= 0){
                                    $month = 12 + $month;
                                }
                                foreach ($incomes[$month] as $income){
                                    $id = $income["id"];
                                    echo "<tr>"
                                            ."<td class=\"data\">".$income["year"]."/".$income["month"]."/".$income["day"]."</td>"
                                            ."<td class=\"data\">￥".$income["amount"]."</td>"
                                            ."<td class=\"data\">".$income["content"]."</td>"
                                            ."<form method=\"post\">"
                                                ."<input type=\"hidden\" name=\"id\" value=\"${id}\">"
                                                ."<td>"."<button class=\"td_button\" type=\"submit\" formaction=\"index.php\" >delete</button>"."</td>"
                                                ."<td>"."<button class=\"td_button\" type=\"submit\" formaction=\"edit.php\">edit</button>"."</td>"
                                            ."</form>"
                                        ."</tr>";
                                }
                            }
                        ?>
                    </table>
                    <table class="payment">
                        <tr><th colspan="5">PAYMENT</th></tr>
                        <?php
                            for($i = 0; $i < 12; $i++){
                                $month = $current_month - $i;
                                if($month <= 0){
                                    $month = 12 + $month;
                                }
                                foreach ($payments[$month] as $payment){
                                    $id = $payment["id"];
                                    echo "<tr>"
                                            ."<td class=\"data\">".$payment["year"]."/".$payment["month"]."/".$payment["day"]."</td>"
                                            ."<td class=\"data\">￥".$payment["amount"]."</td>"
                                            ."<td class=\"data\">".$payment["content"]."</td>"
                                            ."<form method=\"post\">"
                                                ."<input type=\"hidden\" name=\"id\" value=\"${id}\">"
                                                ."<td>"."<button class=\"td_button\" type=\"submit\" formaction=\"index.php\">delete</button>"."</td>"
                                                ."<td>"."<button class=\"td_button\" type=\"submit\" formaction=\"edit.php\">edit</button>"."</td>"
                                            ."</form>"
                                        ."</tr>";
                                }
                            }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <script>
            const incomes = <?php echo $json_incomes; ?>;
            const payments = <?php echo $json_payments; ?>;
            const current_month = <?php echo $json_current_month; ?>;
                
            const months = [];
            const incomes_month = [];
            const payments_month = [];
            const balances_month = [];

            for(let i = 0; i < 12; i++){
                let month = current_month - i;
                if(month <= 0){
                    month = 12 + month;
                }
                months.unshift(month);

                let sum_income = 0;
                incomes[month].map(income => {
                    sum_income += income["amount"];
                })
                incomes_month.unshift(sum_income);

                let sum_payment = 0;
                payments[month].map(payment => {
                    sum_payment -= payment["amount"];
                })
                payments_month.unshift(sum_payment);
                    
                balances_month.unshift(sum_income + sum_payment);
            }

            // 値を設定
            const datasets = [
                {
                    label: 'income',
                    data: incomes_month,
                    backgroundColor: ['#bce0fd']
                },
                {
                    label: 'payment',
                    data: payments_month,
                    backgroundColor: ['#ffbebd']
                },
                {
                    label: 'balance',
                    data: balances_month,
                    backgroundColor: ['#f5ff66']
                }
            ]
            /* ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
            グラフ描画
            ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー */
            var ctx = document.getElementById("myChart");
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: datasets
                },
            });
        </script>
    </body>
</html>