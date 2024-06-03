<?php 

$functions = [
     // The name of your web service function, as discussed above.
     'report_partialplan_get_training_plan' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_partialplan\external\get_training_plan',

        // A brief, human-readable, description of the web service function.
        'description' => 'Display the training plan for a selected date, customer, group and billid.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    // The name of your web service function, as discussed above.
    'report_partialplan_get_courses' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_partialplan\external\get_courses',

        // A brief, human-readable, description of the web service function.
        'description' => 'Display the list of courses filtered by date.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'report_partialplan_get_trainees' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_partialplan\external\get_trainees',

        // A brief, human-readable, description of the web service function.
        'description' => 'Display the list of trainees for a course.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'report_partialplan_get_group_list' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_partialplan\external\get_group_list',

        // A brief, human-readable, description of the web service function.
        'description' => 'Display the list of group for a selected customer.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'report_partialplan_get_assessment' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_partialplan\external\get_assessment',

        // A brief, human-readable, description of the web service function.
        'description' => 'Display the list of group for a selected customer.',

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

