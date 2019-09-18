<?
require 'libs/page.php';
require 'libs/business_classes.php';
$page = new Page();
//$page->Authenticate();
$db = new DBC();
$page::cmd();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
} 
$ord = 0;
//ord=".$page->encryption($order->getUID());
if(isset($_GET['ord'])){
    $ord = $page->encryption($_GET['ord'],'d');
}
if($ord == 0){
?><div class="alert alert-warning" role="alert">
        Error Loading Order
</div><?return;
}


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
        <?

        if($_SESSION['loggedIn']){
            $page->sideBar();
        }?>
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <?

            if($_SESSION['loggedIn']){
                $page->navBar();
            }?>


            <div class="container-fluid">        
                <div class="row justify-content-md-center">
                    <h2>Order #<?=$ord?></h2>
                </div>
            </div>
            <div class="container-fluid view_order"></div>
        </div>            

    </div>

    <script>
        function loadOrder(){
            $('.view_order').Loading();
            $.post('?cmd=loadOrder','&ord=<?=$ord?>',function(html){
                $('.view_order').html(html);
            })
        }
        loadOrder();
        function showMenu(){return;}//this is called from page->head


    </script>
</body>


<?
function loadOrder(){
    global $db;
    $ord = Post('ord');
    $customer = new Customer();
    $customer->load_customer($ord,'order');

    $order = new Order($customer->cust_id);
    $order->load_order($ord);

    $lines = new LineItem($order->getUID());

    //now an array of line items
    $lines = $lines->load_items();

?>
    <h3>Customer Info</h3>
    <table class="table cust_order"style="max-width:700px;">
    <tr>
        <th style="width:1px; white-space:nowrap;">Customer Name</th>
        <td>
            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Full Name" value="<?=$customer->name;?>" >                                        
        </td>
    </tr>
    <tr>

        <th style="width:1px; white-space:nowrap;">Phone</th>
        <td>
            <input type="text" class="form-control" id="customer_phone"  name="customer_phone" placeholder="801-888-9999" value="<?=$customer->phone;?>">
        </td>

    </tr>
    <tr>

        <th style="width:1px; white-space:nowrap;">Cell Phone</th>
        <td>
            <input type="text" class="form-control" id="customer_cell_phone"  name="customer_cell_phone" placeholder="801-999-8888" value="<?=$customer->cell;?>">
        </td>

    </tr>

    <tr>
        <th style="width:1px; white-space:nowrap;">Email</th>
        <td>
            <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="customer@email.com" value="<?=$customer->email;?>">
        </td>
    </tr>
    <tr>
        <td colspan="2"><button class="btn btn-secondary" onclick="toggleTable(this)"><i class="fas fa-chevron-right cust_chev"></i><i class="fas fa-chevron-down cust_chev" style="display:none;"></i></button></td>
        <script>
            function toggleTable(t){
                $(".cust_extra_info").toggle();
                $(".cust_chev").toggle();
            }
        </script>
    </tr>
    <tr class="cust_extra_info" style="display:none;">
        <th style="width:1px; white-space:nowrap;">Address</th>
        <td>
            <input type="text" class="form-control" id="customer_addr1" name="customer_addr1" placeholder="123 Street" value="<?=$customer->addr1;?>">
            <span class="btn-link show_addr2" onclick="$(this).hide(); $('#customer_addr2').fadeIn()">+ Line 2</span>                        
            <input type="text" class="form-control" id="customer_addr2" name="customer_addr2" placeholder="Line Two" style="display:none;" value="<?=$customer->addr2;?>">
        </td>
    </tr>
    <tr class="cust_extra_info" style="display:none;">
        <th style="width:1px; white-space:nowrap;">City</th>
        <td>
            <input type="text" class="form-control" id="customer_city" name="customer_city" placeholder="City" value="<?=$customer->city;?>">
        </td>
    </tr>
    <tr class="cust_extra_info" style="display:none;">
        <th style="width:1px; white-space:nowrap;">State</th>
        <td>
            <input type="text" class="form-control" id="customer_state" name="customer_state" placeholder="State" style="max-width:300px;" value="<?=$customer->state;?>">
        </td>
    </tr>
    <tr class="cust_extra_info" style="display:none;">
        <th style="width:1px; white-space:nowrap;">Zip</th>
        <td>
            <input type="text" class="form-control" id="customer_zip" name="customer_zip" placeholder="00000" style="max-width:300px;" value="<?=$customer->zip;?>">
        </td>
    </tr>
    <tr>
        <th style="width:1px; white-space:nowrap;">Order Date</th>
        <td>
            <input type="text" class="form-control datepicker" id="order_date" name="order_date" value="<?=Date('m/d/Y',strtotime($order->ord_date))?>" placeholder="MM/DD/YYYY" >
        </td>
    </tr>
    <tr>
        <th style="width:1px; white-space:nowrap;">Due Date</th>
        <td>                                        
            <input type="text" class="form-control datepicker" id="order_due_date" name="order_due_date" placeholder="MM/DD/YYYY" value="<?=Date('m/d/Y',strtotime($order->ord_due))?>">
        </td>
    </tr>
    </table>

    <h3><?=$order->ord_type?> Order Info</h3>

    <?
    if($order->ord_type == 'Shirt'){
        $sql = "select * from order_options where uid = ".$order->getUID();
        $order_options = $db->retrieveData($sql);
    ?>
        <div class="card col-sm-10" style="padding:10px;margin:10px;">  
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-check">                        
                        <label class="form-check-label" for="type_digital_print">Digital Printing <?=(in_2d_array('digitalprint', $order_options))?'<i class="fas fa-check"></i>':'';?></label>                        
                    </div>                                    
                </div>

                <div class="col-sm-3">
                    <div class="form-check" >                        
                        <label class="form-check-label" for="type_screen_print">Screen Printing <?=(in_2d_array('screenprint', $order_options))?'<i class="fas fa-check"></i>':'';?></label>
                    </div>                                    
                </div>

                <div class="col-sm-3">

                    <div class="form-check" style="display:inline;">                        
                        <label class="form-check-label" for="type_embroidery">Embroidery <?=(in_2d_array('embroidery', $order_options))?'<i class="fas fa-check"></i>':'';?></label>
                    </div>                                    

                </div>

                <div class="col-sm-3">

                    <div class="form-check" style="display:inline;">                        
                        <label class="form-check-label" for="type_ironon">Iron On <?=(in_2d_array('ironon', $order_options))?'<i class="fas fa-check"></i>':'';?></label>
                    </div>                                    

                </div>                                                                                                 
            </div>
        </div>
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
            <tbody id="order_table_body" >
                <?
                foreach($lines as $row=>$l){                
                ?>
                    <tr>
                        <td><input name="model_number<?=$row?>" class="form-control"type="text" value="<?=$l['model']?>"></td>
                        <td><input name="color<?=$row?>" class="form-control"type="text" value="<?=$l['color']?>"></td>
                        <td><input name="description<?=$row?>" class="form-control"type="text"  value="<?=$l['description']?>"></td>

                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>1" type="radio" name="shirt_sizes<?=$row?>" value="y_xs" <?=($l['size']=='y_xs')?'checked':'';?>><label for="shirt_sizes<?=$row?>1"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>2" type="radio" name="shirt_sizes<?=$row?>" value="y_s" <?=($l['size']=='y_s')?'checked':'';?>><label for="shirt_sizes<?=$row?>2"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>3" type="radio" name="shirt_sizes<?=$row?>" value="y_m" <?=($l['size']=='y_m')?'checked':'';?>><label for="shirt_sizes<?=$row?>3"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>4" type="radio" name="shirt_sizes<?=$row?>" value="y_l" <?=($l['size']=='y_l')?'checked':'';?>><label for="shirt_sizes<?=$row?>4"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>5" type="radio" name="shirt_sizes<?=$row?>" value="a_xs" <?=($l['size']=='a_xs')?'checked':'';?>><label for="shirt_sizes<?=$row?>5"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>6" type="radio" name="shirt_sizes<?=$row?>" value="a_s" <?=($l['size']=='a_s')?'checked':'';?>><label for="shirt_sizes<?=$row?>6"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>7" type="radio" name="shirt_sizes<?=$row?>" value="a_m" <?=($l['size']=='a_m')?'checked':'';?>><label for="shirt_sizes<?=$row?>7"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>8" type="radio" name="shirt_sizes<?=$row?>" value="a_l" <?=($l['size']=='a_l')?'checked':'';?>><label for="shirt_sizes<?=$row?>8"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>9" type="radio" name="shirt_sizes<?=$row?>" value="a_xl" <?=($l['size']=='a_xl')?'checked':'';?>><label for="shirt_sizes<?=$row?>9"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>10" type="radio" name="shirt_sizes<?=$row?>" value="a_2xl" <?=($l['size']=='a_2xl')?'checked':'';?>><label for="shirt_sizes<?=$row?>10"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>11" type="radio" name="shirt_sizes<?=$row?>" value="a_3xl" <?=($l['size']=='a_3xl')?'checked':'';?>><label for="shirt_sizes<?=$row?>11"></label></span></td>
                        <td><span class="md-radio"><input id="shirt_sizes<?=$row?>12" type="radio" name="shirt_sizes<?=$row?>" value="a_4xl" <?=($l['size']=='a_4xl')?'checked':'';?>><label for="shirt_sizes<?=$row?>12"></label></span></td>
                        <td>
                            <input name="size_other<?=$row?>" class="form-control"type="text" style="max-width:50px;padding:1px" value="<?=$l['size_other']?>">
                        </td>
                        <td><input onchange='updateTotals(this);'onkeyup='updateTotals(this)' value="<?=$l['qty']?>" name="qty<?=$row?>" class="form-control qty" type="number" style="max-width:100px;padding:1px;text-align:right;"></td>
                        <td><input onchange='updateTotals(this);'onkeyup='updateTotals(this)' value="<?=$l['price']?>" name="price<?=$row?>" class="form-control price text-money" type="number" style="max-width:200px;padding:1px"></td>
                        <td><input onchange='updateTotals();' onkeyup='updateTotals();' value="<?=$l['total']?>" name="total<?=$row?>" class="form-control total text-money" type="number" style="max-width:200px;padding:1px"></td>
                        <td>&nbsp;</td>
                    </tr>
                <?                
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="16" rowspan="4" ><textarea name="instructions" id="instructions" class="form-control" style="min-height:155px;" placeholder="Instructions:"><?=$order->instructions?></textarea></td>
                    <td>Setup</td>
                    <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" type="number" step="0.01" class="form-control text-money" name="setup" id="setup" value="<?=$order->setup?>" ></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Subtotal</td>
                    <td colspan="2"><input type="number" step="0.01" id="subtotal" name="subtotal" class="form-control text-money" readonly></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Tax(7.1%)</td>
                    <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" value="<?=$order->tax?>" type="number" step="0.01" id="tax" name="tax" class="form-control text-money"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Shipping</td>
                    <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" value="<?=$order->shipping?>" type="number" step="0.01" id="shipping" name="shipping" class="form-control text-money"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="16" rowspan="4" >

                        <?if(file_exists('draw/'.$ord.'_drawing.png')){
                        ?><img src="draw/<?=$ord?>_drawing.png"><?
                        }else{
                            echo '&nbsp;';
                        }?>

                    </td>
                    <td>Total</td>
                    <td colspan="2"><input type="number" step="0.01" id="total" name="total" class="form-control text-money" readonly></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Deposit</td>
                    <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" value="<?=$order->deposit?>" type="number" step="0.01" id="deposit" name="deposit" class="form-control text-money"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Balance Due</td>
                    <td colspan="2"><input type="number" step="0.01" id="balancedue" name="balancedue" class="form-control text-money" readonly></td>
                    <td>&nbsp;</td>
                </tr>

            </tfoot>
        </table>
        <?
        if(file_exists('draw/'.$ord.'_cust_signature.png')){
        ?>
            <h3>Customer Signature:</h3>
            <img src="draw/<?=$ord?>_cust_signature.png" style="max-width:100%">
        <?
        }
        ?>
        <br>
        <br>
        <br>
        <script>
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
            updateTotals();

        </script>


        <?
    }
    else{
        ?>
        <table class="table table-striped general_order_table">
            <thead>
                <tr>
                    <th style="vertical-align: middle;">Item</th>                                        
                    <th style="vertical-align: middle;">Description</th>
                    <th style="vertical-align: middle;">Qty</th>
                    <th style="vertical-align: middle;">Price</th>
                    <th colspan ="2" style="vertical-align: middle;min-width: 150px;">Total</th>
                    <th>&nbsp;</th>

                </tr>

            </thead>
            <tbody>
                <?   if(is_array($lines)){


                    foreach($lines as $row=>$l){
                ?>
                        <tr>
                            <td><input name="general_model_number<?=$row?>" class="form-control"type="text" value="<?=$l['model']?>"></td>

                            <td><input name="general_description<?=$row?>" class="form-control"type="text" value="<?=$l['description']?>"></td>


                            <td><input onchange='updateGeneralTotals(this);'onkeyup='updateGeneralTotals(this)' value="<?=$l['qty']?>" name="general_qty<?=$row?>" class="form-control general_qty" type="number" style="max-width:100px;padding:1px;text-align:right;"></td>
                            <td><input onchange='updateGeneralTotals(this);'onkeyup='updateGeneralTotals(this)' value="<?=$l['price']?>" name="general_price<?=$row?>" class="form-control general_price text-money" type="number" style="max-width:200px;padding:1px"></td>
                            <td colspan="2"><input onchange='updateGeneralTotals();' onkeyup='updateGeneralTotals();' value="<?=$l['total']?>" name="general_total<?=$row?>" class="form-control general_total text-money" type="number" style="padding:1px"></td>
                            <td>&nbsp;</td>
                        </tr>
                <?
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" rowspan="4">
                        <textarea name="general_notes" id="general_notes" class="form-control" style="" placeholder="Notes:"><?=$order->instructions?></textarea>
                    </td>

                    <td>Discount</td>
                    <td colspan="2">

                        <div class="input-group">
                            <input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" value="<?=$order->discount?>" type="number" step="1" class="form-control text-money" name="general_discount" id="general_discount" style="max-width:95%;display:inline;"value="">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>



                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>                                        
                    <td>Design</td>
                    <td colspan="2"><input onchange="updateGeneralTotals(this)" value="<?=$order->setup?>" onkeyup="updateGeneralTotals()" type="number" step="0.01" class="form-control text-money" name="general_design" id="general_design" value=""></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Subtotal</td>
                    <td colspan="2"><input type="number" step="0.01" id="general_subtotal" name="general_subtotal" class="form-control text-money" readonly></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Tax(7.1%)</td>
                    <td colspan="2"><input onchange="updateGeneralTotals(this)" value="<?=$order->tax?>"onkeyup="updateGeneralTotals()" type="number" step="0.01" id="general_tax" name="general_tax" class="form-control text-money"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" rowspan="4">
                        <?if(file_exists('draw/'.$ord.'_general.png')){
                        ?><h4>Sketch:</h4><img src="draw/<?=$ord?>_general.png"><?
                        }else{
                        ?>&nbsp;<?
                        }?>                                        
                    </td>
                    <td>Shipping</td>
                    <td colspan="2"><input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" value="<?=$order->shipping?>"type="number" step="0.01" id="general_shipping" name="general_shipping" class="form-control text-money"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td colspan="2"><input type="number" step="0.01" id="general_total" name="general_total" class="form-control text-money" readonly></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Deposit</td>
                    <td colspan="2"><input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" value="<?=$order->deposit?>" type="number" step="0.01" id="general_deposit" name="general_deposit" class="form-control text-money"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Balance Due</td>
                    <td colspan="2"><input type="number" step="0.01" id="general_balancedue" name="general_balancedue" class="form-control text-money" readonly></td>
                    <td>&nbsp;</td>
                </tr>

            </tfoot>
        </table>
        <?
        if(file_exists('draw/'.$ord.'_cust_signature.png')){
        ?>
            <h3>Customer Signature:</h3>
            <img src="draw/<?=$ord?>_cust_signature.png" style="max-width:100%">
        <?
        }
        ?>
        <br>
        <br>
        <br>
        <script>
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
            updateGeneralTotals();
        </script>

    <?

    }


    ?>





<?
}

//helper function 
function in_2d_array($needle,$haystack){

    if(!is_array($haystack)) return false;

    foreach($haystack as $h){
        if(in_array($needle, $h)){
            return true;
        }
    }
    return false;

}
?>