<?php

include_once dirname(__FILE__) . '/../common/SysLog.php';

class SimpleMysqlSession
{
    // 连接失败时，抛出异常
    function __construct($db_host, $db_user, $db_password, $db_name, $db_port)
    {
        $this->connection_ = mysqli_connect($db_host, $db_user, $db_password, $db_name, $db_port);
        if (!$this->connection_) {
            mysql_log('mysqli_connect failed, error:' . mysqli_connect_error());
            throw new Exception('mysqli_connect failed');
        }
        mysqli_real_query($this->connection_, "SET NAMES 'utf8'");
    }

    // mysql转义字符串
    public function EscapeString($input)
    {
        return mysqli_real_escape_string($this->connection_, $input);
    }

    // 执行select sql，返回结果集
    public function ExecuteSelectSql($sql)
    {
        //mysql_log($sql);
        if (!mysqli_real_query($this->connection_, $sql)) {
            $err_msg = mysqli_error($this->connection_);
            mysql_log('mysqli_real_query failed, sql:' . $sql . ', error:' . $err_msg);
            throw new Exception('mysqli_real_query failed, error:' . $err_msg);
        }
        $result = mysqli_store_result($this->connection_);
        if (mysqli_errno($this->connection_) != 0) {
            $err_msg = mysqli_error($this->connection_);
            mysql_log('mysqli_store_result failed, sql:' . $sql . ', error:' . $err_msg);
            throw new Exception('mysqli_store_result failed, error:' . $err_msg);
        }
        $result_set = array();
        while ($object = mysqli_fetch_assoc($result)) {
            array_push($result_set, $object);
        }
        return $result_set;
    }


    // 执行insert, update, delete，返回影响记录的数目
    public function ExecuteUpdateSql($sql)
    {
        //mysql_log($sql);
        if (!mysqli_real_query($this->connection_, $sql)) {
            $err_msg = mysqli_error($this->connection_);
            mysql_log('mysqli_real_query failed, sql:' . $sql . ', error:' . $err_msg);
            throw new Exception('mysqli_real_query failed, error:' . $err_msg);
        }
        return mysqli_affected_rows($this->connection_);
    }

    // 事务控制
    public function Begin()
    {
        $this->ExecuteUpdateSql('begin');
    }

    public function Commit()
    {
        $this->ExecuteUpdateSql('commit');
    }

    public function Rollback()
    {
        $this->ExecuteUpdateSql('rollback');
    }

    // 添加对象
    // 失败时抛出异常
    // $table: 数据库表名
    // $object: 查询到的对象
    // 返回值：如果表设置了自增列，返回新生成的id
    public function AddObject($table, $object)
    {
        $key_array = array();
        $value_array = array();
        foreach ($object as $key => $value) {
            array_push($key_array, $key);
            array_push($value_array, is_string($value) ? "'$value'" : $value);
        }
        $sql = 'INSERT INTO ' . $table . ' (' . join(',', $key_array) . ')';
        $sql .= ' VALUES(' . join(',', $value_array) . ')';
        //mysql_log($sql);
        $this->ExecuteUpdateSql($sql);
        $result_set = $this->ExecuteSelectSql('SELECT LAST_INSERT_ID() AS id');
        $id = $result_set[0]['id'];
        return $id;
    }

    // 更新对象，使用主键索引
    // 失败时抛出异常
    // $table: 数据库表名
    // $primary_key: 主键
    // $object: 需要更新的字段（不能包含主键）
    public function UpdateObject($table, $primary_key, $object)
    {
        $value_array = array();
        $condition_array = array();
        foreach ($object as $key => $value) {
            array_push($value_array, is_string($value) ? "$key='$value'" : "$key=$value");
        }
        foreach ($primary_key as $key => $value) {
            array_push($condition_array, is_string($value) ? "$key='$value'" : "$key=$value");
        }
        $sql = 'UPDATE ' . $table . ' SET ' . join(',', $value_array) . ' WHERE ';
        $sql .= join(' AND ', $condition_array);
        //mysql_log($sql);
        $update_count = $this->ExecuteUpdateSql($sql);
        return $update_count;
    }

    //关闭连接
    public function Close()
    {
        if (!empty($this->connection_)) {
            mysqli_close($this->connection_);
        }
    }
}

?>
