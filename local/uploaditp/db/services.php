<?php 

$functions = [
     // The name of your web service function, as discussed above.
     'local_uploaditp_remove_tables' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => '\local_uploaditp\external\remove_tables',

        // A brief, human-readable, description of the web service function.
        'description' => 'Reset the tables itp and trainingplan.',

        // Options include read, and write.
        'type'        => 'write',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE
        ]
        
    ],
    
];

