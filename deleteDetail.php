<?php
require("call_log.php");

$detail = $call_log->get_detail($_REQUEST['id']);

if(!$detail)
{
    http_response_code(404);
    die("Call detail not found");
}

if(isset($_REQUEST['confirm']))
{
    $call_log->delete_detail($_REQUEST['id']);
    header('Location: ./view.php?id='.$detail['CallId'], true, 302);
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Delete Call Detail</title>
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
    <h1>Delete Call Detail</h1>
    <h2>Are you sure you want to delete this call detail?</h2>
    <div class="actions">
        <a href="view.php?id=<?=$_REQUEST['id']?>">Cancel</a>
        <a href="deleteDetail.php?id=<?=$_REQUEST['id']?>&confirm=true">Confirm</a>
    </div>

    <h2>Call details</h2>

    <table style="width:100%">
        <tr>
            <td>Date: <?= $detail['Date'] ?></td>
            <td>Hours: <?= $detail['Hours'] ?></td>
            <td>Minutes: <?= $detail['Minutes'] ?></td>
        </tr>
        <tr>
            <td colspan="4"><?= $detail['Details'] ?></td>
        </tr>
    </table>    

</div>
</body>
</html>
