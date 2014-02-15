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
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css" />
<link rel="stylesheet" type="text/css" href="css/site.css" />

<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"
	type="text/javascript"></script>
<script charset="utf-8"
	src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/jquery-ui.min.js"
	type="text/javascript"></script>

<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/ajaxq.js"></script>
<script type="text/javascript" src="js/tag-it.js"></script>
<script type="text/javascript" src="js/massmail.js"></script>

</head>

<body role="document">
	<div role="navigation" class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<a href="#" class="navbar-brand">MassMail</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul id="tabs" class="nav navbar-nav">
					<li class="active"><a id="tab1Link" href="#tabs-1">Lista</a></li>
					<li><a id="tab2Link" href="#tabs-2">Ficheros</a></li>
					<li><a id="tab3Link" href="#tabs-3">Parametros</a></li>
				</ul>
			</div>
		</div>
	</div>

		<div class="container-fluid" id="tabs-1">
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

			<div class="panel panel-default">
				<div class="panel-heading">Empresas</div>

				<div class="panel-body">

					<!-- <div class="row">		 -->
					<form class="col-lg-2" method="post" id="form-search" action="index.php">

						<div class="input-group">

							<input type="hidden" id="empresa_search_id"
								name="empresaSearchId" value="" /> <input type="text"
								class="form-control" id="empresa_search"
								placeholder="Empresa..." name="empresaSearch" value="" /> <span
								class="input-group-btn">
								<button type="submit" id="buscarButton" class="btn btn-primary">
									<span class="glyphicon glyphicon-search"></span> Buscar
								</button>
							</span>

						</div>
						<!-- /input-group -->

					</form>

					<form method="get" id="form-select"
						action="index.php">
						<?php 
						if ($view == $limited) {
							$buttonText = 'Mostrar Todo';
							$imgIcon = 'glyphicon glyphicon-eye-open';
							$valueHidden = $complete;
						} else {
							$buttonText = 'Mostrar solo con Archivo';
							$imgIcon = 'glyphicon glyphicon-eye-close';
							$valueHidden = $limited;
						}
						?>

						<p>
							<button type="button" class="btn btn-primary" id="actionSend"
								onclick="javascript:send('send.php')">
								<span class="glyphicon glyphicon-send"></span> Mandar
							</button>
							<button type="button" class="btn  btn-primary" id="actionAdd"
								onclick="javascript:add('empresas')">
								<span class="glyphicon glyphicon-plus-sign"></span> Agregar
								Empresa
							</button>
							<input type="hidden" name="view"
								value="<?php echo $valueHidden ?>" />
							<button type="submit" class="btn btn-primary" id="changeView"
								value="<?php echo $buttonText ?>">
								<span class="<?php echo $imgIcon ?>"></span>
								<?php echo $buttonText ?>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge"><?php echo count($entities); ?>
								</span>
							</button>


						</p>
					</form>

					<!--  
					<p>
						<div style="display: none;" id="loading_send"
							class="progress progress-striped active">
							<div class="progress-bar" role="progressbar" aria-valuenow="45"
								aria-valuemin="0" aria-valuemax="100" style="width: 45%">
								<span class="sr-only">45% Complete</span>
							</div>

						</div>
						<button style="display: none;" type="button"
							class="btn btn-primary" onclick="javascript:cancelSend()"
							id="cancelSend">Cancelar todo</button>
					</p>
					-->

				</div>


				<table class="table table-striped" id="empresas">
					<thead>
						<tr>
							<th class="text-center">Empresa</th>
							<th class="text-center">Recipientes</th>
							<th class="text-center">Archivos</th>
							<th class="text-center">Mandar<input type="checkbox" id="selectAllLine" /></th>
							<th class="text-center">Estado</th>
							<th class="text-center">Acci&oacute;n</th>
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
							<td style="white-space: nowrap;" id="entity<?php echo $id; ?>"><?php echo $entity->getName(); ?>
								<input id="empresa<?php echo $id; ?>" type="hidden"
								value="<?php echo $entity->getName(); ?>" />
							</td>
							<td><ul id="mails<?php echo $id; ?>">
									<?php foreach($mails as &$mail) echo '<li class="badge">'.$mail['email'].'</li></br>'; ?>
								</ul></td>
							<td style="font-size: 9;"><ul id="files<?php echo $id; ?>">
									<?php foreach($files as &$file) echo $file;?>
								</ul></td>
							<td align="center"><input type="checkbox" name="send[]"
								id="send<?php echo $id; ?>" /></td>
							<td align="center"><img src="img/ajax-loader.gif"
								style="display: none;" id="load<?php echo $id; ?>" alt="loader" />
							</td>
							<td align="center">
								<div class="btn-group btn-group-sm">
									<button class="btn btn-default btn-lg"
										onclick="javascript:changeRow('<?php echo $id; ?>');">
										<span class="glyphicon glyphicon-pencil"></span>
									</button>
									<button class="btn btn-default btn-lg"
										onclick="javascript:saveRow('<?php echo $id; ?>','save.php');">
										<span class="glyphicon glyphicon-floppy-disk"></span>
									</button>
									<button class="btn btn-default btn-lg"
										onclick="javascript:deleteRow('<?php echo $id; ?>','delete.php');"
										class="delete">
										<span class="glyphicon glyphicon-remove"></span>
									</button>
								</div>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>

			</div>

		</div>
		<div class="container" style="display:none;" id="tabs-2">
			<form method="post" action="index.php" enctype="multipart/form-data">
				<fieldset style="font-size: 12px">
					<legend>Subir nuevo archivo</legend>
					<div class="input-group">
						<span class="input-group-btn"> <span
							class="btn btn-primary btn-file"> Eligir Archivo ... <input
								name="fichier" type="file" />
						</span>
						</span> <input type="text" readonly="" class="form-control" />
					</div>
					<span>Seleccionar un archivo con el formato ZIP.</span> 
				</fieldset>
				<br/>
				<input type="submit" class="btn btn-lg btn-warning" value="Subir" />
			</form>
		</div>
		<div class="container" style="display:none;" id="tabs-3">
			<fieldset style="font-size: 12px">
				<legend>Parametros</legend>

				<form>
					<div class="form-group">
						<label for="from">E-mail</label> <input type="text"
							value="<?php echo $From ?>" class="form-control" id="inputEmail"
							name="from" />
					</div>

					<div class="form-group">
						<label for="Password">Password</label> <input type="password"
							class="form-control" name="Password"
							value="<?php echo $Password ?>" />
					</div>
					<div class="form-group">
						<label for="Body">Mesaje</label>
						<textarea rows="15" class="form-control" cols="40" name="Body">
			                <?php echo $Body ?>
			            </textarea>
					</div>
					<button type="submit" class="btn btn-primary">Salvar</button>
				</form>
			</fieldset>
		</div>
</body>
</html>
