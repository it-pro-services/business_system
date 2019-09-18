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
                <h1>Status Manager</h1>
                <button class="btn btn-lg btn-primary" id="newStatus" type="button" onclick="newStatus()">New Status</button>
                <div style="overflow-x:auto" class="main_blocks" id="status_list"></div>                
                <div style="overflow-x:auto" class="main_blocks" id="new_status"></div>                

            </div>
        </div>            

    </div>
    <script>
        function loadStatus(){
            $(".main_blocks").hide();
            $("#status_list").Loading();
            $.post('?cmd=loadStatus',function(html){
                $("#status_list").html(html);
            })
        }
        loadStatus();
        function newStatus(){
            $(".main_blocks").hide();
            $("#new_status").Loading();
            $.post('?cmd=newStatus',function(html){
                $("#new_status").html(html);
            })
        }
    </script>
</body>
<?
function newStatus(){
?>  
    <h2>New Status</h2>
    <table class="table" style="max-width:400px">
        <tr>
            <th>Name</th>
            <td><input class="form-control" id="new_name" placeholder="Open"></td>
        </tr>
        <tr>
            <th>Color</th>
            <td><input type="color" class="form-control" id="new_stat_color" value="#FFFFFF" ></td>
        </tr>

        <tr>
            <th><button class="btn btn-primary" onclick="saveNewStatus(this)">Save</button></th>
            <td style="text-align: right;"><button style="display:inline"  class="btn btn-secondary" onclick="loadStatus()">Cancel</button></td>
        </tr>

    </table>
    <script>

        function saveNewStatus(t){
            $(t).Loading();
            $.post('?cmd=saveNewStaus','&name='+$("#new_name").val()+'&color='+$("#new_stat_color").val(),function(){
                toast('green','Success!','New Status Added');
                loadStatus();
            })
        }

    </script>
    <?
}
function saveNewStaus(){
    global $db, $comp_id;
    $name = Post('name');
    $color = Post('color');
    if(substr($color,0,1) == '#'){
        $color = substr($color, 1);
    }
    $id = $db->retrieveValue("select coalesce(max(status_id)+1,1) from status where comp_id = $comp_id and status_id < 99");
    $sql = "INSERT INTO `status`(`status_id`, `status_name`, `color`, `comp_id`) VALUES ($id,'$name','$color',$comp_id)";
    $res = $db->runSQL($sql);
}
function loadStatus(){
    global $db, $comp_id;
    $sql = "select * from status where comp_id = $comp_id order by status_id asc";
    $stats = $db->retrieveData($sql);

    if(!is_array($stats)){
        echo 'No statuses yet.';
        return;
    }
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Edit</th>
                <th>Status Name</th>
                <th>Color</th>
            </tr>
            <?
            foreach($stats as $s){
            ?><tr>
                    <td><?if($s['status_id']!=99){?><button onclick="editStatus('<?=$s['status_id']?>','<?=$s['color']?>','<?=$s['status_name']?>')">/</button><?}else{echo'&nbsp;';}?></td>
                    <td><?=$s['status_name']?></td>
                    <td style="background-color:#<?=$s['color']?>;">
                    </td>
            </tr><?
            }
            ?>
        </thead>
    </table>
    <script>
        function editStatus(stat_id,color,name){
            $.post('?cmd=editStatModal','&stat='+stat_id+'&color='+color+'&name='+name,function(html){
                $('body').append(html);
                $('#editStatModal').modal();
            })
        }
    </script>
    <?
}

function editStatModal(){
    $stat = Post('stat');
    $color = Post('color');
    $name = Post('name');

    ?>
    <div class="modal fade" id="editStatModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Edit Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <th>Name</th>
                            <td><input class="form-control" id="name" value="<?=$name?>"></td>
                        </tr>
                        <tr>
                            <th>Color</th>
                            <td><input type="color" class="form-control" id="stat_color" value="#<?=substr($color,0,6);?>"></td>
                        </tr>

                    </table>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close_editStatModal" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="deleteStatus()" >Delete</button>
                    <button type="button" class="btn btn-primary" onclick="saveStatus()" >Save</button>
                </div>

            </div>
        </div>
    </div>
    <script>
        function saveStatus(){
            $.post('?cmd=saveStatus','&stat=<?=$stat?>&name='+$("#name").val()+'&color='+$("#stat_color").val(),function(){
                $(".close_editStatModal").trigger('click');
                loadStatus();
                toast('green','Success','Status Saved');
            })
        }
        function deleteStatus(id){
            $.post('?cmd=deleteStatus','&stat=<?=$stat?>',function(){
                $(".close_editStatModal").trigger('click');
                loadStatus();
                toast('green','Success','Status Deleted');
            })
        }
    </script>
<?
}
function deleteStatus(){
    global $comp_id, $db;
    $stat = Post('stat');

    $db->runSQL("DELETE FROM `status` WHERE `status_id` = $stat and `comp_id` = $comp_id");
    
}
function saveStatus(){
    global $comp_id, $db;
    $stat = Post('stat');
    $name= Post('name');
    $color= Post('color');
    if(substr($color,0,1) == '#'){
        $color = substr($color, 1);
    }
    $sql = "UPDATE `status` SET `status_name`='$name',`color`='$color' WHERE `comp_id` = $comp_id and `status_id` = $stat";
    $db->runSQL($sql);


}
?>