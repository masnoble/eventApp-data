<?php	
	include("../connection.php");
    if( isset($_POST['name']) )
	{
		foreach($_POST['name'] as $key => $name) {
			if (!($stmt = $db->prepare("UPDATE contacts SET name = :name, address = :address, phone = :phone  WHERE id = :id"))) {
				die(0);
			}
			
			$address = $_POST['address'][$key];
			$welcomeMessage = $_POST['phone'][$key];
			$id = $_POST["id"];
			
			if (!($stmt->bindValue(':name', $name))) {
				die(1);
			}
			if (!($stmt->bindValue(':address', $address))) {
				die(2);
			}
			if (!($stmt->bindValue(':phone', $phone))) {
				die(3);
			}
		}
	}
?>

<html>
	
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#contacts {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Contacts</h1>
			<form id="form" method="post">
				<div id="contactCards">
				</div>
				<div class="btn" onclick="addContact()">+ Add Contact</div>
				<div class="btn" id="save">Save</div>
			</form>
		</section>

	</body>
	<?php include("../templates/head.php"); ?>
	<script>
		
		$(document).ready(function() {
			addContact();
		});

		function addContact() {
			var html = '<div class="card"><div class="input">Name: <input type="text" name="name"></div>'
						+ '<div class="input">Address: <input type="text" name="address"></div>'
						+ '<div class="input">Phone: <input type="text" name="phone"></div>';
			addFields(html, 'contactCards');
			counter++;
		}
	</script>
</html>

