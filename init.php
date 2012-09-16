<?

// static file serving
Route::set('frontend_files', 'app(/<extension>)/(<file>)', array('file' => '.+'))
    ->defaults(array(
        'controller'    =>  'app',
        'action'        =>  'file',
    ));

