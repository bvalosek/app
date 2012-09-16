<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">

        <!-- info -->
        <title><?= $app_info->name.' v'.$app_info->release_number.'.'.$app_info->build_number ?></title>

        <!-- kickass -->
        <meta name="apple-mobile-web-app-capable" content="yes">

        <!-- js -->
        <? if(isset($files['js'])):
                foreach ($files['js'] as $file):
                    ?><script src="/app/js/<?= $file ?>" type="text/javascript"></script>
        <? endforeach; endif;?>

        <!-- css -->
        <? if(isset($files['css'])):
                foreach ($files['css'] as $file):
                    ?><link href="/app/css/<?= $file ?>" rel="stylesheet" type="text/css" />
        <? endforeach; endif;?>

    </head>
    <body>
<?= $content ?>

    </body>
</html>
