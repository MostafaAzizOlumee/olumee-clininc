<?php
class Medicine extends BaseModel{

  protected $tbl = 'medicine';

  public function search($data, $limit = '') {
    $query = "SELECT `medicine`.*,  `medicine_category`.`name` as 'category', `medicine_type`.`name` as 'type'
              FROM `$this->tbl`
              LEFT JOIN `medicine_category` ON `medicine`.`medicine_category_id` = `medicine_category`.`id`
              LEFT JOIN `medicine_type` ON `medicine`.`medicine_type_id` = `medicine_type`.`id`
              WHERE  `medicine`.is_deleted = '0'
              AND CONCAT_WS(' ', `company_name`, `dose`, `generic_name`) LIKE '%$data%'";
    if ($limit != '') {
      $query .= "LIMIT $limit";
    }
    return mysqli_query($GLOBALS['DB'], QueryBuilder::rawQuery($query));
  }

}