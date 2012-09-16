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

    /** get big list of app files */
    public static function get_app_files() {
        // loop over all include paths to check for app files
        foreach (Kohana::include_paths() as $path) {
            foreach (Controller_App::find_all_files(
                $path.'app') as $ext => $files) {
                    foreach ($files as $file)
                        $ret[$ext][] = $file;
            }
        }

        return $ret;
    }

    /** find all interesting frontend files */
    protected static function find_all_files($dir, $croot = "") {
        $root = @scandir($dir);
        if (!$root)
            return array();

        foreach($root as $value)
        {
            $file = "$dir/$value";

            // skip bullshit
            if ($value === '.' || $value === '..')
                continue;

            // found a file
            if (is_file($file)) {
                $info = pathinfo($file);

                $ext = $info['extension'];
                $result[$ext][] = "$croot$value";
                continue;
            }

            // otherwise recurse
            foreach(Controller_App::find_all_files(
                $file, "$value/") as $ext => $value)
                    $result[$ext] = $value;

        }
        return $result;
    }
}
