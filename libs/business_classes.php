<?

/**
* Customer class to hold the customer information, insert and check database.
*/
class Customer{

    public $name;
    public $phone;
    public $cell;
    public $email;
    
    
    public $addr1;
    public $addr2;
    public $city;
    public $state;
    public $zip;
    public $cust_id;
    private $comp_id;

    function __construct() {
        global $comp_id;
        $this->name='';
        $this->phone='';
        $this->cell='';
        $this->email='';

        $this->addr1='';
        $this->addr2='';        
        $this->city='';        
        $this->state='';        
        $this->zip='';        


        $this->comp_id = $comp_id;
    }
    /**
    * Checks to see if customer exists, if customer exists then puts customer number in public variable and returns true, else false.
    * 
    */
    function exists(){

        global $db;
        $this->phone = unFormatPhone($this->phone);
        $this->cell = unFormatPhone($this->cell);
        $sql = "select * from customers where comp_id = $this->comp_id and name='$this->name' and phone = '$this->phone' and cell='$this->cell' and email ='$this->email' and addr1='$this->addr1' and addr2 = '$this->addr2' and city = '$this->city' and state= '$this->state' and zip='$this->zip'";

        $check = $db->retrieveRow($sql);    

        if(!is_array($check)){
            return false;
        }else{
            $this->cust_id = $check['cust_id'];
            return true;            
        }

    }
    /**
    * Inserts the current customer, sets the public cust_id var and returns true on success. Returns false if error or already exists.
    * 
    */
    function insert_db(){
        global $db;

        $this->name = $db->Escape($this->name);
        $this->phone = unFormatPhone($db->Escape($this->phone));
        $this->cell = unFormatPhone($db->Escape($this->cell));
        $this->email = $db->Escape($this->email);

        $this->addr1 = $db->Escape($this->addr1);
        $this->addr2 = $db->Escape($this->addr2);
        $this->city = $db->Escape($this->city);
        $this->state = $db->Escape($this->state);
        $this->zip = $db->Escape($this->zip);

        $sql = "INSERT INTO `customers`(`name`, `phone`, `cell`, `email`, `addr1`, `addr2`, `city`, `state`,`zip`,`comp_id`) VALUES ('$this->name','$this->phone','$this->cell','$this->email','$this->addr1','$this->addr2','$this->city','$this->state','$this->zip',$this->comp_id)";

        if($this->exists()) return false;

        $res = $db->runSQL($sql);

        if($res){            
            $this->cust_id = $db->retrieveValue("select max(cust_id) from `customers` where comp_id = $this->comp_id");
            return true;

        }else{
            return false;
        }        

    }
    
    /**
    * Takes an id, by default id only is customer id, if it is order ID then type should be 'order'
    * 
    * @param mixed $id customer id
    * @param mixed $type default customer can also be order
    */
    function load_customer($id, $type='customer'){
        global $db;
        if($type == 'order'){
            $id = $db->retrieveValue("select cust_id from orders where uid = $id");
        }
        $sql = "select * from customers where cust_id = $id";
        $customer = $db->retrieveRow($sql);
                
        $this->cust_id = $customer['cust_id'];
        $this->name = $customer['name'];
        $this->phone = $customer['phone'];
        $this->cell = $customer['cell'];
        $this->email = $customer['email'];
        $this->addr1 = $customer['addr1'];
        $this->addr2 = $customer['addr2'];
        $this->city = $customer['city'];
        $this->state = $customer['state'];
        $this->zip = $customer['zip'];
        $this->comp_id = $customer['comp_id'];
    }
    

}

/**
* Order class to hold the order information
*/
class Order{

    private $uid;
    public $order_id;
    public $cust_id;
    private $comp_id;

    public $ord_date;
    public $ord_due;
    public $ord_type;
    public $instructions;

    public $discount;
    public $setup;
    public $tax;
    public $shipping;
    public $deposit;


    function __construct($cust_id = 0) {
        global $comp_id, $db;
        $this->uid='0';
        $this->order_id = '0';
        $this->cust_id = $cust_id;
        $this->comp_id = $comp_id;
        $this->ord_date = Date('m-d-Y');
        $this->ord_due = Date('m-d-Y',strtotime('today + 1 week'));
        $this->ord_type = 'General';
        $this->instructions = '';

        $this->discount = '0.00';
        $this->setup = '0.00';
        $this->tax = '0.00';
        $this->shipping = '0.00';
        $this->deposit = '0.00';
        $this->status = 1;

        $sql = "select ifnull(max(order_id)+1,1) from orders where comp_id = $comp_id";
        $this->order_id = $db->retrieveValue($sql);
        $sql = "select ifnull(max(uid)+1,1) from orders";
        $this->uid = $db->retrieveValue($sql);

    }
    /**
    * Inserts the order variables, returns true on success else false.
    * 
    */
    function insert_db(){
        global $db;
        //db escape the user input variables before sql insertion
        $this->ord_date = Date('Y-m-d',strtotime($db->escape($this->ord_date)));
        $this->ord_due = Date('Y-m-d',strtotime($db->escape($this->ord_due)));
        $this->instructions = $db->escape($this->instructions);

        $this->discount = ($this->discount == '')?0.00:$this->discount;
        $this->setup = ($this->setup == '')?0.00:$this->setup;
        $this->tax = ($this->tax == '')?0.00:$this->tax;
        $this->shipping = ($this->shipping == '')?0.00:$this->shipping;
        $this->deposit = ($this->deposit == '')?0.00:$this->deposit;
        //This value gets used raw for labels..
        $this->ord_type = ucfirst(strtolower($this->ord_type));
                
        $sql = "INSERT INTO `orders`(`order_id`, `order_date`, `order_due`, `order_type`, `instructions`, `comp_id`, `cust_id`,`discount`,`setup`,`tax`,`shipping`,`deposit`,`status`) VALUES ($this->order_id,'$this->ord_date','$this->ord_due', '$this->ord_type', '$this->instructions',$this->comp_id,$this->cust_id,$this->discount,$this->setup,$this->tax,$this->shipping,$this->deposit,$this->status)";

        $res = $db->runSQL($sql);
        if($res){
            return true;
        }else{
            return false;
        }

    }

    /**
    * Takes the items array and type (approval, items, print type, etc.). loops the array and inserts in database.
    * 
    * @param mixed $items array of the value (jacket, hat, etc)
    * @param mixed $type string of tyep (approval, items, etc.)
    */
    function insertAddOns($items, $type){
        global $db;
        //check to make sure something is passed:
        if($items == '' || $type == ''){ return false; }
        //check to make sure it is an array, can be either array or value
        $type = $db->Escape($type);
        try {
            if(is_array($items)){
                foreach($items as $i){
                    $i = $db->Escape($i);
                    $db->runSQL("INSERT INTO `order_options`(`uid`, `order_option`, `val01`) VALUES ($this->uid,'$type','$i')");
                }
            }
            else{
                $items = $db->Escape($items);
                $res = $db->runSQL("INSERT INTO `order_options`(`uid`, `order_option`, `val01`) VALUES ($this->uid,'$type','$items')");
            }        
        } catch (Exception $e) {
            return false;
        }        
        return true;
    }  

    function getUID(){
        return $this->uid;
    }  

    //brandon complete this
    function updateOrder(){

    }
    
    function load_order($id){
        global $db;
        $sql = "SELECT * FROM `orders` where uid = $id";
        $order = $db->retrieveRow($sql);
        
        $this->uid = $id;
        $this->order_id = $order['order_id'];
        $this->cust_id = $order['cust_id'];
        $this->comp_id = $order['comp_id'];
        $this->ord_date = $order['order_date'];
        $this->ord_due = $order['order_due'];
        $this->ord_type = $order['order_type'];
        $this->instructions = $order['instructions'];
        $this->discount = $order['discount'];
        $this->setup = $order['setup'];
        $this->tax = $order['tax'];
        $this->shipping = $order['shipping'];
        $this->deposit = $order['deposit'];
    }
}            

/**
* Inserts the line items into the database
*/
class LineItem{

    public $uid;
    private $line;
    public $model;
    public $color;
    public $description;
    public $size;
    public $size_other;
    public $qty;
    public $price;
    public $total;

    function __construct($uid = 0) {
        $this->uid = $uid;
        $this->line = 1;

        $this->model = '';
        $this->color = '';
        $this->description = '';
        $this->size = '';
        $this->size_other = '';
        $this->qty = 0;
        $this->price = 0;
        $this->total = 0;
    }

    function insert_db(){
        global $db;
        if($this->qty =='') $this->qty = 0;
        if($this->price =='') $this->price = 0;
        if($this->total =='') $this->total = 0;
        $sql = "INSERT INTO `order_lines`(`uid`, `line_number`, `model`, `color`, `description`, `size`, `size_other`,`qty`, `price`, `total`) VALUES ($this->uid,$this->line,'".esc($this->model)."','".esc($this->color)."','".esc($this->description)."','".esc($this->size)."','".esc($this->size_other)."',".esc($this->qty).",".esc($this->price).",".esc($this->total).")";
        //NOTE BRANDON: DEBUG HERE TO SEE IF ESC() PASS BY REF WORKS...
        $res = $db->runSQL($sql);
        if($res){$this->line++;}
    }
    
    function load_items(){
        global $db;
        $sql = "select * from order_lines where uid = $this->uid";
        return $db->retrieveData($sql);        
    }
}

function autoc()
{

    global $db, $comp_id;

    $type = Get('type');
    if($type == 'customer'){

        $customer = $db->escape(strtoupper(str_replace(' ','',Get("customer"))));

        $sql = "SELECT * FROM `customers` WHERE REPLACE(upper(name), ' ', '') LIKE '%$customer%' and `comp_id` = $comp_id LIMIT 10";

        $rs = $db->RetrieveData($sql);
        if ($rs)
        {
            foreach($rs as $r)
                $data[] = array('value'=>$r["name"],'id'=>$r['cust_id'], 'phone'=>$r['phone'],'cell'=>$r['cell'], 'email'=>$r['email'], 'addr1'=>$r['addr1'], 'addr2'=>$r['addr2'], 'city'=>$r['city'], 'state'=>$r['state'], 'zip'=>$r['zip']);
            echo json_encode($data,JSON_HEX_APOS);
            exit();
        }

    }    

    if($type == 'order'){

        $order = $db->escape(strtoupper(str_replace(' ','',Get("order"))));

        $sql = "SELECT * FROM `orders` o left join `customers` c on o.cust_id = c.cust_id and o.comp_id = c.comp_id WHERE REPLACE(upper(c.name), ' ', '') LIKE '%$order%' or o.order_id LIKE '%$order%' and o.comp_id = $comp_id ORDER BY `o`.`order_id` ASC  LIMIT 10";

        $rs = $db->RetrieveData($sql);
        if ($rs)
        {
            foreach($rs as $r)
                $data[] = array('value'=>$r["order_id"],'name'=>$r['name'], 'date'=>Date('m/d/Y',strtotime($r['order_date'])));
            echo json_encode($data,JSON_HEX_APOS);
            exit();
        }


    }

    if($type == 'states'){
        $state_arr = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
            'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii',
            'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana',
            'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota',
            'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire',
            'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota',
            'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island',
            'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont',
            'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
        ];
        $state = Get("state");
        $data = array();
        foreach($state_arr as $s){

            if (strpos(strtolower($s), strtolower($state)) !== false) {
                $data[] = array('value'=>$s);
            }           

        }
        echo json_encode($data,JSON_HEX_APOS);
        exit();

    }

    exit();

}

function formatPhone($phone){
    if($phone == '' || $phone == '--') return $phone;
    $formatted = substr_replace(substr_replace($phone, '-', -4, 0), '-', -8, 0);    
    if(strlen($phone) > 10){
        $formatted = substr_replace($formatted, ' ', -12, 0);
    }
    return $formatted;
}

function unFormatPhone($phone){
    return preg_replace("/[^0-9]/", "",$phone);

}


function esc(&$item){
    global $db;
    $item = $db->Escape($item);
    return $item;
}


function nextDept(){    
    global $db;
    $order_uid = Post('ord_uid');
    $next_dept = Post('next_dept');

    $sql = "UPDATE `route_manager` SET `dept_id` = $next_dept WHERE `uid` = $order_uid";    
    echo $db->runSQL($sql);

    $sql = "UPDATE `orders` SET `status` = 2 WHERE `uid` = $order_uid";    
    $db->runSQL($sql);


}

?>
