<?php
/*
Plugin Name: Amazon Product Price
Plugin URI: http://wordpress.org/extend/plugins/amazon-product-price/
Description: Generate the short code to enter on the post and get the amazon product price by amazon "Asin" Code.
Version: 1.0
Author: Alok Tiwari
Author URI: http://wptricksbook.com/
*/
global $wpdb;
$tblname = $wpdb->prefix . "amazon_setting";
$def_det = $wpdb->get_results('SELECT * FROM '.$tblname.' WHERE id = 1', OBJECT );

define('public_key', $def_det[0]->amazon_access_key);
define('private_key', $def_det[0]->amazon_secret_access_key);
define('associate_tag', $def_det[0]->amazon_associate_tag);

function amproductprice_install()
{
    global $wpdb;
    global $am_table_name;
    
    $am_table_name = $wpdb->prefix . "amazon_setting";
    $am_db_version = "1.0";
    
    
   if($wpdb->get_var("show tables like '$am_table_name'") != $am_db_version) {

      $sql = "CREATE TABLE " . $am_table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  amazon_access_key VARCHAR(255) NOT NULL,
	  amazon_secret_access_key VARCHAR(255) NOT NULL,
	  amazon_associate_tag VARCHAR(255) NOT NULL,
          amazon_region VARCHAR(255) NOT NULL,
	  UNIQUE KEY id (id)
	);";
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      dbDelta($sql);

      add_option("am_db_version", $am_db_version);

   }
}

register_activation_hook(__FILE__,'amproductprice_install');

add_action('admin_menu', 'add_settings_menu');

function add_settings_menu(){
     add_options_page('Amazon Settings', 'Amazon Settings', 'manage_options', 'amazon-setting', 'amazon_setting');
}

function am_setting_url(){
	return admin_url("options-general.php?page=amazon-setting");
}

function amazon_setting()
{
    global $wpdb;
    $tblname = $wpdb->prefix . "amazon_setting";
    if(isset($_POST['submit']))
    {
        $amazon_access_key = trim($_POST['amazon_access_key']);
        $amazon_secret_access_key = trim($_POST['amazon_secret_access_key']);
        $amazon_associate_tag = trim($_POST['amazon_associate_tag']);
        
        if(empty($_POST['up_id']))
        {
            $wpdb->insert($tblname,array('amazon_access_key' => $amazon_access_key, 'amazon_secret_access_key' => $amazon_secret_access_key, 'amazon_associate_tag' => $amazon_associate_tag),array('%s','%s','%s'));
        }
        else
        {
            $wpdb->update($tblname,array('amazon_access_key' => $amazon_access_key, 'amazon_secret_access_key' => $amazon_secret_access_key, 'amazon_associate_tag' => $amazon_associate_tag),array('id'=>$_POST['up_id']),array('%s','%s','%s'),array('%d'));
        }
        
        
    }
    
    $update_det = $wpdb->get_results('SELECT * FROM '.$tblname.' WHERE id = 1', OBJECT );
    
?>
<div class="wrap">
    <form method="post" action="<?php echo am_setting_url();?>">
        <div style="margin-right: 320px;">
            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <td colspan="2"><h3>
                        <?php _e('Basic Settings', 'amazon-product-price'); ?>
                      </h3></td>
                </tr>
                <tr>
                    <th width="30%" valign="top" style="padding-top: 10px;"> <label for="<?php //echo key_ga_status ?>">
                        <?php _e('Amazon Access Key', 'amazon-product-price'); ?>
                        :</label>
                    </th>
                    <td>
                        <input type="text" name="amazon_access_key" class="regular-text" value="<?php if(!empty($update_det[0]->amazon_access_key)) { echo $update_det[0]->amazon_access_key; } ?>" />
                    </td>
                </tr>
                <tr>
                    <th width="30%" valign="top" style="padding-top: 10px;"> <label for="<?php //echo key_ga_status ?>">
                        <?php _e('Amazon Secret Access Key', 'amazon-product-price'); ?>
                        :</label>
                    </th>
                    <td>
                        <input type="text" name="amazon_secret_access_key" value="<?php if(!empty($update_det[0]->amazon_secret_access_key)) { echo $update_det[0]->amazon_secret_access_key; } ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th width="30%" valign="top" style="padding-top: 10px;"> <label for="<?php //echo key_ga_status ?>">
                        <?php _e('Amazon Associate Tag', 'amazon-product-price'); ?>
                        :</label>
                    </th>
                    <td>
                        <input type="text" name="amazon_associate_tag" value="<?php if(!empty($update_det[0]->amazon_associate_tag)) { echo $update_det[0]->amazon_associate_tag; } ?>" class="regular-text" />
                    </td>
                </tr>
                <?php 
                    if(!empty($update_det[0]->id) && ($update_det[0]->id==1))
                    {
                ?>
                    <input type="hidden" name="up_id" value="<?php echo $update_det[0]->id; ?>" /> 
                <?php
                    }
                ?>
            </table>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
            </p>
            
            <p>
            <h4> Enter the shortcode in post or pages " [price asin='B00KQ7SBTE'] "
            <br/>
            asin is found in amazon product detail page URL highlighted text is asin e.g => http://www.amazon.com/Gopro-Water-Resistant-Case-Camkix/dp/<span style='background:red;'>B00KQ7SBTE</span>/ie=UTF8?m=A3KD0OO9H1T8D3&keywords=gopro+case
            </h4>
            </p>
            
            <div>
                If you enjoy plugin purchase a beer => <a href="http://wptricksbook.com/donate" title="Purchase A Beer"><img src="<?php echo  plugins_url('/includes/', __FILE__).'btn_donate_SM.gif'; ?>" /></a>
            </div>
            
        </div>
    </form>
</div>
<?php
}

add_shortcode( 'price', 'price_handler' );

include("includes/amazon_api_class.php");

function price_handler($atts) {
    
    $obj = new AmazonProductAPI();
     try
    {
        $result = $obj->getItemByAsin($atts['asin']);
    }
    catch(Exception $e)
    {
        echo $e->getMessage();
    }
//    echo '<pre>';
//    print_r($result->Items->Item);
    $price = $result->Items->Item->OfferSummary->LowestNewPrice->Amount;
    $MSRP = $result->Items->Item->ItemAttributes->ListPrice->Amount;
    
    if(!empty($MSRP))
    {
        $Savings =  $MSRP - $price; 
        $MSRP2 = number_format($MSRP /100,2);
    }
    $price = number_format($price /100,2);
    echo '<p>';
    if(!empty($price) && !empty($MSRP))
    {
         echo 'Retail Price: '.$MSRP2.'<br>';
         echo 'Lowest Price: '.$price.'<br>';
         $MSRP2 = number_format($MSRP /100,2);
         $percent = ($Savings/$MSRP2);
         echo 'Saveing Percent: '.number_format($percent,2).'%<br>';
    }
    elseif(!empty($price) && empty($MSRP))
    {
        echo 'Price: '.$price.'<br>';
    }
    echo '</p>';
    
}

?>