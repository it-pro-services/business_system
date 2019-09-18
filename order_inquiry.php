<?
require 'libs/page.php';
$page = new Page();
$page->Authenticate();
$comp_id = $_SESSION['comp_id'];
$db = new DBC();
$order = Get('order');
$page::cmd();
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <?=$page->head();?>     
        <style>
            .cust_order.table td, .cust_order.table th {
                padding: .2rem!important;

            }   

            .order_table.table td, .order_table.table th {
                padding: .1rem!important;

            }   
            .next_op.table td, .next_op.table th {
                padding: .4rem!important;

            }   

            .table td, .table th {
                padding: .75rem;
                vertical-align: top;
                border: 1px solid #dee2e6;
            }
            .text-money{
                text-align: right;
            }
        </style>
    </head>

    <body>

        <div class="d-flex" id="wrapper">        
            <!-- Sidebar -->
            <?=$page->sideBar();?>

            <!-- Page Content -->
            <div id="page-content-wrapper">

                <?=$page->navBar()?>


                <div class="container-fluid">

                    <h3 class="">Order Inquiry</h3>
                    <div class="input-group mb-3" style="max-width: 400px;">                        
                        <input type="text" class="form-control order_inquiry" placeholder="Order Number" value="<?=$order?>">
                        <div class="input-group-append" onclick="loadOrderScreen($('.order_inquiry').val())" id="order_search">
                            <span class="input-group-text" id="basic-addon2"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                    <span class="order_entry"></span>

                </div>
            </div>            

        </div>

    </body>
    <script>


        $('document').ready(function(){

            $('.order_inquiry').on('focus',function(){
                $(this).select()
            }).typeahead({
                name: 'value<?=rand()?>',
                limit: 10,
                header: '',
                remote: '?cmd=autoc&order=%QUERY&type=order',
                template: '<p><span  style="white-space: nowrap;"><strong>{{value}}</strong> - {{name}} - {{date}}</span></p>',
                engine: Hogan
            }).parent().css("width","80%").on('typeahead:selected', function (e, datum) {
                //$value = datum.value;  
                loadOrderScreen(datum.value);              
            });

            $('.order_inquiry').keypress(function(event){
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if(keycode == '13'){
                    loadOrderScreen($('.order_inquiry').val())
                }
            });

            $(':input').attr('autocomplete', '0');

        })

        function loadOrderScreen(order){  

            $(".order_entry").html('');
            $(".order_entry").Loading();
            $.post('?cmd=loadOrderScreen','&order='+order,function(html){
                $(".order_entry").html(html);
            })

        }
        <?
        if($order!=''){
        ?>
            loadOrderScreen('<?=$order?>');
        <? 
        }
        ?>

    </script>


</html>
<?


function loadOrderScreen(){
    global $db, $comp_id;
    $order = Post('order');    
    $sql = "SELECT ord.*, c.*, rm.dept_id,depts.dept, depts.next_dept, depts.end, depts.dept_id, next_dept.dept_id as 'NEXT_DEPT_ID' FROM `orders` ord 
    left join `customers` c on ord.cust_id = c.cust_id 
    left join route_manager rm on ord.uid = rm.uid
    left join departments depts on rm.dept_id = depts.dept_id and rm.comp_id = depts.comp_id
    left join departments next_dept on depts.next_dept = next_dept.dept_id and depts.comp_id = next_dept.comp_id
    where c.comp_id = $comp_id 
    and ord.comp_id = $comp_id 
    and ord.order_id = $order";
    $orders = $db->RetrieveRow($sql);

    if(!is_array($orders)){
?>
        <div class="alert alert-warning" role="alert" style="max-width: 400px;">
            Order Not Found
        </div>
    <?
        return;
    }

    ?>

    <div class="col-12-sm" style="height:40px; display:none;">
        <div class="progress">
            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100" style="width: 15%"></div>
            <div class="progress-bar" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100" style="width: 15%"></div>
            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100" style="width: 15%"></div>
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100" style="width: 15%"></div>
        </div>


    </div>

    <div class="col-12-sm" >
        <table class="table next_op"  style="max-width:600px;">        
            <tr>
                <td>Current Department</td>
                <td>Send To</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><?=$orders['dept']?></td>
                <td>
                    <select class="form-control" id="next_dept">
                        <?
                        $sql = "SELECT * FROM `departments` where comp_id = $comp_id order by dept_id asc";
                        $depts = $db->retrievedata($sql);
                        if(is_array($depts)){
                            foreach($depts as $d) {
                        ?><option value="<?=$d['dept_id']?>" <?=($d['dept_id']==$orders['NEXT_DEPT_ID'])?'selected':'';?>><?=$d['dept']?></option><?
                            }
                        }
                        ?>                
                    </select> 
                </td>

                <td><button class="btn btn-success" onclick="nextDept()">Send <i class="fas fa-arrow-right"></i></button></td>
            </tr>
        </table>
        <script>
            function nextDept(){
                $.post('?cmd=nextDept','&ord_uid=<?=$orders['uid']?>&next_dept='+$("#next_dept").val(),function(res){
                    if(res == true){
                        toast('green','Success!','Moved to next department!');
                        document.location.href = 'index.php';
                    }
                })
            }
        </script>



    </div>

    <div class="row order_screen">

        <div class="col-md-8">

            <table class="table cust_order">
                <tr>
                    <th style="width:1px; white-space:nowrap;">Customer Name</th>
                    <td>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Full Name" value="<?=$orders['name']?>">
                        <input type="hidden" id="order" name="order" value="<?=$order?>">                                        
                        <input type="hidden" id="order_uid" name="order_uid" value="<?=$orders['uid']?>">
                    </td>
                </tr>
                <tr>

                    <th style="width:1px; white-space:nowrap;">Phone</th>
                    <td>
                        <input type="text" class="form-control" id="customer_phone"  name="customer_phone" placeholder="801-888-9999" value="<?=formatPhone($orders['phone'])?>">
                    </td>

                </tr>
                <tr>

                    <th style="width:1px; white-space:nowrap;">Cell Phone</th>
                    <td>
                        <input type="text" class="form-control" id="customer_cell_phone"  name="customer_cell_phone" placeholder="801-999-8888" value="<?=formatPhone($orders['cell'])?>">
                    </td>

                </tr>
                <tr>
                    <th style="width:1px; white-space:nowrap;">Email</th>
                    <td>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="customer@email.com" value="<?=$orders['email']?>">
                    </td>
                </tr>
                <tr>
                    <th style="width:1px; white-space:nowrap;">Order Date</th>
                    <td>
                        <input type="text" class="form-control datepicker" id="order_date" name="order_date" placeholder="MM/DD/YYYY" value="<?=Date('m/d/Y',strtotime($orders['order_date']))?>">
                    </td>
                </tr>
                <tr>
                    <th style="width:1px; white-space:nowrap;">Due Date</th>
                    <td>                                        
                        <input type="text" class="form-control datepicker" id="order_due_date" name="order_due_date" placeholder="MM/DD/YYYY" value="<?=Date('m/d/Y',strtotime($orders['order_due']))?>">
                    </td>
                </tr>
            </table>
            <script>

            </script>
        </div>
        <?if($orders['order_type']=='shirt'){
        ?>
            <div class="col-md-4">
                <?
                $sql = "select * from order_options where uid = ".$orders['uid'];
                $order_options = $db->retrieveData($sql);

                ?>
                <div class="card">
                    <div class="card-header">
                        Send Approval To
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input type="checkbox" class="icheck form-check-input" id="sendApprovalEmail" name="sendApprovalTo[]" value="email" <?=(in_2d_array('email', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="sendApprovalEmail">Email</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="icheck form-check-input" id="sendApprovalText" name="sendApprovalTo[]" value="text" <?=(in_2d_array('text', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="sendApprovalText">Text</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="icheck form-check-input" id="sendApprovalPerson" name="sendApprovalTo[]" value="personal" <?=(in_2d_array('personal', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="sendApprovalPerson">Personal</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 spad-container" style="overflow: auto;">
                <canvas id="signature-pad" class="signature-pad" style="border:1px solid #000000;"></canvas>
                <br><button onclick="clearCanvas()" class="btn btn-secondary">Clear</button> <button onclick="defaultIMG()" class="btn btn-outline-danger">Full Reset</button><br><br>

            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        Items:
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input type="checkbox" class="icheck form-check-input" id="items_hoodie" value="hoodie" name="items_cat[]" <?=(in_2d_array('hoodie', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="items_hoodie">Hoodie</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="icheck form-check-input" id="items_jacket" value="jacket" name="items_cat[]" <?=(in_2d_array('jacket', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="items_jacket">Jacket</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="icheck form-check-input" id="items_crewneck" value="crewneck" name="items_cat[]" <?=(in_2d_array('crewneck', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="items_crewneck">Crew-neck</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="icheck form-check-input" id="items_zipup" value="zipup" name="items_cat[]" <?=(in_2d_array('zipup', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="items_zipup">Zip-up</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="icheck form-check-input" id="items_other" value="other" name="items_cat[]" <?=(in_2d_array('other', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="items_other">Other</label>
                        </div>


                    </div>    
                </div>
            </div>
            <!--//checkbox row-->
            <div class="card col-sm-10" style="padding:10px;margin:10px;">  
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input type="checkbox" class="icheck form-check-input" id="type_digital_print" value="digitalprint" name="print_type[]" <?=(in_2d_array('digitalprint', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="type_digital_print">Digital Printing</label>
                        </div>                                    
                    </div>

                    <div class="col-sm-3">
                        <div class="form-check" >
                            <input type="checkbox" class="icheck form-check-input" id="type_screen_print" value="screenprint" name="print_type[]" <?=(in_2d_array('screenprint', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="type_screen_print">Screen Printing</label>
                        </div>                                    
                    </div>

                    <div class="col-sm-3">

                        <div class="form-check" style="display:inline;">
                            <input type="checkbox" class="icheck form-check-input" id="type_embroidery" value="embroidery" name="print_type[]" <?=(in_2d_array('embroidery', $order_options))?'checked':'';?>>
                            <label class="form-check-label" for="type_embroidery">Embroidery</label>
                        </div>                                    

                    </div>

                    <div class="col-sm-3">

                        <div class="form-check" style="display:inline;">
                            <input type="checkbox" class="icheck form-check-input" id="type_ironon" value="ironon" name="print_type[]">
                            <label class="form-check-label" for="type_ironon">Iron On</label>
                        </div>                                    

                    </div>                                                                                                 
                </div>
            </div>

            <div class="col-sm-12" style="overflow: auto;">  

                <table class="table table-striped order_table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align: middle;">Model</th>
                            <th rowspan="2" style="vertical-align: middle;">Color</th>
                            <th rowspan="2" style="vertical-align: middle;">Description</th>

                            <th colspan="4">Youth</th>

                            <th colspan="9">Adult</th>

                            <th rowspan="2" style="vertical-align: middle;">Qty</th>
                            <th rowspan="2" style="vertical-align: middle;">Price</th>
                            <th rowspan="2" style="vertical-align: middle;">Total</th>
                            <th rowspan="2">&nbsp;</th>

                        </tr>
                        <tr>


                            <th>Y/XS</th>
                            <th>Y/S</th>
                            <th>Y/M</th>
                            <th>Y/L</th>


                            <th>XS</th>
                            <th>S</th>
                            <th>M</th>
                            <th>L</th>
                            <th>XL</th>
                            <th>2XL</th>
                            <th>3XL</th>
                            <th>4XL</th>
                            <th>OTHER</th>
                        </tr>
                    </thead>
                    <tbody id="order_table_body" ></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="16" rowspan="7" ><textarea name="instructions" id="instructions" class="form-control" style="min-height:200px;" placeholder="Instructions:"><?=$orders['instructions']?></textarea></td>
                            <td>Setup</td>
                            <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" type="number" step="0.01" class="form-control text-money" name="setup" id="setup" value="<?=$orders['setup']?>"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Subtotal</td>
                            <td colspan="2"><input type="number" step="0.01" id="subtotal" name="subtotal" class="form-control text-money" readonly></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Tax(6.85%)</td>
                            <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" type="number" step="0.01" id="tax" name="tax" class="form-control text-money" value="<?=$orders['tax']?>"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Shipping</td>
                            <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" type="number" step="0.01" id="shipping" name="shipping" class="form-control text-money" value="<?=$orders['shipping']?>"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td colspan="2"><input type="number" step="0.01" id="total" name="total" class="form-control text-money" readonly></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Deposit</td>
                            <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" type="number" step="0.01" id="deposit" name="deposit" class="form-control text-money" value="<?=$orders['deposit']?>"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Balance Due</td>
                            <td colspan="2"><input type="number" step="0.01" id="balancedue" name="balancedue" class="form-control text-money" readonly></td>
                            <td>&nbsp;</td>
                        </tr>

                    </tfoot>
                </table>
                <button type="button" class="btn btn-info"  style="float: left;"  onclick="addItem()"><i class="fas fa-plus-square"></i> Add Item</button>        
                <br>
                <br>
                <button type="button" class="btn btn-primary btn-lg" style="float: left;" onclick="submitOrder(this)"><i class="far fa-save"></i> Save Changes</button>
            </div>
            <?
        }
        if($orders['order_type']=='General'){
            ?>
            <div class="col-lg-12" style="overflow: auto;">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" onclick="saveType('general');">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#general" role="tab" aria-controls="home" aria-selected="true">General</a>
                    </li>
                    <li class="nav-item" onclick="saveType('shirt');" style="display:none;">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#shirt" role="tab" aria-controls="profile" aria-selected="false">Shirt</a>
                    </li>
                    <script>function saveType(type){$("#save_type").val(type);}</script>
                </ul>
                <div class="tab-content " id="myTabContent">

                    <input type="hidden" id="save_type" name="save_type" value="general">
                    <!--//GENERAL-->
                    <div class="container-fluid tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row">

                            <div class="col-lg-12 spad-container" style="overflow: auto;">

                                <?if(file_exists('draw/'.$orders['uid'].'_general.png')){
                                ?><img src="draw/<?=$orders['uid']?>_general.png" style="margin:10px;"><?
                                }?>

                            </div>
                            <div class="col-12" style="overflow: auto; margin-top:15px;">  

                                <table class="table table-striped general_order_table">
                                    <thead>
                                        <tr>
                                            <th style="vertical-align: middle;">Item</th>                                        
                                            <th style="vertical-align: middle;">Description</th>


                                            <th style="vertical-align: middle;">Qty</th>
                                            <th style="vertical-align: middle;">Price</th>
                                            <th colspan ="2" style="vertical-align: middle;">Total</th>
                                            <th>&nbsp;</th>

                                        </tr>

                                    </thead>
                                    <tbody id="general_order_table_body" ></tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"  >
                                                <button type="button" class="btn btn-info"  style="float: left;"  onclick="addGeneralItem()"><i class="fas fa-plus-square"></i> Add Item</button>        
                                            </td>
                                            <td>Discount</td>
                                            <td colspan="2">

                                                <div class="input-group">
                                                    <input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" type="number" step="1" class="form-control text-money" name="general_discount" id="general_discount" style="max-width:95%;display:inline;"value="<?=$orders['discount']?>">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>



                                            </td>

                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>                                        
                                            <td colspan="3" rowspan="7">
                                                <textarea name="general_notes" id="general_notes" class="form-control" style="min-height:300px;" placeholder="Notes:"><?=$orders['instructions']?></textarea>
                                            </td>
                                            <td>Design</td>
                                            <td colspan="2"><input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" type="number" step="0.01" class="form-control text-money" name="general_design" id="general_design" value="<?=$orders['setup']?>"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>Subtotal</td>
                                            <td colspan="2"><input type="number" step="0.01" id="general_subtotal" name="general_subtotal" class="form-control text-money" readonly></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>Tax(7.1%)</td>
                                            <td colspan="2"><input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" type="number" step="0.01" id="general_tax" name="general_tax" class="form-control text-money" value="<?=$orders['tax']?>"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>Shipping</td>
                                            <td colspan="2"><input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" type="number" step="0.01" id="general_shipping" name="general_shipping" class="form-control text-money" value="<?=$orders['shipping']?>"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>Total</td>
                                            <td colspan="2"><input type="number" step="0.01" id="general_total" name="general_total" class="form-control text-money" readonly></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>Deposit</td>
                                            <td colspan="2"><input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" type="number" step="0.01" id="general_deposit" name="general_deposit" class="form-control text-money" value="<?=$orders['deposit']?>"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>Balance Due</td>
                                            <td colspan="2"><input type="number" step="0.01" id="general_balancedue" name="general_balancedue" class="form-control text-money" readonly></td>
                                            <td>&nbsp;</td>
                                        </tr>

                                    </tfoot>
                                </table>

                                <br>
                                <br>

                                <?if(file_exists('draw/'.$orders['uid'].'_cust_signature.png')){
                                ?>
                                    Customer Signature:
                                    <img src="draw/<?=$orders['uid']?>_cust_signature.png">    
                                <?
                                }?>





                            </div>

                            <div class="col-12" style="overflow: auto; margin-top:15px; display:none;">  
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <input type="checkbox" class="icheck" name="email_customer" value="1"> Email Customer
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="checkbox" class="icheck" name="text_customer" value="1"> Text Customer
                                        </div>
                                        <div class="col-sm-3">
                                            <button type="button" class="btn btn-primary btn-lg" onclick="submitGeneralOrder(this)"><i class="fas fa-check"></i> Submit Order</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!--//GENERAL-->
                </div>
            </div>
        <?
        }?>

    </div>
    <br><br><br><br><br><br><br><br><br>
    <script>
        $('document').ready(function(){                 
            var currentTime = new Date();
            var m = currentTime.getMonth()+1;
            var d = currentTime.getDate();
            var Y = currentTime.getFullYear();
            $("#order_date").val(m+'/'+d+'/'+Y);
            //responsive canvas
            <?if($orders['order_type']=='Shirt'){
            ?>
                resizeCanvas();   
                addItem();
            <? 
            }?>

            //keep all input fields from browser auto-fill
            $(':input').attr('autocomplete', '0');
            //add the first item:

            //addGeneralItem();
            icheckUpdate();
            makeDate();
            loadDrawPad();



            $done = 0;
            <?
            $sql = "select * from `order_lines` where uid = ".$orders['uid']." order by line_number asc";
            $lines = $db->retrieveData($sql);
            if(is_array($lines)){
                $call = sizeof($lines);
                for($i = 0; $i < $call; $i++){
            ?>addGeneralItem();<?
                }
            ?>
                checkCallAddRowsData()
            <?}else{$lines = array();}?>




        })

        function checkCallAddRowsData(){  
            $size = '<?=$call?>';

            if($done == $size){

                addRowsData();


            }else{
                setTimeout(function(){ 
                    checkCallAddRowsData() 
                    }, 300);    
            }
        }
        function addRowsData(){
            <?
            foreach($lines as $key=>$l){
            ?>
                $("#general_model_number<?=$key?>").val('<?=$l['model']?>');

                $("#general_description<?=$key?>").val('<?=$l['description']?>');                

                $("#general_qty<?=$key?>").val('<?=$l['qty']?>');
                $("#general_price<?=$key?>").val('<?=$l['price']?>');
                $("#general_total<?=$key?>").val('<?=$l['total']?>');

            <?
            }
            ?>

            $("#general_design").trigger('change')

        }
        var lcanvas = '';
        function loadDrawPad(){
            /*
            lcanvas = LC.init(
            $('.my-drawing')[0] ,
            {imageURLPrefix: 'vendor/literalcanvas/img/'}
            );
            */
        }


        function loadPrevOrders(){
            $('.prev_orders').html('Previous orders will go here');
        }

        function submitShirtOrder(t){
            $(t).Loading('Submitting Order');
            if(confirm("Ready to submit order?")){

                $.post('?cmd=submitShirtOrder',$(".order_entry").Values()+'&draw='+shirtPad.toDataURL(),function(res){

                    if(res == true){
                        toast('green','Success!','Order Saved.');
                        loadOrderScreen();
                        //Scroll to top
                        window.scrollTo(0,0);                        
                    }else{
                        toast('red','Error!','Could not save order. '+res);
                    }

                    $(t).UnLoad();

                })

            }
        }
        function submitGeneralOrder(t){


            $(t).Loading('Submitting Order');
            if(confirm("Ready to submit order?")){

                $lc_saveImg = lcanvas.getImage().toDataURL()
                $.post('?cmd=submitGeneralOrder',$(".order_entry").Values()+'&lc='+$lc_saveImg,function(res){

                    if(res == true){
                        toast('green','Success!','Order Saved.');
                        loadOrderScreen();
                        //Scroll to top
                        window.scrollTo(0,0);                        
                    }else{
                        toast('red','Error!','Could not save order. '+res);
                    }

                    $(t).UnLoad();

                })

            }
        }

        function removeRow(t){            
            if(confirm("Are you sure you want to remove this row?")){ 
                $(t).parent().parent().fadeOut(function(){
                    $(t).parent().parent().remove();                    
                    updateTotals();                    
                });

            }
        }

        function removeGeneralRow(t){            
            if(confirm("Are you sure you want to remove this row?")){ 
                $(t).parent().parent().fadeOut(function(){
                    $(t).parent().parent().remove();                    
                    updateTotals();                    
                });

            }
        }

        var row = 0;
        function addItem(){
            $.post('?cmd=addLine','&row='+row,function(html){
                row++;
                $("#order_table_body").append(html);
            })
        }

        var general_row = -1;
        function addGeneralItem(){
            general_row++;
            $.post('?cmd=addGeneralLine','&row='+general_row,function(html){
                $("#general_order_table_body").append(html);
                $done++;
            })

        }

        //Canvas
        <?if($orders['order_type']=='Shirt'){?>
            {
                function clearCanvas(){
                    canvasReload();
                }

                var canvas = document.querySelector("#signature-pad");
                ctx = canvas.getContext("2d");

                var background = new Image();
                background.src = "img/blank_shirt_hats.png";

                // Make sure the image is loaded first otherwise nothing will draw.
                background.onload = function(){
                    canvasReload()
                }

                function canvasReload(){
                    shirtPad.clear()
                    ctx.drawImage(background, 0, 0, background.width, background.height,     // source rectangle
                        0, 0, canvas.width, canvas.height); // destination rectangle
                }

                var shirtPad = new SignaturePad(canvas);


                $( window ).resize(function() {
                    resizeCanvas()
                });

                function resizeCanvas(){


                    var width = $( window ).width();                         
                    if(width < 1420){
                        canvas.width = $('.spad-container').width()-30;
                    }else{
                        canvas.width = 900;
                    }
                    var height = $(window).height() - 150;
                    if (height > 300){height = 300;}
                    canvas.height = height;

                    //fit the image to the canvas:
                    ctx.drawImage(background, 0, 0, background.width, background.height,     // source rectangle
                        0, 0, canvas.width, canvas.height); // destination rectangle


                }

            }

        <?}?>

        function updateTotals(t){
            if(t != undefined){

                $qty = $(t).parent().parent().find('.qty').val();
                if($qty == '')$qty = 0;
                $price = $(t).parent().parent().find('.price').val();
                if($price == '')$price = 0;
                $total = $(t).parent().parent().find('.total').val();    
                if($total == '')$total = 0;

                $(t).parent().parent().find('.total').val($qty*$price);

                //goes through and formats everything on the line items
                $( ".text-money" ).each(function( index ) {                
                    if($(t).attr('name') != $(this).attr('name') && t != undefined){
                        $(this).val(parseFloat($(this).val()).toFixed(2));    
                    }
                })
            }

            //setup
            $setup = $("#setup").val(); if($setup == ''){$setup = 0;}            


            //subtotal
            $subtotal = 0;
            $( ".total" ).each(function( index ) {

                $total_current = $( this ).val();                                
                if(isNaN($total_current) || $total_current == ''){$total_current = 0;}
                $subtotal += parseFloat($total_current);

            });

            $subtotal += parseFloat($setup);

            $("#subtotal").val(parseFloat($subtotal).toFixed(2));

            //tax
            $tax = ((parseFloat($subtotal)) * 0.071)            
            $("#tax").val(parseFloat($tax).toFixed(2));

            //shipping 
            $shipping = $("#shipping").val(); if($shipping == ''){$shipping = 0;}

            //TOTAL
            $total = parseFloat($shipping) + parseFloat($tax) + parseFloat($subtotal);

            $("#total").val(parseFloat($total).toFixed(2));

            //DEPOSIT:
            $deposit = $("#deposit").val(); if($deposit == ''){$deposit = 0;}

            //Balance Due:
            $balance_due = parseFloat($total) - parseFloat($deposit);
            $("#balancedue").val(parseFloat($balance_due).toFixed(2));

        }

        function updateGeneralTotals(t){
            if(t != undefined){

                $qty = $(t).parent().parent().find('.general_qty').val();
                if($qty == '')$qty = 0;
                $price = $(t).parent().parent().find('.general_price').val();
                if($price == '')$price = 0;
                $total = $(t).parent().parent().find('.general_total').val();    
                if($total == '')$total = 0;

                $(t).parent().parent().find('.general_total').val($qty*$price);

                //goes through and formats everything on the line items
                $( ".text-money" ).each(function( index ) {                
                    if($(t).attr('name') != $(this).attr('name') && t != undefined){
                        $(this).val(parseFloat($(this).val()).toFixed(2));    
                    }
                })
            }


            //subtotal
            $subtotal = 0;
            $( ".general_total" ).each(function( index ) {

                $total_current = $( this ).val();                                
                if(isNaN($total_current) || $total_current == ''){$total_current = 0;}
                $subtotal += parseFloat($total_current);

            });

            //discount:
            $discount = $("#general_discount").val(); if($discount == ''){$discount = 0;} 
            $discount = $discount / 100;
            $discount = $discount * $subtotal;

            //subtotal:
            $subtotal -= $discount;


            //setup
            $setup = $("#general_design").val(); if($setup == ''){$setup = 0;}            

            //subtotal
            $subtotal += parseFloat($setup);





            $("#general_subtotal").val(parseFloat($subtotal).toFixed(2));

            //tax
            $tax = ((parseFloat($subtotal)) * 0.071)            
            $("#general_tax").val(parseFloat($tax).toFixed(2));

            //shipping 
            $shipping = $("#general_shipping").val(); if($shipping == ''){$shipping = 0;}

            //TOTAL
            $total = parseFloat($shipping) + parseFloat($tax) + parseFloat($subtotal);

            $("#general_total").val(parseFloat($total).toFixed(2));

            //DEPOSIT:
            $deposit = $("#general_deposit").val(); if($deposit == ''){$deposit = 0;}

            //Balance Due:
            $balance_due = parseFloat($total) - parseFloat($deposit);
            $("#general_balancedue").val(parseFloat($balance_due).toFixed(2));

        }
    </script>
    <?
}



function submitOrder(){

    //handle the customer information:
    $customer = new Customer();
    $customer->name = Post('customer_name');
    $customer->phone = Post('customer_phone');
    $customer->cell = Post('customer_cell_phone');
    $customer->email = Post('customer_email');

    $cust_id = $customer->exists();
    if($cust_id === false){
        $res = $customer->insert_db();        
        if($res == false) {
            echo 'Error saving customer information'; 
            return;
        }        
    }
    $cust_id = $customer->cust_id;

    //handle the order id and accessories:
    $order = new Order($cust_id);

    $order->ord_date = Post('order_date');
    $order->ord_due = Post('order_due_date');
    $order->instructions = Post('instructions');

    $res = $order->insert_db();
    if($res){
        //get the order addon values
        $sendApprove = Post('sendApprovalTo');
        $items_cat = Post('items_cat');
        $print_type = Post('print_type');        
        if(is_array($sendApprove)){
            $order->insertAddOns($sendApprove, 'send_approve');
        }
        if(is_array($items_cat)){
            $order->insertAddOns($items_cat, 'item_category');
        }
        if(is_array($print_type)){
            $order->insertAddOns($print_type, 'print_type');
        }

        //insert the line items:
        $line = new LineItem($order->getUID());

        $loopPostLineItems = true;
        $i = 0;
        while($loopPostLineItems){            
            if(Post('model_number'.$i)==''){
                $loopPostLineItems = false;
                continue;
            }

            $line->model = Post('model_number'.$i);
            $line->color = Post('color'.$i);
            $line->description = Post('description'.$i);
            $line->size = Post('shirt_sizes'.$i);
            $line->size_other = Post('size_other'.$i);
            $line->qty = Post('qty'.$i);
            $line->price = Post('price'.$i);
            $line->total = Post('total'.$i);

            $line->insert_db();
            $i++;
        }

        echo true;

        //Save drawing:
        $data_uri = Post('draw');
        $encoded_image = str_replace(' ','+',explode(",", $data_uri)[1]);    
        $decoded_image = base64_decode($encoded_image);

        $url='draw/'.$order->getUID().'_drawing.png';

        file_put_contents($url, $decoded_image);

    }else{
        echo 'Could not save order items'; 
        return;
    }


}



function addLine(){
    $row = Post('row');

    ?>
    <tr>
        <td><input name="model_number<?=$row?>"  id="model_number<?=$row?>" class="form-control"type="text"></td>
        <td><input name="color<?=$row?>" id="color<?=$row?>" class="form-control"type="text"></td>
        <td><input name="description<?=$row?>" id="description<?=$row?>" class="form-control"type="text"></td>

        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>1" type="radio" name="shirt_sizes<?=$row?>" value="y_xs"><label for="shirt_sizes<?=$row?>1"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>2" type="radio" name="shirt_sizes<?=$row?>" value="y_s"><label for="shirt_sizes<?=$row?>2"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>3" type="radio" name="shirt_sizes<?=$row?>" value="y_m"><label for="shirt_sizes<?=$row?>3"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>4" type="radio" name="shirt_sizes<?=$row?>" value="y_l"><label for="shirt_sizes<?=$row?>4"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>5" type="radio" name="shirt_sizes<?=$row?>" value="a_xs"><label for="shirt_sizes<?=$row?>5"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>6" type="radio" name="shirt_sizes<?=$row?>" value="a_s"><label for="shirt_sizes<?=$row?>6"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>7" type="radio" name="shirt_sizes<?=$row?>" value="a_m"><label for="shirt_sizes<?=$row?>7"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>8" type="radio" name="shirt_sizes<?=$row?>" value="a_l"><label for="shirt_sizes<?=$row?>8"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>9" type="radio" name="shirt_sizes<?=$row?>" value="a_xl"><label for="shirt_sizes<?=$row?>9"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>10" type="radio" name="shirt_sizes<?=$row?>" value="a_2xl"><label for="shirt_sizes<?=$row?>10"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>11" type="radio" name="shirt_sizes<?=$row?>" value="a_3xl"><label for="shirt_sizes<?=$row?>11"></label></span></td>
        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>12" type="radio" name="shirt_sizes<?=$row?>" value="a_4xl"><label for="shirt_sizes<?=$row?>12"></label></span></td>


        <td>
            <input name="size_other<?=$row?>" id="size_other<?=$row?>" class="form-control" type="text" style="max-width:50px;padding:1px">
        </td>
        <td><input onchange='updateTotals(this);'onkeyup='updateTotals(this)' name="qty<?=$row?>" id="qty<?=$row?>" class="form-control qty" type="number" style="max-width:100px;padding:1px;text-align:right;"></td>
        <td><input onchange='updateTotals(this);'onkeyup='updateTotals(this)' name="price<?=$row?>" id="price<?=$row?>" class="form-control price text-money" type="number" style="max-width:200px;padding:1px"></td>
        <td><input onchange='updateTotals();' onkeyup='updateTotals();'name="total<?=$row?>" id="total<?=$row?>" class="form-control total text-money" type="number" style="max-width:200px;padding:1px"></td>
        <td><button class="btn btn-danger" onclick="removeRow(this)">X</button></td>
    </tr>
    <?
}

function addGeneralLine(){
    $row = Post('row');

    ?>
    <tr>
        <td><input name="general_model_number<?=$row?>" id="general_model_number<?=$row?>" class="form-control"type="text"></td>

        <td><input name="general_description<?=$row?>" id="general_description<?=$row?>" class="form-control"type="text"></td>


        <td><input onchange='updateGeneralTotals(this);'onkeyup='updateGeneralTotals(this)' id="general_qty<?=$row?>" name="general_qty<?=$row?>" class="form-control general_qty" type="number" style="max-width:100px;padding:1px;text-align:right;"></td>
        <td><input onchange='updateGeneralTotals(this);'onkeyup='updateGeneralTotals(this)' id="general_price<?=$row?>" name="general_price<?=$row?>" class="form-control general_price text-money" type="number" style="max-width:200px;padding:1px"></td>
        <td colspan="2"><input onchange='updateGeneralTotals();' onkeyup='updateGeneralTotals();' id="general_total<?=$row?>" name="general_total<?=$row?>" class="form-control general_total text-money" type="number" style="padding:1px"></td>
        <td><button class="btn btn-danger" onclick="removeGeneralRow(this)">X</button></td>
    </tr>
    <?
}



/**
* Helper function to database escape any variable passed by reference.
* 
* @param mixed $item variable to be escaped
*/



/**
* returns true or false depending on if needle is in 2d haystack
* 
* @param mixed $haystack
* @param mixed $needle
*/
function in_2d_array($needle,$haystack){

    if(!is_array($haystack)) return false;

    foreach($haystack as $h){
        if(in_array($needle, $h)){
            return true;
        }
    }
    return false;

}