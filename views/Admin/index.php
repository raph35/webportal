
    <title>Administration</title>
    <?php echo "<link rel='stylesheet' type='text/css' href='".WEBROOT."views/layout/css/cssAdmin.css'>";?>    
    <body>
        <h3>Les utilisateurs connéctés</h3>
        <div class="contenaire center">
            <?php
                $routeur->display();
                
                $view="<script>";
                $view.="setTimeout(update(), 200);";
                $view.="function update()";
                $view.="{";
                $view.="httprequest= new XMLHttpRequest();";
                $view.="httprequest.open('GET','".WEBROOT."Admin/index');";
                $view.="}";
                $view.="</script>";
                echo $view;
            ?>    
    </div>
    </body>