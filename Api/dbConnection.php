<?php

class dbConnection 
{
    private $host = "localhost";
    private $dbName = "iqarenadb";
    private $username = "root";
    private $password = "";
    private $conn;

    public function __construct() 
    {
        $this->connect();
    }

    private function connect() 
    {
        try 
        {
            $dsn = "mysql:host=$this->host;dbname=$this->dbName;charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) 
        {
            die("Bağlantı Hatası: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) 
    {
        try 
        {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            exit("Sorgu Hatası: " . $e->getMessage());
        }
    }

    public function fetchAll($sql, $params = []) 
    {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetch($sql, $params = []) 
    {
        return $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($table, $data) 
    {
        $fields = implode(",", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $this->query($sql, $data);
        return $this->conn->lastInsertId();
    }

    public function update($table, $data, $where) 
    {
        $set = "";
        foreach ($data as $key => $value) 
        {
            $set .= "$key = :$key, ";
        }
        $set = rtrim($set, ", ");

        $whereClause = "";
        foreach ($where as $key => $value) 
        {
            $whereClause .= "$key = :where_$key AND ";
        }
        $whereClause = rtrim($whereClause, " AND ");

        $mergedData = array_merge($data, array_combine(array_map(fn($k) => "where_$k", array_keys($where)), array_values($where)));

        $sql = "UPDATE $table SET $set WHERE $whereClause";
        return $this->query($sql, $mergedData);
    }

    public function delete($table, $where) 
    {
        $whereClause = "";
        foreach ($where as $key => $value) 
        {
            $whereClause .= "$key = :$key AND ";
        }
        $whereClause = rtrim($whereClause, " AND ");

        $sql = "DELETE FROM $table WHERE $whereClause";
        return $this->query($sql, $where);
    }
}

?>