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
}
