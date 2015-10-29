<?php
function size_readable ($size, $retstring = null) {
        // adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
        $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        if ($retstring === null) { $retstring = '%01d %s'; }
        $lastsizestring = end($sizes);
        foreach ($sizes as $sizestring) {
                if ($size < 1024) { break; }
                if ($sizestring != $lastsizestring) { $size /= 1024; }
        }
        if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } // Bytes aren't normally fractional
        return sprintf($retstring, $size, $sizestring);
}

function downloadtable_fromcsv($fichier = "filesdescr.csv") {
	$fic = fopen($fichier, 'rb');
	if($fic!=FALSE) {
		echo '<tr>
				<th width="120" scope="col">Filename</th>
				<th width="160" scope="col">Date</th>
				<th width="50" scope="col">Size</th>
				<th>Description</th>
			</tr>';
		
		$alt = 0;
		for ($ligne = fgetcsv($fic, 1024); !feof($fic); $ligne = fgetcsv($fic, 1024)) {
			if(file_exists("downloads/$ligne[0]"))
			{
				$fsize = size_readable(filesize("downloads/$ligne[0]"));
				$link = "<a href=\"downloads/$ligne[0]\">$ligne[0]</a>";
				if( $ligne[1] != '' ) {
					$link = $link."<br/><a href=\"downloads/$ligne[1]\">$ligne[1]</a>";
				}
			}
			else
			{
				$link = $ligne[0];
				$fsize = '';
			}
			echo "<tr>";
			if( $alt ) {
				echo '<th class="filenamealt" scope="row">'.$link.'</th>';
				echo "<td class=\"alt\">$ligne[2]</td>";
				echo "<td class=\"alt\">".$fsize."</td>";
				echo "<td class=\"alt\">$ligne[3]</td>";
			}
			else {
				echo '<th class="filename" scope="row">'.$link.'</th>';
				echo "<td>$ligne[2]</td>";
				echo "<td>".$fsize."</td>";
				echo "<td>$ligne[3]</td>";
			}
			echo "</tr>";
			$alt = 1 - $alt;
		}
	}
}

// function latest_version_file($fichier) {
	// if($fichier === null) { $fichier = "filesdescr.csv"; }
	// $fic = fopen($fichier, 'rb');
	// return = fgetcsv($fic, 1024);
// }


?>
