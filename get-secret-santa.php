<?php
	$nameChosen = $_POST["name"];

	require 'config.php';
	//
	// // Create connection
	$conn = new MySQLi($db_servername, $db_user, $db_password, $db_name);

	// Check connection
	if ($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	else
	{
		$result = "";
		$availableNames = getAvailableNamesByUsername($nameChosen);
		$chosenNames = getAlreadyChosenNames();
		$peopleWhoHaveMadeTheirChoice = getListOfPeopleWhoHaveMadeTheirChoice();

		// $availableNames = ["Robbie", "Elena", "Achim", "Ingrid", "Tanis", "James"];
		// $chosenNames = ["Achim"];
		// $peopleWhoHaveMadeTheirChoice = ["James"];

		// $popularityIndexes = [
		// 	"Robbie"=>0,
		// 	"Elena"=>0,
		// 	"Achim"=>0,
		// 	"Ingrid"=>0,
		// 	"Tanis"=>0,
		// 	"James"=>0
		// ];

		$dictionaryOfPopularity = array();
		$currentUsersAvailableNamesWithTheirAvailableNames = array();
		$flag = 0;

		foreach ($availableNames as $key => $value)
		{
			array_push($currentUsersAvailableNamesWithTheirAvailableNames, [$value=>getAvailableNamesByUsername($value)]);
		}

		// var_dump($currentUsersAvailableNamesWithTheirAvailableNames);

		foreach ($currentUsersAvailableNamesWithTheirAvailableNames as $key => $value)
		{
			// $namesAvailableNames = $currentUsersAvailableNamesWithTheirAvailableNames[$key][key($value)];
			// var_dump($namesAvailableNames);
			// var_dump($value);
			if(!in_array(key($value), $chosenNames))
			{
				$nameOfPersonForPopularity = key($value);
				$popularity = 0;

				foreach ($currentUsersAvailableNamesWithTheirAvailableNames as $key => $value)
				{
					if(!in_array(key($value),$peopleWhoHaveMadeTheirChoice))
					{
						if(in_array($nameOfPersonForPopularity, current($value)))
						{
							$popularity++;
						}
					}
				}

				$dictionaryOfPopularity[$nameOfPersonForPopularity] = $popularity;
			}
		}

		 var_dump($dictionaryOfPopularity);

		// We have all the information we need!
		// So, now...

		// Get "available names" for all $availableNames
		// Then make into associative array

		// $annesAvailableNamesAvailableNames = [
		// "Robbie"=>["Anne", "Ingrid", "Achim", "Tanis", "James"],
		// "Elena"=>["Anne", "Ingrid", "Achim", "Tanis", "James"],
		// "Achim"=>["Anne", "Robbie", "Elena", "Tanis", "James"],
		// "Ingrid"=>["Anne", "Robbie", "Elena", "Tanis", "James"],
		// "Tanis"=>["Anne", "Robbie", "Elena", "Achim", "Ingrid"],
		// "James"=>["Anne", "Robbie", "Elena", "Achim", "Ingrid"],
		// ]

		// For each available name in $popularityIndexes, do this:

		// // For each "Associated Name" in associative array, check if they are not in $peopleWhoHaveMadeTheirChoice

		// // As a result of this, "James" and his Available Names are removed from the array

		// // // For each item in the associative array, check that their "Available Names" are NOT in $chosenNames

		// If ^^ is true

		echo $result;
	}

	$conn->close();

	function getAvailableNamesByUsername($nameChosen)
	{
		require 'config.php';
		$conn = new MySQLi($db_servername, $db_user, $db_password, $db_name);
		$sql = "SELECT possible_recipients FROM users WHERE name = '" . $nameChosen . "'";
		$result = $conn->query($sql);
		$conn->close();
		$possibleRecipientsArray = "No Records";
		// ^^ LOOK AT THIS LATER

		if ($result->num_rows > 0)
		{
			// output data of each row
			while($name = $result->fetch_assoc())
			{
				$namesString = $name["possible_recipients"];
				$possibleRecipientsArray = explode(",", $namesString);
				// WE HAVE AN ARRAY OF POSSIBLE RCIPIENTS
			}
		}

		return $possibleRecipientsArray;
	}

	function getAlreadyChosenNames()
	{
		require 'config.php';
		$conn = new MySQLi($db_servername, $db_user, $db_password, $db_name);
		$sql = "SELECT name FROM users WHERE chosen = 1";
		$result = $conn->query($sql);
		$conn->close();
		$namesArray = array();

		if($result->num_rows > 0)
		{
			while($name = $result->fetch_assoc())
			{
				array_push($namesArray, $name["name"]);
			}
		}

		return $namesArray;
	}

	function getListOfPeopleWhoHaveMadeTheirChoice()
	{
		require 'config.php';
		$conn = new MySQLi($db_servername, $db_user, $db_password, $db_name);
		$sql = "SELECT name FROM users WHERE choice_made = 1";
		$result = $conn->query($sql);
		$conn->close();
		$namesArray = array();

		if($result->num_rows > 0)
		{
			while($name = $result->fetch_assoc())
			{
				array_push($namesArray, $name["name"]);
			}
		}

		return $namesArray;
	}
?>
