<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once "common.php";

/*
* Api Url genaretor with parameter
 * @param $APIindex - index of database
 * @param $arrayInput - array for pipe separeted inex with value
 * 
 * @return generated full api url
 * @exmaple 
 * 
$data= array();
 
        $data["uid"]="mamuncse068@yahoo.com";
        $data["pass"]="1";
        $data["userid"]="Mamun";
        
echo getAPIURL(2, $data); 
 *  */

function getAPIURL($APIindex, $arrayInput = array())
{

    $cn = connectDB();
    if ($cn) {

        $query = "SELECT CONCAT(cbps_api_host.host_url,cbps_api_list.api_url) as 'api_url', param_name, param_value, pram_value_static
                    FROM cbps_api_list
                    INNER JOIN cbps_api_host ON cbps_api_host.host_id=cbps_api_list.host_id
                    INNER JOIN cbps_api_param ON cbps_api_list.api_id = cbps_api_param.api_id
                    WHERE cbps_api_list.api_id = '$APIindex'";

        $result = Sql_exec($cn, $query);

        $urlAPI = "";
        while ($row = Sql_fetch_array($result)) {
            if ($urlAPI == "") {
                $urlAPI = Sql_Result($row, "api_url") . '?';
            } else {
                $urlAPI = $urlAPI . '&';
            }


            if (Sql_Result($row, "pram_value_static") == 1) {
                $urlAPI = $urlAPI . Sql_Result($row, "param_name") . '=';
                $urlAPI = $urlAPI . Sql_Result($row, "param_value");
            } else {
                if (sizeof($arrayInput) > 0) {
                    $value = Sql_Result($row, "param_value");
                    $arrayValue = explode("|", $value);

                    $length = sizeof($arrayValue);

                    $valueResult = array();
                    for ($i = 0; $i < $length; $i++) {
                        $valueResult[] = $arrayInput[$arrayValue[$i]];
                    }
                    $value = implode("|", $valueResult);
                    $urlAPI = $urlAPI . Sql_Result($row, "param_name") . '=';
                    $urlAPI = $urlAPI . $value;
                }
            }
        }
        Sql_Free_Result($result);
        ClosedDBConnection($cn);

        return $urlAPI;
    }
}
