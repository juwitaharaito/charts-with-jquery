<?php
	if(isset($_POST["from_date"]) && isset($_POST["kategori"]))  
	 {
		$odbc = odbc_connect("Driver={PostgreSQL Unicode(x64)};Server=192.168.2.10;Port=5444;Database=i4_BBC;", "admin", "");
		$bulan = $_POST["from_date"];
		$target = $_POST["kategori"];
		$jumlah = $_POST["jumlah"];
		$qry1 = "SELECT * FROM tbl_user";
		$res2 = odbc_exec($odbc, $qry1);
		$json = json_decode(file_get_contents("kategori3.json"));
				echo $jumlah;
				$in=0; $i=0; $indexbulan = -1;
				foreach($json->kategori[$target-1]->bulantahun as $item)
				{
					if($item->bulan == $bulan)
					{
						$indexbulan = $in;
					}
					$in+=1;
				}
				if ($indexbulan < 0)
				{
					$json->kategori[$target-1]->bulantahun[$in]->bulan = $bulan;
					$indexbulan = $in;
				}
				
				$json->kategori[$target-1]->bulantahun[$indexbulan]->target = [];
				for ($a=0; $a<=$jumlah; $a++)
				{	 $json->kategori[$target-1]->bulantahun[$indexbulan]->target[$a]->nama = ( $_POST["namauser".$a] );
					$json->kategori[$target-1]->bulantahun[$indexbulan]->target[$a]->target= ( $_POST["tg".$a] );
				}
				
						$str = json_encode($json);
						$file = fopen('kategori3.json','w');
						fwrite($file, $str);
						fclose($file);
						$json2 = json_decode(file_get_contents("kategori3.json"));
						print_r($json2);
				odbc_close($odbc);
	}
		
	 else {
echo "gagal";}					 
                     ?>  
	 