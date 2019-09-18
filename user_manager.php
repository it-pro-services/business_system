<?
require 'libs/page.php';
$page = new Page();
$page->Authenticate();
$comp_id = $_SESSION['comp_id'];
$db = new DBC();
$page::cmd();
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <?=$page->head();?>        
    </head>

    <body>

        <div class="d-flex" id="wrapper">        
            <!-- Sidebar -->
            <?=$page->sideBar();?>

            <!-- Page Content -->
            <div id="page-content-wrapper">

                <?=$page->navBar()?>

                <div class="container-fluid">
                    <h1>Users</h1>
                    <button class="btn btn-lg btn-primary" id="newOrder" type="button" onclick="newuser()"><i class="fas fa-plus"></i> New user</button>
                    <br>
                    <br>
                    <div style="overflow-x:auto" class="main_block" id="new_user"></div>                   
                    <div style="overflow-x:auto" class="main_block" id="users_table"></div>                   

                </div>
            </div>            

        </div>

    </body>

    <script>

        function newuser(){
            $(".main_block").hide();
            $("#new_user").Loading();
        }

        function loadusers(){
            $(".main_block").hide();
            $("#users_table").Loading();
            $.post('?cmd=loadusers',function(html){
                $("#users_table").html(html);    
            })

        }
        $("document").ready(function(){
            loadusers();
        })

    </script>
</html>
<?
function loadusers(){
    global $db, $comp_id;
    $sql = "";
    
}