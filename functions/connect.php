<?php
include "db.php";

mysqli_query($connection, "SET FOREIGN_KEY_CHECKS=0");

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function generateRandomNumber($length = 10)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function dumpdie($value)
{ // to be deleted
    echo "<pre>", print_r($value, true), "</pre>";
    die();
}

/**
 * @param $value
 */
function printAll($value)
{ // to be deleted
    echo "<pre>", print_r($value, true), "</pre>";
}

/**
 * @param $sql
 * @param array $data
 * @return false|mysqli_stmt
 */
function executeQuery($sql, $data = [])
{
    global $connection;
    if ($stmt = $connection->prepare($sql)) {
        if (!empty($data)) {
            $values = array_values($data);
            $types = str_repeat('s', count($values));
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
    } else {
        $stmt = var_dump($connection->error);
    }
    return $stmt;
}
function executeQuery2($sql, $data = [])
{
    global $connection;
    if ($stmt = $connection->prepare($sql)) {
        if (!empty($data)) {
            $values = array_values($data);
            $types = str_repeat('s', count($values) + 1);
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
    } else {
        $stmt = var_dump($connection->error);
    }
    return $stmt;
}
function customQuery($sql)
{
    $stmt = executeQuery($sql, "");
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function customQuery2($sql)
{
    $sql = $sql . " LIMIT 1";
    $stmt = executeQuery($sql, "");
    // return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    return $stmt->get_result()->fetch_assoc();
}
/**
 * @param $table
 * @param array $conditions
 * @return mixed
 */
function selectAll($table, $conditions = [])
{
    global $connection;
    $sql = "SELECT * FROM $table";
    if (empty($conditions)) {
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $i = 0;
        foreach ($conditions as $key => $value) {
            if ($i === 0) {
                $sql = $sql . " WHERE $key=?";
            } else {
                $sql = $sql . " AND $key=?";
            }
            $i++;
        }

        $stmt = executeQuery($sql, $conditions);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}


/**
 * @param $table
 * @param array $conditions
 * @return mixed
 */
function selectAllWithOr($table, $conditions = [], $orField, $orValue)
{
    global $connection;
    $sql = "SELECT * FROM $table";
    if (empty($conditions)) {
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $i = 0;
        foreach ($conditions as $key => $value) {
            if ($i === 0) {
                $sql = $sql . " WHERE $key=?";
            } else {
                $sql = $sql . " AND $key=?";
            }
            $i++;
        }

        $sql = $sql . " OR " . $orField . "=" . $orValue;

        $stmt = executeQuery($sql, $conditions);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}


/**
 * @param $table
 * @param $conditions
 * @return array|null
 */
function selectOne($table, $conditions)
{
    global $connection;
    $sql = "SELECT * FROM $table";

    $i = 0;
    foreach ($conditions as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $i++;
    }

    $sql = $sql . " LIMIT 1";

    $stmt = executeQuery($sql, $conditions);
    return $stmt->get_result()->fetch_assoc();
}

function selectOneOr($table, $conditions)
{
    global $connection;
    $sql = "SELECT * FROM $table";

    $i = 0;
    foreach ($conditions as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " OR $key=?";
        }
        $i++;
    }

    $sql = $sql . " LIMIT 1";

    $stmt = executeQuery($sql, $conditions);
    return $stmt->get_result()->fetch_assoc();
}
/**
 * 
 * 
 


 * @param $table
 * @param $conditions
 * @return array|null
 */
function selectOneOrderByDescLimit($table, $column, $order_data)
{
    global $connection;
    $sql = "SELECT $column FROM $table";

    $sql = $sql . " ORDER BY $order_data DESC LIMIT 1";

    $stmt = executeQuery($sql);
    return $stmt->get_result()->fetch_assoc();
}





/**
 * @param $table
 * @param $conditions
 * @return array|null
 */
function selectAllWithOrder($table, $conditions, $orderCondition, $orderType)
{
    $orderType = strtoupper($orderType);
    $table = strtolower($table);
    global $connection;
    $sql = "SELECT * FROM $table";

    $i = 0;
    foreach ($conditions as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $i++;
    }

    $sql = $sql . " ORDER BY $orderCondition $orderType";
    $stmt = executeQuery($sql, $conditions);
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


/**
 * @param $table
 * @param $pickConditions
 * @param array $conditions
 * @return array|mixed|null
 */
function selectSpecificData($table, $pickConditions, $conditions = [])
{
    global $connection;
    $sql = "SELECT";
    $increamental = 0;
    foreach ($pickConditions as $value) {
        if ($increamental === 0) {
            $sql = $sql . " $value";
        } else {
            $sql = $sql . ", $value";
        }
        $increamental++;
    }
    $sql = $sql . " FROM $table";

    if (empty($conditions)) {
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $i = 0;
        foreach ($conditions as $key => $value) {
            if ($i === 0) {
                $sql = $sql . " WHERE $key=?";
            } else {
                $sql = $sql . " AND $key=?";
            }
            $i++;
        }
    }

    $sql = $sql . " LIMIT 1";

    $stmt = executeQuery($sql, $conditions);
    return $stmt->get_result()->fetch_assoc();
}




/**
 * @param $table
 * @param $pickConditions
 * @param array $conditions
 * @return array|mixed|null
 */
function selectAllSpecificData($table, $pickConditions, $conditions = [])
{
    global $connection;
    $sql = "SELECT";
    $increamental = 0;
    foreach ($pickConditions as $value) {
        if ($increamental === 0) {
            $sql = $sql . " $value";
        } else {
            $sql = $sql . ", $value";
        }
        $increamental++;
    }
    $sql = $sql . " FROM $table";

    if (empty($conditions)) {
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $i = 0;
        foreach ($conditions as $key => $value) {
            if ($i === 0) {
                $sql = $sql . " WHERE $key=?";
            } else {
                $sql = $sql . " AND $key=?";
            }
            $i++;
        }
    }

    $stmt = executeQuery($sql, $conditions);
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


/**
 * @param $table
 * @param $data
 * @return int
 */
function create($table, $data)
{
    global $connection;
    $sql = "INSERT INTO $table SET ";

    $i = 0;
    foreach ($data as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " $key=?";
        } else {
            $sql = $sql . ", $key=?";
        }
        $i++;
    }
    $stmt = executeQuery($sql, $data);
    $id = $stmt->insert_id;
    return $id;
}

/**
 * @param $table
 * @param $id
 * @param $conName
 * @param $data
 * @return int
 */
function update($table, $id, $conName, $data)
{
    $sql = "UPDATE $table SET ";
    $i = 0;
    foreach ($data as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " $key=?";
        } else {
            $sql = $sql . ", $key=?";
        }
        $i++;
    }

    $sql = $sql . " WHERE " . $conName . "=?";
    $data[$conName] = $id;
    $stmt = executeQuery($sql, $data);
    return $stmt->affected_rows;
}

/**
 * @param $table
 * @param $id
 * @return int
 */
function delete($table, $id, $consName)
{
    $sql = "DELETE FROM $table WHERE " . $consName . "=?";
    $stmt = executeQuery($sql, [$consName => $id]);
    return $stmt->affected_rows;
}

/**
 * @param $table
 * @return mixed
 */
function countRecords($table)
{
    global $connection;
    $sql = "SELECT COUNT(*) AS count FROM $table";
    $stmt = executeQuery($sql);
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'];
}

function sumRecords($sum, $table)
{
    global $connection;
    $sql = "SELECT SUM($sum) FROM $table";
    $stmt = executeQuery($sql);
    $result = $stmt->get_result()->fetch_assoc();
    return $result["SUM($sum)"];
}

function sumRecordsWhere($sum, $table, $parameter, $condition)
{
    global $connection;
    $sql = "SELECT SUM($sum) FROM $table WHERE $parameter = '$condition'";
    $stmt = executeQuery($sql);
    $result = $stmt->get_result()->fetch_assoc();
    return $result["SUM($sum)"];
}

function countRecordsWhere($sum, $table, $parameter, $condition)
{
    global $connection;
    $sql = "SELECT COUNT($sum) FROM $table WHERE $parameter = '$condition'";
    $stmt = executeQuery($sql);
    $result = $stmt->get_result()->fetch_assoc();
    return $result["COUNT($sum)"];
}

function customCount($sql, $sum)
{
    global $connection;
    $stmt = executeQuery($sql);
    $result = $stmt->get_result()->fetch_assoc();
    return $result["COUNT($sum)"];
}

// function to insert a new row into an arbitrary table, with the columns filled with the values 
// from an associative array and completely SQL-injection safe

function insert($table, $record)
{
    global $connection;
    $cols = array();
    $vals = array();
    foreach (array_keys($record) as $col) $cols[] = sprintf("`%s`", $col);
    foreach (array_values($record) as $val) $vals[] = sprintf("'%s'", mysqli_real_escape_string($connection, $val));

    mysqli_query($connection, sprintf("INSERT INTO `%s`(%s) VALUES(%s)", $table, implode(", ", $cols), implode(", ", $vals)));

    return mysqli_insert_id($connection);
}

// date functions to find individual components of date 
// and add or subtract from date
function getYear($date)
{
    $date = DateTime::createFromFormat("Y-m-d*H:i:s", $date);
    return $date->format("Y");
}

function getMonth($date)
{
    $date = DateTime::createFromFormat("Y-m-d*H:i:s", $date);
    return $date->format("m");
}

function getDay($date)
{
    $date = DateTime::createFromFormat("Y-m-d*H:i:s", $date);
    return $date->format("d");
}

function addYear($date, $period)
{
    $valueDate = date("Y-m-d", strtotime($date . "+$period year"));
    return $valueDate;
}

function addMonth($date, $period)
{
    $valueDate = date("Y-m-d", strtotime($date . "+$period month"));
    return $valueDate;
}

function addWeek($date, $period)
{
    $valueDate = date("Y-m-d", strtotime($date . "+$period week"));
    return $valueDate;
}

function addDay($date, $period)
{
    $valueDate = date("Y-m-d", strtotime($date . "+$period day"));
    return $valueDate;
}

function appendAccountNo($accountNo, $length)
{
    $appendedAccount = '******' . substr($accountNo, $length);
    return $appendedAccount;
}

function greaterDate($date)
{
    $date_now = date("Y-m-d"); // this format is string comparable

    if ($date_now > $date) {
        return true;
    } else {
        return false;
    }
}

function lesserDate($date)
{
    $date_now = date("Y-m-d"); // this format is string comparable

    if ($date_now <= $date) {
        return true;
    } else {
        return false;
    }
}

function isOver30MinutesAgo($datetimeStr)
{
    $datetime = new DateTime($datetimeStr);
    $currentDatetime = new DateTime();
    $interval = $currentDatetime->diff($datetime);
    return ($interval->i >= 30) || ($interval->h > 0) || ($interval->d > 0) || ($interval->m > 0) || ($interval->y > 0);
}


function convertAmount($amount)
{
    $amount1 = floatval(preg_replace('/[^\d.]/', '', $amount));
    return $amount = number_format($amount1, 2);
}

function removeCommaAmount($amount)
{
    $amount = floatval(preg_replace('/[^\d.]/', '', $amount));
    return $amount;
}


function selectAllGreater($table, $conditions = [])
{
    global $connection;
    $sql = "SELECT * FROM $table";
    if (empty($conditions)) {
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $i = 0;
        foreach ($conditions as $key => $value) {
            if ($i === 0) {
                $sql = $sql . " WHERE $key>=?";
            } else {
                $sql = $sql . " AND $key>=?";
            }
            $i++;
        }

        $stmt = executeQuery($sql, $conditions);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

function selectAllLess($table, $conditions = [])
{
    global $connection;
    $sql = "SELECT * FROM $table";
    if (empty($conditions)) {
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $i = 0;
        foreach ($conditions as $key => $value) {
            if ($i === 0) {
                $sql = $sql . " WHERE $key>=?";
            } else {
                $sql = $sql . " AND $key<=?";
            }
            $i++;
        }

        $stmt = executeQuery($sql, $conditions);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}


function selectAllLessEq($table, $conditions, $dateConditions)
{
    global $connection;
    $sql = "SELECT * FROM $table";

    $i = 0;
    foreach ($conditions as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $i++;
    }

    $s = 0;
    foreach ($dateConditions as $keys => $value) {
        if ($s === 0) {
            $sql = $sql . " AND $keys<=?";
        } else {
            $sql = $sql . " AND $keys<=?";
        }
        $s++;
    }
    $stmt = executeQuery($sql, array_merge($conditions, $dateConditions));
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function checkAccount($table, $conditions, $scaleConditions)
{
    global $connection;
    $sql = "SELECT * FROM $table";
    # CHECK CUSTOMERS ACCOUNT BALANCE
    # IF VALUE IS GREATER THAN ZERO
    $i = 0;
    foreach ($conditions as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $i++;
    }

    $s = 0;
    foreach ($scaleConditions as $key => $value) {
        if ($s === 0) {
            $sql = $sql . " AND $key>=?";
        }
        $s++;
    }

    $stmt = executeQuery($sql, array_merge($conditions, $scaleConditions));
    return $stmt->get_result()->fetch_assoc();
}

// find out values that do not exist on tables
function findNotIn($table, $conditions, $notIn, $table2, $sort, $conditions2)
{
    global $connection;
    $sql = "SELECT * FROM $table";
    $i = 0;
    foreach ($conditions as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $i++;
    }

    $sql = $sql . " AND $notIn NOT IN (";
    $sql = $sql . "SELECT $sort FROM $table2";
    $s = 0;
    foreach ($conditions2 as $key => $value) {
        if ($s === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $s++;
    }
    $sql = $sql . ")";
    $stmt = executeQuery2($sql, array_merge($conditions, $conditions2));
    return $stmt->get_result()->fetch_assoc();
}


// find out values that exist on tables
function findIn($table, $conditions, $notIn, $table2, $sort, $conditions2)
{
    global $connection;
    $sql = "SELECT * FROM $table";
    $i = 0;
    foreach ($conditions as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $i++;
    }

    $sql = $sql . " AND $notIn IN(";
    $sql = $sql . "SELECT $sort FROM $table2";
    $s = 0;
    foreach ($conditions2 as $key => $value) {
        if ($s === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $s++;
    }
    $sql = $sql . ")";
    $stmt = executeQuery2($sql, array_merge($conditions, $conditions2));
    return $stmt->get_result()->fetch_assoc();
}


function sumNotIn($sum, $table, $conditions, $notIn, $table2, $sort, $conditions2)
{
    global $connection;
    $sql = "SELECT SUM($sum) FROM $table";
    $i = 0;
    foreach ($conditions as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $i++;
    }

    $sql = $sql . " AND $notIn NOT IN(";
    $sql = $sql . "SELECT $sort FROM $table2";
    $s = 0;
    foreach ($conditions2 as $key => $value) {
        if ($s === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $s++;
    }
    $sql = $sql . ")";
    $stmt = executeQuery2($sql, array_merge($conditions, $conditions2));
    return $stmt->get_result()->fetch_assoc();
}

function sumIn($sum, $table, $conditions, $notIn, $table2, $sort, $conditions2)
{
    global $connection;
    $sql = "SELECT SUM($sum) FROM $table";
    $i = 0;
    foreach ($conditions as $key => $value) {
        if ($i === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $i++;
    }

    $sql = $sql . " AND $notIn IN(";
    $sql = $sql . "SELECT $sort FROM $table2";
    $s = 0;
    foreach ($conditions2 as $key => $value) {
        if ($s === 0) {
            $sql = $sql . " WHERE $key=?";
        } else {
            $sql = $sql . " AND $key=?";
        }
        $s++;
    }
    $sql = $sql . ")";
    $stmt = executeQuery2($sql, array_merge($conditions, $conditions2));
    return $stmt->get_result()->fetch_assoc();
}

function selectAllandNot($table, $conditions = [], $notConditions)
{
    global $connection;
    $sql = "SELECT * FROM $table";
    if (empty($conditions)) {
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $i = 0;
        foreach ($conditions as $key => $value) {
            if ($i === 0) {
                $sql = $sql . " WHERE $key=?";
            } else {
                $sql = $sql . " AND $key=?";
            }
            $i++;
        }

        $sql = $sql . " AND (";
        $s = 0;
        foreach ($notConditions as $key => $value) {
            if ($s === 0) {
                $sql = $sql . " $key!=?";
            } else {
                $sql = $sql . " AND $key!=?";
            }
            $s++;
        }
        $sql = $sql . " )";
        $stmt = executeQuery($sql, array_merge($conditions, $notConditions));
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}


function selectOneWithOr($table, $conditions = [], $orField, $orValue)
{
    global $connection;
    $sql = "SELECT * FROM $table";
    if (empty($conditions)) {
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $i = 0;
        foreach ($conditions as $key => $value) {
            if ($i === 0) {
                $sql = $sql . " WHERE $key=?";
            } else {
                $sql = $sql . " AND $key=?";
            }
            $i++;
        }

        $sql = $sql . " OR " . $orField . "=" . $orValue;

        $stmt = executeQuery($sql, $conditions);
        return $stmt->get_result()->fetch_assoc();;
    }
}

function sendmail($email, $subject, $message)
{
    // Send email to provided address
    $to = $email;
    $headers = "From: no-reply@gifta.social\r\n";
    $headers .= "Content-type: text/html\r\n";
    $mailed = mail($to, $subject, $message, $headers);

    return $mailed;
}

function acronym($string)
{
    $words = explode(' ', $string);
    if (!$words) {
        return false;
    }
    $result = '';
    foreach ($words as $word) $result .= $word[0];
    return strtoupper($result);
}

function auditReport($table, $report)
{
    $createReport = insert($table, $report);
    return $createReport;
}


function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_valid_phone($phone) {
    // Remove any non-digit characters
    $cleaned_phone = preg_replace("/[^0-9]/", "", $phone);
    
    // Validate Nigerian phone number format
    $pattern = "/^0[7-9][0-9]{9}$/";
    
    return preg_match($pattern, $cleaned_phone);
}

function is_valid_password($password) {
    // Check if the password meets the complexity criteria and has a minimum length of 8 characters
    $pattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&!*_])[A-Za-z\d@#$%^&!*_]{8,}$/";

    return preg_match($pattern, $password);
}