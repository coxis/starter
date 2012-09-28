<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>ARPA</title>
	<?php \Coxis\Core\Tools\HTML::show_title() ?>
	<?php \Coxis\Core\Tools\HTML::show_description() ?>
	<?php \Coxis\Core\Tools\HTML::show_keywords() ?>
	<base href="<?php echo \Coxis\Core\URL::base() ?>" />
	<?php \Coxis\Core\Tools\HTML::show_all() ?>

</head>
<body>
	<h1>Coxis</h1>
	<div>
		<?php echo $content ?>
	</div>
	<p>By Michel Hognerud - 2012</p>
</body>
</html>