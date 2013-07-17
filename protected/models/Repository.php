<?php

/**
 * This is the model class for table "repository".
 *
 * The followings are the available columns in table 'repository':
 * @property string $id
 * @property string $name
 * @property string $description
 * @property integer $priority
 *
 * The followings are the available model relations:
 * @property Log[] $logs
 * @property Version[] $versions
 */
class Repository extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Repository the static model class
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
		return 'repository';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, name', 'required'),
			array('priority, count', 'numerical', 'integerOnly'=>true),
			array('id, name, icon', 'length', 'max'=>45),
			array('name, description, icon, generated', 'default', 'value'=>null, 'setOnEmpty'=>true),
            array('needRefresh', 'boolean'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, description, priority, icon, count, needRefresh', 'safe', 'on'=>'search'),
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
			'logs' => array(self::HAS_MANY, 'Log', 'repository_id'),
			'versions' => array(self::HAS_MANY, 'Version', 'repository_id'),
			'versionsCount' => array(self::STAT, 'Version', 'repository_id'),
		);
	}

    public function defaultScope() {
        return array('order' => 'priority DESC');
    }
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'description' => 'Description',
			'priority' => 'Suggested Priority',
            'icon' => 'Icon',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('priority',$this->priority);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'t.priority DESC',
            ),
		));
	}
    
    protected function beforeDelete(){
        //Move all Packages to default Repository
        Version::model()->updateAll(array('package_id'=>Yii::app()->params['defaultRepository']), "package_id = '" . $this->id . "'");
        
		return parent::beforeDelete();
	}
}