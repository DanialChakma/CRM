<?php

// Release note: Wraps up common query functions.
//				 This release checks both for mssql and odbc for forming sql server queries.
// Version: 1.0.1
// Date: August 25, 2011
// Author: Minaoar Hossain Tanzil


require_once $COMMONPHP;
error_reporting(0);

function getpos($mid,$channel,$cn,$startpos,$urlret)
{
	$res=explode("+",$urlret);
	
	if ($startpos == 0)
		return 0;

	// Setting the current position to random number between start_pos and file count 
	// if start_pos is more than zero in BrowseList shape.	
	if ($startpos > 0)
		return rand($startpos, $res[0]);
	
    // If $startpos is less than zero, we should use BR_START_POS.	
	$curpos = GetSaveData($mid, $channel, 'BR_START_POS', $cn);
	
	if(!isset($curpos))	
	{
		// We should have set BR_START_POS. Since this was not set,
		// we are using default 0 value.
		$curpos=0;
	}
	else
	{
		$curpos = abs($curpos)+1;
		if($curpos >= $res[0]) 
			$curpos=0;
	}
	return $curpos;
}

function findService($subscriber,$bno,$cn)
{
		$qry= "select top 1 service from FlowManager where ano=substring('$subscriber', 1, len(ano)) and bno= substring('$bno',1,len(bno))and status=1 order by len(ano) desc";
		$rs = Sql_exec($cn,$qry);
		
		while(Sql_fetch_array($rs))
	  	{
				$Service=Sql_Result($rs,"service");	
				return $Service;
		} 
}


function InsertSaveData($mid,$channel,$Pname,$Pvalue,$cn)
{

         
		    $qry="insert into storedata values('$mid','$channel','$Pname','$Pvalue')"; 
			$rs = Sql_exec($cn,$qry);
	
}

function UpdateSaveData($mid,$channel,$Pname,$Pvalue,$cn)
{
			$qry="update storedata set propertyvalue='$Pvalue' where propertyname='$Pname' and machineid='$mid' and channel='$channel'";
			$rs = Sql_exec($cn,$qry);
}

function DeleteSaveData($mid,$channel,$cn)
{
			$qry="delete from storedata where machineid='$mid' and channel='$channel'";
			
			$rs = Sql_exec($cn,$qry);
			

}

function GetSaveData($mid,$channel,$Pname,$cn)
{
			$qry="select propertyvalue from storedata where machineid='$mid' and channel='$channel' and propertyname='$Pname'";
			$rs = Sql_exec($cn,$qry);
			while($arr=Sql_fetch_array($rs))
	  		{
			    $rs = Sql_exec($cn,$qry);
				$value=Sql_Result($arr,"propertyvalue");	
				return $value;
			} 
}

function GetRetryCount($mid,$channel,$Pname,$cn)
{			
			global $dbtype;
			
			if($dbtype == 'mysql')
			$qry="SELECT COALESCE(SUM(propertyvalue), 0) propertyvalue FROM storedata WHERE machineid='$mid' and channel='$channel' and propertyname='$Pname'";
			else
			$qry="select isnull(sum(cast(propertyvalue as int)),0) propertyvalue from storedata where machineid='$mid' and channel='$channel' and propertyname='$Pname'";
			
			$rs = Sql_exec($cn,$qry);
			while(Sql_fetch_array($rs))
	  		{
				$value=Sql_Result($rs,"propertyvalue");	
				return $value;
			} 
}

function GetValue($mid,$channel,$command,$promptloc,$server,$ano,$bno,$callid,$outdialid,$fileid,$pos,$service,
				$usage,$stream,$cn) 
{
	global $FLOWMANAGER_URL_PREFIX;	
	$position 		= substr(trim($command), 0, 7);
	$SecurePos 		= substr(trim($command), 0, 8);
	
	if (strtolower($position) <> "http://" && strtolower($SecurePos) <> "https://")
	{
		return $promptloc.$command;
	}
	else
	{
		$globalparam='channel=%channel&mid=%mid&ano=%ano&bno=%bno&callid=%callid&outdialid=%outdialid&fileid=%fileid&pos=%pos&service=%service&usage=%usage&stream=%stream';
		$pos=strstr($command,"?");
		
		if ($pos==null)
			$url=$command.'?'.$globalparam;
		else
			$url=$command.'&'.$globalparam;
		
		$url = str_replace("%channel",urlencode($channel),$url);
		$url = str_replace("%mid",urlencode($mid),$url);
		$url = str_replace("%ano",urlencode($ano),$url);
		$url = str_replace("%bno",urlencode($bno),$url);
		$url = str_replace("%callid",urlencode($callid),$url);
		$url=  str_replace("%outdialid",urlencode($outdialid),$url);
		$url=  str_replace("%fileid",urlencode($fileid),$url);
		$url=  str_replace("%pos",urlencode($pos),$url);
		$url=  str_replace("%service",urlencode($service),$url);
		$url=  str_replace("%usage",urlencode($usage),$url);
		$url=  str_replace("%stream",urlencode($stream),$url);
		
	    $url=$server.$url; 
		$url = str_replace("FlowManager",urlencode($FLOWMANAGER_URL_PREFIX),$url);//echo $url;exit(0);		
		$qry= "select propertyname,propertyvalue from storedata where channel='$channel' and machineid='$mid'";
		$rs = Sql_exec($cn,$qry);
		
		while($arr=Sql_fetch_array($rs))
	  	{
			$pname=Sql_Result($arr,"propertyname");	
			$pvalue=Sql_Result($arr,"propertyvalue");	
			$url= str_replace("%$pname",urlencode($pvalue),$url);
		} 
		$url=str_replace(" ","%20",$url);
		//echo $url;exit(0);
		$filex=file($url);
		//print_r($filex);exit(0);
		return $filex[0];		
	}	

}
function SetValue($mid,$channel,$url,$server,$cn)
{
		
		$url=$server.$url; 	//echo "$url<br>";
		//$url=str_replace('"',"\"",$url);
		//echo "$url<br>";exit(0);
		//$url=urlencode($url);
	    $url=str_replace(" ","%20",$url);
		//$url=str_replace("%3C","<",$url);
		//echo "$url<br>";
		//echo $url;exit(0);
		$file=file($url);//echo $file[0];exit;
		return $file[0];

}
function DeleteStoreValue($mid,$channel,$pname,$cn)
{
			$qry="delete from storedata where machineid='$mid' and channel='$channel' and propertyname='$pname'";
			$rs = Sql_exec($cn,$qry);
}

function StoreValue($mid,$channel,$command,$server,$cn,$ano,$bno,$cs,$key,$stream,$rc,$fileid,$service,$usage,$pos,$errname)
{
       
		$res=explode('&',$command);

		foreach($res as $command)
		{
			
			list($data0,$data1)=explode('=',$command);
			$pname=$data0;
			$pvalue=$data1;

			DeleteStoreValue($mid,$channel,$pname,$cn);			

			
			$pvalue = str_replace("%ano",urlencode($ano),$pvalue);
		    $pvalue = str_replace("%bno",urlencode($bno),$pvalue);
		    $pvalue = str_replace("%cs",urlencode($cs),$pvalue);
			$pvalue = str_replace("%key",urlencode($key),$pvalue);
			$pvalue = str_replace("%stream",urlencode($stream),$pvalue);
			$pvalue = str_replace("%channel",urlencode($channel),$pvalue);
			$pvalue = str_replace("%mid",urlencode($mid),$pvalue);
			$pvalue = str_replace("%rc",urlencode($rc),$pvalue);
			$pvalue = str_replace("%fileid",urlencode($fileid),$pvalue);
			$pvalue = str_replace("%service",urlencode($service),$pvalue);
			$pvalue = str_replace("%usage",urlencode($usage),$pvalue);
			$pvalue = str_replace("%er",urlencode($errname),$pvalue);
			$pvalue = str_replace("%pos",urlencode($pos),$pvalue);
			
			$qry= "select propertyname,propertyvalue from storedata where channel='$channel' and machineid='$mid'";
			$rs = Sql_exec($cn,$qry);
			while($arr=Sql_fetch_array($rs))
			{
				$pnam=Sql_Result($arr,"propertyname");	
				$pv=Sql_Result($arr,"propertyvalue");	
				$pvalue= str_replace("%$pnam",$pv,$pvalue);
			} 
			//echo $pname="path";
			//echo $pvalue="E:\ivrsong\event\9.wav";//exit;
			 InsertSaveData($mid,$channel,$pname,$pvalue,$cn);
		}
}


function TranslateURL($URL,$ano,$bno,$cs,$key,$stream,$channel,$mid,$rc,$fileid,$service,$usage,$errname,$callid,$pos,$outdialid,$cn)
{
	global $FLOWMANAGER_URL_PREFIX;	
	$globalparam='&ano=%ano&bno=%bno&cs=%cs&key=%key&stream=%stream&channel=%channel&mid=%mid&retrycount=%rc&fileid=%fileid&service=%service&usage=%usage&errname=%er&callid=%callid&pos=%pos&outdialid=%outdialid';
	$p=strstr($URL,"?");
	if ($p==null)
		$URL=$URL.'?'.$globalparam;
	else
		$URL=$URL.$globalparam;
	$URL = str_replace("%ano",urlencode($ano),$URL);
	$URL = str_replace("%bno",urlencode($bno),$URL);
	$URL = str_replace("%cs",urlencode($cs),$URL);
	$URL = str_replace("%key",urlencode($key),$URL);
	$URL = str_replace("%stream",urlencode($stream),$URL);
	$URL = str_replace("%channel",urlencode($channel),$URL);
	$URL = str_replace("%mid",urlencode($mid),$URL);
	$URL = str_replace("%rc",urlencode($rc),$URL);
	$URL = str_replace("%fileid",urlencode($fileid),$URL);
	$URL = str_replace("%service",urlencode($service),$URL);
	$URL = str_replace("%usage",urlencode($usage),$URL);
	$URL = str_replace("%er",urlencode($errname),$URL);
	$URL = str_replace("%callid",urlencode($callid),$URL);
	$URL = str_replace("%pos",urlencode($pos),$URL);
	$URL = str_replace("%outdialid",urlencode($outdialid),$URL);
	
	$URL = str_replace("FlowManager",urlencode($FLOWMANAGER_URL_PREFIX),$URL);
	//echo $URL; exit;
	$qry= "select propertyname,propertyvalue from storedata where channel='$channel' and machineid='$mid'";
	$rs = Sql_exec($cn,$qry);
		
		while($arr=Sql_fetch_array($rs))
	  	{
			
				$pname=Sql_Result($arr,"propertyname");	
				$pvalue=Sql_Result($arr,"propertyvalue");	
				$URL = str_replace("%$pname",urlencode($pvalue),$URL);
		} 
	//echo "$URL<br>";
	return $URL;
}

function ResetRetry($mid,$channel,$pname,$cn)
{
			$qry="delete from storedata where machineid='$mid' and channel='$channel' and propertyname='$pname'";
			$rs = Sql_exec($cn,$qry);

}

function WriteSms1($Bno,$Ano,$msg,$opt,$cn)
{
			$qry="insert into smsoutbox(srcMN,destMN,msg,smsTime,sentTime,msgStatus,retryCount,srcTON,srcNPI,ContentID,Operator) 	values('$Bno','$Ano','$msg',getdate(),getdate(),'QUE',0,1,1,'','$opt')";
			$rs = Sql_exec($cn,$qry);
}
function StoreState($callid,$ano,$cs,$key,$bno,$nodename,$action,$cn)
{
	if($action==1)
	{
		$qry="insert into nodelog(callid,mn,cs,keyy,st,bno,feature) values('$callid','$ano','$cs','$key',getdate(),'$bno','$nodename')";
	}
	else if($action==2)
	{
		$qry="update nodelog set et=getdate() where callid='$callid' and mn='$ano' and et='NULL'";
	}
	$rs = Sql_exec($cn,$qry);
}

/* @function : This function is used for tracking log for every call.
 * @params : callid, service, key_press, current_state, next_state, machineid, channel, ano, bno
 * author : Nazmul
 * Date : 04/29/2012
 */

function CreateNodeLog($callid, $service, $current_state, $key_press, $next_state, $machineid, $channel, $nodename, $ano, $bno, $curtime, $cn)
{
    global $ingoreList;
    $query = "insert into nodelogs(callid, Service, current_state, key_press, next_state, MachineID, Channel, NodeName, Ano, Bno, startTime, updated) values ('$callid', '$service', '$current_state', '$key_press', '$next_state', '$machineid', '$channel', '$nodename', '$ano', '$bno', $curtime, 0)";
	if(!array_key_exists($nodename, $ignoreList))
	{
		$rs = Sql_exec($cn, $query);
	}
}

/* @function : This function is used for Log if disconnect call.
 * @params : callid, ano
 * author : Nazmul
 * Date : 04/29/2012
 */

function UpdateNodeLog($callid, $curtime, $cn)
{
    global $ingoreList;
	
	$query = "update nodelogs set endTime=$curtime, updated=1 where callid='$callid' and updated=0";
	if(!array_key_exists($nodename, $ignoreList))
	{
		$rs = Sql_exec($cn, $query);
	}
}

function insert($table,$fieldlist,$valuelist,$cn)
{
    //echo $valuelist;//exit;
	$valuelist=str_replace("\\\"","'",$valuelist);
	$valuelist=str_replace('"',"'",$valuelist);
    $fields=str_replace("|",",",$fieldlist);
	$vlist=explode('|',$valuelist);
	$len=sizeof($vlist);
	$v="";
	for($i=0;$i<$len;$i++)
	{	
		$v1=trim($vlist[$i]);
		/* if($v1<>'getdate()')
		{
			$v1=substr($v1,2,strlen($v1)-4);
			$v1="'$v1'";
		} */
		if($i<$len-1)
			$v=$v."$v1,";
		else
			$v=$v."$v1";
		
	}
	$values=$v;
	
    if($fields=="")
		$qry="insert into $table values ($values)";
	else
		$qry="insert into $table ($fields) values ($values)";
	//echo $qry;exit;
    $rs=Sql_exec($cn,$qry);
	if($rs)
		return 1;
	else 
		return -1;
}
function update($table,$fieldlist,$valuelist,$where,$cn)
{
	$where=str_replace("\\","",$where);
	$where=str_replace('"',"'",$where); 
    $fields=explode('|',$fieldlist);
	$values=explode('|',$valuelist);
	$len=sizeof($fields);
	$set="";
	for($i=0;$i<$len;$i++)
	{	
		$f=trim($fields[$i]);
		$v=trim($values[$i]);
		/* if($v<>'getdate()')
		{
			$v=substr($v,2,strlen($v)-4);
			$v="'$v'";
		} */
		if($i<$len-1)
			$set=$set."$f='$v',";
		else
			$set=$set."$f='$v'";	
	}
	//$where=where($wherefieldlist,$wherevaluelist);
	if($where=="")
		$qry="update $table set $set";
	else
		$qry="update $table set $set where $where";
	//echo $qry;exit;
    $rs=Sql_exec($cn,$qry);
	if($rs)
		return 1;
	else 
		return -1;
}
function delete($table,$where,$cn)
{

	//$where=where($wherefieldlist,$wherevaluelist);
	$where=str_replace("\\","",$where);
	$where=str_replace('"',"'",$where); 
    if($where=="")
		$qry="delete from $table";
	else
		$qry="delete from $table where $where";
	//echo $qry;
    $rs=Sql_exec($cn,$qry);
	if($rs)
		return 1;
	else 
		return -1;
}
function selectvalue($table,$fieldname,$where,$cn)
{
	//$where=where($wherefieldlist,$wherevaluelist);
	$table=str_replace("\\\"","'",$table);
	$table=str_replace('"',"'",$table);
	$where=str_replace("\\\"","'",$where);
	$where=str_replace('"',"'",$where); 
	if($table=="")
	{
		$fieldname=str_replace("\\","",$fieldname);
		$fieldname=str_replace('"',"'",$fieldname); 
	    $qry="select $fieldname as fld";
	}
    else if($where=="")
		$qry="select $fieldname as fld from $table";
	else
		$qry="select $fieldname as fld from $table where $where";
	//echo "<br>$qry";exit;
    $rs=Sql_exec($cn,$qry);
	if($rs)
	{
		$arr=Sql_fetch_array($rs);
	    $value=Sql_Result($arr,"fld");
		if($fieldname=="count(*)" and  $value>=1) $value=1;
		return $value;
	}
	else
	    return -1;
}
function selectlist($table,$fields,$where,$orderby,$topfrom,$topto,$cn)
{
    $where=str_replace('"',"'",$where); 
    list($field1,$field2)=explode(',',$fields);
	 $field2=$fields;
	//$fields="$field1 as field1,$field2 as field2";
	global $dbtype;
	if($dbtype == 'odbc'|| $dbtype == 'mssql')
	{
		$fields="$fields,Row_NUMBER() OVER (ORDER BY $orderby) as row";
		 $qry=SQLquery($table,$fields,$where,"",$topfrom,$topto);
	}
	else if($dbtype == 'mysql')
	{
	    $fields="$field1 as field1,$field2 as field2";
		$qry=MySQLquery($table,$fields,$where,$orderby,$topfrom,$topto);
	}
    $rs=Sql_exec($cn,$qry);
	if($rs)
	{
		$count=Sql_Num_Rows($rs);
		if(!$count)
		{
			$ret=$count.'+++';
		}
		else
		{   
			if($dbtype == 'odbc' || $dbtype == 'mssql')
			{
				//$ret=SQLlist($count,$cn,$qry);
				$ret=SQLlist3($field2,$topfrom,$count,$cn,$qry);
			}
			else if($dbtype == 'mysql')
			{
				$ret=MySQLlist($table,$fields,$where,$orderby,$topfrom,$count,$cn,$qry);
			}
		
		}
		$ret=substr($ret,0,strlen($ret)-1);
		return  $ret; 
	}
	else
	    return -1;
}
function MySQLquery($table,$fields,$where,$orderby,$topfrom,$topto)
{
    
	if($where=='')
	{
		if($orderby=='')
		{
			$qry="select $fields from $table limit $topfrom,$topto";
		}
		else
		{
			$qry="select $fields from $table  order by $orderby limit $topfrom,$topto";
		}	
	}
	else
	{
	    if($orderby=='')
		{
			$qry="select $fields from $table  where $where  limit $topfrom,$topto";
		}
		else
		{
			$qry="select $fields from $table  where $where order by $orderby limit $topfrom,$topto";
		}
		
	}

    return $qry;

}
function SQLquery($table,$fields,$where,$orderby,$topfrom,$topto)
{
    
	if($where=='')
	{
		if($orderby=='')
		{
			$qry="select top $topto $fields from $table";
		}
		else
		{
			$qry="select top $topto $fields from $table  order by $orderby";
		}	
	}
	else
	{
	    if($orderby=='')
		{
			$qry="select top $topto $fields from $table  where $where";
		}
		else
		{
			$qry="select top $topto $fields from $table  where $where order by $orderby";
		}
		
	}
    return $qry;

}
function MySQLlist($table,$fields,$where,$orderby,$topfrom,$count,$cn,$qry)
{
    global $contentdir;
	for($i=$topfrom;$i<$count+$topfrom;$i++)
	{
		$j=$i+1;
		$qry=MySQLquery($table,$fields,$where,$orderby,$i,$j);
		$rs=Sql_exec($cn,$qry);
		$data=Sql_fetch_array($rs);
		if($ret=='')
		{
			$ret=$count.'+'.$contentdir.Sql_Result($data,"field1")."+".$contentdir.Sql_Result($data,"field2").'+';
		}
		else
		{
			$ret=$ret.$contentdir.Sql_Result($data,"field1")."+".$contentdir.Sql_Result($data,"field2").'+';
		}
	}
	return $ret;
}
function SQLlist($count,$cn,$qry)
{
    
	global $contentdir;
    $rs=Sql_exec($cn,$qry);
	while($data=Sql_fetch_array($rs))
	{		
		if($ret=='')
		{
			$ret=$count.'+'.$contentdir.Sql_Result($data,"field1")."+".$contentdir.Sql_Result($data,"field2").'+';
		}
		else
		{
			$ret=$ret.$contentdir.Sql_Result($data,"field1")."+".$contentdir.Sql_Result($data,"field2").'+';
		}
	}
	return $ret;
}
function SQLlist2($fields,$topfrom,$count,$cn,$qr)
{
    $ret="";
	list($field1,$field2)=explode(',',$fields);
    $fields="$field1 as field1,$field2 as field2";
    global $contentdir;
	for($i=$topfrom;$i<$count+$topfrom;$i++)
	{
		$pos=$i+1;
		$qry="select $fields from ($qr) as temp where row=$pos";
		$rs=Sql_exec($cn,$qry);
		$data=Sql_fetch_array($rs);
		if($ret=='')
		{
			$ret=$count.'+'.$contentdir.Sql_Result($data,"field1")."+".$contentdir.Sql_Result($data,"field2").'+';
		}
		else
		{
			$ret=$ret.$contentdir.Sql_Result($data,"field1")."+".$contentdir.Sql_Result($data,"field2").'+';
		}
	}
	return $ret;
}
function SQLlist3($fields,$topfrom,$count,$cn,$qr)
{
    $ret="";
	$ret2="";
	list($field1,$field2)=explode(',',$fields);
    $fields="$field1 as field1,$field2 as field2";
    global $contentdir;
	for($i=0;$i<$count;$i++)
	{
		$pos=$i+1;
		$qry="select $fields from ($qr) as temp where row=$pos";// echo "$qry<br>";
		$rs=Sql_exec($cn,$qry);
		$data=Sql_fetch_array($rs);
		if($pos<$topfrom+1)
		{
			$ret2=$ret2.$contentdir.Sql_Result($data,"field1")."+".$contentdir.Sql_Result($data,"field2").'+';
		}
		else
		{		
			$ret=$ret.$contentdir.Sql_Result($data,"field1")."+".$contentdir.Sql_Result($data,"field2").'+';
		}
	}
	//echo "$ret<br>$ret2<br>";
	$result=$count.'+'.$ret.$ret2;
	return $result;
}

function where($wherefieldlist,$wherevaluelist)
{
	$wherefields=explode('|',$wherefieldlist);
	$wherevalues=explode('|',$wherevaluelist);
	$len=sizeof($wherefields);
	$where="";
	for($i=0;$i<$len;$i++)
	{	
		$wf=trim($wherefields[$i]);
		$wv=trim($wherevalues[$i]); 
		$pos=strstr($wv,'"');
	
		if($pos=='')
		{
			if($i<$len-1)
				$where=$where."$wf='$wv' and ";
		 	else
				$where=$where."$wf='$wv'";
		}
		else
		{
			$match='"';
			$replace="'";
			$wv=str_replace("\\","",$wv);
		    $wv=str_replace('"',"'",$wv); 
			if($i<$len-1)
				$where=$where."$wf=dbo.$wv and ";
			else
				$where=$where."$wf=dbo.$wv";	
		}
	}
return $where;
}
?>
