<?php

//get disks

//if($host['available'] == 1 && $host['sa'] == 0) { $keyValue = 'vfs.fs.size'; }
if($host['available'] == 1 ) { $keyValue = 'vfs.fs.size'; }
else { $keyValue = 'inbytes'; }			 
 
	$disks = $api->itemGet(array(
     'output' => 'extend',
     'hostids' => $hostid,
     'search' => array('key_' => $keyValue)
     
));

// print disks ID with graph name
foreach($disks as $disk) {    
 
 	//if($host['available'] == 1 && $host['sa'] == 0) { $searchValSize = 'total'; $searchValUsed = 'used'; }
 	if($host['available'] == 1 ) { $searchValSize = 'total'; $searchValUsed = 'used'; }
	else { $searchValSize = 'hrStorageSizeinBytes'; $searchValUsed = 'hrStorageUsedinBytes'; }
					           
   $diskSize = get_item_values($disk->itemid, $searchValSize);
   $diskUsed = get_item_values($disk->itemid, $searchValUsed);

	//Size
	if(strchr(get_item_label($diskSize['key_']),"A:") != '') {
		if($diskSize['value_max'] != 0) {							
			$arrSize[]= get_item_label($diskSize['key_']).",".$diskSize['value_max'];
		}
	}		

	else {						
		if($diskSize['value_max'] != 0 || get_item_label($diskSize['key_']) != '') {						
			$arrSize[]= get_item_label($diskSize['key_']).",".$diskSize['value_max'];
		}
	}
	
	//Used
	if(strchr(get_item_label($diskUsed['key_']),"A:") == '') {
		if($diskUsed['name'] != '') {		
			$arrUsed[]= get_item_label($diskUsed['key_']).",".$diskUsed['value_max'];		
		}
	}
	else {
		if($diskUsed['name'] != '') {
			if($diskUsed['value_max'] != 0) {
				$arrUsed[]= get_item_label($diskUsed['key_']).",".$diskUsed['value_max'];
			}
		}
	}
			
 }
 
sort($arrSize);
sort($arrUsed);

//print disks size
for($n=0;$n<count($arrUsed);$n++) {

	$u = explode(",",$arrUsed[$n]); 		
	
	if($u[0] != 0 || $u[0] != '') {	
		if(strchr($u[0],":") == '') {				
			$arrUsed2[] = $u[0].",".$u[1];
		}	
	}
}


// Memory

if($host['available'] == 1 ) { $keyValueMem = 'vm.memory.size'; }
else { $keyValueMem = 'inbytes'; }			 
 
$mems = $api->itemGet(array(
  'output' => 'extend',
  'hostids' => $hostid,
  'search' => array('key_' => $keyValueMem)
));

// print Mem

foreach($mems as $mem) {    
  	
 	if($host['available'] == 1 ) { 
 	
	 	$searchValSize = 'total'; $searchValUsed = 'available'; 
						           
	   $memSize = get_item_values($mem->itemid, $searchValSize);
	   $memUsed = get_item_values($mem->itemid, $searchValUsed);
	   //$memUsed = ($memSize['value_max'] - $memUsed['value_max']);
	
		//Size				
		if($memSize['value_max'] != 0 || get_item_label($memSize['key_']) != '') {						
			$arrSizeMem[] = get_item_label($memSize['key_']).",".$memSize['value_max'];
		}

		if($memUsed['name'] != '') {
			if($memUsed['value_max'] != 0) {
				$arrUsedMem[] = get_item_label($memUsed['key_']).",". $memUsed['value_max'];
			}
		}
		
		$zbx_agent = 1;
	}	
	
	else { 
	
		$searchValSize = 'hrStorageSizeinBytes'; $searchValUsed = 'hrStorageUsedinBytes'; 
	
	   $memSize = get_item_values($mem->itemid, $searchValSize);
	   $memUsed = get_item_values($mem->itemid, $searchValUsed);
				
		//Size								
		if($memSize['value_max'] != 0 || get_item_label($memSize['key_']) != '') {						
			$label = get_item_label($memSize['key_']);
									
				if(stripos($label,"memory") != '') {						
					$arrSizeMem[] = $label.",".$memSize['value_max'];
				}
		}

		if($memUsed['name'] != '') {
			if($memUsed['value_max'] != 0) {
				
				$label = get_item_label($memUsed['key_']);
									
				if(stripos($label,"memory") != '') {						
					$arrUsedMem[] = $label.",".$memUsed['value_max'];
				}	
			}
		}
		
		$zbx_agent = 0;				
	}				
}
 
sort($arrSizeMem);
sort($arrUsedMem);

//print mem size
for($n=0;$n<count($arrUsedMem);$n++) {

	$u = explode(",",$arrUsedMem[$n]); 		
	
	if($u[0] != 0 || $u[0] != '') {	
		if(strchr($u[0],":") == '') {				
			$arrUsedMem2[] = $u[0].",".$u[1];
		}	
	}
}


//CPU Load
if($host['available'] == 1 ) { $keyValueCPU = 'system.cpu.util[,system]'; }
else { $keyValueCPU = 'processorLoad'; }	
 
$cpus = $api->itemGet(array(
  'output' => 'extend',
  'hostids' => $hostid,
  'search' => array('key_' => $keyValueCPU)     
));

 
foreach($cpus as $cpu) {
	
 	if($host['available'] == 1 ) { 
 		$searchValSize = 'system.cpu.util[,system]';
 		$cpuLoad = zbx_get_item_values($cpu->itemid, $searchValSize);    
		$arrCPU[] = $cpuLoad['value_max'];  
 	 }

	else { 
		$searchValSize = 'processorload';
		$cpuLoad = get_item_values($cpu->itemid, $searchValSize);    
		$arrCPU[] = $cpuLoad['value_max'];  
	}            
}
 
$cpuNum = count($arrCPU); 

if($cpuNum > 0) {
	$avgCPU = round(array_sum($arrCPU)/$cpuNum,1);
}
else {
	$avgCPU = 0;
}	


//uptime
if($host['available'] == 1 ) { $keyValueUP = 'system.uptime'; }
else { $keyValueUP = 'sysuptime'; }	

 $times = $api->itemGet(array(
     'output' => 'extend',
     'hostids' => $hostid,
     'search' => array('key_' => $keyValueUP)     
 ));
 
 foreach($times as $t) {      
               
    $time = get_item_values($t->itemid, $keyValueUP);
    if($time['value_max'] != 0) {		       	       
     
   	$arrTime[] = $time['value_max'];         
     } 
 }
 
 
// get all network interfaces

if($host['available'] == 1 ) { $keyValueNet = 'net.if'; }
else { $keyValueNet = 'if'; }	

$ifs = $api->itemGet(array(
  'output' => 'extend',
  'hostids' => $hostid,
  'search' => array('key_' => $keyValueNet),
  'sortfield' => 'name'
));

 // print graph ID with graph name
foreach($ifs as $if) {    

 	if($host['available'] == 1 ) { $searchValIn = 'net.if.in'; $searchValOut = 'net.if.out'; }
	else { $searchValIn = 'ifInOctets'; $searchValOut = 'ifOutOctets'; }			            

   $ifSize = get_item_values($if->itemid, $searchValIn);
	$ifUsed = get_item_values($if->itemid, $searchValOut);
				
	if($ifSize['value_max'] != '') {			
		$arrIfSize[]= get_item_label($ifSize['key_']).",".$ifSize['value_max'];;
	}
	
	if($ifUsed['value_max'] != '') {
		$arrIfUsed[]= get_item_label($ifUsed['key_']).",".$ifUsed['value_max'];
	}

	if($ifSize['key_'] != '' ) {			
		$arrIfDesc[]= get_item_label($ifSize['key_']).",".$ifSize['value_max'];		
	}
			
 }

sort($arrIfSize); 
sort($arrIfUsed); 
sort($arrIfDesc); 


// get all graphs
 $graphs = $api->graphGet(array(
     'output' => 'extend',
     'hostids' => $hostid,
     //'search' => array('key_' => 'if'),
     'sortfield' => 'name'
 ));
 
 ?>