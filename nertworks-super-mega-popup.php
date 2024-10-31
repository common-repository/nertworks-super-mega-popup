<?php   
/* 
	Plugin Name: NertWorks Super Mega Popup 
	Plugin URI: http://codebangers.com/ 
	Description: Plugin for showing a popup on a specific url or an entire webpage.
	Author: Codebangers
	Version: 2.20
	Author URI: http://www.codebangers.com 
	*/
error_reporting(E_ALL & ~E_NOTICE);		
$wp_rewrite = new WP_Rewrite();
if ($_GET['cookie_action']=="kill_cookie"){
	add_action( 'init', 'super_mega_seenit_cookie_kill');
}
if (get_option('super_mega_popup_enabled')=="enabled"){
	if (get_option('super_mega_popup_multi_site_all')=="no"){
			global $current_user;
			if (($current_user!=NULL)&& (get_option('colorbox_popup_show_when_logged_in')=="enabled")){
				add_action('wp_footer', 'show_super_mega_popup');
			} else {
				
			}
			if (($current_user==NULL)&&(get_option('colorbox_popup_show_when_logged_out')=="enabled")){
				add_action('wp_footer', 'show_super_mega_popup');
			} else {
				
			}
				
	}
	else{
		$domain = "http://$_SERVER[HTTP_HOST]";
		if ($domain==get_option('super_mega_popup_multi_site_domain')){
			global $current_user;
			if (($current_user!=NULL)&& (get_option('colorbox_popup_show_when_logged_in')=="enabled")&&($_COOKIE['Seenit']==NULL)){
				add_action('wp_footer', 'show_super_mega_popup');
			} else {
				
			}
			if (($current_user==NULL)&&(get_option('colorbox_popup_show_when_logged_out')=="enabled")&&($_COOKIE['Seenit']==NULL)){
				add_action('wp_footer', 'show_super_mega_popup');
			} else {
				
			}
		}		
		
	}
}
//add_action( 'admin_init', 'nertworks_popup_jquery_register' );
if (get_option('super_mega_theme_jquery_enabled')=="enabled")
{
	add_action("wp_enqueue_scripts", "nertworks_popup_jquery_enqueue", 11);
}


function super_mega_seenit_cookie() {
	
		setcookie('Seenit', 1, time()+3600, COOKIEPATH, COOKIE_DOMAIN, false);
	
}
function super_mega_seenit_cookie_kill() {
		//setcookie('Seenit', 1, time()-3600, COOKIEPATH, COOKIE_DOMAIN, false);
		$_COOKIE['Seenit'] = NULL;
}
if (get_option('super_mega_popup_cookie')=="enabled"){
	$popup_url = get_permalink(get_option('url_of_popup1'));
	$page_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	//echo '<script>alert('.$page_url.');</script>';
	$shortcode_search="";
	if ($_GET['p']!=NULL){
		global $post;
		if( has_shortcode( $post->post_content, 'show_super_mega_popup') ) {
			$shortcode_search="found";	
		}
	}
	if(($page_url==$popup_url)||(get_option('super_mega_popup_specific_or_global')=="everywhere")||($shortcode_search=="found")){
		add_action( 'init', 'super_mega_seenit_cookie');
	}
}

function nertworks_popup_jquery_enqueue() {
	wp_deregister_script('jquery');
	wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://code.jquery.com/jquery-1.8.3.js", false, null);
	wp_enqueue_script('jquery');
}

/*
function super_popup_register_jquery()
{
	wp_enqueue_script('jquery');
}*/

add_action( 'init', 'create_post_type_popup' );

function create_post_type_popup() {
	register_post_type( 'popups',
		array(
			'labels' => array(
				'name' => __( 'Popups' ),
				'singular_name' => __( 'Popups' )
			),
		'public' => true,
		'has_archive' => false,
		)
	);
}


add_shortcode( 'show_super_mega_popup', 'show_super_mega_popup' );
function show_super_mega_popup(){
	$popup_url = get_permalink(get_option('url_of_popup1'));
	//echo '<script>alert('.$popup_url.');</script>';
	$page_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	//echo '<script>alert('.$page_url.');</script>';
	$shortcode_search="";
	if (($_GET['p']!=NULL)&&(get_option('super_mega_popup_enabled')=="enabled")){
		global $post;
		if( has_shortcode( $post->post_content, 'show_super_mega_popup') ) {
			$shortcode_search="found";	
		}
	}
	
	if (get_option('super_mega_popup_source')=="colorbox"){
		if(($page_url==$popup_url)||(get_option('super_mega_popup_specific_or_global')=="everywhere")||($shortcode_search=="found")){
			$example=get_option('super_mega_popup_colorbox_theme');
			$iframe=plugins_url('iframe.php', __FILE__);
			echo ' 
			<script type=\'text/javascript\' src="'.plugins_url('/inc/colorbox/jquery.colorbox.js', __FILE__).'"></script>
			<link rel="stylesheet" type="text/css" href="'.plugins_url('/inc/colorbox/'.$example.'/colorbox.css', __FILE__).'">
				';
			echo '
			
			<script type="text/javascript">//<![CDATA[ 
			$.noConflict();
			jQuery(window).load(function(){
			jQuery(document).ready(function(){';
			
			if (get_option('super_mega_colobox_mode')=="html")
			{
				$popup_title = get_the_title(get_option('popup_selection'));
				$popup_content = get_post_field('post_content', get_option('popup_selection'));
				//$popup_content = preg_replace('~>\s+<~', '><', $popup_content);
				//$popup_content = preg_replace('/\s+/', '', $popup_content);
				$popup_content = str_replace('  ', '', $popup_content);
				echo 'jQuery.colorbox({html:\'<h3>'.$popup_title.'</h3>'.$popup_content.'\', width:"'.get_option('colorbox_popup_width').'%", height:"'.get_option('colorbox_popup_height').'%"});';
			}
			if (get_option('super_mega_colobox_mode')=="iframe")
			{			
				echo 'jQuery.colorbox({iframe:true, width:"'.get_option('colorbox_popup_width').'%", height:"'.get_option('colorbox_popup_height').'%",href:"'.$iframe.'"});';
			}		

			
			echo '});
			});
			
			//]]>  
			
			</script>';
			
		}
		//$.colorbox({html:\"<h3>".get_option('super_mega_popup_message1_heading')."</h3>".get_option('super_mega_popup_message1')."\"});
		//$.colorbox({iframe:true, width:\"".get_option('colorbox_popup_width')."%\", height:\"".get_option('colorbox_popup_height')."%\",href:\"".$iframe."\"});
	}
	if (get_option('super_mega_popup_source')=="javascript"){ 
		
		if(($page_url==$popup_url)||(get_option('super_mega_popup_specific_or_global')=="everywhere")||($shortcode_search=="found")){
			$heading=strip_tags(get_option('super_mega_popup_message1_heading'));
			$message=strip_tags(get_option('super_mega_popup_message1'));
			$heading=html_entity_decode($heading); 
			$message=html_entity_decode($message); 
			echo '<script type="text/javascript">
				alert("'.$heading."\\n".$message.'");
			</script>';
		}
	}
	/*if (get_option('super_mega_popup_cookie')=="enabled"){
		add_action( 'init', 'set_super_mega_popup_cookie');
	}*/
}
function nertworks_popup_settings_page() {
	?>
	<div class="wrap">
	<?php $logo=plugins_url('/images/nertworks_logo.png', __FILE__);?>
	
	<a href="http://nertworks.com" target="_blank"><img src="<?php echo $logo; ?>" style="width:20%;"></a>
	<h1>Super Mega Popup Options</h1>
	<div class="about-text">
	<?php _e('Popups just like Mama used to make.' ); ?>

	</div>

	

	<h2 class="nav-tab-wrapper">

	<?php $tab=$_GET['tab']; 

	if ($tab==NULL){

		$tab="general_settings";

	}

	?>	

	<a href="?page=super_mega-options&tab=general_settings" class="nav-tab<?php if ($tab=="general_settings"){echo " nav-tab-active";}?>">

	<?php _e( 'General Settings' ); ?>


	<a href="?page=super_mega-options&tab=tools" class="nav-tab<?php if ($tab=="tools"){echo " nav-tab-active";}?>">

	<?php _e( 'Tools' ); ?>

	</a>
	<a href="?page=super_mega-options&tab=about" class="nav-tab<?php if ($tab=="about"){echo " nav-tab-active";}?>">

	<?php _e( 'About' ); ?>

	</a>
	<a href="?page=super_mega-options&tab=help" class="nav-tab<?php if ($tab=="help"){echo " nav-tab-active";}?>">

	<?php _e( 'Help Meh' ); ?>

	</a>
	</h2>

	<!--Handle the Tabs-->
	<?php if ($tab=="general_settings"){?>
	<!--<script type='text/javascript' src='http://code.jquery.com/jquery-1.6.4.js'></script>-->
		<script type='text/javascript'>//<![CDATA[ 
		$.noConflict();		
		jQuery(window).load(function(){
			jQuery(document).ready(function () {
				jQuery('.group').hide();
				jQuery('#<?php echo get_option('super_mega_popup_specific_or_global'); ?>').fadeIn('slow');
				jQuery('#super_mega_popup_specific_or_global').change(function () {
					jQuery('.group').hide();
					jQuery('#'+jQuery(this).val()).fadeIn('slow');
				})
			});
			
		});

		//]]>  </script>
		<script type='text/javascript'>//<![CDATA[ 
		jQuery(window).load(function(){
			jQuery(document).ready(function () {
				jQuery('.group2').hide();
				jQuery('#<?php echo get_option('super_mega_popup_source'); ?>').fadeIn('slow');
				jQuery('#super_mega_popup_source').change(function () {
					jQuery('.group2').hide();
					jQuery('#'+jQuery(this).val()).fadeIn('slow');
				})
			});
			
		});

		//]]>  </script>
		<script type='text/javascript'>//<![CDATA[ 
		jQuery(window).load(function(){
			jQuery(document).ready(function () {
				jQuery('.group3').hide();
				jQuery('#<?php echo get_option('super_mega_popup_multi_site_enabled'); ?>').fadeIn('slow');
				jQuery('#super_mega_popup_multi_site_enabled').change(function () {
					jQuery('.group3').hide();
					jQuery('#'+jQuery(this).val()).fadeIn('slow');
				})
			});
			
		});

		//]]>  </script>
		
		<script type='text/javascript'>//<![CDATA[ 
		jQuery(window).load(function(){
		jQuery(function () {
			jQuery(".nmbr").slider({
				range: "min",
				min: 1,
				max: 100,
				slide: function (event, ui) {
					jQuery("input[name=" + jQuery(this).attr("id") + "_value]").val(ui.value);
				}
			});
		});
		});//]]>  

		</script>
		<h3>General Settings</h3>
		<?php 
		//Kill Cookie if it's disabled
		if (get_option('super_mega_popup_cookie')=="disabled")
		{
			add_action( 'init', 'super_mega_seenit_cookie_kill');
		}
		?>
		
		<form method="post" action="options.php">
		<?php settings_fields( 'nertworks-popup-settings-group' ); ?>
		<?php do_settings_sections( 'nertworks-popup-settings-group' ); 
			$options = get_option('nertworks-popup-settings-group');
		?>
		<table class="form-table">
		<tr valign="top">
		<th scope="row"><strong>Enable Your Popup </strong><br /></th>
		<td>
		<input type="radio" name="super_mega_popup_enabled" value="enabled" <?php if (get_option('super_mega_popup_enabled')=="enabled"){echo "checked";}?> />Enabled
		<input type="radio" name="super_mega_popup_enabled" value="disabled" <?php if (get_option('super_mega_popup_enabled')=="disabled"){echo "checked";}?>/>Disabled
		</td>
		</tr>
		<tr valign="top">
		<th scope="row"><strong>Enable Super Mega jQuery on you Theme: </strong><br /></th>
		<td>
		<input type="radio" name="super_mega_theme_jquery_enabled" value="enabled" <?php if (get_option('super_mega_theme_jquery_enabled')=="enabled"){echo "checked";}?> />Enabled
		<input type="radio" name="super_mega_theme_jquery_enabled" value="disabled" <?php if (get_option('super_mega_theme_jquery_enabled')=="disabled"){echo "checked";}?>/>Disabled
		<i>This is only needed if you don't already have jQuery enabled already.</i>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row"><strong>Show when users are logged in: </strong><br /></th>
		<td>
		<input type="radio" name="colorbox_popup_show_when_logged_in" value="enabled" <?php if (get_option('colorbox_popup_show_when_logged_in')=="enabled"){echo "checked";}?> />Enabled
		<input type="radio" name="colorbox_popup_show_when_logged_in" value="disabled" <?php if (get_option('colorbox_popup_show_when_logged_in')=="disabled"){echo "checked";}?>/>Disabled
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row"><strong>Show when users are not logged in: </strong><br /></th>
		<td>
		<input type="radio" name="colorbox_popup_show_when_logged_out" value="enabled" <?php if (get_option('colorbox_popup_show_when_logged_out')=="enabled"){echo "checked";}?> />Enabled
		<input type="radio" name="colorbox_popup_show_when_logged_out" value="disabled" <?php if (get_option('colorbox_popup_show_when_logged_out')=="disabled"){echo "checked";}?>/>Disabled
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row"><strong>Enable Cookie: <br /><i>Only shows each user the popup once a day.</i>  </strong><br /></th>
		<td>
		<input type="radio" name="super_mega_popup_cookie" value="enabled" <?php if (get_option('super_mega_popup_cookie')=="enabled"){echo "checked";}?> />Enabled
		<input type="radio" name="super_mega_popup_cookie" value="disabled" <?php if (get_option('super_mega_popup_cookie')=="disabled"){echo "checked";}?>/>Disabled
		<i>If you have enabled the Cookie Feature below in the past it.  Make sure you go to Tools and remove it if you want to be able to test the popup.  Otherwise it'll only show once for each user.  </i>
		</td>
		</tr>
				
		
<?php
		/*
		<tr valign="top">
		<th scope="row"><strong>Popup Heading: </strong><br /></th>
		<td><input type="text" name="super_mega_popup_message1_heading" value="<?php echo get_option('super_mega_popup_message1_heading'); ?>" /></td>
		</tr>
		
		<tr valign="top">
		<th scope="row"><strong>Popup Shortcode to include: </strong><br /><i>currently this plugin only supports one shortcode per popup message.  </i></th>
		<td><input type="text" name="super_mega_popup_message_shortcode" value="<?php echo get_option('super_mega_popup_message_shortcode'); ?>" /></td>
		</tr>

		<tr valign="top">
		<th scope="row"><strong>Popup Message: </strong></th>
		<td>
		
		
		<?php 
		$content = get_option('super_mega_popup_message1');
		$editor_id = 'super_mega_popup_message1';

		wp_editor( $content, $editor_id, $settings = array(
			'media_buttons'=> "true",
			"dfw" => "true",
		) );
		
		?>
		</td>
		*/
?>
		<tr valign="top">
			<th scope="row">Which Popup do you Want to Show?: </th>
			<td>
			<?php 
			/*$args = array(
			'sort_order'   => 'ASC',
			'sort_column'  => 'post_title',
			'name'   => 'popup_selection',
			'selected'   => get_option('popup_selection'),
			'post_type' => 'popups'
			);
			wp_dropdown_pages($args); 
			*/
					
			?>
			<select name="popup_selection">
				<?php $loop = new WP_Query( array( 'post_type' => 'popups', 'posts_per_page' => -1 ) ); ?>
				<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
					<option value="<?php echo $loop->post->ID; ?>" <?php if (get_option('popup_selection')==$loop->post->ID){echo "selected";}?>><?php the_title(); ?></option>
				<?php endwhile; wp_reset_query(); ?>
			</select>
			
			</td>
			<td><i>Select which popup you want to display. Manage Popups <a href="/wp-admin/edit.php?post_type=popups">Here</a></i></td>
		</tr>
		
		
		</tr>
		
		
		<tr valign="top">		
		<th scope="row"><strong>Show on Pages?: </strong></th>
		<td>
		<select name="super_mega_popup_specific_or_global" id="super_mega_popup_specific_or_global">
		<option value="disabled" <?php if (get_option('super_mega_popup_specific_or_global')=="disabled"){echo "selected";}?>>Disabled</option>
		<option value="specific" <?php if (get_option('super_mega_popup_specific_or_global')=="specific"){echo "selected";}?>>Specific Page</option>
		<option value="everywhere" <?php if (get_option('super_mega_popup_specific_or_global')=="everywhere"){echo "selected";}?>>All Pages</option>
		</select>
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row"></th>
		<td>
		<div id="specific" class="group" >
		Which Page?: 
		<?php 
		$args = array(
		'child_of'     => 0,
		'sort_order'   => 'ASC',
		'sort_column'  => 'post_title',
		'hierarchical' => 1,
		'name'   => 'url_of_popup1',
		'selected'   => get_option('url_of_popup1'),
		'post_type' => 'page'
		);
		wp_dropdown_pages($args); ?>
		</div><!--specific-->
		<div id="everywhere" class="group" >
		<i>This will make your popup show everywhere on your site.  Beware, with great power comes great responsibility.  </i>
		</div><!--specific-->
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">Show in Posts?</th>
		<td><i>To show this popup on a POST simple copy the shortcode <strong>[show_super_mega_popup]</strong> into the content of the POST you want to show it on.</i></td>
		</tr>
		
		<tr valign="top">
		<th scope="row"><strong>Are you on a Multi Site Environment?: </strong></th>
		<td>
		<select name="super_mega_popup_multi_site_enabled" id="super_mega_popup_multi_site_enabled">
		<option value="no" <?php if (get_option('super_mega_popup_multi_site_enabled')=="no"){echo "selected";}?>>No</option>
		<option value="yes" <?php if (get_option('super_mega_popup_multi_site_enabled')=="yes"){echo "selected";}?>>Yes</option>
		</select>
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row"></th>
		<td>
		<div id="yes" class="group3" >
		<strong>Should this show on all domains? </strong><br />
		<input type="radio" name="super_mega_popup_multi_site_all" value="no" <?php if (get_option('super_mega_popup_multi_site_all')=="no"){echo "checked";}?>/>No
		<input type="radio" name="super_mega_popup_multi_site_all" value="yes" <?php if (get_option('super_mega_popup_multi_site_all')=="yes"){echo "checked";}?> />Yes
		<br />
		<br />
		Current Domain:
		<?php 
		if (get_option('super_mega_popup_multi_site_domain')==NULL){
			$domain = "http://$_SERVER[HTTP_HOST]";
		}
		else {
			$domain = get_option('super_mega_popup_multi_site_domain');
		}
		
		?>	
		<input type="text" name="super_mega_popup_multi_site_domain" value="<?php echo $domain; ?>" /><br /><br />
		<i>If yes is selected, the popup will show on all domains under this multi site server. If No is selected.  It'll only show for the domain that's in the address bar now.</i>
		</div>
		<div id="no" class="group3" >
		</div>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row"><strong>Popup Source: </strong></th>
		<td>
		<select name="super_mega_popup_source" id="super_mega_popup_source">
		<option value="colorbox" <?php if (get_option('super_mega_popup_source')=="colorbox"){echo "selected";}?>>Colorbox</option>
		<option value="javascript" <?php if (get_option('super_mega_popup_source')=="javascript"){echo "selected";}?>>Javascript</option>
		</select>
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row"></th>
		
		<td>
		<div id="colorbox" class="group2" >
		<i>You have chosen Colorbox.  This popup will show your custom heading, messages, custom html, videos, and/or images.</i><br />
		<h4>Style: </h4>
		<input type="radio" name="super_mega_popup_colorbox_theme" value="example1" <?php if (get_option('super_mega_popup_colorbox_theme')=="example1"){echo "checked";}?> />Example 1
		<?php 
		$example1=plugins_url('/images/colorbox_example1.png', __FILE__);
		echo '<a href="'.$example1.'" class="popupImage" target="_blank"><img src="'.$example1.'" width="140"></a>';
		?>	
		<input type="radio" name="super_mega_popup_colorbox_theme" value="example2" <?php if (get_option('super_mega_popup_colorbox_theme')=="example2"){echo "checked";}?>/>Example 2 
		<?php $example2=plugins_url('/images/colorbox_example2.png', __FILE__);
		echo '<a href="'.$example2.'" class="popupImage" target="_blank"><img src="'.$example2.'" width="140"></a>';
		?>
		<input type="radio" name="super_mega_popup_colorbox_theme" value="example3" <?php if (get_option('super_mega_popup_colorbox_theme')=="example3"){echo "checked";}?>/>Example 3 
		<?php $example3=plugins_url('/images/colorbox_example3.png', __FILE__);
		echo '<a href="'.$example3.'" class="popupImage" target="_blank"><img src="'.$example3.'" width="140"></a>';
		?>
		<input type="radio" name="super_mega_popup_colorbox_theme" value="example4" <?php if (get_option('super_mega_popup_colorbox_theme')=="example4"){echo "checked";}?>/>Example 4 
		<?php $example4=plugins_url('/images/colorbox_example4.png', __FILE__);
		echo '<a href="'.$example4.'" class="popupImage" target="_blank"><img src="'.$example4.'" width="140"></a>';
		?>
		<input type="radio" name="super_mega_popup_colorbox_theme" value="example5" <?php if (get_option('super_mega_popup_colorbox_theme')=="example5"){echo "checked";}?>/>Example 5
		<?php $example5=plugins_url('/images/colorbox_example5.png', __FILE__);
		echo '<a href="'.$example5.'" class="popupImage" target="_blank"><img src="'.$example5.'" width="140"></a>';
		?>
		<br /><br />
		<label for="number"><strong>Popup Size :</strong> <i>(between 10% and 100%)</i></label><br />
		Width: <input type="number" name="colorbox_popup_width" min="10" max="100" value="<?php echo get_option('colorbox_popup_width'); ?>"/>%<br />
		Height: <input type="number" name="colorbox_popup_height"  min="10" max="100" value="<?php echo get_option('colorbox_popup_height'); ?>"/>%<br />
		<?php /*
		<h4>Colorbox Mode</h4>
		<input type="radio" name="super_mega_colobox_mode" value="html" <?php if (get_option('super_mega_colobox_mode')=="html"){echo "checked";}?> />Html Mode
		<input type="radio" name="super_mega_colobox_mode" value="iframe" <?php if (get_option('super_mega_colobox_mode')=="iframe"){echo "checked";}?> />iframe Mode
		<br /><br />
		*/
		?>
		
		</div><!--colorbox-->
		<div id="javascript" class="group2" >
		<i>You have chosen javascript.  This popup will display your custom heading and message content but will not display images and custom html.  </i>
		</div><!--javascript-->
		</td>
		
		</tr>
		
		</table>
		<?php submit_button(); ?>

		</form>
		<?php }

	if ($tab=="tools"){
		echo '<h2>Tools</h2>';
		if ($_COOKIE['Seenit']!=NULL){
			echo '<strong>Seenit Cookie is Active: </strong>'.$_COOKIE['Seenit']."<br />";
			echo '<a href="?page=super_mega-options&tab=tools&cookie_action=kill_cookie" class="button">Remove my SeenIt Cookie</a><br />';
		}
		else {
			echo '<strong>Seenit Cookie is Empty</strong><br />';
		}
	}
	if ($tab=="about"){
		echo '<h2>About</h2>';
		echo '<p>Super Mega Popup is meant to simplify the creation and implementation of site wide or page specific popups.  </p>';
		
		echo '<h3>Credit</h3>';
		echo '<h4>Colorbox</h4>';
		echo 'To learn more about colorbox.  Click <a href="http://www.jacklmoore.com/colorbox/" target="_blank">Here</a><br />';
		
		echo '<h4>Nick and Allen</h4>';
		echo 'To learn more about Nick and Allen <a href="http://www.nertworks.com" target="_blank">Here</a><br />';
		
		echo '<h4>Future Updates</h4>';
		echo 'We are always thinking about making things bigger and better.  Suggestions are definitely welcome.  And if you appreciate what we are doing here. Please by all means help support it below.  <br />';
	}
	if ($tab=="help"){
		echo '<h2>Help Meh</h2>';
		$sad_puppy=plugins_url('/images/sadpuppy.jpg', __FILE__);
		echo '<img src="'.$sad_puppy.'" width="200"><br />';
		echo '<h3>What Happened?  Is something not working correctly?</h3>';
		echo '<h3>Problem: </h3> <p>The popup worked for me but now it doesn\'t show up when I am testing it.</p><br />
		<h3>Solution: </h3>Click on Tools and remove your Seenit Cookie. Then the Popup should show up for you while you are testing.  Also you can disable the cookie before you remove it so you don\t continue to have that issue.<br />';
		echo '<h3>Problem: </h3> <p> I tried the "Remove Cookie" Step and it still no workey.</p><br />
		<h3>Solution: </h3>Disable all plugins that might possibly be conficting with colorbox.  examples: gallery plugins, other popup plugins, plugins from the black market.<br />';
		echo '<hr></hr>';
		echo '<p>If you are still having an issue.  Feel free to email <a href="mailto:support@nertworks.com">Meh</a> and I will do my best.</p>';
		
	}	
	?>
	<hr></hr>
	<div id="donatePopipDiv">
	<i>Keep Nick and Allen awake with coffee to work on updates, features and bugs.  </i> 

	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">

	<input type="hidden" name="cmd" value="_s-xclick">

	<input type="hidden" name="hosted_button_id" value="D6FXJUCLE6RGY">

	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">

	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">

	</form>

	<img src="<?php echo plugins_url('/images/double_dragon.jpg', __FILE__); ?>" width="150">
	</div><!--donatePopipDiv-->
	</div>
	<?php }		
//Adding the CSS File
add_action( 'wp_enqueue_scripts', 'nertworks_super_popup_stylesheet' );

/**
* Enqueue plugin style-file
*/
function nertworks_super_popup_stylesheet() {
	// Respects SSL, Style.css is relative to the current file
	wp_register_style( 'prefix-style', plugins_url('style.css', __FILE__) );
	wp_enqueue_style( 'prefix-style' );
}
// create custom plugin settings menu
//add_action('admin_menu', 'nertworks_create_popup_menu');
/*
function nertworks_create_popup_menu() {
	 //create new top-level menu
	$page_hook_suffix = add_menu_page('NertWorks Super Mega Popup', 'NW SuperPopup', 'administrator', __FILE__, 'nertworks_popup_settings_page',plugins_url('/images/icon16.png', __FILE__));

	//call register settings function
	//
	add_action('admin_print_scripts-' . $page_hook_suffix, 'super_popup_register_jquery');
}*/


//---------------------------------------------------------

add_action( 'admin_init', 'my_plugin_admin_init' );
add_action( 'admin_menu', 'my_plugin_admin_menu' );

    function my_plugin_admin_init() {
        /* Register our script. */
       wp_register_script('nert-jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://code.jquery.com/jquery-1.8.3.js", false, null);
    }

    function my_plugin_admin_menu() {
        /* Add our plugin submenu and administration screen */
        $page_hook_suffix = add_submenu_page( 'options-general.php', // The parent page of this submenu
                                  __( 'SuperMega', 'SuperMega' ), // The submenu title
                                  __( 'Super Mega', 'Super Mega' ), // The screen title
				  'manage_options', // The capability required for access to this submenu
				  'super_mega-options', // The slug to use in the URL of the screen
                                  'nertworks_popup_settings_page' // The function to call to display the screen
                               );

        /*
          * Use the retrieved $page_hook_suffix to hook the function that links our script.
          * This hook invokes the function only on our plugin administration screen,
          * see: http://codex.wordpress.org/Administration_Menus#Page_Hook_Suffix
          */
        add_action('admin_print_scripts-' . $page_hook_suffix, 'my_plugin_admin_scripts');
		add_action( 'admin_init', 'register_nertworks_popup_settings' );
    }

    function my_plugin_admin_scripts() {
        /* Link our already registered script to a page */
        wp_enqueue_script( 'nert-jquery' );
    }

//---------------------------------------------------------



register_activation_hook(__FILE__, 'nertworks_mega_popup_plugin_activate');
add_action('admin_init', 'nertworks_popup_redirect');


function nertworks_mega_popup_plugin_activate() {
	add_option('nertworks_popup_plugin_do_activation_redirect_popup', true);
	update_option('super_mega_popup_enabled', 'enabled');
	update_option('super_mega_popup_message1_heading', 'Sample Heading');
	update_option('super_mega_popup_message1', 'This is your Sample Message');
	update_option('super_mega_include_stylesheets', 'enabled');
	update_option('super_mega_popup_source', 'colorbox');
	update_option('super_mega_popup_colorbox_theme', 'example1');
	update_option('colorbox_popup_width', 50);
	update_option('colorbox_popup_height', 50);
	update_option('super_mega_popup_specific_or_global', 'everywhere');
	update_option('colorbox_popup_show_when_logged_in', 'enabled');
	update_option('colorbox_popup_show_when_logged_out', 'enabled');
	update_option('super_mega_popup_multi_site_enabled', 'no');
	update_option('super_mega_popup_multi_site_all', 'no');
	update_option('super_mega_popup_cookie', 'disabled');
	update_option('super_mega_theme_jquery_enabled', 'enabled');
	update_option('super_mega_colobox_mode', 'iframe');
	
	$popup_id = wp_insert_post(array('post_title'=>'Sample Popup', 'post_type'=>'popups', 'post_content'=>'This is my Sample Popup', 'post_status'=>'publish'));
	
	update_option('popup_selection', $popup_id);
	
}

function nertworks_popup_redirect() {
	if (get_option('nertworks_popup_plugin_do_activation_redirect_popup', false)) {
		delete_option('nertworks_popup_plugin_do_activation_redirect_popup');
		if(!isset($_GET['activate-multi']))
		{
			wp_redirect("options-general.php?page=super_mega-options");
		}
	}
}
function register_nertworks_popup_settings() {
	//register our settings
	register_setting( 'nertworks-popup-settings-group', 'super_mega_theme_jquery_enabled' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_message1_heading' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_message_shortcode' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_specific_or_global' );
	register_setting( 'nertworks-popup-settings-group', 'colorbox_popup_show_when_logged_in' );
	register_setting( 'nertworks-popup-settings-group', 'colorbox_popup_show_when_logged_out' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_colorbox_theme' );
	register_setting( 'nertworks-popup-settings-group', 'colorbox_popup_width' );
	register_setting( 'nertworks-popup-settings-group', 'colorbox_popup_height' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_enabled' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_message1' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_source' );
	register_setting( 'nertworks-popup-settings-group', 'url_of_popup1' );
	register_setting( 'nertworks-popup-settings-group', 'popup_selection' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_multi_site_enabled' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_multi_site_all' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_multi_site_domain' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_popup_cookie' );
	register_setting( 'nertworks-popup-settings-group', 'super_mega_colobox_mode' );
	
}
?>
