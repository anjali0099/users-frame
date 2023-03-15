<!DOCTYPE html>
<html>
    <head>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
    </head>

    <body>
        <div class="container">

            <!-- Layout Header -->
            <div class='row no-margin no-padding'>
                <?php include(assets_dir() . 'Header.php'); ?>
            </div>
            <!-- Layout Header -->
            <div class='row no-margin no-padding'>
                <?php include(assets_dir() . 'Navigation.php'); ?>
            </div>

            <!-- Content Body -->
            <div class='row main-content no-padding'>
                <?= $view_content ?>
            </div>

            

        </div>
    </body>

</html>