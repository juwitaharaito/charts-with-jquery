<?php
	$odbc = odbc_connect("Driver={PostgreSQL Unicode(x64)};Server=192.168.2.10;Port=5444;Database=i4_BBC;", "admin", "");
	$qry1 = "SELECT * FROM tbl_user";
	$res2 = odbc_exec($odbc, $qry1);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<!-- Include meta tag to ensure proper rendering and touch zooming-->
		<title>Grafik seluruh Sales</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../../jquery/jquery-ui.min.css">
		<link rel="stylesheet" href="../../jquery-mobile/jquery.mobile-1.4.5.min.css">
		<link rel="stylesheet" href="../../jquery-mobile-datepicker/jquery.mobile.datepicker.css">
		<link rel="stylesheet" href="../../jquery-mobile-datepicker/jquery.mobile.datepicker.theme.css">
		<style type="text/css">
		#ui-datepicker-div { z-index: 9999 !important; }
		.ui-datepicker-calendar { display: none; }
		</style>
		<script src="../../jquery/external/jquery/jquery.js"></script>
		<script src="../../jquery/jquery-ui.min.js"></script>
		<script src="../../jquery-mobile/jquery.mobile-1.4.5.min.js"></script>
		<script src="../../jquery-mobile-datepicker/jquery.mobile.datepicker.js"></script>
		<script>
		$(window).load(function() {
			$.mobile.ajaxEnabled = false;
		
		});
		
		var categories = [];
		<?php
				$today = date('m-Y');
				$json = json_decode(file_get_contents("kategori3.json"));
				$in=0; $indexbulan = -1;
				foreach($json->kategori[0]->bulantahun as $item)
				{
					if($item->bulan == $today)
					{
						$indexbulan = $in;
						break;
					}
					$in+=1;
				}

				foreach($json->kategori as $mydata)
				{	$cat_id = $mydata->id;
					$cat_name = $mydata->name;
					$bulantahun =$mydata->bulantahun;
					?> 
					var bulantahun_arr = [];  
					<?php
					foreach($mydata->bulantahun as $bt)
					{
						$bulan = $bt->bulan;
						$targets = $bt->target;
						?>
						var bulan = "<?php echo $bulan; ?>";
						var targets_arr = [];
						<?php
						foreach ($bt->target as $targets)
						{
							$nama = $targets->nama;
							$target = $targets->target;
							?>
							var nama = "<?php echo $nama; ?>";
							var target = "<?php echo $target; ?>";
							var targets = {};
							targets["nama"]=nama;
							targets["target"]=target;
							targets_arr.push(targets);
							<?php
						}		
						?>
						var bulantahun = {};
						bulantahun["bulan"] = bulan;
						bulantahun["target"] = targets_arr;
						bulantahun_arr.push(bulantahun);
						<?php
					}
					?> 
					
					var category = {};
					category["id"]= "<?php echo $cat_id; ?>";
					category["name"] = "<?php echo $cat_name; ?>";
					category["bulantahun"] = bulantahun_arr;
					categories.push(category); 
					
					<?php
				} 
					?>
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	</head>
	
	<body>
	<form name= "myform" action="graphs.php" method="get">
	<div class="container" style="width:900px;">  
		<?php

			?><select class="form-control" name="kategori" id="kategori" >
			<?php foreach($json->kategori as $mydata)
			{
				echo "<option value='".$mydata->id."'>".$mydata->name ."</option>";
            } 
			?>
				<option value="tambah">tambah kategori</option>
				</select>
		
		<div ><input type="text" name="from_date" id="from_date" class="date-picker" value="<?php echo $today?>"/></div>
		
		<br/>
		</div>
		<div class="container" style="width:900px; "> 
		<div id ="target"> <fieldset>
			<?php
				$i=0;
				echo "<label>Global</label>";
			?>
			<input type="hidden" id="teks0" name="teks0" value="Global">
			<input type="text" id="text0" name="text0" value="<?php if ($indexbulan >= 0) echo number_format($json->kategori[0]->bulantahun[$indexbulan]->target[0]->target);  else echo "0"?>">
			</fieldset>
			<?php

			while(odbc_fetch_row($res2))
				{
					$i+=1;
					$nama = odbc_result($res2, "nama");
					echo "<label>".$nama."</label>";
					
					
					
					?>
					<input type="hidden" id="teks<?php echo $i ?>" name="teks<?php echo $i ?>" value="<?php echo $nama;?>">
					<input type="text" id="text<?php echo $i ?>" name="text<?php echo $i ?>" value="<?php if ($indexbulan >= 0) {echo number_format($json->kategori[0]->bulantahun[$indexbulan]->target[$i]->target);} else echo "0"?>">
					<?php
				}
			$jumlahtarget = $i;
					odbc_close($odbc);?>
		</div>
		<div id="updatejson" style="display:none"></div>
		<div class="col-md-3"><input type="button" name="update" id="update" value="Update" class="btn btn-info" /> </div>
		<div class="col-md-3"><input type="submit" name="filter" id="filter" value="Lihat Grafik" class="btn btn-info"  onclick="return validate();" /> </div>
	</div>
	</form>
</body>
</html>


<script type="text/javascript">
function numberWithDots(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
	function validate()
{
	<?php 
		for ($a=0; $a<=$jumlahtarget; $a++)
		{?>
			var number = $('#text<?php echo $a ?>').val();
			var cnumber = parseFloat(numberWithDots(number).split(',').join(''));;
			if(isNaN(cnumber))
			{
			alert("data harus berupa angka");
			return false;
			}
	<?php 	}
		?>
   return true;
 }

	function upDate(){
			var kategori = $('#kategori').val();
			var from_date = $('#from_date').val();
			var description = new Array(<?php echo $jumlahtarget; ?>)
			<?php 
					for ($a=0; $a<=$jumlahtarget; $a++)
					{?>
					var namauser<?php echo $a; ?> = $('#teks<?php echo $a ?>').val();
					<?php }
					?>
			for (var i = 0; i<categories.length; i++)
			{
				if	(categories[i].id == kategori)
				{
					for (var j = 0; j<categories[i].bulantahun.length; j++)
					{
						if (categories[i].bulantahun[j].bulan==from_date)
						{
							<?php
								for ($a=0; $a<=$jumlahtarget; $a++)
								{?>
									for (var k = 0; k<categories[i].bulantahun[j].target.length; k++)
									{
										if (namauser<?php echo $a; ?> == categories[i].bulantahun[j].target[k].nama) 
										{
											description[<?php echo $a; ?>] = categories[i].bulantahun[j].target[k].target;
											break;
										}
									}
							<?php }
								?>
							break;
						}
					}
					break;
				}
			}
		  <?php
			for ($a=0; $a<=$jumlahtarget; $a++)
			{?>
				if (typeof(description[<?php echo $a; ?>] )=='undefined') {
					document.myform.text<?php echo $a; ?>.value = "0";
				}
				else
				document.myform.text<?php echo $a; ?>.value = numberWithDots(description[<?php echo $a; ?>]);
			<?php }
					?>
		}
    $(function() {
		
        $('.date-picker').datepicker( {
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'mm-yy',
			//untuk memunculkan bulan dan tahun di datepicker
            onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
			var kategori = $('#kategori').val();
			var from_date = $('#from_date').val(); 
			upDate();
			}
		});
		$('.date-picker').focus(function () {
			$(".ui-datepicker-calendar").hide();
			$("#ui-datepicker-div").position({
				my: "left top",
				at: "left bottom",
				of: $(this)
			});
		});
		
		$('#kategori').change(function(){
				var kategori = $('#kategori').val();
				//alert(kategori);
				var from_date = $('#from_date').val(); 
				//alert (from_date);
				if (kategori != 'tambah')
				{
					if(from_date != '' )  
					{  upDate();}
					else 
						alert("Please Select Date");  }
				else window.open('tambahkategori.php','_self'); 
		});

		$('#update').click(function(){
					var kategori = $('#kategori').val();
					var from_date = $('#from_date').val(); 
					var jumlah = <?php echo $jumlahtarget; ?>;
					var number = true;
					<?php 
					for ($a=0; $a<=$jumlahtarget; $a++)
					{?>
					var str = $('#text<?php echo $a ?>').val();
					var tg<?php echo $a; ?> = parseFloat(str.split(',').join(''));
					var namauser<?php echo $a; ?> = $('#teks<?php echo $a ?>').val();
					if (tg<?php echo $a; ?> == '')
					tg<?php echo $a; ?> = 0;
					else if (isNaN(tg<?php echo $a; ?>))
						number = false;
					<?php }
					?>
					
					alert($('#text0').val());

					if(from_date != '' )  
					{
						if (number)
						{
							if (confirm("are you sure?"))
							{
							$.ajax({
							url:"updatejson2.php",
							data:{"from_date":from_date, 
							<?php 
							for ($a=0; $a<=$jumlahtarget; $a++)
							{?>
							"tg<?php echo $a; ?>":tg<?php echo $a; ?>,
							"namauser<?php echo $a; ?>":namauser<?php echo $a; ?>,
							<?php }
							?>"kategori":kategori,"jumlah":jumlah},
								type:"POST",
								success:function(data)
								{
										$('#updatejson').html(data);
										alert("data berhasil diupdate");
										window.location.reload(true);
								}
							})
							}
						}
						else
							alert("data harus berupa angka");
					}
					else 
						alert("Please Select Date");
		});
    });
</script>
