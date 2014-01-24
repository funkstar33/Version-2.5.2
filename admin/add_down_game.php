<?php
function GetFileExtension($filepath) {
    preg_match('/[^?]*/', $filepath, $matches);
    $string = $matches[0];
    $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
    // check if there is any extension
    if(count($pattern) == 1) {
        echo 'No File Extension Present '.$filepath;
        exit;
    }       
    if(count($pattern) > 1) {
        $filenamepart = $pattern[count($pattern)-1][0];
        preg_match('/[^?]*/', $filenamepart, $matches);
        return $matches[0];
    }
}
?>
<div id="center-column">
<div class="top-bar">
				<a href="index.php?act=uploadgames" title="<?php $translate->__('Upload Games');?>" class="button"><?php $translate->__('ADD GAME');?></a>
				<h1><?php $translate->__('Cpanel - Download Game Section');?></h1>
				<div class="breadcrumbs"><a href="index.php?act=addmedia" title="<?php $translate->__('Add Media');?>"><?php $translate->__('Add Media');?></a> / <a href="index.php?act=addcode" title="<?php $translate->__('Add Code');?>"><?php $translate->__('Add Code');?></a> / <a href="index.php?act=uploadgames" title="<?php $translate->__('Upload Games');?>"><?php $translate->__('Upload Games');?></a> / <a href="index.php?act=brokenfiles" title="<?php $translate->__('Broken Files');?>"><?php $translate->__('Broken Files');?></a></div>
			</div><br />
		  <div class="select-bar">
		    <label>
		    <h3><?php $translate->__('Add Game To Download Section');?></h3>
		    </label>
		  </div>
		  <center>
<?php
if (isset($_POST['upload']) && $_POST['upload']=="1") {
	$num = rand(0, pow(10, 5)) . '-'; // 5 digit random number to prefix game name
	$filecompress = $setting['sitepath'].'/ourgames/games/'.$num.str_replace(GetFileExtension($_FILES['file']['name'][1]), 'zip', $_FILES['file']['name'][1]);
	$file_desc = 'Title: '. $_POST["gamename"]. "\r\n\n" . 'Description: ' . $_POST["description"]. "\r\n\n" . 'Keywords: ' . $_POST["keywords"];
	$zip = new ZipArchive();
	$compress = $zip->open($filecompress, ZIPARCHIVE::CREATE);
	if ($compress === true)	{
		$zip->addFile($_FILES['file']['tmp_name'][0], $num.$_FILES['file']['name'][0]);
		$zip->addFile($_FILES['file']['tmp_name'][1], $num.$_FILES['file']['name'][1]);
		$zip->addFromString('description.txt', $file_desc);
		$zip->close();
		$translate->__('Zipped successfully');
	} else $translate->__('Zip failed');

	copy($_FILES['file']['tmp_name'][0], $setting['sitepath'] . "/ourgames/img/" . $_FILES['file']['name'][0]);
	$gamename = yasDB_clean($_POST["gamename"]);
	$gamedescript = yasDB_clean($_POST["description"]);
	$thumbpath = yasDB_clean("../ourgames/img/" . $_FILES['file']['name'][0]);
	$gamefilepath = yasDB_clean('../ourgames/games/'. $num . str_replace(GetFileExtension($_FILES['file']['name'][1]), 'zip', $_FILES['file']['name'][1]));
	$mochilink = yasDB_clean($_POST['mochigame']);
	$query = yasDB_insert("INSERT INTO downgames (title, description, thumbnail, file, mochi) VALUES ('$gamename', '$gamedescript', '$thumbpath', '$gamefilepath', '$mochilink')",false);
	if (!$query){
		echo("Database Error!");
	} else	{
		echo("<center>Files uploaded succesfuly!</center>");
		echo('<br/><center><a href="index.php?act=managedowngame">Click here to to go back</a></center>');
	}
	if (isset($_POST['notify'])){
		$select_emails = yasDB_select("SELECT * FROM notifydown ORDER BY `id` ",false);
		while($emails = $select_emails->fetch_array(MYSQLI_ASSOC)) {
			$message = 'New game for your website available for download at '.$setting['siteurl']."\r\n";
			$message .= 'Click here to go to the download section: '.$setting['siteurl'].'/index.php?act=download'."\r\n";
			$message .= 'Name of the game: '.$gamename."\r\n";
			$message .= 'Description: '.$gamedescript."\r\n";
			$message .= 'To unsubscribe from the notification list please visit this link: '.$setting['siteurl'].'index.php?act=unsubscribe&id='.$emails['id'];
			$headers = 'From: '.$setting['sitename'].' <'.$setting['sitename'].'>';
			$subject = 'New game for your website available to download at '.$setting['siteurl'];
			@mail($emails['email'], $subject, $message, $headers);
		}
		echo ('<br /><center>Your game was sent to the subscribed emails too.</center>');
	}
} else {
	?>
	<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="upload" value="1" />
	<?php $translate->__('Name of the game:');?>
	<br />
	<input type="text" name="gamename" maxlength="30" width="300" />
	<br /><br />
	<?php $translate->__('Game description:');?>
	<br />
	<textarea name="description" rows="7" cols="50"></textarea>
	<br /><br />
	<?php $translate->__('Keywords:');?>
	<br />
	<textarea name="keywords" rows="7" cols="50"></textarea>
	<br /><br />
	<?php $translate->__('Select Game thumbnail:');?><br />
	<input type="file" name="file[]" />
	<br /><br />
	<?php $translate->__('Select Game file:');?><br />
	<input type="file" name="file[]" />
	<br /><br />
	<?php $translate->__('*Link to the Mochi version of the game:');?>
	<br />
	<input type="text" name="mochigame" maxlength="300" width="300" />
	<br /><br />
	<?php $translate->__('Send notification to the subscribed emails about the new game:');?>
	<br />
	<input type="checkbox" name="notify" value="notify" /><?php $translate->__('Checked = Yes | Unchecked = No');?> 
	<br /><br />
	<input type="submit" class="button" value="Submit" />
	</form>
	<?php
}
?>
</center>
</div>