<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="style.css" />
		<title>Secret Santa 2016</title>
	</head>
	<body>
		<h1>Secret Santa 2016</h1>
		<form>
			<?php
				require 'config.php';

				// Create connection
				$conn = new MySQLi($db_servername, $db_user, $db_password, $db_name);

				// Check connection
				if ($conn->connect_error)
				{
					die("Connection failed: " . $conn->connect_error);
				}
				else
				{
					$sql = "SELECT name FROM users WHERE choice_made = 0";
					$result = $conn->query($sql);

					if ($result->num_rows > 0)
					{
						// output data of each row
						while($row = $result->fetch_assoc())
						{
							echo "<input type='radio' name='recipient' value='" . $row["name"] . "' id='" . $row["name"] . "' /><label for='" . $row["name"] . "'>" . $row["name"] . "</label><br />";
						}
					}
					else
					{
						echo "<p>Everybody has been assigned their secret santa. Merry Christmas!</p>";
					}
				}

				$conn->close();
			?>
			<button class="ghost">Submit</button>
		</form>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script type="text/javascript" src="script.js"></script>
	</body>
</html>
