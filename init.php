<?
// default
Route::set('default_app', 'app(/<action>)')
    ->defaults(array(
        'controller' => 'app',
    ));

// html template package
Route::set('html_template_package', 'app/html_package(/<package>)',
    array('package' => '.+'))
        ->defaults(array(
            'controller'    => 'app',
            'action'        => 'html_template'
        ));

// static file serving
Route::set('frontend_files', 'app(/<extension>)/(<file>)',
    array('file' => '.+'))
        ->defaults(array(
            'controller'    =>  'app',
            'action'        =>  'file',
        ));

