<?
//This gets the page code that we use on all the pages (like the nav-bar and side bar, so we don't need to re-type it every time...)
require 'libs/page.php';
$page = new Page();
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
                    <h1>Title</h1>
                    <p>The starting state of the menu will appear collapsed on smaller screens, and will appear non-collapsed on larger screens. When toggled using the button below, the menu will change.</p>
                    <p>Make sure to keep all page content within the <code>#page-content-wrapper</code>. The top navbar is optional, and just for demonstration. Just create an element with the <code>#menu-toggle</code> ID which will toggle the menu when clicked.</p>
                    When you click this button it will call the jQUERY function called doSomething():
                    <button class="btn btn-primary" onclick="doSomething(this)">Run PHP Code</button>
                    <div id="res"></div>
                </div>
            </div>            

        </div>

        <script>
            //This is the function that is called when the "Run PHP Code" button is clicked
            function doSomething(t){
                //Change the button text using jQUERY to let the user know that something is happening
                $(t).html('Running PHP...');
                //$.post is where we send data to a page, a parameter called 'cmd' and 'variable', then the results are stored in 'res'
                //note: ?cmd is the same as blank_page.php?cmd
                $.post('?cmd=runPHP','&variable=RandomInfo',function(res){
                    //at this point we have sent "RandomInfo" to the runPHP PHP function and now we have the results of the function stored in the 'res' variable.
                    $("#res").html(res);//this puts the res variable in the <div id="res"></div> tags on the page.
                    
                    $(t).html('Run PHP Code'); //This resets the button text now that everything has run.
                })
            }
        </script>        
    </body>

</html>

<?
//this is the php function called from that $.post(?cmd=runPHP) in the javascript up there
//Note that this function can be located in any file, I just keep it in the same to help organize, it is here in the same page since we did ?cmd=runPHP instead of some_page.php?cmd=runPHP
function runPHP(){
    //to get the value passed to the function we use Post('');
    $variable = $_POST['variable']; //This will set $variable to "RandomInfo", this is usually data from forms or something the user enters on the page...
    
    //an easy way to pass data back is to use the php function called echo
    
    echo '<br>You sent the word "'.$variable.'" and we put it in this sentence.';//Try changing the &variable=RandomInfo (on line 41) to &variable=SomethingElse or something to see how it changes
}
?>