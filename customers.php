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
                    <h1>Customers</h1>
                    <button class="btn btn-lg btn-primary" id="newOrder" type="button" onclick="newCustomer()"><i class="fas fa-user"></i> New Customer</button>
                    <br>
                    <br>
                    <div style="overflow-x:auto" class="main_block" id="new_customer"></div>                   
                    <div style="overflow-x:auto" class="main_block" id="customers_table"></div>                   

                </div>
            </div>            

        </div>

    </body>

    <script>

        function newCustomer(){
            $(".main_block").hide();
            $("#new_customer").Loading();
        }

        function loadCustomers(){
            $(".main_block").hide();
            $("#customers_table").Loading();
            $.post('?cmd=loadCustomers',function(html){
                $("#customers_table").html(html);    
            })

        }
        $("document").ready(function(){
            loadCustomers();
        })

    </script>
</html>
<?
function loadCustomers(){
    global $db, $comp_id;
    $sql = "SELECT * FROM `customers` where `comp_id` = $comp_id and active = 1";
    $customers = $db->retrieveData($sql);
    if(!is_array($customers)){
?>
        <div class="alert alert-warning" role="alert">
            No Customers. Click <span class="btn-link" onclick="newCustomer()">Here</span> to add one.
        </div>
    <?
        return;        
    }
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Options</th>
                <th>Customer#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Cell</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?
            foreach($customers as $c){
            ?><tr>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">                                
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" onclick="alert('Edit <?=$c['cust_id']?>')"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <a class="dropdown-item" onclick="alert('Delete <?=$c['cust_id']?>')"><i class="fas fa-trash" style="color:red"></i> Delete</a>
                                
                            </div>
                        </div>
                    </td>
                    <td><?=$c['cust_id']?></td>
                    <td><?=$c['name']?></td>
                    <td><span class="btn-link" onclick="document.location.href = 'tel:<?=$c['phone']?>';"><?=$c['phone']?></td>
                    <td><span class="btn-link" onclick="document.location.href = 'tel:<?=$c['cell']?>';"><?=$c['cell']?></td>
                    <td><span class="btn-link" onclick="document.location.href = 'mailto:<?=$c['email']?>';"><?=$c['email']?></span></td>
            </tr><?
            }?>            
        </tbody>
    </table>

    <?
}