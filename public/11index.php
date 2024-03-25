<!DOCTYPE html>

<html>
<body>
	<?php

	$fairarr = new ArrayObject();
	$aicarr = new ArrayObject();

	$newCand = new ArrayObject();
	$updateNotExistCand = new ArrayObject();
	$updateExistCand = new ArrayObject();

	echo "My first PHP script!<br /> <br />";

	$fair_conn = mysqli_connect('localhost:3306', 'root', '', 'projectfair');
	if (!$fair_conn) {
	   // die('Could not connect: ' . mysqli_error());
		echo "Some trouble!<br /> <br />";
	}

	$fair_data = mysqli_query($fair_conn, 'select * from candidates');
	if (!$fair_data) {
	  echo "An error occurred.\n";
	  exit;
	}

	while ($row = mysqli_fetch_assoc($fair_data)) {
		$fairarr -> append(array(
			$row['id'],               //0
			$row['created_at'],       //1
			$row['updated_at'],       //2
			$row['fio'],              //3
			$row['about'],            //4
			$row['email'],            //5
			$row['numz'],             //6
			$row['phone'],            //7
			$row['course'],           //8
			$row['api_token'],        //9
			$row['training_group'],   //10
			$row['group_id'],         //11
			$row['mira_id'],          //12
			$row['training_group'],             //------------------------------------------------------было добавлено поле 13
			$row['can_send_participations'],   //14
			)
		);
	}

	$url = "https://int.istu.edu/extranet/worker/rp_view/integration/03ae8597-3487-46dc-839e-3317af096889/98tR6K7Iz5T8/yarmproj.stud";

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$resp = curl_exec($curl);
	curl_close($curl);

	$data = json_decode($resp);
	$aicarr = $data -> RecordSet;
	
	//------------------------------------------------тестовыйАИС
/*	
	$JsonData = '{
		"RecordSet": [
			{
				"miraid": "2421022",
				"nomz": "20460265",
				"name": "Абамба Чинеду Соломон",
				"email": "moussa.lakhone@yahoo.com",
				"grup": "ИИКб-20-1",
				"kurs": 4,
				"kafid": 1988624,
				"kafname": "БРИКС",
				"facid": 50,
				"facname": "Байкальский институт БРИКС"
			},
			{
				"miraid": "8888888",
				"nomz": "22222222",
				"name": "Иванов Иван Иванович",
				"email": "ivan@mail.com",
				"grup": "ИСТб-20-2",
				"kurs": 1,
				"kafid": 1988624,
				"kafname": "БРИКС",
				"facid": 51,
				"facname": "Байкальский институт БРИКС"
			},
			{
				"miraid": "2434855",
				"nomz": "21430226",
				"name": "Аббасова Марина Сабировна",
				"email": "marinaabbasova2003@gmail.com",
				"grup": "ХТОб-21-1",
				"kurs": 3,
				"kafid": 35,
				"kafname": "Химической технологии им. Н.И. Ярополова",
				"facid": 43,
				"facname": "Институт высоких технологий"
			}
		]
	}';
	
	$data = json_decode($JsonData);
	$aicarr = $data->RecordSet;
	*/
	
/*
	echo "JSON data from the external server:<br>";
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	echo "<script>";

	echo "var jsonData = " . json_encode($data) . ";";
	echo "console.log('JSON data from the external server:', jsonData);";
	echo "</script>";*/



	$flag = false;
	foreach ($aicarr as $aic) {
	    foreach ($fairarr as $fair) {
	    	if ($aic -> nomz == $fair[6]) {
	    		$flag = true;
	    		break;
	    	}
	    }
	    if (!$flag) {
	    	$newCand -> append(array(
	    		date('Y-m-d H:i:s'),
	    		date('Y-m-d H:i:s'),
	    		$aic -> name,
	    		$aic -> email,
	    		$aic -> nomz,
	    		$aic -> miraid,
	    		$aic -> kurs,
	    		$aic -> grup,
	    		1,
	    	));
	    }

	    $flag = false;
	}



//------------------------------------------------------текущее	

	$flag = false;
	
	foreach ($fairarr as $fair) {		
		foreach ($aicarr as $aic) {			
			//if($fair[12]  != $aic->miraid){
			if($fair[3] != $aic -> name  || $fair[8]  != $aic->kurs || 
			   $fair[13] != $aic -> grup || $fair[5] != $aic->email ){
				if ($aic -> nomz == $fair[6]) {
					$flag = true;
		
					$updateExistCand -> append(array(
						date('Y-m-d H:i:s'),
						$aic -> nomz,
						$aic -> kurs,
						$aic -> miraid,
						$aic -> grup,
						$aic -> name,
						$aic -> email,
						1,
					));
		
					break;
				}
			}
			
		    if($aic -> nomz == $fair[6]){
			  $flag = true;
		    }			
		}
		
		// вот я исправил:
		if (!$flag) {
			if($fair[14] == 1){
				$updateNotExistCand -> append(array(
					date('Y-m-d H:i:s'),
					$fair[2],
					$fair[6],
					0,
				));
			}	    	
			
		}
		$flag = false;
	}

/*
//---------------------------------------новый код для проверки updateExistCand
	foreach ($fairarr as $fair) {
		$flag = false;  
	
		if ($fair[13] == 0) {
			foreach ($aicarr as $aic) {
				if ($aic->nomz == $fair[6]) {
					$updateExistCand->append(array(
						date('Y-m-d H:i:s'),
						$aic->nomz,
						$aic->kurs,
						$aic->miraid,
						$aic->grup,
						$aic->name,
						1,
					));
	
					$flag = true;  
					break;
				}
			}
		}
	
		
		if (!$flag) {
			foreach ($aicarr as $aic) {
				if ($aic->nomz == $fair[6]) {
					$flag = true;
					break;
				}
			}
		}
	
		
		if (!$flag) {
			$updateNotExistCand->append(array(
				date('Y-m-d H:i:s'),
				$fair[2],
				$fair[6],
				0,
			));
		}
	}
*/



//---------------------------------------было

/*
	$flag = false;
	foreach ($fairarr as $fair) {
    	foreach ($aicarr as $aic) {
	    	if ($aic -> nomz == $fair[6]) {
	    		$flag = true;

	    		$updateExistCand -> append(array(
		    		date('Y-m-d H:i:s'),
		    		$aic -> nomz,
					$aic -> kurs,
					$aic -> miraid,
		    		$aic -> grup,
					$aic -> name,
		    		1,
		    	));

		    	break;
	    	}
	    }
		
		// вот я исправил:
		if (!$flag) {	    	
	    	$updateNotExistCand -> append(array(
	    		date('Y-m-d H:i:s'),
	    		$fair[2],
				$fair[6],
	    		0,
	    	));
	    }
	    $flag = false;
	}*/




	echo var_dump(count($newCand));
	echo "newCand <br />";
	echo var_dump(count($updateExistCand));
	echo "updateExistCand <br />";
	echo var_dump(count($updateNotExistCand));
	echo "updateNotExistCand <br />";

	$insert = "";
	foreach ($newCand as $cand) {
		if ($cand[6] == 2) {
			$cand[8] = 0;
		}

		$insert = $insert . sprintf("
			insert into candidates(
				created_at,
				updated_at,
				fio,
				email,
				numz,
				mira_id,
				course,
				training_group,
				phone,
				about,
				can_send_participations
			) values (
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d'
			);",
		$cand[0],
		$cand[1],
		$cand[2],
		$cand[3],
		$cand[4],
		$cand[5],
		$cand[6],
		$cand[7],
		"",
		"",
		$cand[8],
		) . "\n";
	}

	$update = "";
	foreach ($updateExistCand as $cand) {
		$update = $update . sprintf("
update candidates set 
	mira_id = '%s,' 
	fio = '%s', 
	updated_at = '%s' , 
	course = '%d' , 
	training_group = '%s' , 
	email = '%s' ,
	can_send_participations = '%d' 
where numz = '%s';",
			$cand[3],
			$cand[5],
			$cand[0],
			$cand[2],
			$cand[4],
			$cand[6],
			$cand[7],
			$cand[1],
		);
	}

	$updateNotExists = "";
	foreach ($updateNotExistCand as $cand) {
		$updateNotExists = $updateNotExists . sprintf("
update candidates set 
	updated_at = '%s', 
	can_send_participations = '%d' 
where numz = '%s';",
			$cand[0],
			$cand[3],
			$cand[2],
		);
	}

	$file = "update.sql";
	$current = file_get_contents($file);

	$current = $insert . "\n" . $update . "\n" . $updateNotExists;

	file_put_contents($file, $current);

	echo "<br />Script Executed!";

	?>
</body>
</html>