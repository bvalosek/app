<?
/**
 * App controller for serving up sexy javascript.
 *
 */

class Controller_App extends Controller {

    public $template = NULL;

    public function before() {
        parent::before();

        // disable template
        $this->auto_render = FALSE;
    }

    /** output package information as JSON */
    public function action_packages() {
        $this->response->headers('content-type', 'text/javascript');
        echo json_encode(App::get_packages());
    }

    /** output list of files relative to doc root */
    public function action_output_files($ext = 'js') {
        $files = App::get_app_files();
        $files = $files[$ext];
        foreach ($files as $f) {
            $f = preg_replace('!/app/[^/]+/!', '', $f);
            $f = str_replace('.'.$ext, '', $f);
            $f = Kohana::find_file('app', $f, $ext);
            echo "$f\n";
        }
    }

    /** serve some templated HTML */
    public function action_html_template() {
        $package_name = $this->request->param('package');
        $packages = App::get_packages();
        $packages = $packages['html'];

        if (!array_key_exists($package_name, $packages)) {
            $this->response->status(404);
            return;
        }

        $package = $packages[$package_name];

        $file = Kohana::find_file('app',
            $package->kohana_file, $package->extension);

        $html = file_get_contents($file);

        // merge fields
        if ($this->request->query('data'))
            foreach ($this->request->query('data') as $id => $replace) {
                $html = preg_replace('/\$\{'.$id.'\}/', $replace, $html);
            }

        echo $html;
    }

    /** serve a static front-end file */
    public function action_file() {
        // find kohana file
        $ext = $this->request->param('extension');
        $file = $this->request->param('file');
        $file = substr($file, 0, -(strlen($ext) + 1));
        $file = Kohana::find_file('app', $file, $ext);

        if (!$file) {
            $this->response->status(404);
            return;
        }

        // Check if the browser sent an "if-none-match: <etag>" header, and
        // tell if the file hasn't changed
        $this->response->check_cache(
            sha1($this->request->uri()).filemtime($file), $this->request);

        // Send the file content as the response
        $this->response->body(file_get_contents($file));

        // Set the proper headers to allow caching
        $this->response->headers('content-type',  File::mime_by_ext($ext));
        $this->response->headers('last-modified', date('r', filemtime($file)));

    }
}
