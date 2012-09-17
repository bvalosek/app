<?
/**
 * Basic app stuff
 */

class App {

    /** get big list of app files */
    public static function get_app_files() {
        // loop over all include paths to check for app files
        foreach (Kohana::include_paths() as $path) {
            foreach (App::find_all_files(
                $path.'app') as $ext => $files) {
                    foreach ($files as $file)
                        $ret[$ext][] = $file;
            }
        }

        foreach ($ret['js'] as $jsfile) {
            $info = App::parse_file($jsfile);
            echo "\n$jsfile aka $info->package requires ";
            foreach ($info->requires as $req)
                echo "$req, ";
        }

        return $ret;
    }

    /** parse the header for information */
    protected static function parse_file($file_name) {
        $ext    = pathinfo($file_name, PATHINFO_EXTENSION);
        $file   = substr($file_name, 0, -(strlen($ext) + 1));
        $file = Kohana::find_file('app', $file, $ext);

        $file = fopen($file, 'r');

        // look for info
        $requires = array();
        $package = $file_name;
        while (!feof($file)) {
            $matches = array();

            if (preg_match('/@require\s(.+)/', fgets($file), $matches)) {
                $requires[] = $matches[1];
            }

            if (preg_match('/@package\s(.+)/', fgets($file), $matches)) {
                $package = $matches[1];
            }
        }

        return (object)array(
            'requires' => $requires,
            'package' => $package,
        );

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
            foreach(App::find_all_files(
                $file, "$value/") as $ext => $value)
                    $result[$ext] = $value;

        }
        return $result;
    }
}
