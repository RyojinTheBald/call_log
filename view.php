<?php
require("call_log.php");

if(count($_POST) > 0)
{
    $call_log->add_detail($_REQUEST['id'], $_POST['Details'], $_POST['Hours'], $_POST['Minutes']);
}

$call = $call_log->get_call($_REQUEST['id']);
if(!$call)
{
    http_response_code(404);
    die("Call not found");
}

$details = $call_log->get_details($_REQUEST['id']);

?>
<!DOCTYPE html>
<html>
<head>
<title>View Call - <?= $call['Subject'] ?></title>
<style>
    table {
        border-collapse: collapse;
    }
    td {
        padding: 5px;
        border: solid black 1px;
    }
    .actions{
        margin-bottom: 30px;
    }
    .actions a{
        padding-right: 30px;
    }
</style>
</head>
<body>
<div style="width: 50%; margin: auto;">
    <h1>View Call: <?= $call['Subject'] ?></h1>
    <div class="actions">
        <a href="./">Back</a>
        <a href="delete.php?id=<?=$_REQUEST['id']?>">Delete</a>
    </div>

    <h2>Call details</h2>

    <table style="width:100%">
    <tr>
        <td colspan="2">Date: <?= $call['Date'] ?></td>
        <td>IT Person: <?= $call['ITPerson'] ?></td>
        <td>User Name: <?= $call['UserName'] ?></td>
    </tr>
    <tr>
        <td>Subject: <?= $call['Subject'] ?></td>
        <td>Total Hours: <?= $call['Total_Hours'] ?></td>
        <td>Total Minutes: <?= $call['Total_Minutes'] ?></td>
        <td>Status: <?= $call['Status'] ?></td>
    </tr>
    <tr>
        <td colspan="4"><?= $call['Details'] ?></td>
    </tr>
    </table>
    <hr>
    <h2>Add Call Detail</h2>
    <form action="view.php?id=<?=$_REQUEST['id']?>" method="POST">
        <table style="width:100%">
            <tr>
                <td>Hours: <input type="number" name="Hours"></td>
                <td>Minutes: <input type="number" name="Minutes"></td>
            </tr>
            <tr>
                <td colspan="2"><textarea style="width: 99%; height: 150px;" name="Details"></textarea></td>
            </tr>
        </table>
        <div style="text-align:right;">
            <button style="margin:5px;" type="submit">Add Detail</button>
        </div>
    </form>
    <hr>
    <?php
    foreach($details as $detail)
    {
    ?>
    <table style="width:100%">
        <tr>
            <td>Date: <?= $detail['Date'] ?></td>
            <td>Hours: <?= $detail['Hours'] ?></td>
            <td>Minutes: <?= $detail['Minutes'] ?></td>
            <td><a href="deleteDetail.php?id=<?= $detail['RowId']?>">[X]</a></td>
        </tr>
        <tr>
            <td colspan="4"><?= $detail['Details'] ?></td>
        </tr>
    </table>
    <hr>
    <?php
    }
    ?>
</div>
</body>
</html>
