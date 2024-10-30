<?php
/**
* Plugin Name: Bronto Newsletter
* Plugin URI: https://client.scottishbordersdesign.co.uk/scripts/20/Wordpress+Bronto+Newsletter+Signup.html
* Description: Get new bronto subscribers from your wordpress website.
* Version: 2.0.5
* Author: Scottish Borders Design
* Author URI: https://scottishbordersdesign.co.uk/
* License: GPL2
*/
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );
add_shortcode( 'bronto_signup', 'insertSignupForm');
add_action('admin_post_add_subscriber', 'addSubscriber');
add_action('admin_menu', 'bronto_menu');
add_action('admin_init', 'bronto_settings');
add_action( 'wp_ajax_add_subscriber', 'addSubscriber' );
add_action( 'wp_ajax_nopriv_add_subscriber', 'addSubscriber' );
function bronto_menu() {
	$url = plugins_url();
	add_menu_page('Bronto Settings', 'Bronto Settings', 'administrator', 'bronto-settings', 'bronto_settings_page', plugins_url( 'assets/25x25-icon.png', __FILE__ ));
}
function bronto_settings() {
	register_setting( 'bronto-settings-group', 'api_key' );
	register_setting( 'bronto-settings-group', 'list_id' );
	register_setting( 'bronto-settings-group', 'customize_html' );
}
function testconnection($token){
	include( plugin_dir_path( __FILE__ ) . 'bronto.inc.php');
	$bronto = new brontoEmailSender;
	if ($bronto->testConnection($token)) {
		echo "Connection Successful!";
	} else {
		echo "Connection Failed!";
	}
}
function bronto_settings_page() {
	if (isset($_POST['test_con'])) {
		testconnection(get_option('api_key'));
	}
	?>
	<style>
	.form-table th{
		vertical-align: middle;
	}
	.form-table tr{
		border-bottom: 1px solid #CCC;
	}
	</style>
	<div class="wrap">
		<img style="display: inline;float: left;margin-top: -30px;" src="<?php echo plugins_url( 'assets/100x100-icon.png', __FILE__ );?>" alt=""><h2> Bronto Settings</h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'bronto-settings-group' ); ?>
			<?php do_settings_sections( 'bronto-settings-group' ); ?>
			<h2>Useage</h2>
			<p>To use the Bronto signup form, simply use the below shortcode anywhere!</p>
			<code style="display:block">[bronto_signup]</code>
			<br>
			<p>If you are using Visual Composer, go to Visual Composer Settings &gt; Shortcode Mapper and add the above shortcode as a mapping to use it.</p>
			<table class="form-table">
				<tr valign="middle">
					<th scope="row">API Key</th>
					<td>
						<input type="text" name="api_key" style="width:100%;" value="<?php echo esc_attr( get_option('api_key') ); ?>" />
					</td>
					<td width="350px">
						<p>You can get your Bronto API key from Bronto Dashboard &gt; Home (Menu Item) &gt; Settings &gt; Data Exchange.</p>
						<p>If a key is not visible you may need to create one, please see their documentation on how to do this.</p>
					</td>
				</tr>
				<tr valign="middle">
					<th scope="row">List ID</th>
					<td>
						<input type="text" name="list_id" style="width:100%;" value="<?php echo esc_attr( get_option('list_id') ); ?>" />
					</td>
					<td width="350px">
						<p>To get your list ID go to Bronto Dashboard &gt; Tables (Menu Item) &gt; Click on the list you want people added to &gt; Scroll to the bottom of the page and in the bottom right corner you will see <b>List API ID</b> copy the entire string after it it will look simmilar to this <i>e8s62fe6-df54-4df8-asd2-5e46cf0c7a5</i></p>
						<p>If no lists are visible - you may need to create one.</p>
						<p><strong>For multiple lists seperate them by a comma ',' (NO SPACES)</strong></p>
					</td>
				</tr>
				<tr valign="middle">
					<th scope="row">Form HTML</th>
					<td>
						<textarea name="customize_html" id="customize_html" style="width:100%;" id="" cols="30" rows="15"><?php $customHTML = get_option('customize_html');if (empty( $customHTML )) { ?><style>
  .bronto_signup{
  	width:20%;
  }
  .bronto_signup_label{
  	display:table;
  }
  .bronto_signup_input{
  	background: #f7f7f7;
  	background-image: -webkit-linear-gradient(rgba(255, 255, 255, 0), rgba(255, 255, 255, 0));
  	border: 1px solid #d1d1d1;
  	border-radius: 2px;
  	color: #686868;
  	padding: 0.625em 0.4375em;
  	display:inline;
  	width:30%;
  }
  .bronto_signup_submit{
  	background: #1a1a1a;
  	border: 0;
  	border-radius: 2px;
  	color: #fff;
  	font-family: Montserrat, 'Helvetica Neue', sans-serif;
  	font-weight: 700;
  	letter-spacing: 0.046875em;
  	line-height: 1;
  	padding: 0.84375em 0.875em 0.78125em;
  	text-transform: uppercase;
  	display: inline;
  	height: 42.5px;
  }
</style>
<span class='bronto_signup'>
  <span class='bronto_signup_label'>Newsletter Signup</span>
  <input type='email' id='bronto-newsletter_%formID%' class='bronto_signup_input' placeholder='Your Email ...'' name='email'>
  <button type='submit' class='bronto_signup_submit'>Signup</button>
</span>
								<?php } else {
									echo esc_attr( get_option('customize_html') );
								}
							?>
						</textarea>
						</td>
						<td width="350px">
							<p>You can change the HTML that the form uses (please ensure all IDs are kept or the signup wont work.)</p>
							<p>The default form should be loaded by default with all the css.</p>
							<p>If your custom form stops working, you can reset it to the original form by clicking <a href='#' onClick='jQuery("#customize_html").val($html);return false;'>here</a></p>
							<h3>Important Classes/IDs</h3>
							<ul>
								<li>Form Input (ID - <strong>Important</strong>): bronto-newsletter_%formID%</li>
								<li>Form Input (class): bronto_signup_input</li>
								<li>Form Label (class): bronto_signup_label</li>
								<li>Form Submit (class): bronto_signup_submit</li>
							</ul>
						</td>
					</tr>
				<tr valign="middle">
					<th scope="row">Test Connection</th>
					<td>
						<?php
						if (!$_GET['settings-updated'] == 'true') {
							$status = "disabled=disabled";
						} else {
							$status ='';
						}
						?>
						<input type="button" name="test_con" id="test_con" style="width:100%;" value="Test Connection" onClick="testConnection();return false;" <?php echo $status;?> />
						<script>
						function testConnection(){
							jQuery('#test_con').val("Just a moment ...");
					        jQuery.ajax({
					            url: 'admin.php?page=bronto-settings',
					            type: "POST",
					            data: {test_con:'test_con'},
					            success: function(data, textStatus, jqXHR) {
					                var n = data.search("Uncaught SoapFault");
					                if (n = 0) {
					                	alert("Error with API Key :(");
					                		Query('#test_con').val("Test Connection");
					                } else {
					                	jQuery('#test_con').val("Awesome :)");
					                	alert("A Connection was made (Awesome!)");
					                }
					            },
					            error: function(jqXHR, textStatus, errorThrown) {
					                alert(errorThrown);
					                Query('#test_con').val("Test Connection");
					            }
					        });
						}
						</script>
					</td>
					<td width="350px">
						<p>After you have saved this page - click the test connection button to ensure that your API key is valid</p>
					</td>
				</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<br />
		<center>
			<form name="_xclick" action="https://www.paypal.com/uk/cgi-bin/webscr" target=_blank method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="gazajohnstone@outlook.com">
				<input type="hidden" name="item_name" value="Wordpress Plugin Donation">
				<input type="hidden" name="currency_code" value="GBP">
				<input type="hidden" name="amount" value="">
				<input type="image" src="http://www.paypal.com/en_GB/i/btn/x-click-butcc-donate.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
			</form>
			<small><a href="https://scottishbordersdesign.co.uk/" target=_blank>&copy; Scottish Borders Design 2017</a></small>
		</center>
<script>
$html = '<style> \r \
  .bronto_signup{ \r \
  	width:20%; \r \
  } \r \
  .bronto_signup_label{ \r \
  	display:table; \r \
  } \r \
  .bronto_signup_input{ \r \
  	background: #f7f7f7; \r \
  	background-image: -webkit-linear-gradient(rgba(255, 255, 255, 0), rgba(255, 255, 255, 0)); \r \
  	border: 1px solid #d1d1d1; \r \
  	border-radius: 2px; \r \
  	color: #686868; \r \
  	padding: 0.625em 0.4375em; \r \
  	display:inline; \r \
  	width:30%; \r \
  } \r \
  .bronto_signup_submit{ \r \
  	background: #1a1a1a; \r \
  	border: 0; \r \
  	border-radius: 2px; \r \
  	color: #fff; \r \
  	font-family: Montserrat, "Helvetica Neue", sans-serif; \r \
  	font-weight: 700; \r \
  	letter-spacing: 0.046875em; \r \
  	line-height: 1; \r \
  	padding: 0.84375em 0.875em 0.78125em; \r \
  	text-transform: uppercase; \r \
  	display: inline; \r \
  	height: 42.5px; \r \
  } \r \
</style> \r \
<span class="bronto_signup">\r \
  <span class="bronto_signup_label">Newsletter Signup</span>\r \
  <input type="email" id="bronto-newsletter_%formID%" class="bronto_signup_input" placeholder="Your Email ..." name="email">\r \
  <button type="submit" class="bronto_signup_submit">Signup</button>\r \
</span>';
</script>
		<?php
}
function addSubscriber(){
	status_header(200);
	if ($_POST['action'] == 'add_subscriber') {
		$lists = explode(',',get_option('list_id'));
		$lists = array_values($lists);
		array_filter(array_map('trim', $lists));
		include( plugin_dir_path( __FILE__ ) . 'bronto.inc.php');
		$bronto = new brontoEmailSender;
		$email = $_POST['email'];
		$bronto->checkEmail(htmlentities($email), htmlentities($email));
		$bronto->addContact(htmlentities($email), $lists, // lists
		            get_option('api_key')
		            );
	} else{
		echo "Error!";
	}
	exit;
}
function insertSignupForm(){
	$options['api_key'] = get_option('api_key');
	$options['list_id'] = get_option('list_id');
	$form_ID = md5(uniqid(rand(), true));
	$error = 'n';
	$output ="";
	if (empty($options['api_key']) && current_user_can('level_10') ) {
		echo "<div class='error'>Please check your API key isnt empty</div>";
		$error = 'y';
	}
	if (empty($options['list_id']) && current_user_can('level_10')) {
		echo "<div class='error'>Please check your List ID isnt empty</div>";
		$error = 'y';
	}
	if ($error == 'n') {
		$blogURL = get_bloginfo('wpurl');
		$customhtml2 = get_option('customize_html');
		$output .= "<style>
			.info, .success, .warning, .error, .validation {border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 50px;background-repeat: no-repeat;background-position: 10px center;}
			.info {color: #00529B;background-color: #BDE5F8;}
			.success {color: #4F8A10;background-color: #DFF2BF;}
			.warning {color: #9F6000;background-color: #FEEFB3;}
			.error {color: #D8000C;background-color: #FFBABA;}
			</style>";
		$output .="<span class=''>
		    <form onSubmit='return false;' action='".admin_url('admin-ajax.php')."' method='POST' id='bronto-newsletter-form_{$form_ID}' novalidate=''>
		    	<input type='hidden' name='action' value='add_subscriber'>";
	   	if (empty( $customhtml2 )) {
$output .="<style>
  .bronto_signup{
  	width:20%;
  }
  .bronto_signup_label{
  	display:table;
  }
  .bronto_signup_input{
  	background: #f7f7f7;
  	background-image: -webkit-linear-gradient(rgba(255, 255, 255, 0), rgba(255, 255, 255, 0));
  	border: 1px solid #d1d1d1;
  	border-radius: 2px;
  	color: #686868;
  	padding: 0.625em 0.4375em;
  	display:inline;
  	width:30%;
  }
  .bronto_signup_submit{
  	background: #1a1a1a;
  	border: 0;
  	border-radius: 2px;
  	color: #fff;
  	font-family: Montserrat, 'Helvetica Neue', sans-serif;
  	font-weight: 700;
  	letter-spacing: 0.046875em;
  	line-height: 1;
  	padding: 0.84375em 0.875em 0.78125em;
  	text-transform: uppercase;
  	display: inline;
  	height: 42.5px;
  }
</style>
<span class='bronto_signup'>
  <span class='bronto_signup_label'>Newsletter Signup</span>
  <input type='email' id='bronto-newsletter_{$form_ID}' class='bronto_signup_input' placeholder='Your Email ...'' name='email'>
  <button type='submit' class='bronto_signup_submit'>Signup</button>
</span>";
	} else {
		$output .= str_replace("%formID%", $form_ID, $customhtml2);
	}
	$output .= "</form>
			<div id='bronto-validate-email_{$form_ID}' style='display:none;'>&nbsp;</div>
		</div>";
	$output .="	<script>
	function validateEmail_{$form_ID}(email) {
	    var re = /^(([^<>()[\]\\.,;:\s@\']+(\.[^<>()[\]\\.,;:\s@\']+)*)|(\'.+\'))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(email);
	}
	jQuery('#bronto-newsletter-form_{$form_ID}').submit(function(e) {
	    jQuery('#bronto-validate-email_{$form_ID}').slideUp();
	    if (!validateEmail_{$form_ID}(jQuery('#bronto-newsletter_{$form_ID}').val())) {
	        jQuery('#bronto-validate-email_{$form_ID}')
	            .html('<div class=\'warning\'>Your email address is invalid!</div>')
	            .slideDown()
	            .delay(10000)
	            .slideUp();
	    } else {
	        jQuery('#bronto-validate-email_{$form_ID}')
	            .html('<img style=\'width:90px;\' src=\'".plugins_url( 'assets/loading.gif', __FILE__ )."\' alt=\'Loading ...\'>')
	            .slideDown()
	            .delay(10000)
	            .slideUp();
	        var postData = jQuery(this).serializeArray();
	        var formURL = jQuery(this).attr('action');
	        jQuery.ajax({
	            url: formURL,
	            type: 'POST',
	            data: postData,
	            success: function(data, textStatus, jqXHR) {
	                jQuery('#bronto-validate-email_{$form_ID}').html(data);
	                jQuery('#bronto-newsletter-form_{$form_ID}').slideUp();
	                jQuery('#bronto-validate-email_{$form_ID}')
	                    .html(data)
	                    .slideDown()
					    setTimeout(function(){
							jQuery('#sgcboxClose').trigger('click')
							.slideUp();
					    }, 3000);
	            },
	            error: function(jqXHR, textStatus, errorThrown) {
	                jQuery('#bronto-validate-email_{$form_ID}')
	                    .html('<div class=\'error\'>Something went wrong, please try again later!</div>')
	                    .slideDown()
	            }
	        });
	        e.preventDefault();
	    }
	});
	</script>";
	}
	return $output;
}