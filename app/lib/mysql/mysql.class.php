<?php

class DataBase {
    var $Host;
    var $Dbname;
    var $User;
    var $Pass;
    var $Db;
    var $Dsn;

    public function __construct($host,$db,$user,$pass) {
        $this->Host = $host;
        $this->Dbname = $db;
        $this->User = $user;
        $this->Pass = $pass;
        $this->Dsn = sprintf('mysql:host=%s;dbname=%s',$this->Host,$this->Dbname);
    }

    function Connect() {
        $this->Db = new PDO($this->Dsn,$this->User,$this->Pass);
        return is_object($this->Db);
    }

    function isConnected() {
        return is_object($this->Db);
    }

    function Disconnect() {
        $this->Db = null;
    }

    function Query($sql) {
        $stmt = $this->Db->prepare($sql);
        return ($stmt->execute()) ? new ResultSet($stmt) : null;
    }

    public function lastInsertId() {
        return $this->Db->lastInsertId();
    }

    public function Error() {
        $errores = $this->Db->errorInfo();
        return $errores[0]." -  ".$errores[1]." - ".$errores[2];
    }

    function ErrorNum() {
        return $this->Db->errorCode();
    }

    function Escape($sql) {        
        return $this->Db->quote($sql);
    }
	
    public function LastID() {
        return $this->Db->lastInsertId();
    }

}

class ResultSet {
    var $Stmt;
    var $Fields;
    var $Eof;
    var $CurRec;

    function ResultSet ($stmt) {
        $this->Fields = null;
        $this->Eof = true;
        $this->Stmt = $stmt;
    }

    function AffectedRows() {
        return (is_object($this->Stmt)) ? $this->Stmt->rowCount() : 0;
    }

    function NumRows() {
        return $this->Stmt->rowCount();
    }

    function NumFields() {
        return $this->Stmt->columnCount();
    }

    function Fetch() {
        $this->Fields = $this->Stmt->fetch(PDO::FETCH_ASSOC);
        $this->Eof = !is_array($this->Fields);
        return !$this->Eof;
    }

    function Close() {
        $this->Stmt->closeCursor();
    }
}