<?php

class Model {

    private $table = '';
    private $alias = '';
    private $database;
    private $conn;
    private $sql;
    private $select;
    private $where;
    private $limit;
    private $resultset;
    private $orderby = array();
    private $groupby = array();
    private $join_param;
    private $config;
    public $load;

    function __construct($table, $connection = 0) {
        global $config;
        $this->config = $config;
        $this->load = new Core();

        $this->table = $table;
        $this->alias = $table;

        $db = new Database();
        $this->conn = $db->get_connection($connection);
        //$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
    }

    function get_single($execute = true) {
        $this->prepare_select_sql();
        if (!$execute) {
            $this->resultset['Status'] = 'OK';
            $this->resultset['Data'] = $this->sql;
            return $this->resultset;
        }
        $statement = $this->conn->prepare($this->sql);
        if (!$statement) {
            $e = $this->conn->errorInfo();
            $this->resultset['Status'] = 'ERROR';
            $this->resultset['Data'] = implode('|', $e);
        } else {
            $statement->execute();
            $resultset = $statement->fetchAll(2);
            if (count($resultset) > 0) {
                $this->resultset['Status'] = 'OK';
                $this->resultset['Data'] = $resultset[0];
            } else {
                $this->resultset['Status'] = 'OK';
                $this->resultset['Data'] = array();
            }
        }
        $this->clear_param();
        return $this->resultset;
    }

    function get_all($execute = true) {
        $this->prepare_select_sql();
            

        if (!$execute) {
            $this->resultset['Status'] = 'OK';
            $this->resultset['Data'] = $this->sql;
            return $this->resultset;
        }
        $statement = $this->conn->prepare($this->sql);
        if (!$statement) {
            $e = $this->conn->errorInfo();
            $this->resultset['Status'] = 'ERROR';
            $this->resultset['Data'] = implode('|', $e);
        } else {

            $statement->execute();

            $resultset = $statement->fetchAll(2);
            if (count($resultset) > 0) {
                $this->resultset['Status'] = 'OK';
                $this->resultset['Data'] = $resultset;
            } else {
                $this->resultset['Status'] = 'OK';
                $this->resultset['Data'] = array();
            }
        }
        $this->clear_param();
        return $this->resultset;
    }

    function insert($params, $exclude = '', $execute = true) {
        $arr = $this->map_array($params, $exclude);
        $this->prepare_insert_sql($arr);
        if (!$execute) {
            $this->resultset['Status'] = 'OK';
            $this->resultset['Data'] = $this->sql;
            return $this->resultset;
        }
        $statement = $this->conn->prepare($this->sql);
        if (!$statement) {
            $e = $this->conn->errorInfo();
            $this->resultset['Status'] = 'ERROR';
            $this->resultset['Data'] = implode('|', $e);
        } else {
            $statement->execute();
            //$this->resultset['Status']='OK';
            //$this->resultset['Data']=$this->conn->lastInsertId();
            $error_code = $statement->errorCode();
            if ($error_code[0] == '00000') {
                $this->resultset['Status'] = 'OK';
                $this->resultset['Data'] = $this->conn->lastInsertId();
            } else {
                $this->resultset['Status'] = 'ERROR';
                $this->resultset['Data'] = implode('|', $this->conn->errorInfo());
            }
        }
        $this->clear_param();
        return $this->resultset;
    }

    function update($params, $exclude = '', $execute = true) {
        $arr = $this->map_array($params, $exclude);
        $this->prepare_update_sql($arr);
        if (!$execute) {
            $this->resultset['Status'] = 'OK';
            $this->resultset['Data'] = $this->sql;
            return $this->resultset;
        }
        $statement = $this->conn->prepare($this->sql);
        if (!$statement) {
            $e = $statement->errorInfo();
            $this->resultset['Status'] = 'ERROR';
            $this->resultset['Data'] = implode('|', $e);
        } else {
            $statement->execute();
            $error_code = $statement->errorCode();
            if ($error_code[0] == '00000') {
                $this->resultset['Status'] = 'OK';
                $this->resultset['Data'] = 'Updated Successfully';
            } else {
                $this->resultset['Status'] = 'ERROR';
                $this->resultset['Data'] = implode('|', $this->conn->errorInfo());
            }
            //$this->resultset['Status']='OK';
            //$this->resultset['Data']='Updated Successfully';
        }
        $this->clear_param();
        return $this->resultset;
    }

    function delete_row($execute = true) {

        $this->prepare_delete_sql();
        if (!$execute) {
            $this->resultset['Status'] = 'OK';
            $this->resultset['Data'] = $this->sql;
            return $this->resultset;
        }
        $statement = $this->conn->prepare($this->sql);
        if (!$statement) {

            $e = $this->conn->errorInfo();
            $this->resultset['Status'] = 'ERROR';
            $this->resultset['Data'] = implode('|', $e);
        } else {
            $statement->execute();
            $error_code = $statement->errorCode();
            if ($error_code[0] == '00000') {
                $this->resultset['Status'] = 'OK';
                $this->resultset['Data'] = 'Deleted Successfully';
            } else {
                $this->resultset['Status'] = 'ERROR';
                $this->resultset['Data'] = implode('|', $this->conn->errorInfo());
            }
        }
        $this->clear_param();
        return $this->resultset;
    }

    function map_array($params, $exclude = '') {
        $returnarr = $params;
        if ($exclude != '') {
            $exclude_array = explode(',', $exclude);

            if (is_array($exclude_array)) {
                foreach ($exclude_array as $ea) {
                    unset($returnarr[$ea]);
                }
            } else {
                unset($returnarr[$exclude]);
            }
        }
        return $returnarr;
    }

    function raw_sql($params) {
        $this->sql = $params;
        return $this;
    }

    function execute() {
        $statement = $this->conn->prepare($this->sql);
        if (!$statement) {
            $e = $this->conn->errorInfo();
            $this->resultset['Status'] = 'ERROR';
            $this->resultset['Data'] = implode('|', $e);
        } else {
            $statement->execute();
            $resultset = $statement->fetchAll(2);
            if (count($resultset) > 0) {
                $this->resultset['Status'] = 'OK';
                $this->resultset['Data'] = $resultset;
            } else {
                $this->resultset['Status'] = 'OK';
                $this->resultset['Data'] = array();
            }
        }

        return $this->resultset;
    }

    function select($params) {
        $this->select = $params;
        return $this;
    }

    function wheremode($mode, $cond) {
        if ($mode == 'or') {
            $this->where['or'][] = $cond;
        } else {
            $this->where['and'][] = $cond;
        }
    }

    function where($field, $value, $operator = '=') {
        $cond = " $field $operator '$value' ";
        $this->wheremode('and', $cond);
        return $this;
    }

    function like_where($field, $value, $not = "") {
        $cond = " $field $not LIKE '$value' ";
        $this->wheremode('and', $cond);
        return $this;
    }

    function where_between($field, $start, $end) {
        $cond = " ($field >= $start AND $field <= $end) ";
        $this->wheremode('and', $cond);
        return $this;
    }

    function in_where($field, $value, $not = "") {
        $like = "('" . implode("','", $value) . "')";
        $cond = " $field $not IN $like ";
        $this->wheremode('and', $cond);
        return $this;
    }

    function or_where($field, $value, $operator = '=') {
        $cond = " $field $operator '$value' ";
        $this->wheremode('or', $cond);
        return $this;
    }

    function or_like_where($field, $value) {
        $cond = " $field LIKE '$value' ";
        $this->wheremode('or', $cond);
        return $this;
    }

    function or_where_between($field, $start, $end) {
        $cond = " ($field >= $start AND $field <= $end) ";
        $this->wheremode('or', $cond);
        return $this;
    }

    function or_in_where($field, $value) {
        $like = "('" . implode("','", $value) . "')";
        $cond = " $field IN $like ";
        $this->wheremode('or', $cond);
        return $this;
    }

    function limit($row, $offset = 0) {
        $this->limit = " $offset, $row ";
        return $this;
    }

    function unset_limit() {
        $this->limit = "";
        return $this;
    }

    function order_by($field, $mode = 'desc') {
        $this->orderby[] = " $field $mode ";
        return $this;
    }

    function group_by($field) {
        $this->groupby[] = " $field ";
        return $this;
    }

    function set_table_alias($param) {
        $this->alias = $param;
        return $this;
    }

    function join_table($table, $childid, $parentid, $second_table = '', $mode = 'inner') {
        $cond = $childid;
        $cond.=' = ';
        if ($second_table == '') {
            $cond.=$this->alias;
        } else {
            $cond.=$second_table;
        }
        $cond.='.';
        $cond.=$parentid;
        $this->join_param[$mode][] = array(
            'table' => $table,
            'condition' => $cond,
        );
        return $this;
    }

    private function prepare_select_sql() {
        $this->sql = 'SELECT ';
        $this->get_select();
        $this->sql .= ' ' . $this->table . ' ' . $this->alias . " ";
        $this->get_join();
        $this->get_where();
        $this->get_group_by();
        $this->get_order_by();
        $this->get_limit();
    }

    private function prepare_insert_sql($arr) {
        $this->sql = 'INSERT INTO  ' . $this->table;
        $fields = array_keys($arr);
        $this->sql.=' (' . implode(',', $fields) . ') ';
        $this->sql.=' VALUES ';
        $this->sql.=" ('" . implode("', '", $arr) . "') ";
    }

    private function prepare_update_sql($arr) {

        $this->sql = 'UPDATE ' . $this->table . " SET ";
        $fields = array_keys($arr);
        $fields_array = array();
        foreach ($fields as $f) {
            $fields_array[] = $f . " = '" . $arr[$f] . "'";
        }
        $this->sql.=implode(', ', $fields_array);
        $this->get_where();
        //echo $this->sql;exit;
    }

    private function prepare_delete_sql() {
        $this->sql = 'DELETE FROM ' . $this->table . " ";
        $this->get_where();
    }

    private function get_select() {
        if ($this->select != '') {
            $this->sql .= $this->select . ' FROM ';
        } else {
            $this->sql .= ' * FROM ';
        }
    }

    private function get_join() {
        if (!empty($this->join_param)) {
            foreach ($this->join_param as $k => $jp) {
                /* print_r($jp);
                  print_r($k); */
                foreach ($jp as $j) {
                    $this->sql .= " " . strtoupper($k) . ' JOIN ';
                    $this->sql .= $j['table'];
                    $this->sql .= ' ON ' . $j['condition'];
                }
            }
        }
    }

    function get_where() {
        if (!empty($this->where)) {
            $this->sql .= ' WHERE ';
            $wherecond = array();

            foreach ($this->where as $k => $wp) {

                $wherecond[] = implode(" " . strtoupper($k) . " ", $wp);
            }

            $this->sql .='(';
            $this->sql .=implode(") And (", $wherecond);
            $this->sql .=')';
        }
    }

    function get_order_by() {
        if (!empty($this->orderby)) {
            $this->sql .= ' ORDER BY ';

            $ordercond = implode(" , ", $this->orderby);


            $this->sql .=$ordercond;
        }
    }

    function get_group_by() {
        if (!empty($this->groupby)) {
            $this->sql .= ' GROUP BY ';

            $groupcond = implode(" , ", $this->groupby);


            $this->sql .=$groupcond;
        }
    }

    function get_limit() {
        if (!empty($this->limit)) {
            $this->sql .= ' LIMIT ';

            $this->sql .= $this->limit;
        }
    }

    function clear_param() {
        $this->select = '';
        $this->where = array();
        $this->limit = '';
        $this->orderby = array();
        $this->groupby = array();
        $this->join_param = array();
        $this->sql = '';
    }

}
