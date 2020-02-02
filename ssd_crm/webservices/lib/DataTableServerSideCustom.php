<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 5/11/2015
 * Time: 4:00 PM
 */

require('common_server_dataTable.php');

class DataTableServer
{


    /* ========================================================
     * Edit done by Nazibul
     * ========================================================*/
    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     * @param  array $request Data sent to server by DataTables
     * @param  string $table SQL table to query
     * @param  array $columns Column information array
     * @return array          Server-side processing response array
     */

    function simplePagination($request, $table, $columns)
    {
        $cs = myDatabaseConnector::getinstance();

        // Build the SQL query string from the request
        $limit = $this->limit($request, $columns);
        $order = $this->order($request, $columns);
        $where = $this->filter($request, $columns);
//        if(isset($_REQUEST['info']['qryCondition']))
//        {
//            $condition="1";
//        }
//        else 
//        {
//            $condition = $_REQUEST['info']['qryCondition'];
//        }
        $condition = (isset($_REQUEST['info']['qryCondition'])) ? ($_REQUEST['info']['qryCondition']) : '1';
        //echo "ok";
        //print_r($_REQUEST);
        $select_string = (isset($_REQUEST['info']['selectString'])) ? ($_REQUEST['info']['selectString']) : '*';

        if(isset($_REQUEST['info']['orderString'])){
            $order = ' ORDER BY '.$_REQUEST['info']['orderString'].str_replace('ORDER BY',',',$order);
        }

        $ref = explode(' ', $where);


        if (strpos(strtolower('qq' . $ref[0]), 'where')) {
            $where = $where . ' AND (' . $condition . ')';
            //   echo $where.'pp'; exit;
        } else {
            $where = ' WHERE (' . $condition . ')';
            //   echo $where.'tt'; exit;
        }


        // Main query to actually get the data
        //   $query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $this->pluck($columns, 'db')) .
        //       " FROM " . $table . " " . $where . " " . $order . " " . $limit;

            $query = "SELECT $select_string FROM " . $table . " " . $where . " " . $order . " " . $limit;

            
        //echo $query; exit;

        $data = $cs->Sql_exec($query);


        // Data set length after filtering
        //$cs->Sql_Num_Rows($data);

        // Total data set length
        $resTotalLength = $cs->Sql_exec("SELECT COUNT(*) FROM " . $table . " WHERE " . $condition );
        $recordsTotal1 = $cs->Sql_fetch_array($resTotalLength);
        $recordsTotal = $recordsTotal1[0];

        $query = "SELECT COUNT(*) FROM ". $table ." " . $where;
        $recordsFiltered = $cs->Sql_exec($query);

        $recordsFiltered = $cs->Sql_fetch_array($recordsFiltered);
        $recordsFiltered = $recordsFiltered[0];

        /*
         * Output
         */
        return array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $this->data_output($columns, $data, $cs)
        );

    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     * @return string SQL limit clause
     */
    function limit($request, $columns)
    {
        $limit = ' ;';

        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']) . $limit;
        }

        return $limit;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     * @return string SQL order by clause
     */
    function order($request, $columns)
    {
        $order = '';

        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();
            $dtColumns = $this->pluck($columns, 'dt');

            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';

                    $orderBy[] = '' . $column['db'] . ' ' . $dir;
                }
            }
            $order = 'ORDER BY ' . implode(', ', $orderBy);
        }

        return $order;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     * @param  array $a Array to get data from
     * @param  string $prop Property to read
     * @return array        Array of property values
     */
    function pluck($a, $prop)
    {
        $out = array();

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }

        return $out;
    }


    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     * @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     * @return string SQL where clause
     */
    function filter($request, $columns)
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = self::pluck($columns, 'dt');

        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];

            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['searchable'] == 'true') {
                    $binding = "'%" . mysql_real_escape_string($str) . "%'";
                    $globalSearch[] = "" . $column['db'] . " LIKE " . $binding;
                }
            }
        }

        // Individual column filtering
        for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
            $requestColumn = $request['columns'][$i];
            $columnIdx = array_search($requestColumn['data'], $dtColumns);
            $column = $columns[$columnIdx];

            $str = $requestColumn['search']['value'];

            if ($requestColumn['searchable'] == 'true' && $str != '') {
                $binding = "'%" . mysql_real_escape_string($str) . "%'";
                $columnSearch[] = "" . $column['db'] . " LIKE " . $binding;
            }
        }

        // Combine the filters into a single string
        $where = '';

        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }

        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where . ' AND ' . implode(' AND ', $columnSearch);
        }

        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }
        return $where;
    }

    /**
     * Create the data output array for the DataTables rows
     *
     * @param  array $columns Column information array
     * @param  array $data Data from the SQL get
     * @return array          Formatted data in a row based format
     */
    function data_output($columns, $data, $cs)
    {
        $out = array();
        $serial = 1;

        while ($result = $cs->Sql_fetch_array($data)) {
            $row = array();

            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];

                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $column['formatter']($cs->Sql_Result($result, $column['db']), $result);
                } else {
                    $row[$column['dt']] = $cs->Sql_Result($result, $column['db']);
                }
            }
            $out[] = $row;
        }

        return $out;
    }
}