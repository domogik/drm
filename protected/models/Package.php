<?php

/**
 * This is the model class for table "package".
 *
 * The followings are the available columns in table 'package':
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $type_id
 *
 * The followings are the available model relations:
 * @property Type $type
 * @property Version[] $versions
 */
class Package extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Package the static model class
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
		return 'package';
	}

    public function getVersionsHtml()
    {
        $result = "<ul>";
        foreach($this->versionsDeployed as $version) {
            $result .= "<li>" . $version->getHtml() . "</li>";
        }
        $result .= "</ul>";
        return $result;
    }
    
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, name, type_id', 'required'),
			array('id, name, type_id, category', 'length', 'max'=>45),
			array('author, authorEmail, documentation', 'length', 'max'=>255),
			array('name, description, author, authorEmail, documentation', 'default', 'value'=>null, 'setOnEmpty'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, description, type_id, author, authorEmail, documentation, category', 'safe', 'on'=>'search'),
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
			'type' => array(self::BELONGS_TO, 'Type', 'type_id'),
			'versions' => array(self::HAS_MANY, 'Version', 'package_id, type_id'),
			'versionsDeployed' => array(self::HAS_MANY, 'Version', 'package_id, type_id',
                                        'condition'=>'versionsDeployed.deployed = 1'),
			'versionsCount' => array(self::STAT, 'Version', 'package_id, type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
            'author' => 'Author',
            'authorEmail' => 'Author Email',
			'description' => 'Description',
            'documentation' => 'Documentation',
			'type_id' => 'Type',
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
		$criteria->compare('author',$this->author,true);
		$criteria->compare('authorEmail',$this->authorEmail,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('type_id',$this->type_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=> Yii::app()->user->getState('pageSize',50),
            ),
		));
	}
    
    public function searchPublic($repositories)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('name',$this->name,true, 'OR');
		$criteria->compare('description',$this->description,true, 'OR');
        $criteria->with='versionsDeployed';
        $criteria->addInCondition('versionsDeployed.repository_id', $repositories);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>false,
		));
	}
}