<?php
return array(
    /*
    |--------------------------------------------------------------------------
    | Enable emailing errors
    |--------------------------------------------------------------------------
    |
    | Should we email error traces?
    |
    */
    'enabled' => !Config::get('app.debug'),

    /*
    |--------------------------------------------------------------------------
    | Skip emailing errors for some HTTP status codes
    |--------------------------------------------------------------------------
    |
    | For which HTTP status codes should we NOT send error emails?
    |
    */
    'disabledStatusCodes' => array(
        '404' => true,
    ),

    /*
    |--------------------------------------------------------------------------
    | Error email recipients
    |--------------------------------------------------------------------------
    |
    | Email stack traces to these addresses.
    |
    | For a single recipient, the format can just be
    |   'to' => array('address' => 'you@domain.com', 'name' => 'Your Name'),
    |
    | For multiple recipients, just specify an array of those:
    |   'to' => array(
    |       array('address' => 'you@domain.com', 'name' => 'Your Name'),
    |       array('address' => 'me@domain.com', 'name' => 'My Name'),
    |   ),
    |
    */

    'to' =>  array( 'przemek@webwizards.pl', 'biuro@webwizards.pl'),


    // The email the error reports will be sent to.
    'dev-email' => array('przemek@webwizards.pl'),

    // send an email even if mail.pretend == true
    'force-email' => false,

    // The error handler email view.
    'error-email-view' => 'error-emailer::error-email',
    'error-email-view-plain' => 'error-emailer::error-email-plain',

    // The alert log handler email view.
    'alert-email-view' => 'error-emailer::alert-email',
    'alert-email-view-plain' => 'error-emailer::alert-email-plain',

    // The view for generic errors (uncaught exceptions). Set to null and the
    // error handler will not return a view, letting you use your own App::error
    // handler to return the appropriate view with the appropriate data.
    'error-view' => 'error-emailer::generic',

    // The view for 404 errors. Set to null for same reason as above
    'missing-view' => 'error-emailer::missing',

    // The view for CSRF errors. Set to null for same reason as above
    'csrf-view' => 'error-emailer::csrf',

    // The PHP date() format that should be used.
    'date-format' => 'Y-m-d H:i:s e',

    // whether to display more detailed information in stack traces.
    'expand-stack-trace' => false,

    // whether to include query logs in error report emails.
    'include-query-log' => false,
);