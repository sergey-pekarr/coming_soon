<?php
/**
  * Encode / decode text
  */

class CSecur extends CApplicationComponent
{
    public function encryptID( $id )  
    {
        /*$id = (string)$id;        
        $lenID = strlen($id);
        $res = MD5($id);
        for ($i=0; $i<$lenID; $i++)
        {
            $res{2*$i} = $id{$lenID-$i-1};
        }
        $lenHex = dechex(($id{$lenID-1}+1)*$lenID);
        $lenHex = str_pad($lenHex, 2, '00', STR_PAD_LEFT);
        $res{29} = $lenHex{0};
        $res{30} = $lenHex{1};
        return $res;  */
        return MD5( SALT . $id );//do not change algorithm
    }
       
    public function decryptID( $str )
    {
        if (strlen($str)!=32)
        {
            return 0;
        }
  
        $mkey = "users_secur_id_".$str;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT user_id FROM users_secur_id WHERE secur_id='{$str}' LIMIT 1";
            $res = Yii::app()->db->createCommand($sql)->queryScalar();
            Yii::app()->cache->set($mkey, $res);
        }
        return $res;

        /*if (strlen($str)!=32)
        {
            return 0;
        }
        
        $lenID = $str{29}.$str{30};
        $lenID = hexdec($lenID);
        $lenID = $lenID/($str{0}+1);
        
        for ($i=$lenID-1; $i>=0; $i--)
        {
            $id .= $str{2*$i};
        }
        
        return intval($id);*/  
    }
    
    
    
    public function encryptByYii( $text )  
    {
        $sec = Yii::app()->getSecurityManager();
        $sec->cryptAlgorithm = 'des';
        return $sec->encrypt($text, SECURE_KEY);
    }
    
    public function decryptByYii( $text )  
    {
        $sec = Yii::app()->getSecurityManager();
        $sec->cryptAlgorithm = 'des';
        return $sec->decrypt($text, SECURE_KEY);
    }
    
    
    
    
    public function encryptByLikeNC( $text )  //like NC
    {
        require_once Yii::app()->basePath. "/vendors/NC/class.pcrypt.php";
		$crypt = new pcrypt(MODE_ECB, "BLOWFISH", 'naughty2008blowfish2192key');
		return base64_encode($crypt->encrypt($text));
    }
    public static function decryptByLikeNC( $text )  //like NC
    {
        require_once Yii::app()->basePath. "/vendors/NC/class.pcrypt.php";        
		$crypt = new pcrypt(MODE_ECB, "BLOWFISH", 'naughty2008blowfish2192key');
		return $crypt->decrypt(base64_decode($text));
    }    
}
