<?php
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
//----------------ADMINS
function find_admin_by_username($db, $username) {
	$stmnt_query = null;
	$data = null;
	
	try {
		$sql = "SELECT * FROM WM_MVS_CF.ADMINS ";
		$sql .= "WHERE USERNAME = '" . $username . "'";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);		
		
		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = (object) 
			[
				"ID" => intval($row['ID']),
				"FIRST_NAME" => $row['FIRST_NAME'],
				"LAST_NAME" => $row['LAST_NAME'],
				"EMAIL" => $row['EMAIL'],
				"USERNAME" => $row['USERNAME'],
				"HASHED_PASSWORD" => $row['HASHED_PASSWORD']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query);
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function set_options2($db) {
	//change format to = yyyy-mm-dd hh24:mi
	$stmnt_query = null;
	
    try {
        // mm-dd-yyyy hh24:mi
		$sql = "alter session set  NLS_DATE_FORMAT='yyyy-mm-dd hh24:mi'";
        $stmnt_query = oci_parse($db, $sql);
        $status = oci_execute($stmnt_query);
        if ( !$status ) {
            $e = oci_error($db);
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            // throw new \RuntimeException(self::$status);
        }
    }
    catch (Exception $e) {
        $status = "ERROR: Could set database session options";
        // throw new \RuntimeException(self::$status);
    }
	finally {
		oci_free_statement($stmnt_query); 
	}
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_rating_stage_coe($db, $location_id) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select distinct office_id, template_id, location_id, version, native_units, SUBSTR(template_id, 12,4) as agency 
				from CWMS_20.AV_RATING_LOCAL
				where template_id like '%COE' 
					and template_id like 'Stage%' 
					and template_id like '%Flow%'
					and template_id not like '%Flow-%'
					and template_id not like '%,%'
					and location_id not like '%0%'
					and aliased_item is null
					and version = 'Production'
					and location_id = '".$location_id."'
				order by location_id asc";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {

			$data = (object) [
				"office_id" => $row['OFFICE_ID'],
				"template_id" => $row['TEMPLATE_ID'],
				"location_id" => $row['LOCATION_ID'],
				"version" => $row['VERSION'],
				"native_units" => $row['NATIVE_UNITS'],
				"agency" => $row['AGENCY']			
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_carlyle_forecast($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_carlyle_today as (
					select 'CARLYLE' as lake
						,date_time
						,cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as date_time_cst
						,date_time as fcst_date
						,cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as fcst_date_cst
						,to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') as system_date_cst
						,to_char(date_time, 'mm-dd-yyyy') as date_time_2
						,q as outflow
						,'CAYI2' as station
					from wm_mvs_lake.carlyle_gate
					where cwms_util.change_timezone(date_time, 'UTC', 'US/Central') = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss')
				),
				cte_carlyle_5_days as (
					select lake,
						date_time,
						cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as date_time_cst,
						fcst_date,
						cwms_util.change_timezone(fcst_date, 'UTC', 'US/Central') as fcst_date_cst,
						to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') as system_date_cst,
						to_char(date_time, 'mm-dd-yyyy') as date_time_2,
						outflow,
						'CAYI2' as station
					from wm_mvs_lake.qlev_fcst
					where lake = 'CARLYLE'
						and cwms_util.change_timezone(fcst_date, 'UTC', 'US/Central') = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss')
					order by date_time desc
				)
				
				select lake, date_time, date_time_cst, fcst_date, fcst_date_cst, system_date_cst, date_time_2, outflow/1000 as outflow, station from cte_carlyle_today
				union all 
				select lake, date_time, date_time_cst, fcst_date, fcst_date_cst, system_date_cst, date_time_2, outflow/1000 as outflow, station from cte_carlyle_5_days
				order by date_time_2 asc
				fetch next 6 row only";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"lake" => $row['LAKE'],
				"date_time" => $row['DATE_TIME'],
				"date_time_cst" => $row['DATE_TIME_CST'],
				"fcst_date" => $row['FCST_DATE'],
				"fcst_date_cst" => $row['FCST_DATE_CST'],
				"system_date_cst" => $row['SYSTEM_DATE_CST'],
				"date_time_2" => $row['DATE_TIME_2'],
				"outflow" => $row['OUTFLOW'],
				"station" => $row['STATION']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_shelbyville_forecast($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_SHELBYVILLE_today as (
					select 'SHELBYVILLE' as lake
						,date_time
						,cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as date_time_cst
						,date_time as fcst_date
						,cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as fcst_date_cst
						,to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') as system_date_cst
						,to_char(date_time, 'mm-dd-yyyy') as date_time_2
						,q as outflow
						,'SBYI2' as station
					from wm_mvs_lake.SHELBYVILLE_GATE
					where cwms_util.change_timezone(date_time, 'UTC', 'US/Central') = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss')
				),
				cte_SHELBYVILLE_5_days as (
					select lake,
						date_time,
						cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as date_time_cst,
						fcst_date,
						cwms_util.change_timezone(fcst_date, 'UTC', 'US/Central') as fcst_date_cst,
						to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') as system_date_cst,
						to_char(date_time, 'mm-dd-yyyy') as date_time_2,
						outflow,
						'SBYI2' as station
					from wm_mvs_lake.QLEV_FCST
					where lake = 'SHELBYVILLE'
						and cwms_util.change_timezone(fcst_date, 'UTC', 'US/Central') = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss')
					order by date_time desc
				)
				
				select lake, date_time, date_time_cst, fcst_date, fcst_date_cst, system_date_cst, date_time_2, outflow/1000 as outflow, station from cte_SHELBYVILLE_today
				union all 
				select lake, date_time, date_time_cst, fcst_date, fcst_date_cst, system_date_cst, date_time_2, outflow/1000 as outflow, station from cte_SHELBYVILLE_5_days
				order by date_time_2 asc
				fetch next 6 row only";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"lake" => $row['LAKE'],
				"date_time" => $row['DATE_TIME'],
				"date_time_cst" => $row['DATE_TIME_CST'],
				"fcst_date" => $row['FCST_DATE'],
				"fcst_date_cst" => $row['FCST_DATE_CST'],
				"system_date_cst" => $row['SYSTEM_DATE_CST'],
				"date_time_2" => $row['DATE_TIME_2'],
				"outflow" => $row['OUTFLOW'],
				"station" => $row['STATION']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_wappapello_forecast($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_WAPPAPELLO_today as (
					select 'WAPPAPELLO' as lake
						,date_time
						,cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as date_time_cst
						,date_time as fcst_date
						,cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as fcst_date_cst
						,to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') as system_date_cst
						,to_char(date_time, 'mm-dd-yyyy') as date_time_2
						,q as outflow
						,'WPPM7' as station
					from wm_mvs_lake.WAPPAPELLO_GATE
					where cwms_util.change_timezone(date_time, 'UTC', 'US/Central') = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss')
				),
				cte_WAPPAPELLO_5_days as (
					select lake,
						date_time,
						cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as date_time_cst,
						fcst_date,
						cwms_util.change_timezone(fcst_date, 'UTC', 'US/Central') as fcst_date_cst,
						to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') as system_date_cst,
						to_char(date_time, 'mm-dd-yyyy') as date_time_2,
						outflow,
						'WPPM7' as station
					from wm_mvs_lake.QLEV_FCST
					where lake = 'WAPPAPELLO'
						and cwms_util.change_timezone(fcst_date, 'UTC', 'US/Central') = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss')
					order by date_time desc
				)
				
				select lake, date_time, date_time_cst, fcst_date, fcst_date_cst, system_date_cst, date_time_2, outflow/1000 as outflow, station from cte_WAPPAPELLO_today
				union all 
				select lake, date_time, date_time_cst, fcst_date, fcst_date_cst, system_date_cst, date_time_2, outflow/1000 as outflow, station from cte_WAPPAPELLO_5_days
				order by date_time_2 asc
				fetch next 6 row only";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"lake" => $row['LAKE'],
				"date_time" => $row['DATE_TIME'],
				"date_time_cst" => $row['DATE_TIME_CST'],
				"fcst_date" => $row['FCST_DATE'],
				"fcst_date_cst" => $row['FCST_DATE_CST'],
				"system_date_cst" => $row['SYSTEM_DATE_CST'],
				"date_time_2" => $row['DATE_TIME_2'],
				"outflow" => $row['OUTFLOW'],
				"station" => $row['STATION']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_rend_forecast($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_REND_today as (
					select 'REND' as lake
						,date_time
						,cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as date_time_cst
						,date_time as fcst_date
						,cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as fcst_date_cst
						,to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') as system_date_cst
						,to_char(date_time, 'mm-dd-yyyy') as date_time_2
						,q_avg as outflow
						,'RNDI2' as station
					from wm_mvs_lake.REND_FLOW
					where cwms_util.change_timezone(date_time, 'UTC', 'US/Central') = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') - interval '1' day
				),
				cte_REND_5_days as (
					select lake,
						date_time,
						cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as date_time_cst,
						fcst_date,
						cwms_util.change_timezone(fcst_date, 'UTC', 'US/Central') as fcst_date_cst,
						to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') as system_date_cst,
						to_char(date_time, 'mm-dd-yyyy') as date_time_2,
						outflow,
						'RNDI2' as station
					from wm_mvs_lake.QLEV_FCST
					where lake = 'REND'
						and cwms_util.change_timezone(fcst_date, 'UTC', 'US/Central') = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss')
					order by date_time desc
				)
				
				select lake, date_time, date_time_cst, fcst_date, fcst_date_cst, system_date_cst, date_time_2, outflow/1000 as outflow, station from cte_REND_today
				union all 
				select lake, date_time, date_time_cst, fcst_date, fcst_date_cst, system_date_cst, date_time_2, outflow/1000 as outflow, station from cte_REND_5_days
				order by date_time_2 asc
				fetch next 6 row only";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"lake" => $row['LAKE'],
				"date_time" => $row['DATE_TIME'],
				"date_time_cst" => $row['DATE_TIME_CST'],
				"fcst_date" => $row['FCST_DATE'],
				"fcst_date_cst" => $row['FCST_DATE_CST'],
				"system_date_cst" => $row['SYSTEM_DATE_CST'],
				"date_time_2" => $row['DATE_TIME_2'],
				"outflow" => $row['OUTFLOW'],
				"station" => $row['STATION']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_mark_twain_yesterday_forecast($db) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select 'Mark Twain Lk-Salt' as location_id
					, cwms_util.change_timezone(tsv.date_time, 'UTC', 'CST6CDT') date_time
					, value/1000 as value
					, unit_id
					, 'CDAM7' as station
				from cwms_v_tsv_dqu  tsv
				where 
					tsv.cwms_ts_id = 'Mark Twain Lk-Salt.Flow-Turb.Ave.~1Day.1Day.lakerep-rev' 
					and tsv.unit_id = 'cfs'
					and tsv.office_id = 'MVS' 
					and tsv.aliased_item is null
					and cwms_util.change_timezone(tsv.date_time, 'UTC', 'CST6CDT') = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') - interval '1' day";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$data = (object) [
				"location_id" => $row['LOCATION_ID'],
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"station" => $row['STATION']			
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_mark_twain_forecast($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "select lake, 
					date_time,
					cwms_util.change_timezone(date_time, 'UTC', 'US/Central') as date_time_cst,
					fcst_date,
					cwms_util.change_timezone(fcst_date, 'UTC', 'US/Central') as fcst_date_cst,
					to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'US/Central'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss') as system_date_cst,
					to_char(date_time, 'mm-dd-yyyy') as date_time_2, 
					outflow/1000 as outflow,
					'CDAM7' as station
				from wm_mvs_lake.qlev_fcst 
				where lake = 'MT'
					and cwms_util.change_timezone(fcst_date, 'UTC', 'US/Central') = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'CST6CDT'),'mm-dd-yyyy') || '00:00:00','mm-dd-yyyy hh24:mi:ss')
				order by date_time asc
				fetch next 6 row only";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"lake" => $row['LAKE'],
				"date_time" => $row['DATE_TIME'],
				"date_time_cst" => $row['DATE_TIME_CST'],
				"fcst_date" => $row['FCST_DATE'],
				"fcst_date_cst" => $row['FCST_DATE_CST'],
				"system_date_cst" => $row['SYSTEM_DATE_CST'],
				"date_time_2" => $row['DATE_TIME_2'],
				"outflow" => $row['OUTFLOW'],
				"station" => $row['STATION']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_ld24_pool($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_ld24_pool as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'CLKM7' as damlock
			from cwms_v_tsv_dqu_24h tsv
			where 
				 tsv.cwms_ts_id = 'LD 24 Pool-Mississippi.Stage.Inst.30Minutes.0.29'
				 and tsv.unit_id = 'ft'
				 and date_time = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'CST6CDT'),'mm-dd-yyyy') || '06:00:00','mm-dd-yyyy hh24:mi:ss')
				 and tsv.office_id = 'MVS' 
				 and tsv.aliased_item is null
			),
			cte_ld24_pool_netmiss as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'CLKM7' as damlock
			from cwms_v_tsv_dqu
			where cwms_ts_id ='LD 24 Pool-Mississippi.Elev.Inst.~1Day.0.netmiss-compv2' and unit_id = 'ft'
			and date_time > to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '0' DAY
			and date_time < to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '5' DAY
			),
			cte_hinge as (
			select cwms_util.split_text(location_level_id, 1, '.') as location_id
				,sysdate as date_time
				,round(constant_level,2) as value
				,level_unit as unit_id
				,0 as quality_code
				,specified_level_id as damlock
			from CWMS_20.AV_LOCATION_LEVEL 
			where specified_level_id in ('Hinge Max','Hinge Min') 
			and unit_system = 'EN' 
			and location_id in ('Louisiana-Mississippi')
			)
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ld24_pool
			union all
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ld24_pool_netmiss
			union all
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_hinge";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"location_id" => $row['LOCATION_ID'],
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE'],
				"damlock" => $row['DAMLOCK']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_ld24_pool_2($db) {
	// remove current pool reading per NWS
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_ld24_pool_netmiss as (
				select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
					,date_time
					,round(value,2) as value
					,unit_id
					,quality_code
					,'CLKM7' as damlock
				from cwms_v_tsv_dqu
				where cwms_ts_id ='LD 24 Pool-Mississippi.Elev.Inst.~1Day.0.netmiss-compv2' and unit_id = 'ft'
				and date_time > to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '0' DAY
				and date_time < to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '5' DAY
				),
				cte_hinge as (
				select cwms_util.split_text(location_level_id, 1, '.') as location_id
					,sysdate as date_time
					,round(constant_level,2) as value
					,level_unit as unit_id
					,0 as quality_code
					,specified_level_id as damlock
				from CWMS_20.AV_LOCATION_LEVEL 
				where specified_level_id in ('Hinge Max','Hinge Min') 
				and unit_system = 'EN' 
				and location_id in ('Louisiana-Mississippi')
				)
				select location_id, date_time, value, unit_id, quality_code, damlock
				from cte_ld24_pool_netmiss
				union all
				select location_id, date_time, value, unit_id, quality_code, damlock
				from cte_hinge";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"location_id" => $row['LOCATION_ID'],
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE'],
				"damlock" => $row['DAMLOCK']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_ld24_tw($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_ld24_tw as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'CLKM7' as damlock
			from cwms_v_tsv_dqu_24h tsv
			where 
				 tsv.cwms_ts_id = 'LD 24 TW-Mississippi.Stage.Inst.30Minutes.0.lrgsShef-rev'
				 and tsv.unit_id = 'ft'
				 and date_time = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'CST6CDT'),'mm-dd-yyyy') || '06:00:00','mm-dd-yyyy hh24:mi:ss')
				 and tsv.office_id = 'MVS' 
				 and tsv.aliased_item is null
			),
			cte_ld24_tw_netmiss as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'CLKM7' as damlock
			from cwms_v_tsv_dqu
			where cwms_ts_id ='LD 24 TW-Mississippi.Stage.Inst.~1Day.0.netmiss-fcst' and unit_id = 'ft'
			and date_time > to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '0' DAY
			and date_time < to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '5' DAY
			)
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ld24_tw
			union all
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ld24_tw_netmiss";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"location_id" => $row['LOCATION_ID'],
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE'],
				"damlock" => $row['DAMLOCK']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_ld25_pool($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_ld25_pool as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'CAGM7' as damlock
			from cwms_v_tsv_dqu_24h tsv
			where 
				 tsv.cwms_ts_id = 'LD 25 Pool-Mississippi.Stage.Inst.30Minutes.0.29'
				 and tsv.unit_id = 'ft'
				 and date_time = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'CST6CDT'),'mm-dd-yyyy') || '06:00:00','mm-dd-yyyy hh24:mi:ss')
				 and tsv.office_id = 'MVS' 
				 and tsv.aliased_item is null
			),
			cte_ld25_pool_netmiss as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'CAGM7' as damlock
			from cwms_v_tsv_dqu
			where cwms_ts_id ='LD 25 Pool-Mississippi.Elev.Inst.~1Day.0.netmiss-compv2' and unit_id = 'ft'
			and date_time > to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '0' DAY
			and date_time < to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '5' DAY
			),
			cte_hinge as (
			select cwms_util.split_text(location_level_id, 1, '.') as location_id
				,sysdate as date_time
				,round(constant_level,2) as value
				,level_unit as unit_id
				,0 as quality_code
				,specified_level_id as damlock
			from CWMS_20.AV_LOCATION_LEVEL 
			where specified_level_id in ('Hinge Max','Hinge Min') 
			and unit_system = 'EN' 
			and location_id in ('Mosier Ldg-Mississippi')
			)
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ld25_pool
			union all
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ld25_pool_netmiss
			union all
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_hinge";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"location_id" => $row['LOCATION_ID'],
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE'],
				"damlock" => $row['DAMLOCK']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_ld25_pool_2($db) {
	// remove current pool reading per NWS
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_ld25_pool_netmiss as (
				select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
					,date_time
					,round(value,2) as value
					,unit_id
					,quality_code
					,'CAGM7' as damlock
				from cwms_v_tsv_dqu
				where cwms_ts_id ='LD 25 Pool-Mississippi.Elev.Inst.~1Day.0.netmiss-compv2' and unit_id = 'ft'
				and date_time > to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '0' DAY
				and date_time < to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '5' DAY
				),
				cte_hinge as (
				select cwms_util.split_text(location_level_id, 1, '.') as location_id
					,sysdate as date_time
					,round(constant_level,2) as value
					,level_unit as unit_id
					,0 as quality_code
					,specified_level_id as damlock
				from CWMS_20.AV_LOCATION_LEVEL 
				where specified_level_id in ('Hinge Max','Hinge Min') 
				and unit_system = 'EN' 
				and location_id in ('Mosier Ldg-Mississippi')
				)
				select location_id, date_time, value, unit_id, quality_code, damlock
				from cte_ld25_pool_netmiss
				union all
				select location_id, date_time, value, unit_id, quality_code, damlock
				from cte_hinge";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"location_id" => $row['LOCATION_ID'],
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE'],
				"damlock" => $row['DAMLOCK']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_ld25_tw($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_ld25_tw as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'CAGM7' as damlock
			from cwms_v_tsv_dqu_24h tsv
			where 
				 tsv.cwms_ts_id = 'LD 25 TW-Mississippi.Stage.Inst.30Minutes.0.lrgsShef-rev'
				 and tsv.unit_id = 'ft'
				 and date_time = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'CST6CDT'),'mm-dd-yyyy') || '06:00:00','mm-dd-yyyy hh24:mi:ss')
				 and tsv.office_id = 'MVS' 
				 and tsv.aliased_item is null
			),
			cte_ld25_tw_netmiss as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'CAGM7' as damlock
			from cwms_v_tsv_dqu
			where cwms_ts_id ='LD 25 TW-Mississippi.Stage.Inst.~1Day.0.netmiss-fcst' and unit_id = 'ft'
			and date_time > to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '0' DAY
			and date_time < to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '5' DAY
			)
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ld25_tw
			union all
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ld25_tw_netmiss";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"location_id" => $row['LOCATION_ID'],
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE'],
				"damlock" => $row['DAMLOCK']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_ldmp_pool($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_ldpm_pool as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'ALNI2' as damlock
			from cwms_v_tsv_dqu_24h tsv
			where 
				 tsv.cwms_ts_id = 'Mel Price Pool-Mississippi.Stage.Inst.15Minutes.0.29'
				 and tsv.unit_id = 'ft'
				 and date_time = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'CST6CDT'),'mm-dd-yyyy') || '06:00:00','mm-dd-yyyy hh24:mi:ss')
				 and tsv.office_id = 'MVS' 
				 and tsv.aliased_item is null
			),
			cte_ldpm_pool_netmiss as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'ALNI2' as damlock
			from cwms_v_tsv_dqu
			where cwms_ts_id ='Mel Price Pool-Mississippi.Elev.Inst.~1Day.0.netmiss-compv2' and unit_id = 'ft'
			and date_time > to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '0' DAY
			and date_time < to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '5' DAY
			),
			cte_hinge as (
			select cwms_util.split_text(location_level_id, 1, '.') as location_id
				,sysdate as date_time
				,round(constant_level,2) as value
				,level_unit as unit_id
				,0 as quality_code
				,specified_level_id as damlock
			from CWMS_20.AV_LOCATION_LEVEL 
			where specified_level_id in ('Hinge Max','Hinge Min') 
			and unit_system = 'EN' 
			and location_id in ('Grafton-Mississippi')
			)
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ldpm_pool
			union all
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ldpm_pool_netmiss
			union all
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_hinge";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"location_id" => $row['LOCATION_ID'],
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE'],
				"damlock" => $row['DAMLOCK']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_ldmp_pool_2($db) {
	// remove current pool reading per NWS
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_ldpm_pool_netmiss as (
				select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
					,date_time
					,round(value,2) as value
					,unit_id
					,quality_code
					,'ALNI2' as damlock
				from cwms_v_tsv_dqu
				where cwms_ts_id ='Mel Price Pool-Mississippi.Elev.Inst.~1Day.0.netmiss-compv2' and unit_id = 'ft'
				and date_time > to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '0' DAY
				and date_time < to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '5' DAY
				),
				cte_hinge as (
				select cwms_util.split_text(location_level_id, 1, '.') as location_id
					,sysdate as date_time
					,round(constant_level,2) as value
					,level_unit as unit_id
					,0 as quality_code
					,specified_level_id as damlock
				from CWMS_20.AV_LOCATION_LEVEL 
				where specified_level_id in ('Hinge Max','Hinge Min') 
				and unit_system = 'EN' 
				and location_id in ('Grafton-Mississippi')
				)
				select location_id, date_time, value, unit_id, quality_code, damlock
				from cte_ldpm_pool_netmiss
				union all
				select location_id, date_time, value, unit_id, quality_code, damlock
				from cte_hinge";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"location_id" => $row['LOCATION_ID'],
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE'],
				"damlock" => $row['DAMLOCK']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_ldmp_tw($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_ldmp_tw as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'ALNI2' as damlock
			from cwms_v_tsv_dqu_24h tsv
			where 
				 tsv.cwms_ts_id = 'Mel Price TW-Mississippi.Stage.Inst.30Minutes.0.lrgsShef-rev'
				 and tsv.unit_id = 'ft'
				 and date_time = to_date(to_char(cwms_util.change_timezone(sysdate, 'UTC', 'CST6CDT'),'mm-dd-yyyy') || '06:00:00','mm-dd-yyyy hh24:mi:ss')
				 and tsv.office_id = 'MVS' 
				 and tsv.aliased_item is null
			),
			cte_ldmp_tw_netmiss as (
			select cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
				,date_time
				,round(value,2) as value
				,unit_id
				,quality_code
				,'ALNI2' as damlock
			from cwms_v_tsv_dqu
			where cwms_ts_id ='Mel Price TW-Mississippi.Stage.Inst.~1Day.0.netmiss-fcst' and unit_id = 'ft'
			and date_time > to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '0' DAY
			and date_time < to_date( to_char(sysdate, 'mm-dd-yyyy hh24:mm:ss') ,'mm-dd-yyyy hh24:mi:ss') + interval '5' DAY
			)
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ldmp_tw
			union all
			select location_id, date_time, value, unit_id, quality_code, damlock
			from cte_ldmp_tw_netmiss";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"location_id" => $row['LOCATION_ID'],
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE'],
				"damlock" => $row['DAMLOCK']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_crest_forecast_carlyle($db) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "with cte_crest_data as (
					select 
						case
						when lake = 'SHELBYVILLE' then 'Lk Shelbyville-Kaskaskia'
						when lake = 'WAPPAPELLO' then 'Wappapello Lk-St Francis'
						when lake = 'REND' then 'Rend Lk-Big Muddy'
						when lake = 'MT' then 'Mark Twain Lk-Salt'
						when lake = 'CARLYLE' then 'Carlyle Lk-Kaskaskia'
							else 'na'
						end as project_id,
						crest, 
						crst_dt, 
						data_entry_dt, 
						opt
					from wm_mvs_lake.crst_fcst
					where (cwms_util.change_timezone(data_entry_dt, 'UTC', 'CST6CDT')) = to_date(to_char(current_date, 'mm-dd-yyyy') || '00:00' ,'mm-dd-yyyy hh24:mi') - interval '0' day
					order by project_id
				)
				select project_id
					,crest
					,crst_dt
					,data_entry_dt
					,opt
				from cte_crest_data
				where project_id = 'Carlyle Lk-Kaskaskia'";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = (object) [
				"project_id" => $row['PROJECT_ID'],
				"crest" => $row['CREST'],
				"crst_dt" => $row['CRST_DT'],
				"data_entry_dt" => $row['DATA_ENTRY_DT'],
				"opt" => $row['OPT']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);
		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_crest_forecast_shelbyville($db) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "with cte_crest_data as (
					select 
						case
						when lake = 'SHELBYVILLE' then 'Lk Shelbyville-Kaskaskia'
						when lake = 'WAPPAPELLO' then 'Wappapello Lk-St Francis'
						when lake = 'REND' then 'Rend Lk-Big Muddy'
						when lake = 'MT' then 'Mark Twain Lk-Salt'
						when lake = 'CARLYLE' then 'Carlyle Lk-Kaskaskia'
							else 'na'
						end as project_id,
						crest, 
						crst_dt, 
						data_entry_dt, 
						opt
					from wm_mvs_lake.crst_fcst
					where (cwms_util.change_timezone(data_entry_dt, 'UTC', 'CST6CDT')) = to_date(to_char(current_date, 'mm-dd-yyyy') || '00:00' ,'mm-dd-yyyy hh24:mi') - interval '0' day
					order by project_id
				)
				select project_id
					,crest
					,crst_dt
					,data_entry_dt
					,opt
				from cte_crest_data
				where project_id = 'Lk Shelbyville-Kaskaskia'";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = (object) [
				"project_id" => $row['PROJECT_ID'],
				"crest" => $row['CREST'],
				"crst_dt" => $row['CRST_DT'],
				"data_entry_dt" => $row['DATA_ENTRY_DT'],
				"opt" => $row['OPT']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);
		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_crest_forecast_mark_twain($db) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "with cte_crest_data as (
					select 
						case
						when lake = 'SHELBYVILLE' then 'Lk Shelbyville-Kaskaskia'
						when lake = 'WAPPAPELLO' then 'Wappapello Lk-St Francis'
						when lake = 'REND' then 'Rend Lk-Big Muddy'
						when lake = 'MT' then 'Mark Twain Lk-Salt'
						when lake = 'CARLYLE' then 'Carlyle Lk-Kaskaskia'
							else 'na'
						end as project_id,
						crest, 
						crst_dt, 
						data_entry_dt, 
						opt
					from wm_mvs_lake.crst_fcst
					where (cwms_util.change_timezone(data_entry_dt, 'UTC', 'CST6CDT')) = to_date(to_char(current_date, 'mm-dd-yyyy') || '00:00' ,'mm-dd-yyyy hh24:mi') - interval '0' day
					order by project_id
				)
				select project_id
					,crest
					,crst_dt
					,data_entry_dt
					,opt
				from cte_crest_data
				where project_id = 'Mark Twain Lk-Salt'";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = (object) [
				"project_id" => $row['PROJECT_ID'],
				"crest" => $row['CREST'],
				"crst_dt" => $row['CRST_DT'],
				"data_entry_dt" => $row['DATA_ENTRY_DT'],
				"opt" => $row['OPT']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);
		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_crest_forecast_wappapello($db) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "with cte_crest_data as (
					select 
						case
						when lake = 'SHELBYVILLE' then 'Lk Shelbyville-Kaskaskia'
						when lake = 'WAPPAPELLO' then 'Wappapello Lk-St Francis'
						when lake = 'REND' then 'Rend Lk-Big Muddy'
						when lake = 'MT' then 'Mark Twain Lk-Salt'
						when lake = 'CARLYLE' then 'Carlyle Lk-Kaskaskia'
							else 'na'
						end as project_id,
						crest, 
						crst_dt, 
						data_entry_dt, 
						opt
					from wm_mvs_lake.crst_fcst
					where (cwms_util.change_timezone(data_entry_dt, 'UTC', 'CST6CDT')) = to_date(to_char(current_date, 'mm-dd-yyyy') || '00:00' ,'mm-dd-yyyy hh24:mi') - interval '0' day
					order by project_id
				)
				select project_id
					,crest
					,crst_dt
					,data_entry_dt
					,opt
				from cte_crest_data
				where project_id = 'Wappapello Lk-St Francis'";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = (object) [
				"project_id" => $row['PROJECT_ID'],
				"crest" => $row['CREST'],
				"crst_dt" => $row['CRST_DT'],
				"data_entry_dt" => $row['DATA_ENTRY_DT'],
				"opt" => $row['OPT']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);
		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_crest_forecast_rend($db) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "with cte_crest_data as (
					select 
						case
						when lake = 'SHELBYVILLE' then 'Lk Shelbyville-Kaskaskia'
						when lake = 'WAPPAPELLO' then 'Wappapello Lk-St Francis'
						when lake = 'REND' then 'Rend Lk-Big Muddy'
						when lake = 'MT' then 'Mark Twain Lk-Salt'
						when lake = 'CARLYLE' then 'Carlyle Lk-Kaskaskia'
							else 'na'
						end as project_id,
						crest, 
						crst_dt, 
						data_entry_dt, 
						opt
					from wm_mvs_lake.crst_fcst
					where (cwms_util.change_timezone(data_entry_dt, 'UTC', 'CST6CDT')) = to_date(to_char(current_date, 'mm-dd-yyyy') || '00:00' ,'mm-dd-yyyy hh24:mi') - interval '0' day
					order by project_id
				)
				select project_id
					,crest
					,crst_dt
					,data_entry_dt
					,opt
				from cte_crest_data
				where project_id = 'Rend Lk-Big Muddy'";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = (object) [
				"project_id" => $row['PROJECT_ID'],
				"crest" => $row['CREST'],
				"crst_dt" => $row['CRST_DT'],
				"data_entry_dt" => $row['DATA_ENTRY_DT'],
				"opt" => $row['OPT']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);
		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_roller_tainter_ld24($db) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select cwms_util.change_timezone(date_time, 'UTC', 'CST6CDT') as date_time
					,value
					,cwms_ts_id
					,cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
					,unit_id
				from CWMS_20.AV_TSV_DQU_30D
				where cwms_ts_id = 'LD 24 Pool-Mississippi.Opening.Inst.~2Hours.0.lpmsShef-raw-Taint'
					and (unit_id = 'ft' or unit_id = 'cfs' or unit_id = 'F' or unit_id = 'mg/l' or unit_id = 'umho/cm')
					--and cwms_util.change_timezone(date_time, 'UTC', 'CST6CDT') = to_date('07-21-2022 07:00' ,'mm-dd-yyyy hh24:mi')
					and date_time  >= cast(cast(current_date as timestamp) at time zone 'UTC' as date) - interval '48' HOUR
					and date_time  <= cast(cast(current_date as timestamp) at time zone 'UTC' as date) 
				order by date_time desc
				fetch first 1 rows only";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = (object) [
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"cwms_ts_id" => $row['CWMS_TS_ID'],
				"location_id" => $row['LOCATION_ID']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);
		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_roller_tainter_ld25($db) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select cwms_util.change_timezone(date_time, 'UTC', 'CST6CDT') as date_time
					,value
					,cwms_ts_id
					,cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
					,unit_id
				from CWMS_20.AV_TSV_DQU_30D
				where cwms_ts_id = 'LD 25 Pool-Mississippi.Opening.Inst.~2Hours.0.lpmsShef-raw-Taint'
					and (unit_id = 'ft' or unit_id = 'cfs' or unit_id = 'F' or unit_id = 'mg/l' or unit_id = 'umho/cm')
					--and cwms_util.change_timezone(date_time, 'UTC', 'CST6CDT') = to_date('07-21-2022 07:00' ,'mm-dd-yyyy hh24:mi')
					and date_time  >= cast(cast(current_date as timestamp) at time zone 'UTC' as date) - interval '48' HOUR
					and date_time  <= cast(cast(current_date as timestamp) at time zone 'UTC' as date) 
				order by date_time desc
				fetch first 1 rows only";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = (object) [
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"cwms_ts_id" => $row['CWMS_TS_ID'],
				"location_id" => $row['LOCATION_ID']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);
		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_roller_tainter_mp($db) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select cwms_util.change_timezone(date_time, 'UTC', 'CST6CDT') as date_time
					,value
					,cwms_ts_id
					,cwms_util.split_text(cwms_ts_id, 1, '.') as location_id
					,unit_id
				from CWMS_20.AV_TSV_DQU_30D
				where cwms_ts_id = 'Mel Price Pool-Mississippi.Opening.Inst.~2Hours.0.lpmsShef-raw-Taint'
					and (unit_id = 'ft' or unit_id = 'cfs' or unit_id = 'F' or unit_id = 'mg/l' or unit_id = 'umho/cm')
					--and cwms_util.change_timezone(date_time, 'UTC', 'CST6CDT') = to_date('07-21-2022 07:00' ,'mm-dd-yyyy hh24:mi')
					and date_time  >= cast(cast(current_date as timestamp) at time zone 'UTC' as date) - interval '48' HOUR
					and date_time  <= cast(cast(current_date as timestamp) at time zone 'UTC' as date) 
				order by date_time desc
				fetch first 1 rows only";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = (object) [
				"date_time" => $row['DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"cwms_ts_id" => $row['CWMS_TS_ID'],
				"location_id" => $row['LOCATION_ID']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);
		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------