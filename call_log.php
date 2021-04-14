<?php

class CallLog
{
    public $db;

    function __construct() 
    {
        $this->db = mysqli_connect("localhost", "call_log", "call_log", "call_log");
        $this->create_tables();
    }

    function create_tables()
    {
        $res = $this->db->query("
            CREATE TABLE IF NOT EXISTS `call_header` (
                `CallId` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `Date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `ITPerson` VARCHAR(32) NOT NULL,
                `UserName` VARCHAR(32) NOT NULL,
                `Subject` VARCHAR(64) NOT NULL,
                `Details` TEXT,
                `Total_Hours` INT DEFAULT 0,
                `Total_Minutes` INT DEFAULT 0,
                `Status` VARCHAR(32) DEFAULT 'New',
                `deleted` BOOLEAN DEFAULT FALSE
            )
        ");
        if (!$res)
            die('Error: '. $this->db->error);
        
        $res = $this->db->query("
            CREATE TABLE IF NOT EXISTS `call_details` (
                `RowId` INT AUTO_INCREMENT PRIMARY KEY,
                `CallId` INT NOT NULL,
                `Date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `Details` TEXT,
                `Hours` INT DEFAULT 0,
                `Minutes` INT DEFAULT 0
            )
        ");
        if (!$res)
            die('Error: '. $this->db->error);
    }
    
    function create_call($ITPerson, $UserName, $Subject, $Details, $Status)
    {
        //get next available deleted CallId
        $res = $this->db->query("SELECT `CallId` FROM `call_header` WHERE `deleted`=TRUE ORDER BY `CallId` ASC LIMIT 1");
        if (!$res)
            die('Error: '. $this->db->error);

        $CallId = null;
        if ($res->num_rows == 1)
        {
            $CallId = $res->fetch_row()[0];
            $sql = sprintf("DELETE FROM `call_header` WHERE `CallId`='%s'", $CallId);

            $res = $this->db->query($sql);
            if (!$res)
                die('Error: '. $this->db->error);
        }

        $sql = sprintf("
            INSERT INTO `call_header` (`CallId`, `ITPerson`, `UserName`, `Subject`, `Details`, `Status`)
            VALUES ('%u', '%s', '%s', '%s', '%s', '%s')
        ",
            $CallId,
            $this->db->real_escape_string($ITPerson),
            $this->db->real_escape_string($UserName),
            $this->db->real_escape_string($Subject),
            $this->db->real_escape_string($Details),
            $this->db->real_escape_string($Status),
        );
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);

        return $this->db->insert_id;
    }

    function add_detail($CallId, $Details, $Hours, $Minutes)
    {

        $Hours = intval($Hours);
        $Minutes = intval($Minutes);

        if ($Minutes >= 60)
        {
            $Hours += floor($Minutes / 60);
            $Minutes = $Minutes % 60;            
        }

        $CallId = $this->db->real_escape_string($CallId);

        $sql = sprintf("
            INSERT INTO `call_details` (`CallId`, `Details`, `Hours`, `Minutes`)
            VALUES ('%u', '%s', '%u', '%u')
        ",
            $CallId,
            $this->db->real_escape_string($Details),
            $this->db->real_escape_string($Hours),
            $this->db->real_escape_string($Minutes)
        );
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);

        //don't forget to update the call_header's total times
        $call = $this->get_call($CallId);

        $Hours = $Hours + intval($call['Total_Hours']);
        $Minutes = $Minutes + intval($call['Total_Minutes']);

        if ($Minutes >= 60)
        {
            $Hours += floor($Minutes / 60);
            $Minutes = $Minutes % 60;
        }

        $sql = sprintf("
            UPDATE `call_header`
            SET `Total_Hours`='%u', `Total_Minutes`='%u'
            WHERE `CallId`='%u'
        ",
            $Hours,
            $Minutes,
            $CallId
        );
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);
        
    }

    function get_details($CallId)
    {
        $sql = sprintf("SELECT * FROM `call_details` WHERE `CallId`='%u' ORDER BY `Date` DESC", $this->db->real_escape_string($CallId));
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);

        return $res->fetch_all(MYSQLI_ASSOC);
    }

    function get_call($CallId)
    {
        $sql = sprintf("SELECT * FROM `call_header` WHERE `deleted`=FALSE AND `CallId`='%u'", $this->db->real_escape_string($CallId));
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);

        if ($res->num_rows == 1)
            return $res->fetch_assoc();
        
        return null;
    }

    function delete_call($CallId)
    {
        $CallId = $this->db->real_escape_string($CallId);

        $sql = sprintf("UPDATE `call_header` SET `deleted`=TRUE WHERE `CallId`='%u'", $CallId);
        // $sql = sprintf("DELETE FROM `call_header` WHERE `CallId`='%s'", $CallId);
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);

        $sql = sprintf("DELETE FROM `call_details` WHERE `CallId`='%u'", $CallId);
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);
        
    }

    function get_detail($rowId)
    {
        $sql = sprintf("SELECT * FROM `call_details` WHERE `RowId`='%u'", $this->db->real_escape_string($rowId));
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);

        return $res->fetch_assoc();
    }

    function delete_detail($rowId)
    {
        $detail = $this->get_detail($rowId);

        //don't forget to update the call_header's total times
        $call = $this->get_call($detail['CallId']);

        $Hours = intval($call['Total_Hours']) - intval($detail['Hours']);
        $Minutes = intval($call['Total_Minutes']) - intval($detail['Minutes']);

        if ($Minutes < 0)
        {
            $Hours -= 1;
            $Minutes += 60;
        }

        $sql = sprintf("
            UPDATE `call_header`
            SET `Total_Hours`='%u', `Total_Minutes`='%u'
            WHERE `CallId`='%u'
        ",
            $Hours,
            $Minutes,
            $detail['CallId']
        );
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);

        $sql = sprintf("DELETE FROM `call_details` WHERE `RowId`='%u'", $rowId);
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);
    }

    function list_calls($limit = 0, $offset = 10)
    {
        $sql = sprintf("SELECT * FROM `call_header` WHERE `deleted`=FALSE LIMIT %u OFFSET %u", intval($limit), intval($offset));
        $res = $this->db->query($sql);
        if (!$res)
            die('Error: '. $this->db->error);

        return $res->fetch_all(MYSQLI_ASSOC);
    }
}

$call_log = new CallLog();
