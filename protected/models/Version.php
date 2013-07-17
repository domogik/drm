<?php

/**
 * This is the model class for table "version".
 *
 * The followings are the available columns in table 'version':
 * @property string $id
 * @property string $date_added
 * @property string $date_deployed
 * @property string $number
 * @property integer $deployed
 * @property string $comment
 * @property string $changelog
 * @property string $package_id
 * @property string $type_id
 * @property string $repository_id
 *
 * The followings are the available model relations:
 * @property Package $package
 * @property Repository $repository
 */
class Version extends CActiveRecord
{
	
	public function getHtml()
	{
		if ($this->changelog) {
			$option = array('class'=>'tooltip', 'tooltip'=>nl2br($this->changelog));
		} else {
			$option = array();
		}
		if ($this->repository_id == 'nightly') {
			$doc = 'http://docs.domogik.org/' . $this->package->type_id . '/' . $this->package_id . '/dev/en';
		} else {
			$doc = 'http://docs.domogik.org/' . $this->package->type_id . '/' . $this->package_id . '/' . $this->number . '/en';
		}
		$result = CHtml::link($this->number . '&nbsp;(' . $this->repository->name . ')', $doc, $option);
		$result .= "&nbsp;" . CHtml::link(CHtml::image(Yii::app()->baseUrl . '/images/download_16.png', 'Download'), array("/version/download", "repository" => $this->repository_id, "type" => $this->type_id, "package" => $this->package_id, "version" => $this->number));
		return $result;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return Version the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'version';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type_id, package_id, repository_id', 'required'),
			array('deployed, stat_downloads', 'numerical', 'integerOnly'=>true),
			array('number, package_id, repository_id, domogikMinRelease, generated', 'length', 'max'=>45),
   			array('date_added','default',
              'value'=>new CDbExpression('NOW()'),
              'setOnEmpty'=>true),
			array('comment, changelog, xml, date_deployed', 'safe'),
			array('date_added, date_deployed, number, comment, changelog, info, generated', 'default', 'value'=>null, 'setOnEmpty'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date_added, date_deployed, number, deployed, comment, changelog, type_id, package_id, repository_id, info, stat_downloads, domogikMinRelease', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'package' => array(self::BELONGS_TO, 'Package', 'package_id, type_id'),
			'repository' => array(self::BELONGS_TO, 'Repository', 'repository_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'date_added' => 'Date Added',
            'date_deployed' => 'Date Deployed',
			'number' => 'Release',
			'generated' => 'Generated',
			'deployed' => 'Deployed',
            'changelog' => 'ChangeLog',
			'comment' => 'Comment',
			'package_id' => 'Package',
			'type_id' => 'Type',
			'repository_id' => 'Repository',
            'info' => 'Json info',
            'stat_downloads'=>'Downloads',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($type_id, $package_id)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('date_added',$this->date_added,true);
		$criteria->compare('date_deployed',$this->date_deployed,true);
		$criteria->compare('number',$this->number,true);
		$criteria->compare('generated',$this->generated,true);
		$criteria->compare('deployed',$this->deployed);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('changelog',$this->changelog,true);
        $criteria->compare('package_id',$package_id,true);
		$criteria->compare('type_id',$type_id,true);
		$criteria->compare('repository_id',$this->repository_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']),
            ),
		));
	}
    
    protected function afterDelete(){
		$file = Yii::app()->params['repository'] . $this->id;
		if (is_file($file) == TRUE)
        {
            unlink($file);
        }
		return parent::afterDelete();
	}
}