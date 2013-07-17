<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
    <link rel="Shortcut Icon" type="image/png" href="http://www.domogik.org/banner/favicon.png" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/nav.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/drm.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/icons.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
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
    <div class="container" id="page">
        <?php echo $content; ?>
    </div>
</body>
</html>