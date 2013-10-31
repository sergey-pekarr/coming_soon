<?php

class HelperPromo
{
    /**
     * returns RANDOM promos
     * using range of ids in database
     * @return array of id
     * WITHOUT CHECKING PROMO exists or not
     */
    static public function getPromosRandom($limitFrom, $limitTo)
    {
    	$range1 = array(110790, 501202);//promos only in this range!!!
		
    	$limit = rand($limitFrom, $limitTo);
    	
    	$ids = array();
    	while (count($ids)<$limit)
    	{
    		$id = rand($range1[0], $range1[1]);
    		if (!in_array($id, $ids))
    			$ids[] = $id;
    	}
    	
    	return $ids;
    }
}
