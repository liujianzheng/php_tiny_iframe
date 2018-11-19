<?php

require_once('simple_mysql_session.class.php');

class Dao
{
    //连接失败时，抛出异常
    function __construct($db_host, $db_user, $db_password, $db_name, $db_port)
    {
        date_default_timezone_set('PRC');
        $this->session_ = new SimpleMysqlSession($db_host, $db_user, $db_password, $db_name, $db_port);
    }

    //执行查询sql
    public function ExecuteSelectSql($sql)
    {
        try {
            return $this->session_->ExecuteSelectSql($sql);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            sys_log("Error:{$msg}");
            throw new Exception($msg);
        }
    }

    //执行更新sql
    public function ExecuteUpdateSql($sql)
    {
        try {
            return $this->session_->ExecuteUpdateSql($sql);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            sys_log("Error:{$msg}");
            throw new Exception($msg);
        }
    }

    //按对象执行插入sql
    public function AddObjectSql($table, $data)
    {
        try {
            return $this->session_->AddObject($table, $data);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            sys_log("Error:{$msg}");
            throw new Exception($msg);
        }
    }

    //按对象执行更新sql
    public function UpdateObjectSql($table, $primary_key, $data)
    {
        try {
            return $this->session_->UpdateObject($table, $primary_key, $data);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            sys_log("Error:{$msg}");
            throw new Exception($msg);
        }
    }

    public function EscapeString($str)
    {
        return $this->session_->EscapeString($str);
    }

    //关闭连接
    public function Close()
    {
        if (!empty($this->session_)) {
            $this->session_->Close();
        }
    }
}
