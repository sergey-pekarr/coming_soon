<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $passwd
 * @property string $role
 * @property string $expire_at
 * @property string $birthday
 * @property string $gender
 * @property string $looking_for_gender
 * @property integer $pics
 * @property string $promo
 * @property string $form
 * @property string $affid
 * @property string $sbc
 * @property string $panamus_id
 */
class Users extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Users the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'users';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('username, email, password, passwd, expire_at, pics, sbc, panamus_id', 'required'),
            array('pics', 'numerical', 'integerOnly' => true),
            array('username, password, panamus_id', 'length', 'max' => 32),
            array('email', 'length', 'max' => 129),
            array('passwd', 'length', 'max' => 255),
            array('role, affid, sbc', 'length', 'max' => 10),
            array('gender, promo', 'length', 'max' => 1),
            array('looking_for_gender', 'length', 'max' => 3),
            array('form', 'length', 'max' => 4),
            array('birthday', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, username, email, password, passwd, role, expire_at, birthday, gender, looking_for_gender, pics, promo, form, affid, sbc, panamus_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'images'=>array(self::HAS_MANY, 'UserImage', 'user_id'),
            'location'=>array(self::HAS_ONE, 'UsersLocation', 'user_id'),
        );
        
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'passwd' => 'Passwd',
            'role' => 'Role',
            'expire_at' => 'Expire At',
            'birthday' => 'Birthday',
            'gender' => 'Gender',
            'looking_for_gender' => 'Looking For Gender',
            'pics' => 'Pics',
            'promo' => 'Promo',
            'form' => 'Form',
            'affid' => 'Affid',
            'sbc' => 'Sbc',
            'panamus_id' => 'Panamus',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('passwd', $this->passwd, true);
        $criteria->compare('role', $this->role, true);
        $criteria->compare('expire_at', $this->expire_at, true);
        $criteria->compare('birthday', $this->birthday, true);
        $criteria->compare('gender', $this->gender, true);
        $criteria->compare('looking_for_gender', $this->looking_for_gender, true);
        $criteria->compare('pics', $this->pics);
        $criteria->compare('promo', $this->promo, true);
        $criteria->compare('form', $this->form, true);
        $criteria->compare('affid', $this->affid, true);
        $criteria->compare('sbc', $this->sbc, true);
        $criteria->compare('panamus_id', $this->panamus_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}