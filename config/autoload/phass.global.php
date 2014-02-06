<?php 

return array(
    'phass' => array(
        'applicationName' => "Phass Demo Application",
        'development' => (APPLICATION_ENV == "development"),
        'subscriptionController' => 'Application\Controller\GlassSubscriptions'
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\GlassSubscriptions' => 'Application\Controller\GlassSubscriptionController'
         )
    )
);
