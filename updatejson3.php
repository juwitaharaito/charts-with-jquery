<?php
	if(isset($_POST["namakategori"]))  
	 {
		$namakategori = ($_POST["namakategori"]); 
		$json = json_decode(file_get_contents("kategori3.json"));
		$count = count($json->kategori);
		$json->kategori[$count]->id = ($json->kategori[$count-1]->id)+1;
		$json->kategori[$count]->name = $namakategori;
		$json->kategori[$count]->bulantahun = [];
		$str = json_encode($json);
		$file = fopen('kategori3.json','w');
		fwrite($file, $str);
		fclose($file);
		$json2 = json_decode(file_get_contents("kategori3.json"));
		print_r($json2);
		}
		
	 else {
echo "gagal";
					 }					 
                     ?>  
	 

<?php 					?>