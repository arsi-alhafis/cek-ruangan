<?php 
	$today = getdate()[weekday];
	$senin = "";
	$selasa = "";
	$rabu = "";
	$kamis = "";
	$jumat = "";

	switch ($today) {
		case 'Monday':
			$senin = "selected";
			break;

		case 'Tuesday':
			$selasa = "selected";
			break;

		case 'Wednesday':
			$rabu = "selected";
			break;

		case 'Thursday':
			$senin = "selected";
			break;

		case 'Friday':
			$kamis = "selected";
			break;
		
		default:
			$senin = "selected";
			break;
	}

	function httpGet($url)
	{
	    $ch = curl_init();  
	 
	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	 
	    $output=curl_exec($ch);
	 
	    curl_close($ch);
	    return $output;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$hari = $_POST['hari'];
		$ruangan = $_POST['ruangan'];
		$senin = "";
		$selasa = "";
		$rabu = "";
		$kamis = "";
		$jumat = "";

		switch ($hari) {
			case 'senin':
				$senin = "selected";
				break;

			case 'selasa':
				$selasa = "selected";
				break;

			case 'rabu':
				$rabu = "selected";
				break;

			case 'senin':
				$senin = "selected";
				break;

			case 'kamis':
				$kamis = "selected";
				break;
			
			default:
				$senin = "selected";
				break;
		}

		$resultJson = httpGet("http://localhost/cekruangan/api/jadwal/".$hari."/".$ruangan);

		$result = json_decode($resultJson)->data;
		$countMatkul = count($result);

		$startDay = strtotime("1970-01-01 08:00:00 UTC");

		$matkul = [];
		$jamMulai = [];
		$jamSelesai = [];
		$durasi = [];
		$margin = [];

		for ($i = 0; $i < $countMatkul; $i++) {
			$mulai = $result[$i]->jam_mulai;
			$mulaiSeconds = strtotime("1970-01-01 $mulai UTC");
			$selesai = $result[$i]->jam_selesai;
			$selesaiSeconds = strtotime("1970-01-01 $selesai UTC");
			$nama = $result[$i]->matkul;

			$lama = ($selesaiSeconds - $mulaiSeconds) / 60;

			$matkul[$i] = $nama;
			$jamMulai[$i] = $mulai;
			$jamSelesai[$i] = $selesai;
			$durasi[$i] = $lama;

			$selisih = ($mulaiSeconds - $startDay) / 60 + 10;

			if ($i != 0) {
				$prevMatkul =$jamSelesai[$i - 1];
				$prevMatkulSeconds = strtotime("1970-01-01 $prevMatkul UTC");
				$selisih = ($mulaiSeconds - $prevMatkulSeconds) / 60; 
			}

			$margin[$i] = $selisih;
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Cek Ruangan</title>
	<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!--Import materialize.css-->
	<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
	<link type="text/css" rel="stylesheet" href="css/style.css"/>
	<link rel="icon" type="image/ico" href="favicon-space-shuttles.ico" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script>
		$(function() {
			$( "#ruangan" ).autocomplete({
				source: 'search.php'
			});
		});
	</script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<style type="text/css">
		.matkul {
			vertical-align: top;
		}
		.matkul-box {
			border-radius: 10px;
			background-color: #80cbc4;
		}
		.matkul-title {
			color: #fff;
			height: 25px;
			border-top-left-radius: 10px;
			border-top-right-radius: 10px;
			background-color: #4db6ac;
		}
		.hour {
			font-weight: 200;
			height: 30px;
		}
		.table {
			width: 100%;
		}
		.bold {
			font-weight: 400;
		}
	</style>
</head>
<body>
	<!--Import jQuery before materialize.js-->
	<script type="text/javascript" src="js/materialize.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
		    $('select').material_select();
		});
	</script>
	<nav class="teal">
		<div class="nav-wrapper">
			<a href="" class="brand-logo center logo logo1">CEK<span class="logo logo2">RUANGAN</span></a>
		</div>
	</nav>
	<div class="row content">
		<h5 class="center-align title">RUANGAN INI LAGI ADA KELAS GA YA?</h5>
		<div class="col l4 <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') { echo 'offset-l2';} else {echo 'offset-l4';} ?>">
			<div class="card">
				<div class="card-content">
					<form action="" method="post">
						<div class="row">
							<div class="input-field col s6">
								<select name="hari">
									<option <?php echo $senin;?> value="senin">Senin</option>
									<option <?php echo $selasa;?> value="selasa">Selasa</option>
									<option <?php echo $rabu;?> value="rabu">Rabu</option>
									<option <?php echo $kamis;?> value="kamis">Kamis</option>
									<option <?php echo $jumat;?> value="jumat">Jumat</option>
								</select>
								<label>Pilih hari</label>
							</div>
							<div class="input-field col s6">
								<div class="ui-widget">
									<input required <?php echo "value='$ruangan'"; ?> name="ruangan" placeholder="Ex: 2402" id="ruangan" type="text">
									<label for="ruangan" class="active">Pilih ruangan</label>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col s12 center-align">
								<button id="submit_form" onclick="form_submit()" data-target="modal" class="btn waves-effect waves-light" type="submit" name="action">CEK Ruangan
									<i class="material-icons right">search</i>
								</button>
							</div>
						</div>
						<div class="row generate center-align">
							<label>Version 1.0.0</label><br>
							<label>Sumber didapat dari jadwal Ilmu Komputer</label><br>
							<label>Untuk jadwal Sistem Informasi masih dalam proses</label>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class='col l4'>
			<?php 
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					echo "<div class='card'>";
					echo "<div class='card-content center-align'>";
					echo "<h5 class='center-align title'>KELAS HARI INI</h5>";

					if ($countMatkul <= 0) {
						echo "TIDAK ADA KELAS PADA HARI INI DI RUANG $ruangan";
					} else {
						echo "<table class='table bordered'>";
						echo "<thead>";
						echo "<tr>";
						echo "<td>";
						echo "JAM";
						echo "</td>";
						echo "<td class='center-align'>";
						echo "MATKUL";
						echo "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "</thead>";
						echo "<td>";
						echo "<p class='hour bold'>08.00</p>";
						echo "<p class='hour'>08.30</p>";
						echo "<p class='hour bold'>09.00</p>";
						echo "<p class='hour'>09.30</p>";
						echo "<p class='hour bold'>10.00</p>";
						echo "<p class='hour'>10.30</p>";
						echo "<p class='hour bold'>11.00</p>";
						echo "<p class='hour'>11.30</p>";
						echo "<p class='hour bold'>12.00</p>";
						echo "<p class='hour'>12.30</p>";
						echo "<p class='hour bold'>13.00</p>";
						echo "<p class='hour'>13.30</p>";
						echo "<p class='hour bold'>14.00</p>";
						echo "<p class='hour'>14.30</p>";
						echo "<p class='hour bold'>15.00</p>";
						echo "<p class='hour'>15.30</p>";
						echo "<p class='hour bold'>16.00</p>";
						echo "<p class='hour'>16.30</p>";
						echo "<p class='hour bold'>17.00</p>";
						echo "<p class='hour'>17.30</p>";
						echo "<p class='hour bold'>18.00</p>";
						echo "<p class='hour'>18.30</p>";
						echo "<p class='hour bold'>19.00</p>";
						echo "<p class='hour'>19.30</p>";
						echo "<p class='hour bold'>20.00</p>";
						echo "<p class='hour'>20.30</p>";
						echo "<p class='hour bold'>21.00</p>";
						echo "<p class='hour'>21.30</p>";
						echo "<p class='hour bold'>22.00</p>";
						echo "</td>";
						echo "<td class='matkul'>";
						for ($i = 0; $i < $countMatkul; $i++) {
							echo "<div class='matkul-box center-align' style='margin-top: $margin[$i]px; height: $durasi[$i]px;'>";
							echo "<div class='matkul-title center-align'>";
							echo $matkul[$i];
							echo "</div>";
							echo "<p>".substr($jamMulai[$i], 0, -3)." - ".substr($jamSelesai[$i], 0, -3)."</p>";
							echo "</div>";	
						}
						echo "</td>";
						echo "</tr>";
						echo "</table>";
					}
					echo "</div>";
					echo "</div>";
				}
			?>
		</div>
	</div>
</body>
</html>