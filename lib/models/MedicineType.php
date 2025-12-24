<?php

class MedicineType {

  private $tbl = 'medicine_type';

  public function get($id = '') {
    if ($id != '') {
      $result = mysqli_query($GLOBALS['DB'], QueryBuilder::select($this->tbl, [], "WHERE `is_deleted` = 0 AND `id` = '$id'"));
      return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
      $result = mysqli_query($GLOBALS['DB'], QueryBuilder::select($this->tbl, [], "WHERE `is_deleted` = 0"));
      return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
  }

  public function getType($medicineID) {
    $query = QueryBuilder::rawQuery("SELECT `$this->tbl`.`name` AS 'name' FROM `medicines` INNER JOIN `$this->tbl` ON `medicines`.`medicine_type_id` = `$this->tbl`.`id` WHERE `medicines`.`id` = $medicineID");
    return mysqli_fetch_assoc(mysqli_query($GLOBALS['DB'], $query));
  }

  public function add($data) {
    $query = QueryBuilder::insert($this->tbl, $data);
    return mysqli_query($GLOBALS['DB'], $query);
  }

  public function delete($id) {
    $query = QueryBuilder::update($this->tbl, [ 'is_deleted' => '1' ], "`id` = $id");
    return mysqli_query($GLOBALS['DB'], $query);
  }

  public function addMultiple($data) {
    $this->startTransaction();
    $logs = [];

    foreach($data as $dt){
      $query = QueryBuilder::insert($this->tbl, $dt);
      
      $logs[] = mysqli_query($GLOBALS['DB'], $query);
    }
    
    $this->endTransaction($logs);
  }

  public function startTransaction(){
    $transaction_result = mysqli_query($GLOBALS['DB'], 'START TRANSACTION');
    if (!$transaction_result) {
        echo "Failed to start transaction<br>";
        // echo 'ERROR: ' . mysqli_error($GLOBALS['DB']);
        die();
    }
  }

  public function endTransaction(array $logs, $redirect=''){
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
            echo "Something wrong happend while commiting ";
            // echo 'ERROR: ' . mysqli_error($GLOBALS['DB']);
            die();
        }

        $redir = $_SERVER['REQUEST_URI'];
        $redir = str_replace("?msg=success", "", $redir);
        $redir = str_replace("?msg=error", "", $redir);
        
        if( isset($redirect) && !empty($redirect) ){
          header("Location: $redirect&msg=success"); die;
        }else{
          header("Location: {$redir}?msg=success"); die;
        }
    } else {
        $rollback_result = mysqli_query($GLOBALS['DB'], 'ROLLBACK');
        
        if (!$rollback_result) {
          echo "Something wrong happend while rollback";
          // echo 'ERROR: ' . mysqli_error($GLOBALS['DB']);
          die();
        }

        $redir = $_SERVER['REQUEST_URI'];
        $redir = str_replace("?msg=success", "", $redir);
        $redir = str_replace("?msg=error", "", $redir);

        if( isset($redirect) && !empty($redirect) ){
          header("Location: $redirect&msg=error"); die;
        }else{
          header("Location: $redir?msg=error"); die;
        }
    }
  }
  
  public function isDuplicate($name) {
    $query = QueryBuilder::select($this->tbl, [ 'name' ], "WHERE `name` = '$name' and `is_deleted` = 0 ");
    $result = mysqli_query($GLOBALS['DB'], $query);
    $rows = mysqli_fetch_row($result);
    return $rows != null ? true : false;
  }

  public function canBeDeleted($typeID) {
    $query = QueryBuilder::rawQuery("SELECT `id` FROM `medicines` WHERE `medicines`.`medicine_type_id` = '$typeID'");
    $result = mysqli_query($GLOBALS['DB'], $query);
    return mysqli_num_rows($result) == 0 ? true : false;
  }
}