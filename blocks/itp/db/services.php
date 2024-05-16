<?php 

$functions = [
    // The name of your web service function, as discussed above.
    'block_itp_get_itp' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\block_itp\external\get_itp',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the ITP displayed in dashboard by startdate.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'block_itp_get_assessment_details' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\block_itp\external\get_assessment_details',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the assessment details given: courseid and user details.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'block_itp_get_daily_attendance' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\block_itp\external\get_daily_attendance',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the daily attendance of a given: courseid and user details.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'block_itp_get_group' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\block_itp\external\get_group',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the group of a given idgroup.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
];

//Queda pendiente averiguar como puñetas hacer un custom service.
$service = array(
    'Custom web service ITP'=> array(
        'functions'=>array(
            'block_itp_get_itp'
        ),
        'restrictedusers'=>0,
        'enabled'=>1,
        'shortname'=>'custom_block_itp_service'
    ),
);

