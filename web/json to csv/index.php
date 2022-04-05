
<?php if($_GET['msg']){
	echo "<b style='color:red'>{$_GET['msg']}</b>";
}?>
<form action="convert.php" method="post" enctype="multipart/form-data">
	  Upload zip file
	  <input type="file" name="fileToUpload" id="fileToUpload">
	  <input type="submit" value="Upload" name="submit">
	</form>