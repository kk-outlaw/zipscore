<?php
require_once('../../zipscore/calendar.php');
require_once('../../zipscore/scoreboard.php');

$title = "zipscore";

$calendar = new Calendar();

//日付が指定されていたら試合一覧を取得する
$scoreboard = new Scoreboard($calendar->getYear(), $calendar->getMonth(), $calendar->getDay());
$scoreboard->load();
if(!$scoreboard->isEmpty()) {
    $scoreboard->parse();
    
    $title = $scoreboard->getTitle();
}

$tweetButton = '<a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-show-count="false">Tweet</a>';
$tweetScript = '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';

    echo <<<HEADER
<!DOCTYPE html>
<html lang="ja">
<head>
    <title>$title</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    {$tweetScript}
    
    <style>
        @media screen and (min-width:640px) {
            /* PC向けCSS */
            html {
                font-size: 62.5%;
            }
            body {
                font-size: 1.4em;
            }
            table {
                border-collapse: collapse;
                table-layout: fixed;
            }
            th {
                background-color: #00FFFF;
                border: solid 1px;
                padding: 2px;
            }
            td {
                border: solid 1px;
                padding: 2px;
            }
        }
        @media screen and (max-width:639px) {
            /* スマホ向けCSS */
            html {
                font-size: 100%;
            }
            body {
                font-size: 0.4em;
            }
            form {
                font-size: 1.0em;
            }
            table {
                border-collapse: collapse;
                table-layout: fixed;
            }
            th {
                background-color: #00FFFF;
                border: solid 1px;
                padding: 2px;
            }
            td {
                border: solid 1px;
                padding: 2px;
            }
        }
</style>
</head>
<body>
HEADER;

echo '<hr>';
echo $calendar->datePickerForm("zipscore.php");

echo '<hr>';

//試合一覧
if(!$scoreboard->isEmpty()) {
    print $tweetButton;
    print "<br>";
    
    echo <<<BEGIN_TABLE
<table>
<tr>
    <th>series</th>
    <th colspan='2'>away</th>
    <th colspan='2'>home</th>
    <th>status</th>
    <th>note</th>
    <!-- th></th -->
</tr>
BEGIN_TABLE;
    foreach($scoreboard->getScores() as $score) {
        echo <<<ROW
        <tr>
            <td>{$score['series']}</td>
            <td>{$score['away']}</td>
            <td style="text-align: right">{$score['away_runs']}</td>
            <td style="text-align: right">{$score['home_runs']}</td>
            <td>{$score['home']}</td>
            <td>{$score['status']}</td>
            <td>{$score['note']}</td>
            <!-- td>
                <form style="display: inline" id="gid" method="GET" action="scoreboard.php" target="_self">
                    <input type="hidden" name="guid" value="{$score['guid']}">
                    <input type="hidden" name="year" value="{$calendar->getYear()}">
                    <input type="hidden" name="month" value="{$calendar->getMonth()}">
                    <input type="hidden" name="day" value="{$calendar->getDay()}">
                    <input type="submit" name="submit" value="BOX">
            </td -->
</form>
        </tr>
ROW;
    }
    echo <<<END_TABLE
</table>
END_TABLE;
} else {
    echo $scoreboard->getMessage();
}

    echo <<<FOOTER
    </body>
</html>
FOOTER;
?>
