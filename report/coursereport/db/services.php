<?php 

$functions = [
     // The name of your web service function, as discussed above.
     'report_coursereport_get_group_list' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_coursereport\external\get_group_list',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the group list of a selected customer.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'report_coursereport_get_trainee_list' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_coursereport\external\get_trainee_list',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the trainee list of a selected group.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'report_coursereport_get_assessment' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_coursereport\external\get_assessment',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the course assessment.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'report_coursereport_get_course_list' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_coursereport\external\get_course_list',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the course list of a selected customer.',

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