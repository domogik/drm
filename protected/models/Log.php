<?php

/**
 * This is the model class for table "log".
 *
 * The followings are the available columns in table 'log':
 * @property string $id
 * @property string $date
 * @property string $description
 * @property string $repository_id
 *
 * The followings are the available model relations:
 * @property Repository $repository
 */
class Log extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Log the static model class
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
		return 'log';
	}

    public static function info($repository_id, $description) {
        $log = new Log;
        $log->repository_id = $repository_id;
        $log->description = $description;
        $log->save();
        return $log;
    }
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, repository_id', 'required'),
			array('description', 'length', 'max'=>255),
			array('repository_id', 'length', 'max'=>45),
            array('date','default',
                'value'=>new CDbExpression('NOW()'),
                'setOnEmpty'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date, description, repository_id', 'safe', 'on'=>'search'),
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
			'repository' => array(self::BELONGS_TO, 'Repository', 'repository_id'),
		);
	}

    public function scopes()
    {
        return array(
            'today'=>array(
                'condition'=>'DATE(date) = DATE(NOW())',
                'order'=>'date ASC',
            ),
        );
    }
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'date' => 'Date',
			'description' => 'Description',
			'repository_id' => 'Repository',
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('repository_id',$this->repository_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}