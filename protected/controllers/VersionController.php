<?php

class VersionController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('info', 'download', 'upload', 'send'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('update', 'delete'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Version']))
		{
            $previous_repository = $model->repository;
            $previous_deployed = $model->deployed;
			$model->attributes=$_POST['Version'];
            if ($model->deployed && !$previous_deployed) {
                $model->date_deployed = new CDbExpression('NOW()');
                Repository::model()->updateByPk($model->repository_id, array('needRefresh'=>true)); // Repository need refresh
				$this->deployDoc($model);
            }
            if (!$model->deployed && $previous_deployed) {
                $model->date_deployed = null;
                Repository::model()->updateByPk($model->repository_id, array('needRefresh'=>true)); // Repository need refresh
				$this->undeployDoc($model);
			}			

			if($model->save()) {
                if ($model->repository_id != $previous_repository->id) {
                    Repository::model()->updateByPk($model->repository_id, array('needRefresh'=>true)); // Repository need refresh
                    Repository::model()->updateByPk($previous_repository->id, array('needRefresh'=>true)); // Repository need refresh

                    Log::info($previous_repository->id, "Package '" . $model->package->name . "' version '" . $model->number . "' has been moved to " . $model->repository->name);
                    Log::info($model->repository_id, "Package '" . $model->package->name . "' version '" . $model->number . "' has been moved from " . $previous_repository->name);
                }
				$this->redirect(array('/package/view', 'type_id'=>$model->type_id, 'id'=>$model->package_id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	private function deployDoc($version) {
		// Each file is stored in Yii::app()->params['repository'].$version->id
		// ex. /var/www/repo/protected/uploads/1
		if ($version->repository_id == "nightly") {
			$new_version = "dev";
		}
		else {
			$new_version = $version->number;
		}
		$cmd = "nohup /var/www/sphinx/docsbuild_pkg.sh " . Yii::app()->params['repository'].$version->id . " " .  $version->package->type_id . " " . $version->package_id . " " . $new_version . " &";
		exec($cmd);
	}
	
	private function undeployDoc($version) {
		if ($version->repository_id == "nightly") {
			$new_version = "dev";
		}
		else {
			$new_version = $version->number;
		}
		$cmd = "nohup /var/www/sphinx/docsdelete_pkg.sh " . $version->package->type_id . " " . $version->package_id . " " . $new_version . " &";
		exec("echo '" . $cmd . "' > /tmp/fle-delete");
		exec($cmd);
	}
	
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
            $model = $this->loadModel($id);
			$package_id = $model->package_id;
			$type_id = $model->type_id;
            Repository::model()->updateByPk($model->repository_id, array('needRefresh'=>true)); // Repository need refresh

            $model->delete();
            
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/package/view', 'type_id'=>$type_id, 'id'=>$package_id));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionInfo($repository, $type, $package, $version)
	{
		$model = $this->loadModelNumberDeployed($repository, $type, $package, $version);
        header("content-type: application/json");
        header("Content-Length: " . strlen($model->info));
        echo $model->info;
	}

    public function actionDownload($repository, $type, $package, $version)
    {
        $model=$this->loadModelNumberDeployedOnce($repository, $type, $package, $version);
        $path = Yii::app()->params['repository'].$model->id;
        if (!is_file($path))
            throw new CHttpException(400,'Error. This package file does not exist');
        $model->stat_downloads++;
        $model->save();
        $name = $model->package->type_id . '-' . $model->package_id . '-' . $model->number . '.tgz';
        header('Content-Type: application/x-download');
        header('Content-Disposition: attachment; filename="'.$name.'"');
        header("Content-length: " . $model->filesize);
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo file_get_contents($path);
    }
    
    public function actionSend($repository=NULL, $deploy=false)
    {
        Yii::import("ext.EAjaxUpload.qqFileUploader");
        $folder=Yii::app()->params['tmpUpload'];// folder for uploaded files
        $allowedExtensions = array("tgz");//array("jpg","jpeg","gif","exe","mov" and etc...
        $sizeLimit = 2 * 1024 * 1024;// maximum file size in bytes
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload($folder);
        $infopath = 'phar://'.Yii::app()->params['tmpUpload'].$result['filename'].'/info.json';
        $iconpath = 'phar://'.Yii::app()->params['tmpUpload'].$result['filename'].'/icon.png';
        $bannerpath = 'phar://'.Yii::app()->params['tmpUpload'].$result['filename'].'/banner.png';
        $screenshotpath = 'phar://'.Yii::app()->params['tmpUpload'].$result['filename'].'/screenshot.png';
        if (is_file($infopath)) {
            $infojson = file_get_contents($infopath);
            $infoobj = json_decode($infojson);
            if ($infoobj and isset($infoobj->identity)) {
                if (isset($infoobj->identity->id) and isset($infoobj->identity->type)) {
                    if (isset($infoobj->identity->version)) {
                        if (Type::model()->findByPk($infoobj->identity->type)) {
                            $package = Package::model()->findByAttributes(array('type_id'=>$infoobj->identity->type, 'id'=>$infoobj->identity->id));
                            if (!$package) { // Create new package
                                $package = new Package();
                                $package->id = $infoobj->identity->id;
                                $package->name = $infoobj->identity->id;
                                $package->type_id = $infoobj->identity->type;
                            }
    
                            if (is_file($iconpath))
                                $package->icon = file_get_contents($iconpath);

                            if (is_file($bannerpath))
                                $package->banner = file_get_contents($bannerpath);

                            if (is_file($screenshotpath))
                                $package->screenshot = file_get_contents($screenshotpath);

                            if (isset($infoobj->identity->category)) // Update category
                                $package->category = $infoobj->identity->category;

                            if (isset($infoobj->identity->description)) // Update description
                                $package->description = $infoobj->identity->description;
    
                            if (isset($infoobj->identity->documentation)) // Update documentation
                                $package->documentation = $infoobj->identity->documentation;

                            if (isset($infoobj->identity->author)) // Update author
                                $package->author = $infoobj->identity->author;
    
                            if (isset($infoobj->identity->author_email)) // Update author email
                                $package->authorEmail = $infoobj->identity->author_email;

                            $package->save();

                            if (Version::model()->findByAttributes(array('package_id'=>$package->id, 'number'=>$infoobj->identity->version))) {
                                $result['success'] = false;
                                $result['error'] = "A version '" . $infoobj->identity->version . "' for package '" . $package->id . "' already exist.";
                            } else {
                                $version = new Version;
                                $version->package_id = $package->id;
                                $version->type_id = $package->type_id;
                                $version->number = $infoobj->identity->version;
                                $version->generated = $infoobj->identity->generated;
                                $version->filesize = filesize($folder.$result['filename']);//GETTING FILE SIZE
                                if ($repository and Repository::model()->findByPk($repository)) {
                                    $version->repository_id = $repository;
                                } else {
                                    $version->repository_id = Yii::app()->params['defaultRepository'];                                
                                }
                                $version->info = $infojson;
                                if (isset($infoobj->identity->changelog))
                                    $version->changelog = $infoobj->identity->changelog;

                                if (isset($infoobj->identity->domogik_min_version)) // Update domogik_min_version
                                    $version->domogikMinRelease = $infoobj->identity->domogik_min_version;

                                if ($version->save()) {
                                    Repository::model()->updateByPk($version->repository_id, array('needRefresh'=>true)); // Repository need refresh

                                    rename(Yii::app()->params['tmpUpload'].$result['filename'], Yii::app()->params['repository'].$version->id);
                                    if ($deploy) {
                                        Version::model()->updateAll(array('deployed'=>false),'repository_id="'.$version->repository_id.'" AND package_id="' .$version->package_id. '" AND type_id="'.$version->type_id.'" AND deployed = true');
                                        $version->deployed = true;
                                        $version->date_deployed = new CDbExpression('NOW()');
                                        $version->save();
										$this->deployDoc($version);
                                    }
                                    Log::info($version->repository_id, "Package '" . $version->package->name . "' version '" . $version->number . "' has been uploaded");
                                } else {
                                    $result['success'] = false;
                                    $result['error'] = '';
                                    $attributes = $version->getErrors();
                                    foreach($attributes as $attribute=>$errors) {
                                        foreach($errors as $error) {
                                            $result['error'] .= $attribute . ' : ' .$error . "\n";
                                        }
                                    }
                                }
                            }                        
                        } else {
                            $result['success'] = false;
                            $result['error'] = 'Unknown package type in info.json. Please upload a valid domogik .tgz package';
                        }
                    } else {
                        $result['success'] = false;
                        $result['error'] = 'Missing package version in info.json found. Please upload a valid domogik .tgz package';
                    }
                } else {
                    $result['success'] = false;
                    $result['error'] = 'Missing package id or type in info.json found. Please upload a valid domogik .tgz package';
                }
            } else {
                $result['success'] = false;
                $result['error'] = 'Invalid info.json. Please upload a valid domogik .tgz package';
            }
        } else {
            $result['success'] = false;
            $result['error'] = 'No info.json found. Please upload a valid domogik .tgz package';
        }

        $result=htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        echo $result;
    }
    
    public function actionUpload()
	{
		$this->render('upload');
	}
    
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Version::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested package version does not exist.');
		return $model;
	}

	public function loadModelNumberDeployed($repository, $type, $package, $version)
	{
		$model=Version::model()->findByAttributes(array('repository_id'=>$repository, 'type_id'=>$type, 'package_id'=>$package, 'number'=>$version, 'deployed'=>true));
		if($model===null)
			throw new CHttpException(404,'The requested package version does not exist.');
		return $model;
	}

	public function loadModelNumberDeployedOnce($repository, $type, $package, $version)
	{
		$model=Version::model()->find("repository_id = '" . $repository . "' AND type_id = '" . $type . "' AND package_id = '" . $package . "' AND number = '" . $version . "' AND date_deployed IS NOT NULL");
		if($model===null)
			throw new CHttpException(404,'The requested package version does not exist.');
		return $model;
	}
    
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='version-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
