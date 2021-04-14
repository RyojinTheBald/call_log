<?php
include ("call_log.php");

$call = $call_log->get_call($_REQUEST['id']);
if(!$call)
{
    http_response_code(404);
    die("Call not found");
}

if(isset($_REQUEST['confirm']))
{
    $call_log->delete_call($_REQUEST['id']);
    header('Location: ./', true, 302);
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Delete Call - <?= $call['Subject'] ?></title>
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
    <h1>Delete Call: <?= $call['Subject'] ?></h1>
    <h2>Are you sure you want to delete this call?</h2>
    <div class="actions">
        <a href="view.php?id=<?=$_REQUEST['id']?>">Cancel</a>
        <a href="delete.php?id=<?=$_REQUEST['id']?>&confirm=true">Confirm</a>
    </div>

    <h2>Call details</h2>

    <table style="width:100%">
    <tr>
        <td colspan="2"><?= $call['Date'] ?></td>
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
</div>
</body>
</html>
