<?php

abstract class BaseModel{

    protected $tbl;

    public function get($columns, $conditions, $current_page = '', $records_per_page = '') {

        if( empty( $records_per_page ) )
        {
            $query = QueryBuilder::select($this->tbl, $columns, $conditions);
            return mysqli_query($GLOBALS['DB'], $query);
        }else
        {
        $limit =  (($current_page - 1) * $records_per_page) . ', ' . $records_per_page;
        $query = QueryBuilder::select($this->tbl, $columns, $conditions, $limit);
        return mysqli_query($GLOBALS['DB'], $query);
        }

    }

    public function add($data) {
        $query = QueryBuilder::insert($this->tbl, $data);
        return mysqli_query($GLOBALS['DB'], $query);
    }

    public function update($data, $condition) {
        $query = QueryBuilder::update($this->tbl, $data, $condition);
        return mysqli_query($GLOBALS['DB'], $query);
    }

    public function raw($rawQuery)  {
        $query = QueryBuilder::rawQuery($rawQuery);
        return mysqli_query($GLOBALS['DB'], $query);
    }

    public function lastInsertId(){
        return mysqli_insert_id($GLOBALS['DB']);
    }

    public function startTransaction(){
        $transaction_result = mysqli_query($GLOBALS['DB'], 'START TRANSACTION');
        if (!$transaction_result) {
            echo "Failed to start transaction<br>";
            // echo 'ERROR: ' . mysqli_error($GLOBALS['DB']);
            die();
        }
    }
  
    public function endTransaction(array $logs):bool
    {
        // Are all transactions commitable
        $commit = true;
        foreach ($logs as $log) {
            if (!$log) {
                $commit = false;
                break;
            }
        }

        if ($commit) {
            $commit_result = mysqli_query($GLOBALS['DB'], 'COMMIT');
            if (!$commit_result) {
                echo "Error while commiting the transaction";
                echo "<br>";
                echo "به سیستم مشکل جدی پیش آمده است، لطفا قبلا از بستن این صفحه اسکرین شات از تمام صفحه تهیه و به تیم توسعه دهنده ارایه نمائید. ";
                // echo 'ERROR: ' . mysqli_error($GLOBALS['DB']);
                die();
            }

            return true;
        } else {
            $rollback_result = mysqli_query($GLOBALS['DB'], 'ROLLBACK');

            if (!$rollback_result) {
                echo "Error while Rolling Back the Transaction";
                echo "<br>";
                echo "به سیستم مشکل جدی پیش آمده است، لطفا قبلا از بستن این صفحه اسکرین شات از تمام صفحه تهیه و به تیم توسعه دهنده ارایه نمائید. ";
                // echo 'ERROR: ' . mysqli_error($GLOBALS['DB']);
                die();
            }

            return false;
        }
    }
}