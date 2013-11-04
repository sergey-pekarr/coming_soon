<?php

/**
 * This is the model class for table "user_image".
 *
 * The followings are the available columns in table 'user_image':
 * @property string $user_id
 * @property integer $n
 * @property string $primary
 * @property string $approved
 * @property string $xrated
 */
class UserImage extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserImage the static model class
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
		return 'user_image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, n', 'required'),
			array('n', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>10),
			array('primary, approved', 'length', 'max'=>1),
			array('xrated', 'length', 'max'=>7),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, n, primary, approved, xrated', 'safe', 'on'=>'search'),
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
                    'image_user'=>array(self::BELONGS_TO, 'Users', 'id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'User',
			'n' => 'N',
			'primary' => 'Primary',
			'approved' => 'Approved',
			'xrated' => 'Xrated',
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

		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('n',$this->n);
		$criteria->compare('primary',$this->primary,true);
		$criteria->compare('approved',$this->approved,true);
		$criteria->compare('xrated',$this->xrated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}