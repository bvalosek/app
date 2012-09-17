<?
/**
 * Controller that outputs all static app files in header
 *
 * css, js, what the fuck ever
 */

class Controller_App_Template extends Controller_Template {

    public $template = 'app/template';

    /** sprinkle in a bit of info */
    public function before() {
        parent::before();
        $this->template->files  = App::get_app_files();
        $this->template->app_info = Kohana::$config->load('version');
    }
}
