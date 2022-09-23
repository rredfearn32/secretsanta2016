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

		if(in_array($nameChosen, $peopleWhoHaveMadeTheirChoice))
		{
			return;
		}

		$dictionaryOfPopularity = array();
		$currentUsersAvailableNamesWithTheirAvailableNames = array();
		$flag = 0;

		foreach ($availableNames as $key => $value)
		{
			array_push($currentUsersAvailableNamesWithTheirAvailableNames, [$value=>getAvailableNamesByUsername($value)]);
		}

		foreach ($currentUsersAvailableNamesWithTheirAvailableNames as $key => $value)
		{
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

		 $minPop = min($dictionaryOfPopularity);
		 $lowestPopularityNames = array();

		 foreach ($dictionaryOfPopularity as $key => $value)
		 {
			 if($value == $minPop)
			 {
				 array_push($lowestPopularityNames, $key);
			 }
		 }

		 $lengthOfLowPopNamesArray = count($lowestPopularityNames);
		 $randomNumber = mt_rand(0, $lengthOfLowPopNamesArray - 1);

		$result = $lowestPopularityNames[$randomNumber];

		setChosenAndChoiceMade($nameChosen, $result);

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

	function setChosenAndChoiceMade($thisUser, $chosenUser)
	{
		require 'config.php';
		$conn = new MySQLi($db_servername, $db_user, $db_password, $db_name);
		// $sql = "SELECT name FROM users WHERE choice_made = 1";
		$sqlOne = "UPDATE users SET choice_made = 1 WHERE name = '" . $thisUser . "'";
		$result = $conn->query($sqlOne);
		$sqlTwo = "UPDATE users SET chosen = 1 WHERE name = '" . $chosenUser . "'";
		$result = $conn->query($sqlTwo);
		$sqlThree = "UPDATE users SET chosen_name = '" . $chosenUser . "' WHERE name = '" . $thisUser . "'";
		$result = $conn->query($sqlThree);
		$conn->close();
	}
?>
