<?php

function t2m_menu_page(){

if ( !current_user_can( 'manage_options' ) ) {
wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
?>
<h1 style="padding-top: 10px;">T2MChat Config</h1>
<?php
// echo ABSPATH;

global $wpdb;
$table_name = $wpdb->prefix . "t2mchat";

$charset_collate_is = $wpdb->get_charset_collate();
$check_table = "SHOW TABLES LIKE $table_name";
//check for table
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'")!== $table_name) {
//if there is no table
echo '<br>
<div class="alert alert-danger" role="alert">
    There is no table "t2m____" in the database.
</div>';
echo '<div class="alert alert-danger" role="alert">
    To create the table reactivate the plugin "T2MChat"
</div>';
}
//if there is a table
else{
$results = $wpdb->get_results( "SELECT * FROM $table_name");
if (empty($results))
{?>
    <div class="container">
        <div class="alert alert-warning text-center">Don't have a <b>Client Id</b>, <b>Secret</b> and <b>Service Name</b>? No worries, sign up for one <a target="_blank" href="https://techstacksolutions.com/product-search.html#/">here</a></div>
    </div>
<?php
}

//check results
//if results appear
if(!empty($results)){
?>
<!-- Update form  -->
<br>
<form class="" action="admin.php?page=t2m-config-menu" method="post">
<div class="form-group">
    <label for="id"> Client ID </label>
    <input type="text" class="form-control" name="clientID" id="cID" placeholder="Client ID" required>
</div>
<div class="form-group">
    <label for="cSecret">Client Secret</label>
    <input type="text" class="form-control" name="clientSecret" id="cSecret" placeholder="Client Secret" required>
</div>
<div class="form-group">
    <label for="Service">Client Service</label>
    <input type="text" class="form-control" name="clientService" id="cService" placeholder="Client Service"
        required>
</div>
<input type="submit" name="t2m_submit" class="btn btn-primary" value="Submit">
<?php wp_nonce_field( 't2m_submit', 't2m_submit_nonce' ); ?>
</form>
<?php

    if(isset( $_POST['t2m_submit'] ) 
    || wp_verify_nonce( $_POST['t2m_submit_nonce'], 't2m_submit' ) )
    {
        $data = array(
            "ClientID" => sanitize_text_field($_POST['clientID']),
            "ClientSecret" => sanitize_text_field($_POST['clientSecret']),
            "ClientService" => sanitize_text_field($_POST['clientService'])
            );

        $success = $wpdb->replace($table_name, $data);
       
        if($success){
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Successfully Updated!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
           </button>
          </div>';
        }
        else {
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            Not Updated!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>';
        }
    }
}
//if no results from table
else{
    // insert the data
    ?>
<!-- Insert form -->
<form class=""  method="post">
<div class="form-group">
    <label for="id">Client ID</label>
    <input type="text" class="form-control" name="clientID" id="cID" placeholder="Client ID" required>
</div>
<div class="form-group">
    <label for="cSecret">Client Secret</label>
    <input type="text" class="form-control" name="clientSecret" id="cSecret" placeholder="Client Service" required>
</div>
<div class="form-group">
    <label for="Service">Client Service</label>
    <input type="text" class="form-control" name="clientService" id="cService" placeholder="Client Service"
        required>
</div>
<input type="submit" name="t2m_submit" class="btn btn-primary" value="Submit">
<?php wp_nonce_field( 't2m_submit', 't2m_submit_nonce' ); ?>
</form>

<?php

    if(isset( $_POST['t2m_submit_nonce'] ) 
    || wp_verify_nonce( $_POST['t2m_submit_nonce'], 't2m_submit' ) )
    {
        $data = array(
            "ClientID" => sanitize_text_field($_POST['clientID']),
            "ClientSecret" => sanitize_text_field($_POST['clientSecret']),
            "ClientService" => sanitize_text_field($_POST['clientService'])
            );

    $success = $wpdb->replace($table_name, $data);
    if($success){
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        Successfully Inserted!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>';
    }
    else {
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        Not Inserted!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>';
        }
    }
}
$results = $wpdb->get_results( "SELECT * FROM $table_name");
//check results
//if results appear show in table
if(!empty($results)){
    foreach($results as $row){   
        $clientID = $row->ClientID;
        $clientService = $row->ClientService;
        $clientSecret = $row->ClientSecret;
            ?>
<br>
<table class="table table-bordered">
<thead>
    <tr>
        <th scope="col">Client ID</th>
        <th scope="col">Client Secret</th>
        <th scope="col">Client Service</th>
        <th scope="col">Action</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td>
            <?php echo $clientID; ?>
        </td>
        <td>
            <?php echo $clientSecret; ?>
        </td>
        <td>
            <?php echo $clientService; ?>
        </td>
        <td>
            <button class="btn btn-info" onclick="t2mEditValues()">Edit</button>
        </td>
    </tr>
</tbody>
</table>
<script>
function t2mEditValues() {
    document.getElementById("cID").value = "<?php echo $clientID; ?>";
    document.getElementById("cService").value = "<?php echo $clientService ;?>";
    document.getElementById("cSecret").value = "<?php echo $clientSecret; ?>";
}
</script>
<?php
        }
} 
}
}
?>