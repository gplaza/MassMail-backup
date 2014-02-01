<?php

include('utils.php');
include('inc.config.php');
include("db.class.php");
include("entity.class.php"); 

$limited = 'limited';
$complete = 'complete';

$view = $limited;

if(isset($_GET['view']))
	$view = $_GET['view'];

if(isset($_FILES['fichier']))
{
	deltree($basePath);
	mkdir($basePath);
	move_uploaded_file($_FILES['fichier']['tmp_name'], $basePath.basename($_FILES['fichier']['name']));
	$fileReport =  $_FILES['fichier']['name'];

	include "zip/pclzip.lib.php";
	$path=$basePath.$fileReport;
	$archive = new PclZip($path);
	$result = $archive->extract(PCLZIP_OPT_PATH, $basePath);

	checkFileName($basePath);
	unlink($path);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="es">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>MassMail</title>
<link rel="stylesheet" type="text/css" href="css/jquery.tagit.css" />
<link rel="stylesheet" type="text/css" href="css/site.css" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/themes/humanity/jquery-ui.css" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript" ></script>
<script charset="utf-8" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/jquery-ui.min.js" type="text/javascript" ></script>
<script type="text/javascript" src="js/jquery.ajaxq-0.0.1.js"></script>
<script type="text/javascript" src="js/tag-it.js"></script>
<script type="text/javascript" src="js/massmail.min.js"></script>

</head>

<body style="background:#fde2a0;font-family:Arial,Helvetica,sans-serif">

<div>
	<img src="img/logo.png" alt="logo" />
	<div class="main" >
		<div id="tabs" >

			<ul>
				<li><a href="#tabs-1">Mandar</a></li>
				<li><a href="#tabs-2">Ficheros</a></li>
				<li><a href="#tabs-3">Parametros</a></li>
			</ul>
			<div id="tabs-1">
<?php

if(isset($_POST['empresaSearchId']))
	$search = $conn->query("SELECT * from empresa where empresa.id=".$_POST['empresaSearchId']);
else
	$search = $conn->query("SELECT * from empresa");
	
$result = $search->fetchAll(PDO::FETCH_ASSOC);
$entities = array();

foreach ($result as $empresa) {
	
	$files = CheckDirectory($empresa['nombre'],$basePath);
	
	if (count($files) > 0 || $view == $complete || isset($_POST['empresaSearchId']))
	{
		$entity=new entity();
		$entity->setName($empresa['nombre']);
		$entity->setFiles($files);
		$entity->setId($empresa['id']);
		$entity->setMails($conn->query('select mail.email from empresa_mail,mail WHERE empresa_mail.id_empresa = '.$empresa['id'].' AND mail.id = empresa_mail.id_mail')->fetchAll(PDO::FETCH_ASSOC));
		$entities[] = $entity;
	}
}
?>
				<form method="get" id="form-select" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
					<fieldset style="padding:5px;">
						<?php 
						if ($view == $limited) {
							$buttonText = 'Mostrar Todo';
							$valueHidden = $complete;
						} else {
							$buttonText = 'Mostrar solo con Archivo';
							$valueHidden = $limited;
						}
						?>
						<button type="button" id="actionSend" onclick="javascript:send('send.php')">Mandar</button>
						<button type="button" id="actionAdd" onclick="javascript:add('empresas')">Agregar Empresa</button>
						<input type="hidden" name="view" value="<?php echo $valueHidden ?>" />
						<button type="submit" id="changeView" value="<?php echo $buttonText ?>"><?php echo $buttonText ?></button>
						<?php echo 'Total : '.count($entities); ?>
					</fieldset>
				</form>
				<fieldset id="loading_send" style="display:none;padding: 10px;">
					<div id="progressbar" style="float:left;width:700px;margin-right:15px;" > </div><button type="button" onclick="javascript:cancelSend()" id="cancelSend">Cancelar todo</button>
				</fieldset>								
				<form method="post" id="form-search" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
					<fieldset style="padding:5px;">
						<legend>Busqueda</legend>
						<label>Empresa : </label>
						<input type="hidden" id="empresa_search_id" name="empresaSearchId" value="" />
						<input type="text" id="empresa_search" name="empresaSearch" value="" />
						<button type="submit" id="search" value="Buscar">Buscar</button>
					</fieldset>
				</form>
				
				<table id="empresas" style="width: 1024px;margin: auto;">
					<thead>
						<tr>
							<th>Empresa</th>
							<th>recipientes</th>
							<th>files</th>
							<th>mandar<input type="checkbox" id="selectAllLine"  /></th>
							<th>Estado</th>
							<th>Acci&oacute;n</th>
						</tr>
					</thead>
					<tbody>
<?php

foreach($entities as &$entity)
{
	$files = $entity->getFiles();
	$mails = $entity->getMails();
	$id = $entity->getId();
?>

						<tr id="line<?php echo $id; ?>">
							<td style="white-space:nowrap;" id="entity<?php echo $id; ?>">
								<?php echo $entity->getName(); ?>
								<input id="empresa<?php echo $id; ?>" type="hidden" value="<?php echo $entity->getName(); ?>" />
							</td>
							<td><ul id="mails<?php echo $id; ?>"><?php foreach($mails as &$mail) echo '<li>'.$mail['email'].'</li>'; ?></ul></td>
							<td style="font-size:9;"><ul id="files<?php echo $id; ?>"><?php foreach($files as &$file) echo $file;?></ul></td>
							<td align="center" ><input type="checkbox" name="send[]" id="send<?php echo $id; ?>" /></td>
							<td align="center" ><img src="img/ajax-loader.gif" style="display:none;" id="load<?php echo $id; ?>" alt="loader" /></td>
							<td align="center" style="width:100px;" >
								<button onclick="javascript:changeRow('<?php echo $id; ?>');" class="edit">Editar</button>
								<button onclick="javascript:saveRow('<?php echo $id; ?>','save.php');" class="save">Grabar</button>
								<button onclick="javascript:deleteRow('<?php echo $id; ?>','delete.php');" class="delete">Quitar</button>
							</td>
						</tr>
<?php } ?>
					</tbody>
				</table>
			</div>
			<div id="tabs-2">
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
					<fieldset style="font-size: 12px">
						<legend>Subir nuevo archivo</legend>
							<p>Archivo :
							<input type="file" name="fichier" /></p>
							<input type="submit" value="Subir" />
					</fieldset>
				</form>
			</div>
			<div id="tabs-3">
				<fieldset style="font-size: 12px">
					<legend>Parametros</legend>
					<label>E-mail :</label><input type="text" name="From" value="<?php echo $From ?>" /><br />
					<label>Usuario :</label><input type="text" name="Username" value="<?php echo $Username ?>" /><br />
					<label>Password :</label><input type="password" name="Password" value="<?php echo $Password ?>" /><br />
					<label>Mesaje :</label><textarea rows="15" cols="40" name="Body" ><?php echo $Body ?></textarea><br />
				</fieldset>
			</div>
		</div>
	</div>
</div>
</body>
</html>