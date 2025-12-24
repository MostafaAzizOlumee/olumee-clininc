<?php

class QueryBuilder {

    public static function select($tbl, $cols = [], $condition = '', $limit = '') {
        $output = "SELECT ";
        if (is_array($cols) && count($cols) != 0) {
            if (count($cols) > 1) {
                $output .= "`" . implode('`, `', $cols) . "` ";
            } else {
                $output .= " `$cols[0]` ";
            }
        } else {
            $output .= "* ";
        }
        $output .= "FROM `$tbl`";

        if ($condition != '') {
            $output .= " $condition";
        }
        if( !empty( $limit ) ){
            $output .= " LIMIT " . $limit;
        }
        return $output;
    }
   
    public static function insert($tbl, $data) {
        $cols = "`" . implode("`, `", array_keys($data)) . "`";
        $vals = "'" . implode("', '", array_map("escape_data", array_values($data))) . "'";
        return "INSERT INTO `$tbl` ($cols) VALUES ($vals)";
    }

    public static function update($tbl, $data, $condition) {
        $output = "UPDATE `$tbl` SET ";
        $counter = 0;
        foreach ($data as $col => $val) {
            $output .= "`$col` = ";
            if( $data[$col] == "" ):
                $output .= "NULL";
            else:
                $output .= "'" . escape_data($val) . "'";
            endif;
            if ($counter < count($data) - 1) {
                $output .= ", ";
            }
            $counter++;
        }

        if ($condition != '') {
            $output .= " WHERE $condition";
        }
        return $output;
    }

    public static function delete($tbl, $condition) {
        return "DELETE FROM `$tbl` WHERE $condition";
    }

    public static function rawQuery($query) {
        return "$query";
    }

    public static function count($tbl, $col = "id", $condition = 1) {
        return "SELECT COUNT(`" . $col ."`) AS 'rows' FROM `$tbl` WHERE $condition";
    }
    public static function totalMedicineQuantityByBarcode($barcode){
        $query = "SELECT
        `inventory_transactions`.`generated_barcode`,
        `medicine_categories`.`name` AS 'category',
        `medicines`.`generic_name`,
        `medicines`.`dose`,
        `medicines`.`company_name`,
        `medicines`.`sale_price`,
        `medicines`.`id` AS 'medicines_id',
        `shelves`.`id` AS shelf_id,
        `shelves`.`row`,
        `shelves`.`column`,
        `shelves`.`medicine_type`,
        `purchase_orders`.`expiration_date`,
        `purchase_orders`.`purchased_price`,
        `purchase_orders`.`quantity_per_unit`,
        TRUNCATE
            (
                (
                    SUM(
                        CASE WHEN `inventory_transactions`.`io_type` = 'in' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                    END
                ) - SUM(
                    CASE WHEN `inventory_transactions`.`io_type` = 'out' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                END
            )
        ) / `purchase_orders`.`quantity_per_unit`,
        0
        ) AS 'total_remaining',
        TRUNCATE
            (
                (
                    SUM(
                        CASE WHEN `inventory_transactions`.`io_type` = 'in' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                    END
                ) - SUM(
                    CASE WHEN `inventory_transactions`.`io_type` = 'out' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                END
            )
        ) % `purchase_orders`.`quantity_per_unit`,
        0
        ) AS 'total_remaining_per_unit'
        FROM
            `inventory_transactions`
        LEFT JOIN `purchase_orders` ON `purchase_orders`.`generated_barcode` = `inventory_transactions`.`generated_barcode`
        LEFT JOIN `medicines` ON `medicines`.`id` = `inventory_transactions`.`medicines_id`
        LEFT JOIN `medicine_categories` ON `medicines`.`medicine_category_id` = `medicine_categories`.`id`
        LEFT JOIN `inventory_transactions_in_shelves` ON `inventory_transactions_in_shelves`.`inventory_transactions_id` = `inventory_transactions`.`id`
        LEFT JOIN `shelves` ON `shelves`.`id` = `inventory_transactions_in_shelves`.`shelf_id`
        WHERE
            `inventory_transactions`.`generated_barcode` = $barcode
            AND 
            `inventory_transactions`.is_deleted = 0 
        GROUP BY
            `inventory_transactions`.`generated_barcode`,
            `shelves`.`id`
        ORDER BY
            `shelves`.`id`";
        return $query;
    }
    public static function totalMedicineQuantityByName($phrase){
        $query = "SELECT
        `inventory_transactions`.`generated_barcode`,
        `medicine_categories`.`name` AS 'category',
        `medicines`.`generic_name`,
        `medicines`.`dose`,
        `medicines`.`sale_price`,
        `medicines`.`company_name`,
        `medicines`.`id` AS 'medicines_id',
        `shelves`.`id` AS shelf_id,
        `shelves`.`row`,
        `shelves`.`column`,
        `shelves`.`medicine_type`,
        `purchase_orders`.`expiration_date`,
        `purchase_orders`.`purchased_price`,
        `purchase_orders`.`quantity_per_unit`,
        TRUNCATE
            (
                (
                    SUM(
                        CASE WHEN `inventory_transactions`.`io_type` = 'in' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                    END
                ) - SUM(
                    CASE WHEN `inventory_transactions`.`io_type` = 'out' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                END
            )
        ) / `purchase_orders`.`quantity_per_unit`,
        0
        ) AS 'total_remaining',
        TRUNCATE
            (
                (
                    SUM(
                        CASE WHEN `inventory_transactions`.`io_type` = 'in' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                    END
                ) - SUM(
                    CASE WHEN `inventory_transactions`.`io_type` = 'out' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                END
            )
        ) % `purchase_orders`.`quantity_per_unit`,
        0
        ) AS 'total_remaining_per_unit'
        FROM
            `inventory_transactions`
        LEFT JOIN `purchase_orders` ON `purchase_orders`.`generated_barcode` = `inventory_transactions`.`generated_barcode`
        LEFT JOIN `medicines` ON `medicines`.`id` = `inventory_transactions`.`medicines_id`
        LEFT JOIN `medicine_categories` ON `medicines`.`medicine_category_id` = `medicine_categories`.`id`
        LEFT JOIN `inventory_transactions_in_shelves` ON `inventory_transactions_in_shelves`.`inventory_transactions_id` = `inventory_transactions`.`id`
        LEFT JOIN `shelves` ON `shelves`.`id` = `inventory_transactions_in_shelves`.`shelf_id`
        WHERE
			    CONCAT_WS('', `medicines`.`generic_name`, `medicines`.`company_name`, `medicines`.`dose`) LIKE '%".$phrase."%'
        
        AND 
            `inventory_transactions`.is_deleted = 0 
        
        GROUP BY
            `inventory_transactions`.`generated_barcode`,
            `shelves`.`id`
        ORDER BY
            `shelves`.`id`";
        return $query;
    }
    public static function getAccountType( $code ){
        $query = "SELECT
            `account_type`
        FROM
            `accounts`
        INNER JOIN `accounts_sub_categories` 
            ON `accounts_sub_categories`.`id` = `accounts`.`account_sub_categories_id`
        WHERE
            `accounts`.code = '".$code."'";
        return $query;

    }
    public static function totalMedicineQuantityByID($medicine_id){
        $query = "SELECT
                SUM(
                    `total_per_barcode`.total_remaining
                ) AS `total_quantity`,
                SUM(
                    `total_per_barcode`.total_remaining_per_unit
                ) AS `total_quantity_per_unit`
            FROM
                (
                SELECT
                TRUNCATE
                    (
                        (
                            SUM(
                                CASE WHEN `inventory_transactions`.`io_type` = 'in' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                            END
                        ) - SUM(
                            CASE WHEN `inventory_transactions`.`io_type` = 'out' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                        END
                    )
                ) / `purchase_orders`.`quantity_per_unit`,
                0
                ) AS 'total_remaining',
                TRUNCATE
                    (
                        (
                            SUM(
                                CASE WHEN `inventory_transactions`.`io_type` = 'in' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                            END
                        ) - SUM(
                            CASE WHEN `inventory_transactions`.`io_type` = 'out' THEN `inventory_transactions`.`quantity_in_piece` ELSE 0
                        END
                    )
                ) % `purchase_orders`.`quantity_per_unit`,
                0
                ) AS 'total_remaining_per_unit'
            FROM
        `inventory_transactions`
    LEFT JOIN `purchase_orders` ON `purchase_orders`.`generated_barcode` = `inventory_transactions`.`generated_barcode`
    WHERE
        `inventory_transactions`.`medicines_id` = $medicine_id
        AND 
        `inventory_transactions`.is_deleted = 0 
    GROUP BY
        `inventory_transactions`.`generated_barcode`
    ) AS total_per_barcode";
        return $query;
    }
    public static function isDuplicate($tbl, $condition_parameters, $exception = [] ) {
        if( is_array($condition_parameters) && !empty($condition_parameters) && is_array($exception) ){
            $counter = 0;
            $prepare_condition = '';
            foreach($condition_parameters as $col=>$val):
                $prepare_condition .= "`$col` = '" . escape_data($val) . "'";
                if ($counter < count($condition_parameters) - 1):
                    $prepare_condition .= " && ";
                endif;
                $counter++;
            endforeach;
            if( !empty($exception) ):
                foreach( $exception as $col=>$val):
                    $prepare_condition .= " && `$col` <> $val";
                endforeach;
            endif;
            return  self::select($tbl, [ array_keys($condition_parameters)[0] ], "WHERE $prepare_condition");
        }else{
          echo "isDuplicate() function in QueryBuilder.php expects associative array, but something else has been passed for conditions or exceptions!!";
          die();
        }
    }

}
