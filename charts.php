<?php 
 if(isset($_GET["from_date"]) && isset($_GET["kategori"]))  
 {  
	$odbc = odbc_connect("Driver={PostgreSQL Unicode(x64)};Server=192.168.2.10;Port=5444;Database=i4_BBC;", "admin", "");  
	$bulan = $_GET["from_date"];
	$target = $_GET["kategori"];
	$date = DateTime::createFromFormat('d-m-Y', ('1-'.$bulan));
	$dateawal1 = $date->format('Y-m-d'); 
	$dateakhir1 = date("Y-m-t", strtotime($dateawal1));
	$dateawal = $dateawal1." 00:00:00";
	$dateakhir = $dateakhir1." 23:59:59";
?>
<!DOCTYPE html>  
 <html>  
 <head>
 <title> Menampilkan grafik profit </title>
		<!-- Include meta tag to ensure proper rendering and touch zooming-->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="refresh" content="1800" >
		<link rel="stylesheet" href="../../jquery/jquery-ui.min.css">
		<link rel="stylesheet" href="../../jquery-mobile/jquery.mobile-1.4.5.min.css">
		<link rel="stylesheet" href="../../jquery-mobile-datepicker/jquery.mobile.datepicker.css">
		<link rel="stylesheet" href="../../jquery-mobile-datepicker/jquery.mobile.datepicker.theme.css">
		<style type="text/css">
		#box {
			align:center;
			height:570px;
			background:000;
			overflow-x:hidden;
			overflow-y:scroll;
			white-space:nowrap;
			
		}
		#ui-datepicker-div { z-index: 9999 !important; }
		.ui-datepicker-calendar { display: none; }
		</style>
		<script src="../../jquery/external/jquery/jquery.js"></script>
		<script src="../../jquery/jquery-ui.min.js"></script>
		<script src="../../jquery-mobile/jquery.mobile-1.4.5.min.js"></script>
		<script src="../../jquery-mobile-datepicker/jquery.mobile.datepicker.js"></script>
		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
		<script>
		$(window).load(function() {
			$.mobile.ajaxEnabled = false;
		
		});
		$(document).ready(function() { generatetable();});
		</script>
		

 </head>
 <body>
 <div style="height:50px";>
<h4> PROFIT PENJUALAN BULAN <br> <?php echo $bulan;?></h4>
 <?php
		$jumlahhari = ((abs(strtotime ($dateawal1) - strtotime ($dateakhir1)))/(60*60*24))+1;
		$sehari =  date('Y-m-d', strtotime($dateawal. ' - 1 days')); //sehari sebelum
		$n=10;
		$x = floor($jumlahhari/$n);
		$y = $jumlahhari%$n;
		if ($x==0)
			$n = $y; //kalau jumlah<10 titiknya cuma y

		for ($i= 0; $i < $n; $i++)
		{
			if ( $i < $y)
				$jumlah = $x+1; //sampai titik kesekian rentang harinya x+1
			else 
				$jumlah = $x;
			
			
			if ($i>0)
				$batas[$i] = date('Y-m-d', strtotime($batas[$i-1]. ' + '.$jumlah.' days'));
			else
				$batas[$i] = date('Y-m-d', strtotime($sehari. ' + '.$jumlah.' days'));  //titik pertama dihitung dari sehari sebelum tanggal awal
			$tanggalaja[$i] =	 date ('j', strtotime($batas[$i]));
		}

		
	?>
	<div id="box">
	<table>
	<?php

		$qry1 = "SELECT * FROM tbl_user";
		$res1 = odbc_exec($odbc, $qry1);
		$x = 0;
		$untung[$x][0] = 0;
		$untung1[$x][0] = 0;
		$untung2[$x][0] = 0;
			$query1 = " select

						tbl_ikhd.notransaksi as nama,
						tbl_ikhd.tanggal,
						tbl_ikdt.kodeitem as kodeitem,
						tbl_item_ik.jumlahdasar as jumlah,
						tbl_item_ik.jumlahdasar*tbl_ikdt.harga as harga_jual,
						tbl_item_ik.jumlahdasar*tbl_item_im.hargadasar as harga_beli,
						(tbl_item_ik.jumlahdasar*tbl_ikdt.harga)-(tbl_item_ik.jumlahdasar*tbl_item_im.hargadasar) as profit
					from
						tbl_ikhd
						
						LEFT JOIN tbl_user
						ON tbl_ikhd.user1 = tbl_user.userid
						LEFT JOIN tbl_ikdt
						ON
						tbl_ikhd.notransaksi = tbl_ikdt.notransaksi
				
						LEFT JOIN tbl_item_ik
						ON tbl_item_ik.iddetailtrs = tbl_ikdt.iddetail
						LEFT JOIN tbl_item_im
						ON 
						tbl_item_im.iddetail = tbl_item_ik.iddetailim
						
					where tbl_ikhd.tipe = 'JL' AND tbl_ikhd.tanggal >= '".$dateawal."' and tbl_ikhd.tanggal <= '".$dateakhir."'
					order by tbl_ikhd.tanggal asc";
			$result1 = odbc_exec($odbc, $query1);
			$query2 = "	SELECT 
						tbl_ikhd.user1,
						a.tanggal,
						a.notransaksi,
						a.kodeitem,
						a.jumlah,
						a.hargajual,
						a.hargabeli,
						a.profit
						
					FROM (
						select
						
						tbl_ikhd.notransaksi,
						tbl_ikhd.tanggal,
						tbl_ikdt.kodeitem as kodeitem,
						tbl_item_im.jumlahdasar as jumlah,
						tbl_ikdt.harga as hargajual,
						tbl_item_im.hargadasar as hargabeli,
						tbl_item_im.jumlahdasar*(tbl_ikdt.harga-tbl_item_im.hargadasar) as profit,
						tbl_ikhd.notrsretur

					from
						tbl_ikhd
						
						LEFT JOIN tbl_ikdt
						ON
						tbl_ikhd.notransaksi = tbl_ikdt.notransaksi

						LEFT JOIN tbl_item_im
						ON 
						tbl_item_im.notransaksi = tbl_ikhd.notransaksi
						and tbl_item_im.kodeitem = tbl_ikdt.kodeitem
					where tbl_ikhd.tipe = 'RJ' and tbl_item_im.tipe = 'RJ' AND tbl_ikhd.tanggal >= '".$dateawal."' and tbl_ikhd.tanggal <= '".$dateakhir."'order by tbl_ikhd.tanggal asc
					) a
					LEFT JOIN tbl_ikhd
					ON tbl_ikhd.notransaksi = a.notrsretur";
			$result2 = odbc_exec($odbc, $query2);
		?>
			<tr><td><div id="container<?php echo $x;?>"  style="width:430px; height:270px;border: 1px solid #ccc">
			
			<?php
			$nama[0]="Global";
			$i = 0;
			while($row = odbc_fetch_row($result1))  
			{
				$tanggal = odbc_result($result1,"tanggal");
				$profit = odbc_result($result1,"profit");

				if ($tanggal<= $batas[$i]." 23:23:59")
					$untung1[$x][$i] = $untung1[$x][$i] + $profit;
				else if ($i<$n-1)
				{
					while ($tanggal > $batas[$i]." 23:23:59" AND $i<$n-1)
					{
						$i+=1;
						$untung1[$x][$i] = $untung1[$x][$i-1];
					}
						$untung1[$x][$i] = $untung1[$x][$i] + $profit;
				}
			}
			$j = 0;
			while($row = odbc_fetch_row($result2))  
			{
				$tanggal = odbc_result($result2,"tanggal");
				$profit = odbc_result($result2,"profit");

				if ($tanggal<= $batas[$j]." 23:23:59")
				{	$untung2[$x][$j] = $untung2[$x][$j] + $profit;
				//echo "batas ke ".$j."tanggal = ".$tanggal.", rugi =".$untung2[$j]."<BR>";
				}
				else if ($j<$n-1)
				{
					while ($tanggal > $batas[$j]." 23:23:59" AND $j<$n-1)
					{
						$j+=1;
						$untung2[$x][$j] = $untung2[$x][$j-1];
					}
						$untung2[$x][$j] = $untung2[$x][$j]+ $profit;
						//echo "batas ke ".$j."tanggal = ".$tanggal.", rugi =".$untung2[$j]."<BR>";
				}
			}
			
			//untuk memasukkan semua nilai profit ke array
			while ($i < $n-1)
			{
				$i+=1;
				$untung1[$x][$i] = $untung1[$x][$i-1];
			}
			while ($j < $n-1)
			{
				$j+=1;
				$untung2[$x][$j] = $untung2[$x][$j-1];
			}
			$arr = array();
			
			
			$temp[$x] = 0;
			echo $temp[$x]."<BR>";
			for ($i= 0; $i < $n; $i++)
			{
				$untung[$x][$i] = $untung1[$x][$i]-$untung2[$x][$i];
				if ($temp[$x]<$untung[$x][$i])
					$temp[$x] = $untung[$x][$i];
			}
		
				$x+=1;	
				echo "</div></td>";

		while(odbc_fetch_row($res1))
		{
			$userid = odbc_result($res1, "userid");
			$nama[$x] = odbc_result($res1, "nama");
			
			$untung[$x][0] = 0;
			$query3 = " select

						tbl_ikhd.notransaksi as nama,
						tbl_ikhd.tanggal,
						tbl_ikdt.kodeitem as kodeitem,
						tbl_item_ik.jumlahdasar as jumlah,
						tbl_item_ik.jumlahdasar*tbl_ikdt.harga as harga_jual,
						tbl_item_ik.jumlahdasar*tbl_item_im.hargadasar as harga_beli,
						(tbl_item_ik.jumlahdasar*tbl_ikdt.harga)-(tbl_item_ik.jumlahdasar*tbl_item_im.hargadasar) as profit
					from
						tbl_ikhd
						
						LEFT JOIN tbl_user
						ON tbl_ikhd.user1 = tbl_user.userid
						LEFT JOIN tbl_ikdt
						ON
						tbl_ikhd.notransaksi = tbl_ikdt.notransaksi
				
						LEFT JOIN tbl_item_ik
						ON tbl_item_ik.iddetailtrs = tbl_ikdt.iddetail
						LEFT JOIN tbl_item_im
						ON 
						tbl_item_im.iddetail = tbl_item_ik.iddetailim
						
					where tbl_ikhd.tipe = 'JL' and tbl_ikhd.user1 = '".$userid."' AND tbl_ikhd.tanggal >= '".$dateawal."' and tbl_ikhd.tanggal <= '".$dateakhir."'
					order by tbl_ikhd.tanggal asc";
			$result3 = odbc_exec($odbc, $query3);
			$query4 = "	SELECT 
							tbl_ikhd.user1,
							a.tanggal,
							a.notransaksi,
							a.kodeitem,
							a.jumlah,
							a.hargajual,
							a.hargabeli,
							a.profit
							
						FROM (
							select
							
							tbl_ikhd.notransaksi,
							tbl_ikhd.tanggal,
							tbl_ikdt.kodeitem as kodeitem,
							tbl_item_im.jumlahdasar as jumlah,
							tbl_ikdt.harga as hargajual,
							tbl_item_im.hargadasar as hargabeli,
							tbl_item_im.jumlahdasar*(tbl_ikdt.harga-tbl_item_im.hargadasar) as profit,
							tbl_ikhd.notrsretur

						from
							tbl_ikhd
							
							LEFT JOIN tbl_ikdt
							ON
							tbl_ikhd.notransaksi = tbl_ikdt.notransaksi

							LEFT JOIN tbl_item_im
							ON 
							tbl_item_im.notransaksi = tbl_ikhd.notransaksi
							and tbl_item_im.kodeitem = tbl_ikdt.kodeitem
						where tbl_ikhd.tipe = 'RJ' and tbl_item_im.tipe = 'RJ' AND tbl_ikhd.tanggal >= '".$dateawal."' and tbl_ikhd.tanggal <= '".$dateakhir."'order by tbl_ikhd.tanggal asc
						) a
						LEFT JOIN tbl_ikhd
						ON tbl_ikhd.notransaksi = a.notrsretur
						where tbl_ikhd.user1 = '".$userid."'";
			$result4 = odbc_exec($odbc, $query4);
			if ($x%3 ==0)
			echo "<tr>";
			?>
			<td><div id="container<?php echo $x;?>"  style="width:430px; height:270px;border: 1px solid #ccc">
			<?php
			$i = 0;
			while($row = odbc_fetch_row($result3))  
			{
				$tanggal = odbc_result($result3,"tanggal");
				$profit = odbc_result($result3,"profit");

				if ($tanggal<= $batas[$i]." 23:23:59")
					$untung1[$x][$i] = $untung1[$x][$i] + $profit;
				else if ($i<$n-1)
				{
					while ($tanggal > $batas[$i]." 23:23:59" AND $i<$n-1)
					{
						$i+=1;
						$untung1[$x][$i] = $untung1[$x][$i-1];
					}
						$untung1[$x][$i] = $untung1[$x][$i] + $profit;
				}
			}
			$j = 0;
			while($row = odbc_fetch_row($result4))  
			{
				$tanggal = odbc_result($result4,"tanggal");
				$profit = odbc_result($result4,"profit");

				if ($tanggal<= $batas[$j]." 23:23:59")
				{	$untung2[$x][$j] = $untung2[$x][$j] + $profit;
				//echo "batas ke ".$j."tanggal = ".$tanggal.", rugi =".$untung2[$j]."<BR>";
				}
				else if ($j<$n-1)
				{
					while ($tanggal > $batas[$j]." 23:23:59" AND $j<$n-1)
					{
						$j+=1;
						$untung2[$x][$j] = $untung2[$x][$j-1];
					}
						$untung2[$x][$j] = $untung2[$x][$j]+ $profit;
						//echo "batas ke ".$j."tanggal = ".$tanggal.", rugi =".$untung2[$j]."<BR>";
				}
			}
			
			//untuk memasukkan semua nilai profit ke array
			
			while ($i < $n-1)
			{
				$i+=1;
				$untung1[$x][$i] = $untung1[$x][$i-1];
			}
			while ($j < $n-1)
			{
				$j+=1;
				$untung2[$x][$j] = $untung2[$x][$j-1];
			}
			
			$temp[$x] = 0;
			for ($i= 0; $i < $n; $i++)
			{
				$untung[$x][$i] = $untung1[$x][$i]-$untung2[$x][$i];
				if ($temp[$x]<$untung[$x][$i])
					$temp[$x] = $untung[$x][$i];
			}
			echo "<BR>".$temp[$x]."<BR>";
				$x+=1;	
			?></div></td>
			
			<?php
			if ($x%3 ==0)
			echo "</tr>";
		}
		odbc_free_result($res1);
		odbc_close($odbc);
		?>
	</table></div>

<a href="index.php" class="btn btn-default">Back</a>
 </body>  
  </html>  
    <?php  
                     } else {
echo "gagal";
					 }					 
                     ?>  
<script>
var $div = $('#box');
var startTime = new Date().valueOf();
var timePassed;
	$div.animate({ scrollTop: ($('#box')[0].scrollHeight)-$div.height()}, 10000);
	$div.hover(function() { //mouseenter
        $div.stop(true, false);
        timePassed = (new Date()).valueOf() - startTime;
    }, function() { //mouseleave
		var position = $div.scrollTop();
		//alert (($('#box')[0].scrollHeight)-$div.height()-position);
        $div.animate({ scrollTop: ($('#box')[0].scrollHeight)-$div.height()-position}, 10000);
		
    });


function generatetable(){

<?php
for ($jumcont = 0; $jumcont<$x; $jumcont++)
{?>
var name = "container<?php echo $jumcont; ?>";


Highcharts.chart('container<?php echo $jumcont; ?>', {
				<?php
					$max = floatval(str_replace(',','',$_GET["text".$jumcont]));
					if ($max == null)
					$max = 0;
					if ($temp[$jumcont]>$max)
					{
						$max = 1.1*$temp[$jumcont];
					}
					?>

				title: {
					text: 'Profit <?php echo $nama[$jumcont]; ?>'
				},
				subtitle: {
					text: 'Profit/Target : Rp. <?php echo number_format($untung[$jumcont][$n-1])?> / Rp. <?php echo number_format($max)?>'
				},
				xAxis: {
					title: {
						text: 'Tanggal'
					},
					categories: ['<?php echo join($tanggalaja, '\',\'') ?>']
				},

				yAxis: {
					
					
					min: 0, 
					max: <?php echo $max;?>,
					tickInterval: 500000,
					labels: {
					formatter: function(){
                    return this.value/1000000 + "JT";}},
					title: {
						text: 'Profit'
					}
				},
				legend: {
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
	
				series: [{
					name: 'Profit',
					data: [<?php echo join($untung[$jumcont], ',') ?>]
				}]

			});
			<?php } ?>
}
</script>