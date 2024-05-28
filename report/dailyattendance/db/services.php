<?php

$functions = [
    // The name of your web service function, as discussed above.
    'report_dailyattendance_get_list_courses' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => 'report_dailyattendance\external\get_list_courses',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the list of courses that matches the customerid, startdate, enddate and status attendance.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            // A standard Moodle install includes one default service:
            // - MOODLE_OFFICIAL_MOBILE_SERVICE.
            // Specifying this service means that your function will be available for
            // use in the Moodle Mobile App.
            MOODLE_OFFICIAL_MOBILE_SERVICE,
        ]
    ],
    // The name of your web service function, as discussed above.
    'report_dailyattendance_get_list_group' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => 'report_dailyattendance\external\get_list_group',

        // A brief, human-readable, description of the web service function.
        'description' => 'Get the list of group that matches the customerid, startdate, enddate and status attendance.',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            // A standard Moodle install includes one default service:
            // - MOODLE_OFFICIAL_MOBILE_SERVICE.
            // Specifying this service means that your function will be available for
            // use in the Moodle Mobile App.
            MOODLE_OFFICIAL_MOBILE_SERVICE,
        ]
    ],
];