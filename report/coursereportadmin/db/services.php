<?php 

$functions = [
     
    'report_coursereportadmin_get_total_assessment' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_coursereportadmin\external\get_total_assessment',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the total assessment of all courses.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'report_coursereportadmin_get_total_dailyattendance' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_coursereportadmin\external\get_total_dailyattendance',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the total dailyattendance of all courses.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    'report_coursereportadmin_get_total_trainee_report' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\report_coursereportadmin\external\get_total_trainee_report',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the total trainee report.',

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