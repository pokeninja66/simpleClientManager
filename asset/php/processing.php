<?php

if (!isset($_REQUEST['action'])) {
    die("You can't acces this file!");
} else {
    $action = trim($_REQUEST['action']);
}


// for testing purposes
define("IN_DEMO_MODE", 1); // 0-not active / 1 -active

################################ client stuff ################################
if ($action == 'addClient') {

    #print_r($_REQUEST);

    header('Content-type: application/json');
    $JsonObj = new stdClass();
    $JsonObj->msg = '';
    $JsonObj->status = true;


    # proverki (the if-ening)
    if (!isset($_REQUEST['Form'])) {
        $JsonObj->msg = "Error! No  data!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }

    include "./DB.php";

    $FormDataArr = array();
    foreach ($_REQUEST['Form'] as $key => $oneDataValue) {
        $FormDataArr[$key] = mysqli_real_escape_string($conn, trim($oneDataValue));
    }

    if (!$FormDataArr['name']) {
        $JsonObj->msg = "at least enter a name!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }
    #print_r($FormDataArr);

    $query = "INSERT INTO `clients` (`name`,`phone`,`email`,`type`,`product_id`) 
    VALUES('" . $FormDataArr['name'] . "','" . $FormDataArr['phone'] . "','" . $FormDataArr['email'] . "','" . $FormDataArr['type'] . "','" . $FormDataArr['product_id'] . "')";
    #echo $query;
    if (!$conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
        $JsonObj->msg = 'An error....';
        $JsonObj->status = false;
        $JsonObj->query = $query;
        echo json_encode($JsonObj);
        exit;
    }

    $last_inserted_id = $conn->insert_id;

    $JsonObj->last_inserted_id = $last_inserted_id;
    $JsonObj->paid_status_update = updateClientPaiment($last_inserted_id, $FormDataArr['type'], $FormDataArr['product_id']);

    $JsonObj->msg = 'Client added!';
    echo json_encode($JsonObj);
    exit;
}

if ($action == 'updateClient') {
    #print_r($_REQUEST);

    header('Content-type: application/json');
    $JsonObj = new stdClass();
    $JsonObj->msg = '';
    $JsonObj->status = true;

    if (!isset($_REQUEST['Form'])) {
        $JsonObj->msg = "Error! No  data!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }

    include "./DB.php";

    $FormDataArr = array();
    foreach ($_REQUEST['Form'] as $key => $oneDataValue) {
        $FormDataArr[$key] = mysqli_real_escape_string($conn, trim($oneDataValue));
    }
    if (!$FormDataArr['name']) {
        $JsonObj->msg = "at least enter a name!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }
    #print_r($FormDataArr);

    $query = " UPDATE `clients` SET
        `name` ='" .  $FormDataArr['name'] . "',
        `phone` ='" .  $FormDataArr['phone'] . "',
        `email` ='" .  $FormDataArr['email'] . "',
        `type` ='" .  $FormDataArr['type'] . "',
        `product_id` ='" .  $FormDataArr['product_id'] . "'

        WHERE id=" . $FormDataArr['id'] . "
    ";
    #echo $query;
    if (!$conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
        $JsonObj->msg = 'An error....';
        $JsonObj->status = false;
        $JsonObj->query = $query;
        echo json_encode($JsonObj);
        exit;
    }

    $JsonObj->paid_status_update = updateClientPaiment($FormDataArr['id'], $FormDataArr['type'], $FormDataArr['product_id']);
    $JsonObj->msg = 'Client updated!';
    echo json_encode($JsonObj);
    exit;
}

if ($action == 'getClientData') {
    #print_r($_REQUEST);

    header('Content-type: application/json');
    $JsonObj = new stdClass();
    $JsonObj->msg = '';
    $JsonObj->status = true;

    if (!isset($_REQUEST['update_id'])) {
        $JsonObj->msg = "Error! No  data!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }
    $update_id = $_REQUEST['update_id'];
    $JsonObj->clientData = new stdClass();

    include "./DB.php";

    $query = "SELECT `id`,`name`,`phone`,`email`,`type`,`product_id` FROM `clients` WHERE name!='' AND id=$update_id ";

    if (!$result = $conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
        $JsonObj->msg = 'An error....';
        $JsonObj->status = false;
        $JsonObj->query = $query;
        echo json_encode($JsonObj);
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        $JsonObj->clientData->id = $row['id'];
        $JsonObj->clientData->name = $row['name'];
        $JsonObj->clientData->phone = $row['phone'];
        $JsonObj->clientData->email = $row['email'];
        $JsonObj->clientData->type = $row['type'];
        $JsonObj->clientData->product_id = $row['product_id'];
    }
    $JsonObj->msg = 'Product Data loaded!';
    echo json_encode($JsonObj);
    exit;
}

################################ prouct stuff ################################
// add a product
if ($action == 'addProuct') {
    #print_r($_REQUEST);

    header('Content-type: application/json');
    $JsonObj = new stdClass();
    $JsonObj->msg = '';
    $JsonObj->status = true;

    if (!isset($_REQUEST['addProduct'])) {
        $JsonObj->msg = "Error! No  data!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }

    include "./DB.php";

    $FormDataArr = array();
    foreach ($_REQUEST['addProduct'] as $key => $oneDataValue) {
        $FormDataArr[$key] = mysqli_real_escape_string($conn, trim($oneDataValue));
    }
    if (!$FormDataArr['name']) {
        $JsonObj->msg = "at least enter a name!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }
    #print_r($FormDataArr);

    $query = "INSERT INTO `products` (`name`,`price`,`quantity`) VALUES('" . $FormDataArr['name'] . "','" . $FormDataArr['price'] . "','" . $FormDataArr['quantity'] . "')";
    #echo $query;
    if (!$conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
        $JsonObj->msg = 'An error....';
        $JsonObj->status = false;
        $JsonObj->query = $query;
        echo json_encode($JsonObj);
        exit;
    }


    $JsonObj->msg = 'Product added!';
    echo json_encode($JsonObj);
    exit;
}

if ($action == 'updateProd') {
    #print_r($_REQUEST);

    header('Content-type: application/json');
    $JsonObj = new stdClass();
    $JsonObj->msg = '';
    $JsonObj->status = true;

    if (!isset($_REQUEST['Form'])) {
        $JsonObj->msg = "Error! No  data!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }

    include "./DB.php";

    $FormDataArr = array();
    foreach ($_REQUEST['Form'] as $key => $oneDataValue) {
        $FormDataArr[$key] = mysqli_real_escape_string($conn, trim($oneDataValue));
    }
    if (!$FormDataArr['name']) {
        $JsonObj->msg = "at least enter a name!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }
    #print_r($FormDataArr);

    $query = " UPDATE `products` SET
        `name` ='" .  $FormDataArr['name'] . "',
        `price` ='" .  $FormDataArr['price'] . "',
        `quantity` ='" .  $FormDataArr['quantity'] . "'
        WHERE id=" . $FormDataArr['id'] . "
    ";
    #echo $query;
    if (!$conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
        $JsonObj->msg = 'An error....';
        $JsonObj->status = false;
        $JsonObj->query = $query;
        echo json_encode($JsonObj);
        exit;
    }


    $JsonObj->msg = 'Product updated!';
    echo json_encode($JsonObj);
    exit;
}

if ($action == 'getProdData') {
    #print_r($_REQUEST);

    header('Content-type: application/json');
    $JsonObj = new stdClass();
    $JsonObj->msg = '';
    $JsonObj->status = true;

    if (!isset($_REQUEST['update_id'])) {
        $JsonObj->msg = "Error! No  data!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }
    $update_id = $_REQUEST['update_id'];
    $JsonObj->prodData = new stdClass();

    include "./DB.php";

    $query = "SELECT `id`,`name`,`price`,`quantity` FROM `products` WHERE name!='' AND id=$update_id ";

    if (!$result = $conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
        $JsonObj->msg = 'An error....';
        $JsonObj->status = false;
        $JsonObj->query = $query;
        echo json_encode($JsonObj);
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        $JsonObj->prodData->id = $row['id'];
        $JsonObj->prodData->name = $row['name'];
        $JsonObj->prodData->price = $row['price'];
        $JsonObj->prodData->quantity = $row['quantity'];
    }
    $JsonObj->msg = 'Product Data loaded!';
    echo json_encode($JsonObj);
    exit;
}

################################ data stuff ################################
// load select for client products

if ($action == 'selectAddClientProduct') {

    include "./DB.php";

    $str = '    <select name="Form[product_id]">
                <option value="0" disabled selected>Choose your option</option>';

    $query = "SELECT id,name FROM `products` WHERE name!='' ";

    if (!$result = $conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
    }

    while ($row = $result->fetch_assoc()) {
        $str .= "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
    }

    $str .= "   </select>
                <label>Chose a product</label>
                <script>
                $('select').formSelect();
                </script>";

    echo $str;
}
if ($action == 'loadProducts') {

    include "./DB.php";

    $str = '    <select id="currentProds" >
                <option value="0" disabled selected>Choose your option</option>';

    $query = "SELECT id,name FROM `products` WHERE name!='' ";

    if (!$result = $conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
    }

    while ($row = $result->fetch_assoc()) {
        $str .= "<option  value='" . $row['id'] . "'>" . $row['name'] . "</option>";
    }

    $str .= "   </select>
                <label>Chose a product</label>
                <script>
                $('select').formSelect();
                $('#currentProds').change(function () {
                    //console.log($(this).val());
                    updateBy($(this).val(),'product');
                });
                </script>";

    echo $str;
}

if ($action == 'loadClients') {

    include "./DB.php";

    $str = '    <select id="currentClients" >
                <option value="0" disabled selected>Choose your option</option>';

    $query = "SELECT id,name FROM `clients` WHERE name!='' ";

    if (!$result = $conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
    }

    while ($row = $result->fetch_assoc()) {
        $str .= "<option  value='" . $row['id'] . "'>" . $row['name'] . "</option>";
    }

    $str .= "   </select>
                <label>Chose a product</label>
                <script>
                $('select').formSelect();
                $('#currentClients').change(function () {
                    //console.log($(this).val());
                    updateBy($(this).val(),'client');
                });
                </script>";

    echo $str;
}

if ($action == 'ClientsTable') {

    include "./DB.php";

    $str = '        <br />
                    <h2>Client Data</h2>
                        <table class="striped">
                            <thead>
                            <tr>
                                <th class="sort-cli" data-sort="id" data-table="clients" >ID</th>
                                <th class="sort-cli" data-sort="name" data-table="clients" >Name</th>
                                <th class="sort-cli" data-sort="phone" data-table="clients" >Phone</th>
                                <th class="sort-cli" data-sort="email" data-table="clients" >Email</th>
                                <th class="sort-cli" data-sort="type" data-table="clients" >Type</th>
                                <th class="sort-cli" data-sort="paid" data-table="clients">Paid</th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>';

    $sortBy = '';
    if (isset($_REQUEST['sort'])) {
        $sortBy = "ORDER BY " . trim($_REQUEST['sort']) . " DESC";
    }

    $query = "SELECT * FROM `clients` WHERE name!='' $sortBy ";

    if (!$result = $conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
    }

    while ($row = $result->fetch_assoc()) {
        $str .= "
        <tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>" . $row['phone'] . "</td>
            <td>" . $row['email'] . "</td>
            <td>" . $row['type'] . "</td>
            <td>$ " . $row['paid'] . "</td>
            <td><a class='delete' data-delete='" . $row['id'] . "' data-table='clients'><i class='material-icons'>delete</i></a></td>
        </tr>
        ";
    }

    $str .= "   </tbody>
            </table>
            <script>
            
     $('.sort-cli').click(function(){
        console.log($(this).attr('data-table'));
        $('.sort-cli').removeClass('active-sort');
        if($(this).attr('data-table')=='clients'){
            $(this).addClass('active-sort')
           $('#ClientsTable').load('asset/php/processing.php?action=ClientsTable&sort='+$(this).attr('data-sort'));   
       }
    });

    
    $('.delete').click(function () {
        if (confirm('Delete the Item?')) {
            let action = 'Delete';
            let table = 'products';
            if ($(this).attr('data-table') == 'clients') {
                table = 'clients';
            }
            deleteAJAX($(this).attr('data-delete'), action, table);
            $(this).closest('tr').remove();
        }
    });
            </script>
            ";

    echo $str;
}


if ($action == 'ProductsTable') {

    include "./DB.php";

    $str = '        <br />
                    <h2>Product Data</h2>
                        <table class="striped">
                            <thead>
                            <tr>
                                <th class="sort-prod" data-sort="id" data-table="products" >ID</th>
                                <th class="sort-prod" data-sort="name" data-table="products" >Name</th>
                                <th class="sort-prod" data-sort="price" data-table="products" >Price</th>
                                <th class="sort-prod" data-sort="quantity" data-table="products" >Quantity</th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>';

    $sortBy = '';
    if (isset($_REQUEST['sort'])) {
        $sortBy = "ORDER BY " . trim($_REQUEST['sort']) . " DESC";
    }

    $query = "SELECT * FROM `products` WHERE name!='' $sortBy ";

    if (!$result = $conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
    }

    while ($row = $result->fetch_assoc()) {
        $str .= "
        <tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>$ " . $row['price'] . "</td>
            <td>" . $row['quantity'] . "</td>
            <td><a class='delete' data-delete='" . $row['id'] . "' data-table='products'><i class='material-icons'>delete</i></a></td>
        </tr>
        ";
    }

    $str .= "   </tbody>
            </table>
            <script>
            
     $('.sort-prod').click(function(){
        console.log($(this).attr('data-table'));
        $('.sort-prod').removeClass('active-sort');
        if($(this).attr('data-table')=='products'){
            $(this).addClass('active-sort')
           $('#ProductsTable').load('asset/php/processing.php?action=ProductsTable&sort='+$(this).attr('data-sort'));   
       }
    });
    
    $('.delete').click(function () {
        if (confirm('Delete the Item?')) {
            let action = 'Delete';
            let table = 'products';
            if ($(this).attr('data-table') == 'clients') {
                table = 'clients';
            }
            deleteAJAX($(this).attr('data-delete'), action, table);
            $(this).closest('tr').remove();
        }
    });
            </script>
            ";

    echo $str;
}

if ($action == 'searchByPrice') {

    include "./DB.php";

    $sortBy = '';
    if (!isset($_REQUEST['price']) || empty($_REQUEST['price'])) {
        echo "";
        exit;
    }
    $price = trim($_REQUEST['price']);


    $str = '        <br />
                    <h3>Client Data By price >="' . $price . '"</h3>
                        <table class="striped">
                            <thead>
                            <tr>
                                <th >ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Paid</th>
                               
                            </tr>
                            </thead>
                            <tbody>';



    $query = "SELECT * FROM `clients` WHERE name!='' AND paid>=$price ";

    if (!$result = $conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
    }

    while ($row = $result->fetch_assoc()) {
        $str .= "
        <tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>" . $row['phone'] . "</td>
            <td>" . $row['email'] . "</td>
            <td>" . $row['type'] . "</td>
            <td>$ " . $row['paid'] . "</td>
           
        </tr>
        ";
    }

    $str .= "   </tbody>
            </table>";

    echo $str;
}

if ($action == 'Delete') {
    #print_r($_REQUEST);
    header('Content-type: application/json');
    $JsonObj = new stdClass();
    $JsonObj->msg = '';
    $JsonObj->status = true;

    if (!isset($_REQUEST['delete_id'])) {
        $JsonObj->msg = "Error! No  id to delete!";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }
    $delete_id = trim($_REQUEST['delete_id']);

    if (!isset($_REQUEST['table'])) {
        $JsonObj->msg = "Error! No  table";
        $JsonObj->status = false;
        echo json_encode($JsonObj);
        exit;
    }
    $table = trim($_REQUEST['table']);

    include "./DB.php";
    $sql_delete = "DELETE FROM `$table` WHERE id=$delete_id";
    #print_r($FormDataArr);

    if (!$conn->query($sql_delete)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $sql_delete);
        }
        $JsonObj->msg = 'An error....';
        $JsonObj->status = false;
        $JsonObj->query = $sql_delete;
        echo json_encode($JsonObj);
        exit;
    }


    $JsonObj->msg = 'The Item was deleted!';
    echo json_encode($JsonObj);
    exit;
}

/* not in use
if ($action == 'loadClientAddProdOption') {

    header('Content-type: application/json');
    $JsonObj = new stdClass();
    $JsonObj->msg = '';
    $JsonObj->status = true;

    include "./DB.php";

    $query = "SELECT id,name FROM `products` WHERE name!='' ";

    if (!$result = $conn->query($query)) {
        if (IN_DEMO_MODE == 1) {
            die("Can't Query: " . $query);
        }
        $JsonObj->msg = 'An error....';
        $JsonObj->status = false;
        $JsonObj->query = $query;
        echo json_encode($JsonObj);
        exit;
    }

    $tempArr = array();
    while ($row = $result->fetch_assoc()) {
        $arr =  array(
            'id' => $row['id'],
            'name   ' => $row['name']
        );
        $tempArr[] = $arr;
    }

    $JsonObj->options = $tempArr;
    $JsonObj->msg = 'Product options loaded!';
    echo json_encode($JsonObj);
    exit;
}
*/


################################ Functions ################################

function updateClientPaiment($clientId, $clientType, $product_id)
{
    global $conn;

    $tempPrice = 0;
    // sql 
    $select_product = "SELECT price FROM `products` WHERE id=$product_id ";

    switch ($clientType) {
        case 1:
            // paid all
            if (!$result = $conn->query($select_product)) {
                if (IN_DEMO_MODE == 1) {
                    die("Can't Query: " . $select_product);
                }
            }
            while ($row = $result->fetch_assoc()) {
                $tempPrice = $row['price'];
            }
            $sql_update = "UPDATE `clients` SET paid=$tempPrice WHERE id=$clientId ";
            // update client
            return $conn->query($sql_update);

            break;
        case 2:
            // paid 15%
            if (!$result = $conn->query($select_product)) {
                if (IN_DEMO_MODE == 1) {
                    die("Can't Query: " . $select_product);
                }
            }
            while ($row = $result->fetch_assoc()) {
                $tempPrice = $row['price'] * 0.15;
            }

            $sql_update = "UPDATE `clients` SET paid=$tempPrice WHERE id=$clientId ";
            // update client
            return $conn->query($sql_update);
            break;
        case 3:
            // not paid
            $sql_update = "UPDATE `clients` SET paid=$tempPrice WHERE id=$clientId ";
            return $conn->query($sql_update);
            break;
        default:
            // not paid
            $sql_update = "UPDATE `clients` SET paid=$tempPrice WHERE id=$clientId ";
            return $conn->query($sql_update);
    }

    return false;
}
