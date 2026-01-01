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

  public function getList(array $params = []) {
    $columns = " `medicine`.*,  `medicine_category`.`name` as 'category', `medicine_type`.`name` as 'type' ";
    $conditions = " `medicine`.is_deleted = '0' ";

    if (!empty($params['search'])) {
      $search = mysqli_real_escape_string($GLOBALS['DB'], $params['search']);
      $conditions .= " AND CONCAT_WS(' ', `company_name`, `dose`, `generic_name`) LIKE '%$search%' OR 
                           CONCAT_WS(' ', `generic_name`, `dose`, `company_name`) LIKE '%$search%'";
    }

    $current_page = $params['page'] ?? '';
    $records_per_page = $params['per_page'] ?? '';

    $limit = '';
    if (!empty($records_per_page) && !empty($current_page)) {
      $limit =  (($current_page - 1) * $records_per_page) . ', ' . $records_per_page;
    }

    $query = "SELECT $columns
              FROM `$this->tbl`
              LEFT JOIN `medicine_category` ON `medicine`.`medicine_category_id` = `medicine_category`.`id`
              LEFT JOIN `medicine_type` ON `medicine`.`medicine_type_id` = `medicine_type`.`id`
              WHERE $conditions
              " . (!empty($limit) ? "LIMIT $limit" : "");

    $result = mysqli_query($GLOBALS['DB'], QueryBuilder::rawQuery($query));
    
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
  }
}