<?php
include("call_log.php");

if (count($_POST) > 0)
{
    $post_id = $call_log->create_call($_POST['ITPerson'], $_POST['UserName'], $_POST['Subject'], $_POST['Details'], $_POST['Status']);

    header('Location: ./view.php?id='.$post_id, true, 302);
    die();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Create Call</title>
<style>
    button {
        margin-right: 30px;
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
    <div style="width: 50%; margin: auto">
        <h1>Create Call</h1>
        <div class="actions">
            <a href="./">Back</a>
        </div>
        <form action="create.php" method="POST">
            <table style="width:100%">
            <tr>
                <td>IT Person: <input type="text" name="ITPerson"></td>
                <td>User Name: <input type="text" name="UserName"></td>
                <td>Status: <select name="Status">
                    <option>New</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                </select>
                </td>
            </tr>
            <tr>
                <td>Subject: <input type="text" name="Subject"></td>
            </tr>
            <tr>
                <td colspan="3">Call Details: <br><textarea name="Details" style="width: 100%; height: 200px;"></textarea></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td><button type="reset">Clear</button><button type="submit">Create Call</button></td>
            </tr>
            </table>
        </form>

    </div>
</body>
</html>