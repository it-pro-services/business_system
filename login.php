<?
require 'libs/page.php';
$page = new Page();
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
            <div id="page-content-wrapper">

                <div class="container" style="max-width:300px;">

                    <form class="form-signin" id="login" style="padding-top:100px;">
                        <h2 class="form-signin-heading">Please sign in</h2>

                        <input type="email" name="username" id="username" class="form-control" placeholder="Username or Email" required autofocus>
                        <br>

                        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>

                        <span style="color:red;" class='alert'>&nbsp;</span>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember Me</label>
                        </div>
                        <br>
                        <button class="btn btn-lg btn-primary btn-block" id="loginBtn" type="button" onclick="validate()">Sign in</button>

                    </form>

                </div> <!-- /container -->

            </div>            

        </div>

    </body>

    <script>
        $('document').ready(function(){


            <?$prev = $page->encryption(Get('prev'),'d');
            if($prev != FALSE){
            ?>localStorage.setItem('prev_page','<?=$prev?>');<?    
            }else{
            ?>localStorage.setItem('prev_page','');<?
            }?>
            
        })
        validate = function(){
            $('.alert').html("");//clear the alert field
            //disable input fields/button here and loading symbol
            $.post('?cmd=validate',$('#login').Values(),function($res){

                if($res == true){
                    localStorage.setItem('username',$('#username').val());

                    if(localStorage.prev_page == '' || localStorage.prev_page == 'login.php'){                        
                        localStorage.prev_page = 'index.php'
                    }
                    window.location.replace(localStorage.prev_page);
                }
                else{
                    $('.alert').html("Invalid Username or Password");                    
                }

            })

        }

        $("#username").keyup(function(event) {
            if (event.keyCode === 13) {
                $("#loginBtn").click();
            }
        });
        $("#inputPassword").keyup(function(event) {
            if (event.keyCode === 13) {
                $("#loginBtn").click();
            }
        });


    </script>
</html>

<?
function validate(){
    global $db,$page;
    $username = strtolower($_POST['username']);
    $password = $_POST['password'];

    $sql = "select password, user_id, comp_id from users where username = '".$username."'";
    $hashed_password = $db->retrieveRow($sql);

    if(is_array($hashed_password)){
        if (hash_equals($hashed_password['password'], crypt($password, $hashed_password['password']))) {
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $hashed_password['user_id'];            
            $_SESSION['comp_id'] = $hashed_password['comp_id'];
            $_SESSION['loggedIn'] = true;            

            echo true;
        }
        else{
            echo false;
        }        

    }else{
        echo false;
    }

}

function logout(){

    session_start();
    session_unset();
    session_destroy();

}
?>