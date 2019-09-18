<?
require 'libs/page.php';
require 'libs/business_classes.php';
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
            <?=$page->sideBar();?>

            <!-- Page Content -->
            <div id="page-content-wrapper">

                <?=$page->navBar()?>

                <div class="container-fluid order_entry"></div>
            </div>            

        </div>

    </body>
    <script>
        $('document').ready(function(){
            loadOrderScreen();
        })
        function loadOrderScreen(){
            $.post('?cmd=loadOrderScreen',function(html){
                $(".order_entry").html(html);
            })
        }
    </script>


</html>
<?


function loadOrderScreen(){
?>
    <h2>Order Entry</h2>
    <div class="row order_screen">
        <div class="col-md-8">
            <table class="table cust_order">
                <tr>
                    <th style="width:1px; white-space:nowrap;">Customer Name</th>
                    <td>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Full Name" >                                        
                    </td>
                </tr>
                <tr>

                    <th style="width:1px; white-space:nowrap;">Phone</th>
                    <td>
                        <input type="text" class="form-control" id="customer_phone"  name="customer_phone" placeholder="801-888-9999" >
                    </td>

                </tr>
                <tr>

                    <th style="width:1px; white-space:nowrap;">Cell Phone</th>
                    <td>
                        <input type="text" class="form-control" id="customer_cell_phone"  name="customer_cell_phone" placeholder="801-999-8888">
                    </td>

                </tr>
                <tr>
                    <th style="width:1px; white-space:nowrap;">Email</th>
                    <td>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="customer@email.com" >
                    </td>
                </tr>
                <tr>
                    <th style="width:1px; white-space:nowrap;">Address</th>
                    <td>
                        <input type="text" class="form-control" id="customer_addr1" name="customer_addr1" placeholder="123 Street">
                        <span class="btn-link show_addr2" onclick="$(this).hide(); $('#customer_addr2').fadeIn()">+ Line 2</span>                        
                        <input type="text" class="form-control" id="customer_addr2" name="customer_addr2" placeholder="Line Two" style="display:none;">
                    </td>
                </tr>
                <tr>
                    <th style="width:1px; white-space:nowrap;">City</th>
                    <td>
                        <input type="text" class="form-control" id="customer_city" name="customer_city" placeholder="City">
                    </td>
                </tr>
                <tr>
                    <th style="width:1px; white-space:nowrap;">State</th>
                    <td>
                        <input type="text" class="form-control" id="customer_state" name="customer_state" placeholder="State" style="max-width:300px;">
                    </td>
                </tr>
                <tr>
                    <th style="width:1px; white-space:nowrap;">Zip</th>
                    <td>
                        <input type="text" class="form-control" id="customer_zip" name="customer_zip" placeholder="00000" style="max-width:300px;">
                    </td>
                </tr>
                <tr>
                    <th style="width:1px; white-space:nowrap;">Order Date</th>
                    <td>
                        <input type="text" class="form-control datepicker" id="order_date" name="order_date" value="<?=Date('m/d/Y')?>" placeholder="MM/DD/YYYY" >
                    </td>
                </tr>
                <tr>
                    <th style="width:1px; white-space:nowrap;">Due Date</th>
                    <td>                                        
                        <input type="text" class="form-control datepicker" id="order_due_date" name="order_due_date" placeholder="MM/DD/YYYY" value="<?=Date('m/d/Y',strtotime('today + 7 days'))?>">
                    </td>
                </tr>
            </table>
            <script>
                $('document').ready(function(){

                    $('#customer_name').on('focus',function(){
                        $(this).select()
                    }).typeahead({
                        name: 'value<?=rand()?>',
                        limit: 10,
                        header: '',
                        remote: '?cmd=autoc&customer=%QUERY&type=customer',
                        template: '<p><span  style="white-space: nowrap;"><strong>{{value}}</strong></span></p>',
                        engine: Hogan
                    }).parent().css("width","100%").on('typeahead:selected', function (e, datum) {
                        //$value = datum.value;
                        //$id = datum.id;
                        $("#customer_phone").val(datum.phone);
                        $("#customer_cell_phone").val(datum.cell);
                        $("#customer_email").val(datum.email);
                        $("#customer_addr1").val(datum.addr1);
                        debugger;
                        $("#customer_addr2").val(datum.addr2);
                        if(datum.addr2 != '' && datum.addr2 != null){
                            $(".show_addr2").trigger('click');
                        }
                        $("#customer_city").val(datum.city);
                        $("#customer_state").val(datum.state);
                        $("#customer_zip").val(datum.zip);
                        loadPrevOrders();
                    });                                        

                    $('#customer_state').on('focus',function(){
                        $(this).select()
                    }).typeahead({
                        name: 'value<?=rand()?>',
                        limit: 10,
                        header: '',
                        remote: '?cmd=autoc&state=%QUERY&type=states',
                        template: '<p><span  style="white-space: nowrap;"><strong>{{value}}</strong></span></p>',
                        engine: Hogan
                    }).parent().css("width","100%").on('typeahead:selected', function (e, datum) {

                    });                                        

                })
            </script>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Previous Orders
                </div>
                <div class="card-body prev_orders">
                    <div class="alert alert-secondary" role="alert">
                        No previous orders
                    </div>
                </div>
            </div>
        </div>           

        <div class="col-lg-12" style="overflow: auto;">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" onclick="saveType('general');">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#general" role="tab" aria-controls="home" aria-selected="true">General</a>
                </li>
                <li class="nav-item" onclick="saveType('shirt');">
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

                            <div class="my-drawing" style="margin-top:15px;"></div>


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
                                                <input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" type="number" step="1" class="form-control text-money" name="general_discount" id="general_discount" style="max-width:95%;display:inline;"value="">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>



                                        </td>

                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>                                        
                                        <td colspan="3" rowspan="7">
                                            <textarea name="general_notes" id="general_notes" class="form-control" style="min-height:300px;" placeholder="Notes:"></textarea>
                                        </td>
                                        <td>Design</td>
                                        <td colspan="2"><input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" type="number" step="0.01" class="form-control text-money" name="general_design" id="general_design" value=""></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Subtotal</td>
                                        <td colspan="2"><input type="number" step="0.01" id="general_subtotal" name="general_subtotal" class="form-control text-money" readonly></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Tax(7.1%)</td>
                                        <td colspan="2"><input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" type="number" step="0.01" id="general_tax" name="general_tax" class="form-control text-money"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Shipping</td>
                                        <td colspan="2"><input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" type="number" step="0.01" id="general_shipping" name="general_shipping" class="form-control text-money"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td colspan="2"><input type="number" step="0.01" id="general_total" name="general_total" class="form-control text-money" readonly></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Deposit</td>
                                        <td colspan="2"><input onchange="updateGeneralTotals(this)" onkeyup="updateGeneralTotals()" type="number" step="0.01" id="general_deposit" name="general_deposit" class="form-control text-money"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Balance Due</td>
                                        <td colspan="2"><input type="number" step="0.01" id="general_balancedue" name="general_balancedue" class="form-control text-money" readonly></td>
                                        <td>&nbsp;</td>
                                    </tr>

                                </tfoot>
                            </table>   





                        </div>         



                    </div>             

                </div>
                <!--//GENERAL-->



                <div class="container-fluid tab-pane fade" id="shirt" role="tabpanel" aria-labelledby="profile-tab"> <br>
                    <div class="row">
                        <div class="col-lg-6 spad-container" style="overflow: auto;">
                            <canvas id="signature-pad" class="signature-pad" style="border:1px solid #000000;"></canvas>
                            <br><button onclick="clearCanvas()" class="btn btn-secondary">Clear</button>
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    Items:
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input type="checkbox" class="icheck form-check-input" id="items_hoodie" value="hoodie" name="items_cat[]">
                                        <label class="form-check-label" for="items_hoodie">Hoodie</label>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="icheck form-check-input" id="items_jacket" value="jacket" name="items_cat[]">
                                        <label class="form-check-label" for="items_jacket">Jacket</label>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="icheck form-check-input" id="items_crewneck" value="crewneck" name="items_cat[]">
                                        <label class="form-check-label" for="items_crewneck">Crew-neck</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="icheck form-check-input" id="items_zipup" value="zipup" name="items_cat[]">
                                        <label class="form-check-label" for="items_zipup">Zip-up</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="icheck form-check-input" id="items_other" value="other" name="items_cat[]">
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
                                        <input type="checkbox" class="icheck form-check-input" id="type_digital_print" value="digitalprint" name="print_type[]">
                                        <label class="form-check-label" for="type_digital_print">Digital Printing</label>
                                    </div>                                    
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-check" >
                                        <input type="checkbox" class="icheck form-check-input" id="type_screen_print" value="screenprint" name="print_type[]">
                                        <label class="form-check-label" for="type_screen_print">Screen Printing</label>
                                    </div>                                    
                                </div>

                                <div class="col-sm-3">

                                    <div class="form-check" style="display:inline;">
                                        <input type="checkbox" class="icheck form-check-input" id="type_embroidery" value="embroidery" name="print_type[]">
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
                                        <td colspan="16" ><button type="button" class="btn btn-info"  style="float: left;"  onclick="addItem()"><i class="fas fa-plus-square"></i> Add Item</button></td>

                                        <td>Setup</td>
                                        <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" type="number" step="0.01" class="form-control text-money" name="setup" id="setup" value=""></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="16" rowspan="6" ><textarea name="instructions" id="instructions" class="form-control" style="min-height:200px;" placeholder="Instructions:"></textarea></td>
                                        <td>Subtotal</td>
                                        <td colspan="2"><input type="number" step="0.01" id="subtotal" name="subtotal" class="form-control text-money" readonly></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Tax(7.1%)</td>
                                        <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" type="number" step="0.01" id="tax" name="tax" class="form-control text-money"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Shipping</td>
                                        <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" type="number" step="0.01" id="shipping" name="shipping" class="form-control text-money"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td colspan="2"><input type="number" step="0.01" id="total" name="total" class="form-control text-money" readonly></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Deposit</td>
                                        <td colspan="2"><input onchange="updateTotals(this)" onkeyup="updateTotals()" type="number" step="0.01" id="deposit" name="deposit" class="form-control text-money"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Balance Due</td>
                                        <td colspan="2"><input type="number" step="0.01" id="balancedue" name="balancedue" class="form-control text-money" readonly></td>
                                        <td>&nbsp;</td>
                                    </tr>

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="col-12" style="overflow: auto; margin-top:15px;">  
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6 cust-spad-container" style="">
                                Customer Signature:
                                <canvas id="customer_signature-pad" class="customer_signature-pad" style="border:1px solid #000000;"></canvas>
                                <br><button onclick="cust_sign_pad.clear()" class="btn btn-secondary">Clear</button>
                            </div>


                            <div class="col-sm-2">
                                <input type="checkbox" class="icheck" name="email_customer" value="1"> Email Customer
                            </div>
                            <div class="col-sm-2">
                                <input type="checkbox" class="icheck" name="text_customer" value="1"> Text Customer
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-primary btn-lg" onclick="submitOrder(this)"><i class="fas fa-check"></i> Submit Order</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>




    </div>
    <br><br><br><br><br><br>
    <script>

        $('document').ready(function(){                 
            var currentTime = new Date();
            var m = currentTime.getMonth()+1;
            var d = currentTime.getDate();
            var Y = currentTime.getFullYear();
            $("#order_date").val(m+'/'+d+'/'+Y);
            //responsive canvas
            resizeCanvas();
            //keep all input fields from browser auto-fill
            $(':input').attr('autocomplete', '0');
            //add the first item:
            addItem();
            addGeneralItem();
            icheckUpdate();
            makeDate();
            loadDrawPad();

        })
        var lcanvas;
        function loadDrawPad(){

            lcanvas = LC.init(
                $('.my-drawing')[0] ,
                {imageURLPrefix: 'vendor/literalcanvas/img/'}
            );
        }


        function loadPrevOrders(){
            $('.prev_orders').html('Previous orders will go here');
        }

        function submitShirtOrder(t){
            $(t).Loading('Submitting Order');
            if(confirm("Ready to submit order?")){

                $.post('?cmd=submitOrder',$(".order_entry").Values()+'&draw='+shirtPad.toDataURL()+'&signature='+cust_sign_pad.toDataURL(),function(res){

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

        function submitOrder(t){
            $(t).Loading('Submitting Order');
            if(confirm("Ready to submit order?")){

                $lc_saveImg ='';
                try {
                    $lc_saveImg = lcanvas.getImage().toDataURL();
                }
                catch(err) {
                    $lc_saveImg ='';
                }

                $.post('?cmd=submitOrder',$(".order_entry").Values()+'&draw='+shirtPad.toDataURL()+'&lc='+$lc_saveImg+'&signature='+cust_sign_pad.toDataURL(),function(res){

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

        var general_row = 0;
        function addGeneralItem(){
            $.post('?cmd=addGeneralLine','&row='+general_row,function(html){
                general_row++;
                $("#general_order_table_body").append(html);
            })
        }

        //Canvas
        {
            function clearCanvas(){
                canvasReload();
            }

            var canvas = document.querySelector("#signature-pad");
            ctx = canvas.getContext("2d");

            //Customer Signature:

            var cust_canvas = document.querySelector("#customer_signature-pad");
            cust_ctx = cust_canvas.getContext("2d");

            var cust_sign_pad = new SignaturePad(cust_canvas);
            resizeCustomerCanvas();
            ///////

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
                resizeCustomerCanvas();
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

            function resizeCustomerCanvas(){


                var width = $( window ).width();                         
                if(width < 1420){
                    cust_canvas.width = $('.cust-spad-container').width()-40;
                }else{
                    cust_canvas.width = 900;
                }                
                cust_canvas.height = 100;

            }

        }

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
use Twilio\Rest\Client;
function submitShirtOrder(){

    //handle the customer information:
    $customer = new Customer();
    $customer->name = Post('customer_name');
    $customer->phone = Post('customer_phone');
    $customer->cell = Post('customer_cell_phone');
    $customer->email = Post('customer_email');
    $customer->addr1 = Post('customer_addr1');
    $customer->addr2 = Post('customer_addr2');
    $customer->city = Post('customer_city');
    $customer->state = Post('customer_state');
    $customer->zip = Post('customer_zip');

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
    $order->ord_type = 'Shirt';
    $order->instructions = Post('instructions');

    $order->setup = Post('setup');
    $order->tax = Post('tax');
    $order->shipping = Post('shipping');
    $order->deposit = Post('deposit');



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

        //Save drawing:
        $data_uri = Post('draw');
        $encoded_image = str_replace(' ','+',explode(",", $data_uri)[1]);    
        $decoded_image = base64_decode($encoded_image);

        $url='draw/'.$order->getUID().'_drawing.png';

        file_put_contents($url, $decoded_image);

        echo true;

    }else{
        echo 'Could not save order items'; 
        return;
    }


}
//Todo Combine shirt and general order, this was setup this way due to lack of time
function submitOrder(){
    global $page;
    //handle the customer information:
    $customer = new Customer();
    $customer->name = Post('customer_name');
    $customer->phone = Post('customer_phone');
    $customer->cell = Post('customer_cell_phone');
    $customer->email = Post('customer_email');
    $customer->addr1 = Post('customer_addr1');
    $customer->addr2 = Post('customer_addr2');
    $customer->city = Post('customer_city');
    $customer->state = Post('customer_state');
    $customer->zip = Post('customer_zip');

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

    $order->ord_type = Post('save_type');

    $order->ord_date = Post('order_date');
    $order->ord_due = Post('order_due_date');

    if($order->ord_type == 'general'){
        $order->instructions = Post('general_notes');
        $order->discount = Post('general_discount');
        $order->setup = Post('general_design');
        $order->tax = Post('general_tax');
        $order->shipping = Post('general_shipping');
        $order->deposit = Post('general_deposit');

    }else if($order->ord_type == 'shirt'){

        $order->instructions = Post('instructions');
        $order->setup = Post('setup');
        $order->tax = Post('tax');
        $order->shipping = Post('shipping');
        $order->deposit = Post('deposit');

    }

    $res = $order->insert_db();
    if($res){
        $line = new LineItem($order->getUID());

        if(strtolower($order->ord_type) == 'shirt'){    
            //shirt specific addons...
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
        }
        //insert the line items:
        $loopPostLineItems = true;
        $i = 0;
        while($loopPostLineItems){


            if(strtolower($order->ord_type) == 'general'){    

                if(Post('general_model_number'.$i)==''){
                    $loopPostLineItems = false;
                    continue;
                }
                $line->model = Post('general_model_number'.$i);
                $line->description = Post('general_description'.$i);
                $line->qty = Post('general_qty'.$i);
                $line->price = Post('general_price'.$i);
                $line->total = Post('general_total'.$i);

            }
            if(strtolower($order->ord_type) == 'shirt'){
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

            }            

            $line->insert_db();
            $i++;
        }
        echo true;
        //Save drawing:

        $data_uri = Post('lc');
        $encoded_image = str_replace(' ','+',explode(",", $data_uri)[1]);    
        $decoded_image = base64_decode($encoded_image);

        $url='draw/'.$order->getUID().'_general.png';

        file_put_contents($url, $decoded_image);

        //save shirt drawing:
        $data_uri = Post('draw');
        $encoded_image = str_replace(' ','+',explode(",", $data_uri)[1]);    
        $decoded_image = base64_decode($encoded_image);

        $url='draw/'.$order->getUID().'_drawing.png';

        file_put_contents($url, $decoded_image);


        //save signature:

        $data_uri = Post('signature');
        $encoded_image = str_replace(' ','+',explode(",", $data_uri)[1]);    
        $decoded_image = base64_decode($encoded_image);

        $url='draw/'.$order->getUID().'_cust_signature.png';

        file_put_contents($url, $decoded_image);

        //notify customer:

        //if Post('text_customer')==1
        //text the customer a link to the coming soon page...

        //if Post('email_customer')== 1 
        //send the same but via email

        $text_customer = (Post('text_customer') == 1)?true:false;
        $email_customer = (Post('email_customer') == 1)?true:false;

        if($text_customer && is_numeric($customer->cell)){

            require_once "libs/Twilio/autoload.php";
            // Your Account SID and Auth Token from twilio.com/console
            $sid = '';
            $token = '';
            $client = new Client($sid, $token);

            $msg = "Your order has been created, click here to view: https://hlook.infofast.net/view_ord.php?ord=".$page->encryption($order->getUID());
            $res = $client->messages->create($customer->cell, array('from' => '+18015152926', 'body' => $msg));

        }

        if($email_customer && $customer->email != ''){
            $to = $customer->email;
            $subject = "Order Created";
            $message = "
            <html>
            <head>
            <title>Order Created</title>
            </head>
            <body>
            <p>Your order has been created, click here to view: https://hlook.infofast.net/view_ord.php?ord=".$page->encryption($order->getUID())."</p>
            </body>
            </html>
            ";

            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // More headers
            $headers .= 'From: <info@hlook.infofast.net>' . "\r\n";
            //$headers .= 'Cc: myboss@example.com' . "\r\n";
            //mail($to,$subject,$message,$headers);

            //mail('hlookgraphics@gmail.com',$subject,$message,$headers);
        }




    }else{
        echo 'Could not save order items'; 
        return;
    }


}



function addLine(){
    $row = Post('row');

    ?>
    <tr>
        <td><input name="model_number<?=$row?>" class="form-control"type="text"></td>
        <td><input name="color<?=$row?>" class="form-control"type="text"></td>
        <td><input name="description<?=$row?>" class="form-control"type="text"></td>

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
            <input name="size_other<?=$row?>" class="form-control"type="text" style="max-width:50px;padding:1px">
        </td>
        <td><input onchange='updateTotals(this);'onkeyup='updateTotals(this)' name="qty<?=$row?>" class="form-control qty" type="number" style="max-width:100px;padding:1px;text-align:right;"></td>
        <td><input onchange='updateTotals(this);'onkeyup='updateTotals(this)' name="price<?=$row?>" class="form-control price text-money" type="number" style="max-width:200px;padding:1px"></td>
        <td><input onchange='updateTotals();' onkeyup='updateTotals();'name="total<?=$row?>" class="form-control total text-money" type="number" style="max-width:200px;padding:1px"></td>
        <td><button class="btn btn-danger" onclick="removeRow(this)">X</button></td>
    </tr>
    <?
}

function addGeneralLine(){
    $row = Post('row');

    ?>
    <tr>
        <td><input name="general_model_number<?=$row?>" class="form-control"type="text"></td>

        <td><input name="general_description<?=$row?>" class="form-control"type="text"></td>


        <td><input onchange='updateGeneralTotals(this);'onkeyup='updateGeneralTotals(this)' name="general_qty<?=$row?>" class="form-control general_qty" type="number" style="max-width:100px;padding:1px;text-align:right;"></td>
        <td><input onchange='updateGeneralTotals(this);'onkeyup='updateGeneralTotals(this)' name="general_price<?=$row?>" class="form-control general_price text-money" type="number" style="max-width:200px;padding:1px"></td>
        <td colspan="2"><input onchange='updateGeneralTotals();' onkeyup='updateGeneralTotals();'name="general_total<?=$row?>" class="form-control general_total text-money" type="number" style="padding:1px"></td>
        <td><button class="btn btn-danger" onclick="removeGeneralRow(this)">X</button></td>
    </tr>
    <?
}

/**
* Helper function to database escape any variable passed by reference.
* 
* @param mixed $item variable to be escaped
*/

