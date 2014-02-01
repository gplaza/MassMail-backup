<?php

function checkFileName($dossier){

	if (($dir=opendir($dossier))===false)
		return;

	while($name=readdir($dir)){

		if($name==='.' or $name==='..')
			continue;

		$full_name = $dossier.'/'.$name;

		if(is_dir($full_name)) {
			$newName = iconv('cp437', 'iso-8859-1', $full_name);		
			$newName = trim($newName, chr(0xC2).chr(0xA0));
			rename($full_name,$newName);
			checkFileName($newName);
		} else {
			$newName = iconv('cp437', 'iso-8859-1', $name);
			rename($full_name,$dossier.'/'.$newName);
		}
	}
}

function deltree($dossier) {
	
	if(($dir=@opendir($dossier))===false)
            return;
			
        while($name=readdir($dir)){
            if($name==='.' or $name==='..')
                continue;
            
			$full_name=$dossier.'/'.$name;
 
            if(is_dir($full_name))
                deltree($full_name);
            else unlink($full_name);
        }
 
        closedir($dir);
		@rmdir($dossier);
}

function CheckDirectory($empresa,$basePath) {

	$result = array();
	$path = $basePath.$empresa;
	
	if (is_dir(substr($path,0,-1)))
		$path = substr($path,0,-1);

	if (is_dir($path)) {
		if ($filesEmpresa = opendir($path)) {
			$j = 0;
			while($file = @readdir($filesEmpresa))
				if($file != '..' && $file != '.') {
				$dirName = str_replace($basePath,'',$path);
				$url = $basePath.rawurlencode($dirName).'/'.rawurlencode($file);
				$result[$j++] = '<li><a href="'.$url.'" >'.$file.'</a></li>';
			}
				
		}
	}

	return $result;
}
