<!DOCTYPE html>
<html>
	<head>
		<link href="https://fonts.googleapis.com/css?family=Mountains+of+Christmas|Open+Sans" rel="stylesheet">
		<link href="/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" href="normalize.css" />
		<link rel="stylesheet" href="style.css" />
		<title>Secret Santa 2016</title>
	</head>
	<body>
		<video id="bg-vid" autoplay muted loop playsinline>
			<source src="christmas.mp4" type="video/mp4" />
		</video>
		<div class="flex">
			<div class="inner">
				<h1>Secret Santa 2016</h1>
				<p>
					What's your name?
				</p>
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
						echo "<ul>";
						while($row = $result->fetch_assoc())
						{
							echo "<li data-name='" . $row["name"] . "' class='name' />" . $row["name"] . "</li>";
						}
						echo "</ul>";
					}
					else
					{
						echo "<p>Everybody has been assigned their secret santa. Merry Christmas!</p>";
					}
				}

				$conn->close();
				?>
				<div id="choose-name" class="ghost">
					Submit <i class="fa fa-arrow-right" aria-hidden="true"></i>
				</div>
			</div>
		</div>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script type="text/javascript" src="script.js"></script>
	</body>
</html>
