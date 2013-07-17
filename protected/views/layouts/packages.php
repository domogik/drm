<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
    <link rel="Shortcut Icon" type="image/png" href="http://www.domogik.org/banner/favicon.png" />
   	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/nav.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/packages.css" media="screen, projection" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
    <div id="dmgnavbar">Loading</div>
	<script type="text/javascript" src='http://www.domogik.org/banner/script.js'></script>
	<script type="text/javascript">
	// <![CDATA[
	banner('drm', 'en');
	// ]]>
	</script>
    <div id='nav'><ul>
        <?php if (!Yii::app()->user->isGuest): ?>
        <li><?php echo CHtml::link('Home',array('repository/index')); ?></li>
        <li><?php echo CHtml::link('Repositories',array('repository/admin')); ?></li>
        <li><?php echo CHtml::link('Packages',array('package/admin')); ?></li>
        <li><?php echo CHtml::link('Upload',array('version/upload')); ?></li>
        <li><?php echo CHtml::link('Logout',array('site/logout')); ?></li>
        <?php else: ?>
        <li><?php echo CHtml::link('Home',array('repository/index')); ?></li>
        <li><?php echo CHtml::link('Login',array('site/login')); ?></li>
        <?php endif; ?>
    </ul></div>
<?php echo $content; ?>
</body>
</html>