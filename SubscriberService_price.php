<?php

include "subsLib.php";

$appid = $_GET['appid'];
$apppass = $_GET['apppass'];
$cmdid = $_GET['cmdid'];
$cmdparam = $_GET['cmdparam'];
$cmdvalue = $_GET['cmdvalue'];

$cn=ConnectDB();

$auth= IsAppIdValid($appid,$apppass,$cn);
$cntparam=CountParam($cmdparam);


if($auth==false)
{
	echo(raise_error(0));
	ClosedDBConnection($cn);
	die();
}

if(CheckParam($cmdid,$cntparam)==false)
{
	echo(raise_error(2));
	ClosedDBConnection($cn);
	die();
}

switch($cmdid)
{
	case 'ADD_SUB_PAYEMNT_METHOD':

		list($uid,$method,$paymentpriority,$paymenttype,$creditCardNo,$status,$userid)=explode('|',$cmdparam);

		if(IsSubPaymentMethodExits($uid,$method,$cn))
		{
			$qry="update subscriberpaymethod set `paymentpriority`='$paymentpriority',`paymenttype`='$paymenttype',`creditCardNo`='$creditCardNo',`status`='$status',`userid`='$userid',`LastUpdate`=NOW() where subscriberno='$uid' and method='$method'";
		}else
		{
			$qry="insert into subscriberpaymethod(`subscriberno`,`method`,`paymentpriority`,`paymenttype`,`creditCardNo`,`status`,`userid`,`LastUpdate`)values('$uid','$method',$paymentpriority,'$paymenttype','$creditCardNo','$status','$userid',NOW())";
		}

		if (Sql_exec($cn,$qry))
			echo "+OK";
		else
			echo "Cannot add subscriber payment method";
		break;
	case 'SHOW_CHARGING_HISTROY':
		list($uid,$startDate,$endDate,$query)=explode('|',$cmdparam);
		$startDate=$startDate." 00:00:00";
		$endDate=$endDate." 23:59:59";
		$qry="SELECT cdrid,serviceID,ChargingType,CGWID,Ano,Bno,Direction,RateID,ResultCode,Amount,StartTime,EndTime,Purpose,SubscriptionStatus,RefundAmount,TotalConsumedUnit,Device FROM cdrintegrated WHERE ((Ano='$uid' AND Direction=0 ) OR (bno='$uid' AND Direction=1 )) AND StartTime BETWEEN '$startDate' AND '$endDate'";
		if(!empty($query))
			$qry=$qry." AND ".$query;

		$rs=Sql_exec($cn,$qry);
		$cnt=Sql_Num_Rows($rs);
		if ($cnt<1)
		{
			echo raise_error(11);
			break;
		}
		echo '+OK';
		echo "\n";
		echo $cnt;
		echo "\n";
		if($rs)
		{
			echo "cdrid|serviceID|ChargingType|CGWID|Ano|Bno|Direction|RateID|ResultCode|Amount|StartTime|EndTime|Purpose|SubscriptionStatus|RefundAmount|TotalConsumedUnit|Device";
			echo "\n";
			while($row = Sql_fetch_array($rs))
			{
				$cdrid=Sql_Result($row,"cdrid");
				$serviceID=Sql_Result($row,"serviceID");
				$ChargingType=Sql_Result($row,"ChargingType");
				$CGWID=Sql_Result($row,"CGWID");
				$ano=Sql_Result($row,"Ano");
				$bno=Sql_Result($row,"Bno");
				$direction=Sql_Result($row,"Direction");
				$rateID=Sql_Result($row,"RateID");
				$resultCode=Sql_Result($row,"ResultCode");
				$amount=Sql_Result($row,"Amount");
				$startTime=Sql_Result($row,"StartTime");
				$endTime=Sql_Result($row,"EndTime");
				$purpose=Sql_Result($row,"Purpose");
				$subscriptionStatus=Sql_Result($row,"SubscriptionStatus");
				$refundAmount=Sql_Result($row,"RefundAmount");
				$totalConsumedUnit=Sql_Result($row,"TotalConsumedUnit");;
				$device=Sql_Result($row,"Device");

				$temp=$cdrid."|".$serviceID."|".$ChargingType."|".$CGWID."|".$ano."|".$bno."|".$direction."|".$rateID."|".$resultCode."|".$amount."|".$startTime."|".$endTime."|".$purpose."|".$subscriptionStatus."|".$refundAmount."|".$totalConsumedUnit."|".$device;

				echo $temp;
				echo "\n";
			}
		}
		break;

	case 'CHG_SUB':
		$params = explode('|', $cmdparam);
		$uid = $params[0];
		$paramcount = $params[1];
		$val_count = CountParam($cmdvalue);
		if($paramcount == $val_count)
		{
			$values = explode('|', $cmdvalue);
			$res = IsSubExist($uid, $cn);
			if($res)
			{
				$qry = "update subscriber set ";
				$j = 0;
				for($i = 2; $i < ($paramcount + 2); $i++)
				{
					$qry = $qry.$params[$i]."='$values[$j]'";
					if($i < ($paramcount + 1))
						$qry = $qry.',';
					$j++;
				}
				$qry = $qry." where uid='$uid'";
				if (Sql_exec($cn,$qry))
					echo("+OK");
				else
					echo(raise_error(138));
			}
			else
			{
				echo(raise_error(127));
			}
		}
		else
			echo(raise_error(2));
		break;
	case 'CHG_SUB_STAT':

		list($uid,$status,$userid)=explode('|',$cmdparam);
		$subscriberexist = IsSubExist($uid,$cn);
		if(!$subscriberexist)
		{
			echo(raise_error(127));
			break;
		}

		$qry="update subscriber set status='$status'  where uid='$uid'";
		if (Sql_exec($cn,$qry))
			echo "+OK";
		else
			echo(raise_error(121));
		break;
	case 'CHK_SUB_REG':

		list($uid,$userid)=explode('|',$cmdparam);
		$qry="select * from subscriber where uid='$uid' and UCASE(STATUS) !='DEACTIVE'";
		$rs=Sql_exec($cn,$qry);
		$cnt=Sql_Num_Rows($rs);

		if ($cnt>0)
			echo "+OK";
		else
			echo(raise_error(127));

		break;
	case 'CHK_USERINFO_REG':

		list($uid,$userid)=explode('|',$cmdparam);

		$qry="select * from userinfo where uid='$uid' AND UCASE(STATUS) !='DEACTIVE'";
		$rs=Sql_exec($cn,$qry);
		$cnt=Sql_Num_Rows($rs);

		if ($cnt>0)
			echo "+OK";
		else
			echo(raise_error(127));

		break;
	case 'DEL_SUB':
		$uid = $cmdparam;
		$qry ="select uid from subscriber where uid='$uid'";
		echo $qry;
		$rs=Sql_exec($cn,$qry);
		if(Sql_Num_Rows($rs)>0)
		{
			$qry="delete from subscriber where uid='$uid'";
			if (Sql_exec($cn,$qry))
				echo("+OK");
			else
				echo(raise_error(124));
		}
		else
			echo(raise_error(11));
		break;

	case 'SHOW_SUB_LIST':
		$cmdparam=str_replace("\\","",$cmdparam);
		$qry="SELECT * FROM subscriber_details  ".$cmdparam;

		$rs=Sql_exec($cn,$qry);
		$data=Sql_Num_Rows($rs);
		if ($data<1)
		{
			echo raise_error(11);
			break;
		}

		echo '+OK';
		echo "\n";
		echo $data;
		echo "\n";

		echo $showservicestr=('uid'."|".'packageid'."|".'status'."|".'activationdate'."|".'provisiondate'."|".'provisionedby'."|".'initialbalance'."|".'initialvalidity'."|".'expirydate'."|".'balance'."|".'lastaccess'."|".'hascustinfo'."|".'custinfovalidity'."|".'accountno'."|".'accounttype'."|".'creditlimit'."|".'showbalance'."|".'isp2preg'."|".'pin'."|".'ActBonusUsed'."|".'BillcycleID'."|".'UpdatedBy'."|".'updateDate'."|".'maxDevice'."|".'location'."|".'firstname'."|".'lastname'."|".'user_info_status'."|".'id');
		echo "\n";

		$rs=Sql_exec($cn,$qry);
		if ($rs)
		{
			while($row = Sql_fetch_array($rs))
			{
				$uid=Sql_Result($row,"uid");
				$packageid=Sql_Result($row,"packageid");
				$status=Sql_Result($row,"status");
				$activationdate=Sql_Result($row,"activationdate");
				$provisiondate=Sql_Result($row,"provisiondate");
				$provisionedby=Sql_Result($row,"provisionedby");
				$initialbalance=Sql_Result($row,"initialbalance");
				$initialvalidity=Sql_Result($row,"initialvalidity");
				$expirydate=Sql_Result($row,"expirydate");
				$balance=Sql_Result($row,"balance");
				$lastaccess=Sql_Result($row,"lastaccess");
				$hascustinfo = Sql_Result($row,"hascustinfo");
				$custinfovalidity = Sql_Result($row,"custinfovalidity");
				$accountno = Sql_Result($row,"accountno");
				$accounttype = Sql_Result($row,"accounttype");
				$creditlimit = Sql_Result($row,"creditlimit");
				$showbalance = Sql_Result($row,"showbalance");
				$isp2preg = Sql_Result($row,"isp2preg");
				$pin = Sql_Result($row,"pin");
				$ActBonusUsed = Sql_Result($row,"ActBonusUsed");
				$BillcycleID = Sql_Result($row,"BillcycleID");
				$UpdatedBy = Sql_Result($row,"UpdatedBy");
				$updateDate = Sql_Result($row,"updateDate");
				$maxDevice = Sql_Result($row,"maxDevice");
				$location = Sql_Result($row,"location");
				$firstName=Sql_Result($row,"firstname");
				$lastName=Sql_Result($row,"lastname");
				$user_info_status=Sql_Result($row,"user_info_status");
				$id=Sql_Result($row,"id");

				$temp=$uid."|".$packageid."|".$status."|".$activationdate."|".$provisiondate."|".$provisionedby."|".$initialbalance."|".$initialvalidity."|".$expirydate."|".$balance."|".$lastaccess."|".$hascustinfo."|".$custinfovalidity."|".$accountno."|".$accounttype."|".$creditlimit."|".$showbalance."|".$isp2preg."|".$pin."|".$ActBonusUsed."|".$BillcycleID."|".$UpdatedBy."|".$updateDate."|".$maxDevice."|".$location."|".$firstName."|".$lastName."|".$user_info_status."|".$id;

				echo $temp;
				echo "\n";
			}
		}
		else
			echo(raise_error(11));

		break;

	case 'P2P_TOPUP':
		list($srcsubid,$destsubid,$amount,$password)=explode('|',$cmdparam);
		$adminCheck=0;
		$qry = "call P2P_TopUP('$srcsubid','$destsubid',$amount,'$password',$adminCheck)";
		$rs=Sql_exec($cn,$qry);
		$result_code= Sql_GetField($rs,"result_code");
		list($Errcode,$tranId)=explode('|',$result_code);
		if($Errcode!=1000)
			echo raise_error($Errcode);
		else
			echo "+OK|".$tranId;
		break;
	case 'P2P_TOPUP_ADMIN':
		list($srcsubid,$destsubid,$amount,$adminPassword,$adminUid)=explode('|',$cmdparam);

		$qry="SELECT isAdmin('$adminUid','$adminPassword') AS isAdmin";
		$rs=Sql_exec($cn,$qry);
		$data=Sql_Num_Rows($rs);
		if($data<1)
		{
			echo raise_error(100);
			break;
		}
		$row=Sql_fetch_array($rs);
		$result=Sql_Result($row,'isAdmin');
		if($result==0)
		{
			echo raise_error(97);
		}else
		{
			$adminCheck=1;
			$qry = "call P2P_TopUP('$srcsubid','$destsubid',$amount,'$adminPassword',$adminCheck)" ;
			$rs=Sql_exec($cn,$qry);
			$result_code= Sql_GetField($rs,"result_code");
			list($Errcode,$tranId)=explode('|',$result_code);
			if($Errcode!=1000)
				echo raise_error($Errcode);
			else
				echo "+OK|".$tranId;
		}
		break;
	case 'CHG_SUB_VALIDITY':
		list($uid,$validity,$userid)=explode('|',$cmdparam);
		$qry="select * from subscriber where uid='$uid'";
		$rs=Sql_exec($cn,$qry);
		$cnt=Sql_Num_Rows($rs);
		if ($cnt>0)
		{
			$qry="update subscriber set expirydate='$validity',updatedby='$userid',updatedate=now() where uid='$uid'";
			if (Sql_exec($cn,$qry))
				echo "+OK";
			else
				echo(raise_error(121));
		}
		else
		{
			echo(raise_error(127));
		}
		break;

	case 'Show_userinfo':
		$cmdparam=str_replace("\\","",$cmdparam);
		$qry="select * from userinfo ".$cmdparam;
		$rs=Sql_exec($cn,$qry);
		$data=Sql_Num_Rows($rs);
		if ($data<1)
		{
			echo raise_error(11);
			break;
		}
		echo "+OK";
		echo "\n";
		echo $data;
		echo "\n";
		if($rs)
		{
			echo "uid|status|isSubscriber|FirstName|LastName|SubscriberType|EmailAddress|DateOfBirth|Age|Gender|Occupation|Nationality|PermanentAddress|PresentAddress1|PresentAddress2|city|ContactNo|ContactType|FathersName|MothersName|HusbandWifeName|SecondContacts|PhotoIdentity|AttachmentOne|AttachmentTwo|POSCode|PackageID|SecurityDeposit|Others|UserName|UserId|LastUpdate";
			echo "\n";
			while($row = Sql_fetch_array($rs))
			{
				$uid=Sql_Result($row,"uid");
				$status=Sql_Result($row,"status");
				$isSubscriber=Sql_Result($row,"isSubscriber");
				$FirstName=Sql_Result($row,"FirstName");
				$LastName=Sql_Result($row,"LastName");
				$SubscriberType=Sql_Result($row,"SubscriberType");
				$EmailAddress = Sql_Result($row,"EmailAddress");
				$DateOfBirth=Sql_Result($row,"DateOfBirth");
				$Age=Sql_Result($row,"Age");
				$Gender=Sql_Result($row,"Gender");
				$Occupation=Sql_Result($row,"Occupation");
				$Nationality=Sql_Result($row,"Nationality");
				$PermanentAddress=Sql_Result($row,"PermanentAddress");
				//======================present address splited into 3 parts====
				$PresentAddress1=Sql_Result($row,"PresentAddress1");
				$PresentAddress2=Sql_Result($row,"PresentAddress2");
				$city=Sql_Result($row,"City");
				//=====================================
				$ContactNo=Sql_Result($row,"ContactNo");
				$ContactType=Sql_Result($row,"ContactType");
				$FathersName=Sql_Result($row,"FathersName");
				$MothersName=Sql_Result($row,"MothersName");
				$HusbandWifeName=Sql_Result($row,"HusbandWifeName");
				$SecondContacts=Sql_Result($row,"SecondContacts");
				$PhotoIdentity=Sql_Result($row,"PhotoIdentity");
				$AttachmentOne=Sql_Result($row,"AttachmentOne");
				$AttachmentTwo=Sql_Result($row,"AttachmentTwo");
				$POSCode=Sql_Result($row,"POSCode");
				$PackageID=Sql_Result($row,"PackageID");
				$SecurityDeposit=Sql_Result($row,"SecurityDeposit");
				if(!isset($SecurityDeposit) or empty($SecurityDeposit))$SecurityDeposit=0;
				$Others=Sql_Result($row,"Others");
				$UserName=Sql_Result($row,"UserName");
				$UserID=Sql_Result($row,"UserID");
				$LastUpdate=Sql_Result($row,"LastUpdate");
				$temp = "$uid|$status|$isSubscriber|$FirstName|$LastName|$SubscriberType|$EmailAddress|$DateOfBirth|$Age|$Gender|$Occupation|$Nationality|$PermanentAddress|$PresentAddress1|$PresentAddress2|$city|$ContactNo|$ContactType|$FathersName|$MothersName|$HusbandWifeName|$SecondContacts|$PhotoIdentity|$AttachmentOne|$AttachmentTwo|$POSCode|$PackageID|$SecurityDeposit|$Others|$UserName|$UserId|$LastUpdate";
				echo $temp ;
				echo "\n";

			}
		}
		break;

	case 'Del_userinfo':
		$cmdparam=str_replace("\\","",$cmdparam);
		$qry="delete from userinfo  ".$cmdparam;
		if (Sql_exec($cn,$qry))
			echo("+OK");
		else
			echo(raise_error(81));
		break;


	case 'SHOW_SUBSCRIPTION_LIST':
		$key_value = array();
		/*$query = "SELECT rateid , steppulserate/100 as PackagePrice FROM ratepulse WHERE rateid in (SELECT SubscriptionGroupID FROM subscriptiongroup);";
		$rows=Sql_exec($cn,$query);
		while($row = Sql_fetch_array($rows))
		{
			$rateid = Sql_Result($row,"rateid");
			$PackagePrice = Sql_Result($row,"PackagePrice");
			$key_value[strtolower($rateid)] = $PackagePrice;
		}*/

		$cmdparam=str_replace("\\","",$cmdparam);
		$qry="SELECT * FROM user_subscription_details  ".$cmdparam;

		$rs=Sql_exec($cn,$qry);
		$data=Sql_Num_Rows($rs);
		if ($data<1)
		{
			echo raise_error(11);
			break;
		}



		$rs=Sql_exec($cn,$qry);
		if ($rs)
		{
			$count = 0;
			while($row = Sql_fetch_array($rs))
			{
				$msisdn=Sql_Result($row,"msisdn");
				$parentID=Sql_result($row,"parentID");
				$subscriptionGroupID=Sql_result($row,"SubscriptionGroupID");
				$ServiceID=Sql_result($row,"ServiceID");
				$registrationDate=Sql_result($row,"registrationDate");
				$serviceDuration=Sql_result($row,"ServiceDuration");
				$status=Sql_Result($row,"status");
				$chargingDueDate=Sql_result($row,"ChargingDueDate");
				$nextRenewalDate=Sql_result($row,"NextRenewalDate");
				//$PackagePrice = $key_value[strtolower($subscriptionGroupID)];
				$qry="SELECT GetChargingAmount('$msisdn','NA',0,'$ServiceID','SPECIFIC','NA','NA','$subscriptionGroupID')";
				$rs_ca=Sql_exec($cn,$qry);
				$PackagePrice= Sql_GetField($rs_ca,0)/100;
				Sql_Free_Result($rs_ca);
				$query="SELECT balance FROM subscriber_details where uid = '$msisdn'";
				$rows=Sql_exec($cn,$query);
				$balance= Sql_GetField($rows, 0);
				$dueAmount = $PackagePrice - $balance;
				if($dueAmount < 0){

					$data--;
				}
				else {

					$qry1="select * from userinfo where uid = '$msisdn'";
					$rs1=Sql_exec($cn,$qry1);
					if($rs1){
						$row1 = Sql_fetch_array($rs1);




						$FirstName=Sql_Result($row1,"FirstName");
						$LastName=Sql_Result($row1,"LastName");
						$fulname = $FirstName.' '.$LastName;
						$ContactNo=Sql_Result($row1,"ContactNo");
						$PermanentAddress=str_replace(array("\r\n","\r","\n")," ",Sql_Result($row1,"PermanentAddress"));
						//======================present address splited into 3 parts====
						$PresentAddress1= str_replace(array("\r\n","\r","\n")," ",Sql_Result($row1,"PresentAddress1"));
						$PresentAddress2= str_replace(array("\r\n","\r","\n")," ",Sql_Result($row1,"PresentAddress2"));
						$city=Sql_Result($row1,"City");
						$Gender=Sql_Result($row1,"Gender");
						$Occupation=Sql_Result($row1,"Occupation");
					}
					//$data1=Sql_Num_Rows($rs1);
					$temp[$count++] = $msisdn . "|" . $parentID . "|" . $subscriptionGroupID . "|" .
						$registrationDate . "|" . $serviceDuration . "|" . $status . "|" .
						$chargingDueDate . "|" . $nextRenewalDate . "|" . $ServiceID . "|" .
						number_format((float)$PackagePrice, 2, '.', '') . "|" . number_format((float)$dueAmount, 2, '.', '').
						"|".$fulname."|".$ContactNo."|".$PermanentAddress."|".$PresentAddress1."|".$PresentAddress2."|".$city."|".$Gender."|".$Occupation;
				}

			}
		}
		else
			echo(raise_error(11));

		if ($data<1)
		{
			echo raise_error(11);

		}
		else{
			echo '+OK';
			echo "\n";
			echo $data;
			echo "\n";
			echo $showservicestr=('msisdn'."|".'parentID'."|".'SubscriptionGroupID'."|".'registrationDate'."|"."ServiceDuration"."|"."status"."|".'ChargingDueDate'."|".'NextRenewalDate'."|".'ServiceID'."|".'PackagePrice'.
				"|".'DueAmount'."|".'fulname'."|".'ContactNo'."|".'PermanentAddress'."|".'PresentAddress1'."|".'PresentAddress2'."|".'city'."|".'Gender'."|".'Occupation');
			echo "\n";
			foreach ($temp as &$value) {
				echo $value;
				echo "\n";
			}

		}
		break;

	case 'ADD_SUBSCRIBER' :

		list($uid,$packageid,$provisiondate,$initialbalance,$initialvalidity,$expirydate,$hascustinfo,$custinfovalidity,$accountno,$accounttype,$creditlimit,$showbalance,$isp2preg,$pin,$ActBonusUsed,$BillcycleID,$maxDevice,$location)=explode('|',$cmdparam);
		if(!isset($provisiondate) or empty($initialbalance)) $provisiondate='NULL';
		if(!isset($initialbalance) or empty($initialbalance)) $initialbalance=0;
		if(!isset($initialvalidity) or empty($initialvalidity)) $initialvalidity=0;
		if(!isset($expirydate) or empty($expirydate)) $expirydate='NULL';
		if(!isset($hascustinfo) or empty($hascustinfo)) $hascustinfo=0;
		if(!isset($custinfovalidity) or empty($custinfovalidity)) $custinfovalidity=0;
		if(!isset($creditlimit) or empty($creditlimit)) $creditlimit=0;
		if(!isset($showbalance) or empty($showbalance)) $showbalance=0;
		if(!isset($isp2preg) or empty($isp2preg)) $isp2preg=0;
		if(!isset($ActBonusUsed) or empty($ActBonusUsed)) $ActBonusUsed=0;
		if(!isset($BillcycleID) or empty($BillcycleID)) $BillcycleID=0;
		if(!isset($maxDevice) or empty($maxDevice)) $maxDevice=0;
		$date = date('Y-m-d H:i:s');
		$cn=ConnectDB();
		$qry="select status from subscriber where uid='$uid' " ;
		$rs=sql_exec($cn,$qry);
		while($row = Sql_fetch_array($rs))
		{
			$status = $row['status'];
		}
		if(strtoupper($status)=="ACTIVE")
			echo "FAILED|Already registered" ;
		if(strtoupper($status)=="INACTIVE")
		{
			$qry="update subscriber set status='active', packageid='$packageid', activationdate='$date', provisiondate='$provisiondate', initialbalance=$initialbalance, balance=$initialbalance, initialvalidity=$initialvalidity, expirydate='$expirydate', hascustinfo=$hascustinfo, custinfovalidity=$custinfovalidity, accountno='$accountno', accounttype='$accounttype', creditlimit=$creditlimit, showbalance=$showbalance, isp2preg=$isp2preg, pin='$pin', ActBonusUsed=$ActBonusUsed, BillcycleID=$BillcycleID, maxDevice=$maxDevice, location='$location', lastaccess='$date',updatedate='$date' where uid='$uid'";
			$qry=str_replace("'NULL'","NULL",$qry);
			if(sql_exec($cn,$qry))
				echo "+OK|Status Updated";
		}
		if($status==null)
		{
			$qry="insert into subscriber(uid,status,packageid,activationdate,provisiondate,initialbalance,balance,initialvalidity,expirydate,hascustinfo,custinfovalidity,accountno,accounttype,creditlimit,showbalance,isp2preg,pin,ActBonusUsed,BillcycleID,maxDevice,location,lastaccess,updateDate) values('$uid','active','$packageid','$date','$provisiondate',$initialbalance,$initialbalance,$initialvalidity,'$expirydate',$hascustinfo,$custinfovalidity,'$accountno','$accounttype',$creditlimit,$showbalance,$isp2preg,'$pin',$ActBonusUsed,$BillcycleID,$maxDevice,'$location','$date','$date')";
			$qry=str_replace("'NULL'","NULL",$qry);
			if(sql_exec($cn,$qry))
				echo "+OK|new registration";
		}
		break;

}

ClosedDBConnection($cn);
?>