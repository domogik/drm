<?php
class PackageController extends Controller
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
			array('allow',
				'actions'=>array('index', 'displayIcon'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('delete', 'create', 'update', 'admin', 'view'),
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
	public function actionView($type_id, $id)
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);
        }

   		$modelVersion=new Version('search');
		$modelVersion->unsetAttributes();  // clear any default values
		if(isset($_GET['Version']))
			$modelVersion->attributes=$_GET['Version'];

		$this->render('view',array(
			'model'=>$this->loadModel($type_id, $id),
            'modelVersion'=>$modelVersion,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Package;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Package']))
		{
			$model->attributes=$_POST['Package'];
			if($model->save())
				$this->redirect(array('view','type_id'=>$model->type_id, 'id'=>$model->id));
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
	public function actionUpdate($type_id, $id)
	{
		$model=$this->loadModel($type_id, $id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Package']))
		{
			$model->attributes=$_POST['Package'];
			if($model->save())
				$this->redirect(array('view','type_id'=>$model->type_id, 'id'=>$model->id));
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
	public function actionDelete($type_id, $id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($type_id, $id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionAdmin()
	{
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);
        }
		$model=new Package('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Package']))
			$model->attributes=$_GET['Package'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
    
    /**
    * Opens the dialog in browser to open/save the image.
    */
    public function actionDisplayIcon($type, $package, $repository=NULL, $version=NULL)
    {
        $model=$this->loadModel($type, $package);     
		if($model->icon===null)
			throw new CHttpException(404,'No icon for this package');
        Yii::app()->getRequest()->sendFile('icon.png', $model->icon, 'image/png');
    }
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Package('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['q'])) {
             $model->name = $_GET['q'];
             $model->description = $_GET['q'];
        }
        if (isset($_GET['repositories']) && count($_GET['repositories']) > 0) {
            $repositories = $_GET['repositories'];
        } else {
            $repositories = array('stable');
        }
		$this->render('index',array(
			'model'=>$model,
            'dataprovider'=>$model->searchPublic($repositories),
            'repositories'=>$repositories,
		));

	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($type_id, $id)
	{
		$model=Package::model()->findByAttributes(array('type_id'=>$type_id, 'id'=>$id));
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='member-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
