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

        // build out the file list for JS
        foreach ($ret as $ext => $files) {
            $sources = array();
            foreach ($files as $file) {
                $info = App::parse_file($file);

                if(array_key_exists($info->package, $sources))
                    die("Package colision: $info->package");

                $sources[$info->package] = (object)array(
                    'file' => '/app/'.$info->extension.'/'.$file,
                    'requires' => $info->requires,
                    'added' => false,
                );
            }

            // output
            $output = array();
            foreach ($sources as $source => $info)
                App::add_source($source, $sources, $output);

            $ret[$ext] = $output;
        }

        return $ret;
    }

    /** create an array of sources files to be added in the correct order */
    protected static function add_source($source_key, &$sources, &$output_list) {
        if (!array_key_exists($source_key, $sources))
            die("package not found: $source_key");
        $source = $sources[$source_key];

        if ($source->added)
            return;

        foreach ($source->requires as $req) {
            if ($req == $source_key)
                die ("circular dependency on $req");
            App::add_source($req, $sources, $output_list);
        }

        $source->added = true;
        $output_list[] = $source->file;
    }

    /** parse the header for information */
    protected static function parse_file($file_name) {
        $ext    = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_n  = substr($file_name, 0, -(strlen($ext) + 1));
        $file = Kohana::find_file('app', $file_n, $ext);

        $file = fopen($file, 'r');

        // look for info
        $requires = array();
        $package = $file_name;
        while (!feof($file)) {
            $matches = array();
            $str = fgets($file);

            if (preg_match('/@require\s+([^\s]+)/', $str, $matches)) {
                $requires[] = $matches[1];
            }

            if (preg_match('/@package\s+([^\s]+)/', $str, $matches)) {
                $package = $matches[1];
            }

            if (preg_match('/@namespace\s+([^\s]+)/', $str, $matches)) {
                $package = $matches[1].'.'.$file_n;
            }
        }

        return (object)array(
            'requires' => $requires,
            'package' => $package,
            'extension' => $ext,
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
