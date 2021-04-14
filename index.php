<?php
include("call_log.php");

$limit = $_REQUEST['limit'] ?? 10;
$offset = $_REQUEST['offset'] ?? 0;

$sql = "SELECT * FROM `call_header` WHERE `deleted`=FALSE ".
    (isset($_REQUEST['UserName']) && $_REQUEST['UserName'] ? "AND `UserName`='". $call_log->db->real_escape_string($_REQUEST['UserName'])."' " : "").
    (isset($_REQUEST['CallId']) && $_REQUEST['CallId'] ? "AND `CallId`='". $call_log->db->real_escape_string($_REQUEST['CallId'])."' " : "").
    (isset($_REQUEST['dateFrom']) && $_REQUEST['dateFrom'] ? "AND `Date`>='". $call_log->db->real_escape_string($_REQUEST['dateFrom'])."' " : "").
    (isset($_REQUEST['dateTo']) && $_REQUEST['dateTo'] ? "AND `Date`<='". $call_log->db->real_escape_string($_REQUEST['dateTo'])."' " : "");

$res = $call_log->db->query($sql);
if (!$res)
    die('Error: '. $call_log->db->error);

$calls = $res->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
<title>Call Log</title>
<style>
    button{
        margin: 3px;
    }
</style>
</head>
<body>
    <div style="width: 50%; margin: auto">
        <h1>Call Log</h1>

        <div style="margin-bottom: 30px;">
            <a href="create.php">Create new call</a>
        </div>
        <div style="margin-bottom: 30px;">
            <form>
                <table style="border: solid black 1px;">
                    <thead>
                        <td><h3 style="margin-top: 0px;">Search</h3></td>
                    </thead>
                    <tr>
                        <td>Username: <input type="text" name="UserName" value="<?=$_REQUEST['UserName']??''?>"></td>
                        <td>Call ID: <input type="text" name="CallId" value="<?=$_REQUEST['CallId']??''?>"></td>
                    </tr>
                    <tr>
                        <td colspan="2">Date From: <input type="datetime-local" name="dateFrom" value="<?=$_REQUEST['dateFrom']??''?>"> Date To: <input type="datetime-local" name="dateTo" value="<?=$_REQUEST['dateTo']??''?>"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="text-align:right;"><button type="reset">Reset</button><button type="submit">Search</button></td>
                    </tr>
                </table>
            </form>
        </div>

        <table style="width: 100%;">
            <thead>
                <td>Call ID</td>
                <td>Date</td>
                <td>IT Person</td>
                <td>User Name</td>
                <td>Subject</td>
                <td>Total Hours</td>
                <td>Total Minutes</td>
                <td>Status</td>
            </thead>
            <?php 
            foreach($calls as $call)
            {
                $link = "view.php?id=".$call['CallId'];
            ?>
            <tr>
                <td><a href="<?=$link?>"><?=$call['CallId']?></a></td>
                <td><a href="<?=$link?>"><?=$call['Date']?></a></td>
                <td><a href="<?=$link?>"><?= $call['ITPerson'] ?></a></td>
                <td><a href="<?=$link?>"><?= $call['UserName'] ?></a></td>
                <td><a href="<?=$link?>"><?= $call['Subject'] ?></a></td>
                <td><a href="<?=$link?>"><?= $call['Total_Hours'] ?></a></td>
                <td><a href="<?=$link?>"><?= $call['Total_Minutes'] ?></a></td>
                <td><a href="<?=$link?>"><?= $call['Status'] ?></a></td>
            </tr>
            <?php
            }
            ?>    
        </table>

        <div style="margin-top: 30px;">
            <?php if ($offset > 0) { ?>
            <a style="float: left;" href="?offset=<?=max($offset - $limit, 0)?>&limit=<?=$limit?>">Previous Page</a>
            <?php } ?>
            <?php if (count($calls) >= $limit) { ?>
            <a style="float: right;" href="?offset=<?=$offset + $limit?>&limit=<?=$limit?>">Next Page</a>
            <?php } ?>
        </div>
    </div>
</body>
</html>
