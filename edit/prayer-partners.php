<?php 
	include("../templates/check-event-exists.php");

	include("../connection.php");
	include("../helper.php");
	
	$event_id = getEventId();
    if( isset($_POST['action']) )
	{
		if($_POST['action'] == 'updatePartners') {
			$get_prayer_group_stmt = $db->prepare("SELECT * FROM prayer_partners where event_ID=:id order by sequential_ID asc");
			$get_prayer_group_stmt->bindValue(":id",$event_id);
			$get_prayer_group_stmt->execute();

			$reset_stmt = $db->prepare("UPDATE attendees set prayer_group_ID = null where prayer_group_ID=:prayer_group_ID");

			$stmt = $db->prepare("UPDATE attendees set prayer_group_ID = :prayer_group_ID where event_ID=:event_id and sequential_ID=:sequence");
			while($get_prayer_group_res = $get_prayer_group_stmt->fetch(PDO::FETCH_ASSOC)) {
				$stmt->bindValue(':prayer_group_ID', $get_prayer_group_res["group_ID"]);
				$reset_stmt->bindValue(':prayer_group_ID', $get_prayer_group_res["group_ID"]);
				$reset_stmt->execute();

				foreach($_POST['partner'][$get_prayer_group_res["sequential_ID"]] as $key => $sequence) {				
					$stmt->bindValue(":sequence",$sequence);
					$stmt->bindValue(':event_id', $event_id);
					$stmt->execute();
				}
			}
		}
		else if($_POST['action'] == 'addGroup') {
			$stmt = $db->prepare("INSERT into prayer_partners(event_ID, sequential_ID) values(:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from prayer_partners where event_ID=:event_id) as temp))");
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();

		}
		else if ($_POST['action'] == 'deleteGroup') {
			$stmt = $db->prepare("DELETE from prayer_partners where event_ID=:id and sequential_ID=:sequence");
			$stmt->bindValue(":id",$event_id);
			$stmt->bindValue(":sequence", $_POST['sequence']);
			$stmt->execute();
		}

		header("Location: ".full_url($_SERVER)."?id=".$_POST['id']);
		die();
	}
	include("../templates/check-event-exists.php");

?>

<html>
	
	<body>
		<?php include("../templates/left-nav.php"); ?>

		<style>
			#prayer-partners {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Prayer Partners</h1>
			<form id="form" action = "prayer-partners.php" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "updatePartners">
				<div id="sectionCards">
					<?php			
						$id = $_GET["id"];
						$get_prayer_group_stmt = $db->prepare("SELECT * FROM prayer_partners where event_ID=:id order by sequential_ID asc");
						$get_prayer_group_stmt->bindValue(":id",$event_id);
						$get_prayer_group_stmt->execute();

						while($get_prayer_group_res = $get_prayer_group_stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<div class="card">';
							echo '<div class="btn" onclick="deleteGroup('.$get_prayer_group_res["sequential_ID"].')">X</div>';
							echo '<div class="input">Partners:';
							echo '<div id="partners[' . $get_prayer_group_res['sequential_ID'] . ']">';

							$get_info_prayer_res = $db->prepare("SELECT * FROM attendees where event_ID=:id and prayer_group_ID = :prayer_ID");
							$get_info_prayer_res->bindValue(":id", $event_id);
							$get_info_prayer_res->bindValue(":prayer_ID", $get_prayer_group_res["group_ID"]);
							$get_info_prayer_res->execute();

							while($get_attendees_house_res = $get_info_prayer_res->fetch(PDO::FETCH_ASSOC)) {
								echo '<select id="partner[' . $get_prayer_group_res['sequential_ID'] . ']['.$get_attendees_house_res['sequential_ID'].']">';
								
								$get_attendees_stmt = $db->prepare("SELECT * FROM attendees where event_ID=:id");
								$get_attendees_stmt->bindValue(":id", $event_id);
								$get_attendees_stmt->execute();

								while($get_attendees_res = $get_attendees_stmt->fetch(PDO::FETCH_ASSOC)) {
									echo '<option value="'.$get_attendees_res["sequential_ID"].'">' . $get_attendees_res['name'] . '</option>';
								}

								echo '</select>';
								echo '<div class="btn" onclick="deletePartner('.$get_prayer_group_res['sequential_ID'].','.$get_attendees_house_res['sequential_ID'].')">X</div>';
							}		
							echo '<div class="btn" onclick="addPartner(' . $get_prayer_group_res['sequential_ID'] . ')">Add Partner</div>';
							echo '</div>';
							echo '</div>';
							echo '</div>';
						}
					?>
				</div></div>
				<div class="btn" onclick="addGroup()">Add Group</div>
				<div class="btn" id="save">Save</div>
			</form>
		</section>

		<form id = "addGroup" action = "prayer-partners.php" method="post">	
			<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
			<input type="hidden" name="action" value = "addGroup">
		</form>

		<form id="deleteGroup" action="prayer-partners.php" method="post">
			<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type = "hidden" name="action" value="deleteGroup">
			<input type = "hidden" name="sequence" value="">
		</form>

	</body>
	<?php include("../templates/head.php"); ?>
	<script>

		function addGroup() {
			$("#addGroup").submit();
		}

		function deleteGroup(sequential_id) {
			$('#deleteGroup > input[name="sequence"]').val(sequential_id);
			$("#deleteGroup").submit();
		}

		function addPartner(num) {
			var html = '<select id="partner[]"><?php
					
				$get_attendees_stmt = $db->prepare("SELECT * FROM attendees where event_ID=:id");
				$get_attendees_stmt->bindValue(":id", $event_id);
				$get_attendees_stmt->execute();

				while($get_attendees_res = $get_attendees_stmt->fetch(PDO::FETCH_ASSOC)) {
					echo '<option value="'.$get_attendees_res["sequential_ID"].'">' . $get_attendees_res['name'] . '</option>';
				}
				?></select>';

			addFields(html, 'partners\\[' + num + '\\]');
		}
	</script>

</html>
