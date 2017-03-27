<?php
	$odbc = odbc_connect("Driver={PostgreSQL Unicode(x64)};Server=192.168.2.10;Port=5444;Database=i4_BBC;", "admin", "");
	$qry1 = "SELECT * FROM tbl_user";
	$res2 = odbc_exec($odbc, $qry1);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<!-- Include meta tag to ensure proper rendering and touch zooming-->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../../jquery/jquery-ui.min.css">
		<link rel="stylesheet" href="../../jquery-mobile/jquery.mobile-1.4.5.min.css">
		<script src="../../jquery/external/jquery/jquery.js"></script>
		<script src="../../jquery/jquery-ui.min.js"></script>
		<script src="../../jquery-mobile/jquery.mobile-1.4.5.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	</head>
	<body>
	<div class="container" style="width:900px;">  
		<?php
			$json = json_decode(file_get_contents("kategori3.json"));
			?>
			<div ><input type="text" name="newcat" id="newcat" placeholder="Enter New Category" /></div>
			<div class="col-md-3"><input type="button" name="add" id="add" value="Add" class="btn btn-info" /> </div>
			<div id="updatejson" style="display:none"></div>
	</div>
	</body>
</html>
<script>
 $(function() {
		$('#add').click(function(){
					var namakategori = $('#newcat').val();
					var ada = false;
					if (namakategori != '')
					{
						<?php
							foreach($json->kategori as $mydata)
							{ ?>
								if (namakategori == "<?php echo $mydata->name ?>")
								{	ada = true;
								}
							<?php
							}
							?>
						if (ada)
						{
							alert ("Kategori sudah ada");
						}
						else
						{
							$.ajax({
							url:"updatejson3.php",
							data:{"namakategori":namakategori},
							type:"POST",
							success:function(data)
							{
								$('#updatejson').html(data);
								alert("Kategori berhasil ditambahkan");
								window.open('tesjson.php','_self'); 
							}
							})
									
						}
							
					}
					else
						alert("Masukkan nama kategori");
					
		});
    });

    </script>
