<?php

/**
 * 針對特定欄位做批次更新 update when case
 *
 * @param array $table
 * @param array $field
 * @param array $data 內容
 * @return string
 */
if (!function_exists('update_when_case_string')) {
    function update_when_case_string($table, $field, $data) {
        $query = "UPDATE $table SET $field = (CASE id ";

        foreach ($data as $num => $row) {
            if (isset($row[$field])) {
                $id = $row['id'];
                $val = is_numeric($row[$field]) ? ceil($row[$field]) : 0;
                $query .= "WHEN $id THEN $val ";
            }
        }

        $query .= "ELSE id END)";

        return $query;
    }
}
