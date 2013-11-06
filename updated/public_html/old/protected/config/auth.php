<?php 
return array(
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Guest',
        'bizRule' => null,
        'data' => null
    ),
    'deleted' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Deleted',
        'children' => array(
            'guest',
        ),
        'bizRule' => null,
        'data' => null
    ),    
    'banned' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Banned',
        'children' => array(
            'guest',
        ),    
        'bizRule' => null,
        'data' => null
    ),    
    'justjoined' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Just Joined',
        'children' => array(
            'guest',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'free' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Free',
        'children' => array(
            'justjoined',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'gold' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Gold',
        'children' => array(
            'free',
        ),
        'bizRule' => null,
        'data' => null
    ),    
    
    
    /*'limited' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Limited',
        'children' => array(
            'justjoined',
        ),
        'bizRule' => null,
        'data' => null
    ),    
    'approved' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Approved',
        'children' => array(
            'limited',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'user' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'User',
        'children' => array(
            'trial',
        ),
        'bizRule' => null,
        'data' => null
    ),*/
    /*'moderator' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Moderator',
        'children' => array(
            'approved', 
        ),
        'bizRule' => null,
        'data' => null
    ),
    'administrator' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Administrator',
        'children' => array(
            'moderator', 
        ),
        'bizRule' => null,
        'data' => null
    ),*/
    
    
    
    
    
    'banned' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Banned',
        'children' => array(
            'justjoined',
        ),
        'bizRule' => null,
        'data' => null
    ),      
);
