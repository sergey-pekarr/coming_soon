<?php

/**
 * This is the model class for table "users_location".
 *
 * The followings are the available columns in table 'users_location':
 * @property integer $user_id
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $zip
 * @property double $latitude
 * @property double $longitude
 */
class UsersLocation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UsersLocation the static model class
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
		return 'users_location';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, country, state, city, zip, latitude, longitude', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('latitude, longitude', 'numerical'),
			array('country, state', 'length', 'max'=>2),
			array('city', 'length', 'max'=>50),
			array('zip', 'length', 'max'=>13),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, country, state, city, zip, latitude, longitude', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'User',
			'country' => 'Country',
			'state' => 'State',
			'city' => 'City',
			'zip' => 'Zip',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
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

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('zip',$this->zip,true);
		$criteria->compare('latitude',$this->latitude);
		$criteria->compare('longitude',$this->longitude);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}