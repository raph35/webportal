
    <title>Administration</title>
    <link rel="stylesheet" type="text/css" href="/views/layout/css/cssAdmin.css">    
    <body>
        <h3>Les utilisateurs connéctés</h3>
        <div class="contenaire center">
            <?php
                $routeur->display();
            ?>
            <script>
                setTimeout(update(), 200);
                function update()
                {
                    httprequest= new XMLHttpRequest();
                    httprequest.open("GET","/Admin/index");
                }
            </script>
                
    </div>
    </body>