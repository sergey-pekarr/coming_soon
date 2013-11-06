<?php 
return array(
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Guest',
        'bizRule' => null,
        'data' => null
    ),
    'manager' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Manager',
        'children' => array(
            'guest',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'administrator' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Administrator',
        'children' => array(
            'manager',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'superadmin' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Super Administrator',
        'children' => array(
            'administrator',
        ),
        'bizRule' => null,
        'data' => null
    ),
);
