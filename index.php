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
                    <h1>Order Overview</h1>
                    <button class="btn btn-lg btn-primary" id="newOrder" type="button" onclick="newOrder()">New Order</button>
                    <div style="overflow-x:auto" class="main_blocks" id="order_area"></div>
                    <div style="overflow-x:auto" class="main_blocks" id="order_view"></div>

                </div>
            </div>            

        </div>

    </body>

    <script>
        $('document').ready(function(){
            //$('.container-fluid').Loading();
            loadOrders();           

        })

        function loadOrders(){
            $(".main_blocks").hide();
            $("#order_area").show().Loading();
            $.post('?cmd=loadOrders',function(html){
                $("#order_area").html(html);
            })
        }

        function loadOrder(ord_id){
            document.location.href = 'view_ord.php?ord='+ord_id;
            //this needs work:
            //document.location.href = 'order_inquiry.php?order='+ord_id;
        }


        function newOrder(){
            document.location.href = 'order_entry.php';   
        }
    </script>
</html>
<?
function loadOrders(){

    global $db,$comp_id, $page;

    $sql = "SELECT ord.*, ord_sum.total, c.*, s.*, rm.dept_id, d.dept_id, d.dept, d.next_dept, d.end FROM `orders` ord 
    left join (select uid, sum(total) as total from `order_lines` group by uid) ord_sum on ord.uid = ord_sum.uid 
    left join `customers` c on ord.cust_id = c.cust_id and ord.comp_id = c.comp_id
    left join `status` s on ord.status = s.status_id and ord.comp_id = s.comp_id
    left join `route_manager` rm on ord.uid = rm.uid
    left join `departments` d on rm.dept_id = d.dept_id and rm.comp_id = d.comp_id
    where c.comp_id = $comp_id 
    and ord.comp_id = $comp_id
    and ord.status not in(99)
    order by ord.order_id";
    $orders = $db->retrieveData($sql);

?>
    <style>
        .table td, .table th {
            padding: 0.4rem;
        }
    </style>
    <table class="table" style="margin-top:10px;">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Type</th>
                <th>Order Due</th>                                
                <th style="text-align: right;">Invoiced</th>                                
                <th>Status</th>
                <th>&nbsp;</th><!--//Tags-->
                <th>Operation</th>
                <th>Close Ord</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            <?
            foreach($orders as $o){                                                
                $flag = '&nbsp;';
                if(strtotime($o['order_due']) < strtotime('now')){
                    $flag = '<span class="bg-danger" style="float:left; text-align:center; !important; color:white;"><i class="fas fa-flag"></i> LATE</span>';
                }
            ?>
                <tr <?='style="background-color:#'.substr($o['color'],0,6).'9c"'?>>
                    <td  onclick="loadOrder('<?=$page->encryption($o['order_id'])?>')"><?=$o['order_id']?></td>
                    <td  onclick="loadOrder('<?=$page->encryption($o['order_id'])?>')"><?=Date('m/d/Y',strtotime($o['order_date']))?></td>
                    <td  onclick="loadOrder('<?=$page->encryption($o['order_id'])?>')"><?=$o['name']?></td>
                    <td><?=$o['order_type']?></td>
                    <td><?=Date('m/d/Y',strtotime($o['order_due']))?></td>
                    <td style="text-align:right;">$<?=($o['total'] == '')?'0.00':$o['total'];?></td>
                    <td>                        
                        <?
                        $sql = "select * from status where comp_id = $comp_id order by status_id asc";
                        $stats = $db->retrieveData($sql);
                        ?><select onchange="updateStatus(this,'<?=$o['uid']?>')" class="form-control">
                            <?
                            foreach($stats as $s){
                            ?><option value="<?=$s['status_id']?>" <?=($s['status_id'] == $o['status'])?'selected':'';?>><?=$s['status_name']?></option><?
                            }
                            ?>
                        </select>
                    </td>
                    <td style="text-align:left;"><?=$flag;?></td>
                    <td><?=$o['dept']?></td>
                    <td><button class='btn btn-secondary' onclick="closeOrder('<?=$o['uid']?>',this); event.stopPropagation();return false;">CLOSE</button></td>
                    <td  onclick="loadOrder('<?=$page->encryption($o['order_id'])?>')"><i class="fas fa-search"></i></td>
                </tr>
            <?
            }
            
            ?>
            <script>
            function updateStatus(t,uid){
                $stat = $(t).val();
                $.post('?cmd=updateStatus','&stat='+$stat+'&uid='+uid, function(res){
                        if(res == true){
                            toast('green','Success!','Status updated');
                        }else{
                            toast('red','Error!','Couldn\'t Save Status');
                        }
                })
                
            }
            </script>
        </tbody>
    </table>
    <script>
        function closeOrder(uid,t){

            var c = uid;
            debugger;
            if(confirm("Are you sure you want to close this order?")){
                $.post('?cmd=closeOrder','&order_uid='+uid,function(){
                    $(t).parent().parent().fadeOut();
                })

            };

        }
    </script>
<?
}

function updateStatus(){
    global $db;
    $stat = Post('stat');
    $uid = Post('uid');
    $sql = "UPDATE `orders` SET `status`= $stat WHERE `uid` = $uid";
    $res = $db->runSQL($sql);
    if($res) echo true;
    else echo false;
    
}

function closeOrder(){
    global $db, $comp_id;
    $uid = Post('order_uid');
    $sql = "UPDATE `orders` SET `status`=99 WHERE `uid` = $uid";
    $res = $db->runSQL($sql);
}



function numberStatus(&$status){
    if($status == 1){
        return 'Open';
    }
    if($status == 2){
        return 'In Progress';
    }
    if($status == 3){
        return 'Closed';
    }
    if($status == 4){
        return 'Late';
    }
    return 'Other';
}
?>