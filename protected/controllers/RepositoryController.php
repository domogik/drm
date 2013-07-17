<?php

class RepositoryController extends Controller
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
				'actions'=>array('index','view','icon','data'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('admin','create','update','delete', 'clear'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
        $model = $this->loadModel($id);
        $archive = Yii::app()->basePath . '/runtime/' . $model->id . '.phar.tar.gz';
        if ($model->needRefresh || !file_exists($archive)) {
            $this->generateRepoInfo($model);
        }
        $this->renderPartial('view',array(
			'model'=>$model,
		));
	}

	public function actionData($id)
	{
        $model = $this->loadModel($id);
        $archive = Yii::app()->basePath . '/runtime/' . $model->id . '.phar.tar.gz';
        if ($model->needRefresh || !file_exists($archive)) {
            $this->generateRepoInfo($model);
        }
        $this->renderPartial('data',array(
			'model'=>$model,
            'archive'=>$archive,
		));
	}
    
	public function actionIcon($id)
	{
        $model = $this->loadModel($id);
        $path = Yii::app()->basePath . '/../images/icon-' . $model->icon . '.png';
        header('Content-Type: image/png');
        echo file_get_contents($path);
	}
    
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Repository;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Repository']))
		{
			$model->attributes=$_POST['Repository'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('create',array(
			'model'=>$model,
		));
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

        if(isset($_POST['Repository']))
        {
            $model->attributes=$_POST['Repository'];
            if($model->save())
                $this->redirect(array('admin'));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        if ($id == Yii::app()->params['defaultRepository'])
        	throw new CHttpException(400,'Invalid request. You can not delete this repository');

		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionAdmin()
	{
		$model=new Repository('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Repository']))
			$model->attributes=$_GET['Repository'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function actionIndex()
	{
        $this->layout='//layouts/home';

        $dataProvider=new CActiveDataProvider('Repository');
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
	}
    
    public function actionClear($id)
	{
        // we only allow deletion via POST request
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
            $model = $this->loadModel($id);
            foreach($model->versions as $version) {
                $version->delete();
            }
    
            $model->needRefresh = true;
            $model->save();
    
    		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }
    
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Repository::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='repository-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
    
    private function generateRepoInfo($model) {
        $filename = Yii::app()->basePath . '/runtime/' . $model->id . '.phar.tar';
        $archive = Yii::app()->basePath . '/runtime/' . $model->id . '.phar.tar.gz';
        $packages = array();
        
        if (file_exists($archive)) { unlink($archive); }
        if (file_exists($filename)) { unlink($filename); }
        
        $phar = new Phar($filename);

        $phar->addEmptyDir('images');
        foreach($model->versions as $version) {
            if ($version->deployed) {
                $infoobj = json_decode($version->info);
                $package = array(
                    'id' => $version->package_id,
                    'version' => $version->number,
                    'generated' => $version->generated,
                    'type' => $version->type_id,
                    'name' => $version->package->name,
                    'fullname' => $version->type_id . '-' . $version->package_id,
                    'changelog' => $version->changelog,
                    'author' => $version->package->author,
                    'author_email' => $version->package->authorEmail,
                    'domogik_min_version' => $version->domogikMinRelease,
                    'documentation' => $version->package->documentation,
                    'description' => $version->package->description,
                    'category' => $version->package->category,
                    'dependencies' => ((property_exists($infoobj->identity, 'dependencies'))?$infoobj->identity->dependencies:null),
                    'archive_url' => Yii::app()->createAbsoluteUrl("/version/download", array("repository" => $model->id, "type" => $version->type_id, "package" => $version->package_id, "version" =>$version->number)),
                    "source" => Yii::app()->createAbsoluteUrl("/repository/view", array("id" => $model->id)),
                );
                array_push($packages, $package);
				
                if ($version->package->icon) $phar->addFromString('images/' . cleanfilename('icon_' . $version->package->type_id . '_' . $version->package_id . '_' . $version->number) . '.png', $version->package->icon);
                if ($version->package->banner) $phar->addFromString('images/' . cleanfilename('banner_' . $version->package->type_id . '_' . $version->package_id . '_' . $version->number) . '.png', $version->package->banner);
                if ($version->package->screenshot) $phar->addFromString('images/' . cleanfilename('screenshot_' . $version->package->type_id . '_' . $version->package_id . '_' . $version->number) . '.png', $version->package->screenshot);
            }
        }
    
        $json = array(
            'json_version' => 1,
            'id' => $model->id,
            'name' => $model->name,
            'generated' => $model->generated,
            'count' => $model->count,
            'status_url' => Yii::app()->createAbsoluteUrl("/repository/view", array("id" => $model->id)),
            'data_url' => Yii::app()->createAbsoluteUrl("/repository/data", array("id" => $model->id)),
            'icon_url' => Yii::app()->createAbsoluteUrl("/repository/icon", array("id" => $model->id)),
            'packages' => $packages,
        );
        $jsonEncoded = json_encode($json);
        $repoinfo = str_replace('\\/', '/', $jsonEncoded);
        
        $phar->addFromString('repo.info', $repoinfo);
                
        $phar->compress(Phar::GZ);

        $model->count++;
        $model->needRefresh = false;
        $model->generated = new CDbExpression('NOW()');
        $model->save();
    }
}

function cleanfilename($name) {
    $dangerous_characters = array(" ", '"', "'", "&", "/", "\\", "?", "#");
    $slug = str_replace($dangerous_characters, '_', $name);
          //this will replace all non alphanumeric char with '-'
    $slug = mb_strtolower($slug);
          //convert string to lowercase
    return $slug;
}