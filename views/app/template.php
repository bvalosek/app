<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">

        <!-- kickass -->
        <meta name="apple-mobile-web-app-capable" content="yes">

        <!-- js -->
        <? if(isset($files['js'])):
                foreach ($files['js'] as $file):
                    ?><script src="/app/js/<?= $file ?>"></script>
        <? endforeach; endif;?>

        <!-- css -->
        <? if(isset($files['css'])):
                foreach ($files['css'] as $file):
                    ?><script src="/app/css/<?= $file ?>"></script>
        <? endforeach; endif;?>

    </head>
    <body>
<?= $content ?>

    </body>
</html>
