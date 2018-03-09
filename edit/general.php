<?php include("../helper.php"); ?>

<?php
	// include the database connection
	include("../connection.php");

	// If we are coming from the events page to create a new event
	if(isset($_POST['action']) && $_POST['action'] == 'newEvent') {

		// create a new event
		$new_event_stmt = $db->prepare("INSERT into event SET ID = UUID()");
		$new_event_stmt->execute();

		// get the id of that event
		$new_event_id_stmt = $db->prepare("SELECT * from event where internal_ID = (select MAX(internal_ID) from event)");
		$new_event_id_stmt->execute();

		$id;
		while($new_event_id = $new_event_id_stmt->fetch(PDO::FETCH_ASSOC)) {
			$id = $new_event_id['ID'];
		}

		// reroute to this page with the new event id
		header("Location: ".full_url($_SERVER)."?id=".$id);
		die();
	}	

    if(isset($_POST['name'])) {
		$stmt = $db->prepare("UPDATE event SET name = :name, time_zone = :time_zone, welcome_message = :welcome_message, visible = :visible, logo = :logo WHERE id = :id");
		
		$name = $_POST['name'];
		$timeZone = $_POST['timezone'];
		$welcomeMessage = $_POST['welcome'];
		$visible = isset($_POST['visible']);
		$id = $_POST["id"];
		$logo = null;

		// If the user specified a logo file
		if(isset($_FILES["logo"]["name"])) {
			
			// The directory to save the file to
			$uploaddir = '../temp/';

			// Get the full path to save the uploaded file to
			$uploadfile = $uploaddir . basename($_FILES['logo']['name']);

			// Try to upload the file
			if(move_uploaded_file($_FILES['logo']['tmp_name'], $uploadfile)) {
				$logo = base64_encode(file_get_contents($uploadfile));
				echo "<p>File succesfully uploaded</p>";
			} else {
				echo "<p>Error uploading file</p>";
			}
		
			// Remove the contents of the temporary directory
			$files = glob($uploaddir); 	// get all file names
			foreach($files as $file) {  // iterate files
				if(is_file($file))
					unlink($file); 		// delete file
			}
		}
		
		$stmt->bindValue(':name', $name);
		$stmt->bindValue(':time_zone', $timeZone);
		$stmt->bindValue(':welcome_message', $welcomeMessage);
		$stmt->bindValue(':id', $id);
		$stmt->bindValue(':visible', $visible);	
		$stmt->bindValue(':logo', $logo);
		$stmt->execute();

		// reroute to this page with the new event id
		header("Location: ".full_url($_SERVER)."?id=".$_POST['id']);
		die();
	}
	
	include("../templates/check-event-exists.php");

	$get_event_stmt = $db->prepare("SELECT name, time_zone, welcome_message, visible FROM event where ID=:id");
	$get_event_stmt->bindValue(":id", $_GET["id"]);
	$get_event_stmt->execute();

	$get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);

	if(count($get_event_res) != 1) {
		die();
	}

	$get_event_res = $get_event_res[0];
?>


<html>
	
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#general {
				background-color: grey;
				color: white;
			}
		</style>
		
		<section id="main">
			<h1>General</h1>
				<form action = "general.php" method = "post" enctype="multipart/form-data" id="form">
					<div class="card">
						<input type="hidden" name="id" value="<?php echo($_GET['id'])?>">
						<div class="input">Event Name:<input type="text" name="name" value="<?php echo $get_event_res["name"] ?>"></div>
						<div class="input">Logo:<input type="file" name="logo" ></div>
						<div class="input">Time Zone:<input type="text" name="timezone" value="<?php echo $get_event_res["time_zone"] ?>"></div>
						<div class="input">Welcome Message:<input type="text" name="welcome" value="<?php echo $get_event_res["welcome_message"] ?>"></div>
						<div class="input">Visible:<input autocomplete="off" type="checkbox" name="visible" value="true" <?php echo ($get_event_res["visible"]) ? "checked" : ""; ?>></div>
					</div>
					<br>
					<div class="btn" id="save">Save</div>
				</form>
		</section>
	</body>

	<?php include("../templates/head.php"); ?>

</html>
