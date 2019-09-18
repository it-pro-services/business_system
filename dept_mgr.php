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
                    <h1>Departments</h1>
                    <button class="btn btn-lg btn-primary" id="newOrder" type="button" onclick="newDepartment()"><i class="fas fa-plus"></i> New Department</button>
                    <br>
                    <br>
                    <div style="overflow-x:auto" class="main_block" id="crud_department"></div>                   
                    <div style="overflow-x:auto" class="main_block" id="departments_table"></div>                   

                </div>
            </div>            

        </div>

    </body>

    <script>

        function newDepartment(){
            $(".main_block").hide();
            $("#crud_department").Loading();
            $.post('?cmd=newDepartment',function(html){
                $("#crud_department").html(html);
                icheckUpdate();
            })
        }

        function loadDepartments(){
            $(".main_block").hide();
            $("#departments_table").Loading();
            $.post('?cmd=loadDepartments',function(html){
                $("#departments_table").html(html);    
            })

        }
        $("document").ready(function(){
            loadDepartments();
        })

    </script>
</html>
<?
function loadDepartments(){
    global $db, $comp_id;
    $sql = "SELECT ord1.*, nxt.dept as 'next_dept' FROM `departments` ord1 left join `departments` nxt on ord1.next_dept = nxt.dept_id and ord1.comp_id = nxt.comp_id where ord1.comp_id = $comp_id order by dept_id asc";
    $depts = $db->retrieveData($sql);
    if(!is_array($depts)){
?>
        <div class="alert alert-warning" role="alert" style="max-width: 400px;">
            No Departments Yet. Click <span class="btn-link" onclick="newDepartment()">HERE</span> to create one.
        </div>     
    <?
        return;
    }
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Options</th>
                <th>ID</th>
                <th>Name</th>
                <th>Next Department</th>
                <th>End Department</th>
            </tr>
        </thead>
        <tbody>
            <?foreach($depts as $d){
                    if($d['dept_id']=='999') continue;
                ?>
                <tr>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">                                
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" onclick="alert('Edit <?=$d['dept_id']?>')"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <a class="dropdown-item" onclick="alert('Delete <?=$d['dept_id']?>')"><i class="fas fa-trash" style="color:red"></i> Delete</a>

                            </div>
                        </div>

                    </td>

                    <td>
                        <?=$d['dept_id']?>
                    </td>
                    <td>
                        <?=$d['dept']?>                        
                    </td>
                    <td>
                        <?=$d['next_dept']?>
                    </td>
                    <td>
                        <?=($d['end']==1)?'<i class="fas fa-check"></i>':'';?>
                    </td>
                </tr>

            <?}?>

        </tbody>
    </table>
    <?


}

function newDepartment(){    
    ?>
    <table class="table" style="max-width:400px;"id="newDeptTbl">
    <tr>
        <th>Department Name</th>
        <td><input type="text" name="deptName" class="form-control"></input></td>
    </tr>
    <tr>
        <th>End Dept?</th>
        <td>
            <input type="checkbox" class="icheck form-check-input" id="endDept" name="endDept" value="end">
        </td>
    </tr>
    <tr>
        <td colspan="2"><button class="btn btn-primary"onclick="saveDept();">Save</button></td>  
    </tr>
    </table>
    <script>
        function saveDept(){
            $.post('?cmd=createDept',$("#newDeptTbl").Values(),function(res){
                if(res == 'true'){                    
                    toast('green','Success!','Department Added');    
                    loadDepartments();
                }else{
                    toast('green','Success!','Department Added');    
                }

            })
        }
    </script>
    <?    
}

function createDept(){    
    global $db,$comp_id;
    $deptName = esc(Post('deptName'));    
    $endDept = (Post('endDept') == 'end')?1:0;

    $sql = "INSERT INTO `departments`(`dept`, `end`,`comp_id`) VALUES ('$deptName',$endDept,$comp_id)";
    echo $db->runSQL($sql);
}