<?php
function anps_sidebar_meta_box() {
	anps_sidebar_generator::edit_form();
}

class anps_sidebar_generator {

	function __construct() {
		add_action('init', array('anps_sidebar_generator','init'));
		add_action('add_meta_boxes', array('anps_sidebar_generator','add_meta_box'));

        if(current_user_can('manage_options')) {
    		add_action('admin_menu', array('anps_sidebar_generator','admin_menu'));
    		add_action('admin_print_scripts', array('anps_sidebar_generator','admin_print_scripts'));
    		add_action('wp_ajax_add_sidebar', array('anps_sidebar_generator','add_sidebar') );
    		add_action('wp_ajax_remove_sidebar', array('anps_sidebar_generator','remove_sidebar') );

    		//save posts/pages
    		add_action('edit_post', array('anps_sidebar_generator', 'save_form'));
    		add_action('publish_post', array('anps_sidebar_generator', 'save_form'));
    		add_action('save_post', array('anps_sidebar_generator', 'save_form'));
    		add_action('edit_page_form', array('anps_sidebar_generator', 'save_form'));
		}
	}

	public static function add_meta_box() {
		add_meta_box(
			'anps_sidebars_meta_box',
			esc_html__('Sidebars', 'anps_theme_plugin'),
			'anps_sidebar_meta_box',
			array('post', 'portfolio', 'page'),
			'normal',
			'core'
		);
	}

	public static function init() {
		//go through each sidebar and register it
	    $sidebars = anps_sidebar_generator::get_sidebars();

	    if(is_array($sidebars)) {
			foreach($sidebars as $sidebar) {
				$sidebar_slug = anps_sidebar_generator::name_to_slug($sidebar);

				register_sidebar(array(
					'name'          => $sidebar,
					'id'            => $sidebar_slug,
			        'before_widget' => '<div id="%1$s" class="widget %2$s">',
			        'after_widget' => '</div>',
			        'before_title' => '<h3 class="widget-title">',
			        'after_title' => '</h3>',
		    	));
			}
		}
	}

	public static function admin_print_scripts() {
		wp_enqueue_script('anps-sidebar-generator');
		wp_enqueue_style('anps-sidebar-generator');
	}

	public static function add_sidebar() {
        $retrieved_nonce = $_POST['security'];
        if (!wp_verify_nonce($retrieved_nonce, 'anps_sidebar_generator' ) ) die( 'Failed security check' );

		$sidebars = anps_sidebar_generator::get_sidebars();
		$name = str_replace(array("\n","\r","\t"),'',$_POST['sidebar_name']);
		$id = anps_sidebar_generator::name_to_slug($name);

		$arr = array();

		if(isset($sidebars[$id])){
			$arr = array(
				'error' => esc_html__( 'Sidebar already exists, please use a different name.', 'anps_theme_plugin' )
			);
		} else {
			$arr = array(
				'name' => $name,
				'ID'   => $id
			);

			$sidebars[$id] = $name;
			anps_sidebar_generator::update_sidebars($sidebars);
		}

		die(json_encode($arr));
	}

	public static function remove_sidebar() {
        $retrieved_nonce = $_POST['security'];
        if (!wp_verify_nonce($retrieved_nonce, 'anps_sidebar_generator' ) ) die( 'Failed security check' );

		$sidebars = anps_sidebar_generator::get_sidebars();
		$slug = $_POST['sidebar_slug'];

		$arr = array();

		if(!isset($sidebars[$slug])){
			$arr = array(
				'error' => esc_html__( 'Sidebar does not exist.', 'anps_theme_plugin' )
			);
		} else {
			unset($sidebars[$slug]);
			anps_sidebar_generator::update_sidebars($sidebars);

			$arr = array(
				'rowNum' => $_POST['row_num']
			);
		}

		die(json_encode($arr));
	}

	public static function admin_menu(){
        add_theme_page(esc_html__('Sidebars', 'anps_theme_plugin'), esc_html__('Sidebars', 'anps_theme_plugin'), 'manage_options', 'anps_sidebar_generator', array('anps_sidebar_generator','admin_page'));
	}

	static function admin_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e('Sidebar Generator', 'anps_theme_plugin'); ?></h2>
            <?php wp_nonce_field('anps_sidebar_generator'); ?>
			<table class="widefat page striped sbg-table" id="sbg_table" data-remove="<?php esc_html_e('Remove', 'anps_theme_plugin'); ?>" data-none="<?php esc_html_e('No sidebars found', 'anps_theme_plugin'); ?>" data-prompt="<?php esc_html_e('Sidebar Name:', 'anps_theme_plugin'); ?>" data-confirm="Are you sure you want to remove %s?">
				<thead>
					<tr>
						<th><?php esc_html_e('Name', 'anps_theme_plugin'); ?></th>
						<th><?php esc_html_e('Slug', 'anps_theme_plugin'); ?></th>
						<th><?php esc_html_e('Remove', 'anps_theme_plugin'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$sidebars = anps_sidebar_generator::get_sidebars();

				if(is_array($sidebars) && !empty($sidebars)){
					$cnt=0;
					foreach($sidebars as $sidebar){
						$alt = ($cnt%2 == 0 ? 'alternate' : '');
				?>
				<tr class="<?php echo esc_attr($alt);?>">
					<td data-sidebar="name"><?php echo esc_html($sidebar); ?></td>
					<td data-sidebar="slug"><?php echo esc_html(anps_sidebar_generator::name_to_slug(esc_html($sidebar))); ?></td>
					<td><button data-sidebar="remove" class="remove-sidebar"><?php esc_html_e('Remove', 'anps_theme_plugin'); ?></button></td>
				</tr>
				<?php
						$cnt++;
					}
				}else{
					?>
					<tr class="no-items">
						<td class="colspanchange" colspan="3"><?php esc_html_e('No sidebars found', 'anps_theme_plugin'); ?></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<div class="sbg-add">
				<button data-sidebar="add" class="button button-primary" title="<?php esc_html_e('Add a sidebar', 'anps_theme_plugin'); ?>">
					<?php esc_html_e('Add Sidebar', 'anps_theme_plugin'); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * for saving the pages/post
	*/
	public static function save_form($post_id){
        if( isset($_POST['sbg_edit']) ) {
            $is_saving = $_POST['sbg_edit'];
        }

		if(!empty($is_saving)){
			delete_post_meta($post_id, 'anps_left_sidebar');
			delete_post_meta($post_id, 'anps_right_sidebar');
			add_post_meta($post_id, 'anps_left_sidebar', $_POST['anps_sidebar_generator']);
			add_post_meta($post_id, 'anps_right_sidebar', $_POST['anps_sidebar_generator_replacement']);			
                        add_post_meta($post_id, 'anps_mobile_sidebar_after', $_POST['anps_mobile_sidebar_after'], true) or update_post_meta($post_id, 'anps_mobile_sidebar_after', $_POST['anps_mobile_sidebar_after']);
                        add_post_meta($post_id, 'anps_mobile_sidebar_hide', $_POST['anps_mobile_sidebar_hide'], true) or update_post_meta($post_id, 'anps_mobile_sidebar_hide', $_POST['anps_mobile_sidebar_hide']);
                }
	}

	public static function edit_form(){
	    global $post;
	    $post_id = $post;

	    if (is_object($post_id)) {
	    	$post_id = $post_id->ID;
	    }

	    $selected_sidebar = get_post_meta($post_id, 'anps_left_sidebar', true);
	    if(!is_array($selected_sidebar)){
	    	$tmp = $selected_sidebar;
	    	$selected_sidebar = array();
	    	$selected_sidebar[0] = $tmp;
	    }

	    $selected_sidebar_replacement = get_post_meta($post_id, 'anps_right_sidebar', true);
		if(!is_array($selected_sidebar_replacement)){
	    	$tmp = $selected_sidebar_replacement;
	    	$selected_sidebar_replacement = array();
	    	$selected_sidebar_replacement[0] = $tmp;
	    }
            
            $value_sidebar_after = get_post_meta($post_id, 'anps_mobile_sidebar_after', true);
            $value_sidebar_hide = get_post_meta($post_id, 'anps_mobile_sidebar_hide', true);
            $checked_sidebar_after = checked($value_sidebar_after, '1', false);
            $checked_sidebar_hide = checked($value_sidebar_hide, '1', false);
		?>

	<div id='sbg-sortables'>
		<div id="sbg_box">
			<input name="sbg_edit" type="hidden" value="sbg_edit" />

					<p>
						<?php esc_html_e( 'Select the sidebar you wish to display. If no value is selected, then the global sidebar will be used. Any other value will overwrite the global option. Use "None" to remove the global sidebar for on a specific post/page.', 'anps_theme_plugin' ); ?>
					</p>
					<table class="anps-sidebars">
					<?php global $wp_registered_sidebars; 
                                                for($i=0;$i<1;$i++) {
                                        ?>
							<tr>
							<td>
								<?php esc_html_e("Left sidebar", 'anps_theme_plugin'); ?>:
							</td><td>
							<select name="anps_sidebar_generator">
								<option value="0"></option>
								<option value="-1"<?php if($selected_sidebar[$i] == '-1'){ echo " selected";} ?>><?php esc_html_e("None", 'anps_theme_plugin'); ?></option>
							<?php
							$sidebars = $wp_registered_sidebars;
							if(is_array($sidebars) && !empty($sidebars)){
								foreach($sidebars as $sidebar){
									if($selected_sidebar[$i] == $sidebar['name']){
										echo "<option value='".esc_attr($sidebar['name'])."' selected>".esc_attr($sidebar['name'])."</option>\n";
									}else{
										echo "<option value='".esc_attr($sidebar['name'])."'>".esc_attr($sidebar['name'])."</option>\n";
									}
								}
							}
							?>
							</select></td></tr><tr><td>
							<?php esc_html_e("Right sidebar", 'anps_theme_plugin'); ?>:
							</td><td>
							<select name="anps_sidebar_generator_replacement">
								<option value="0"></option>
								<option value="-1"<?php if($selected_sidebar_replacement[$i] == '-1'){ echo " selected";} ?>><?php esc_html_e("None", 'anps_theme_plugin'); ?></option>
							<?php

							$sidebar_replacements = $wp_registered_sidebars;//anps_sidebar_generator::get_sidebars();
							if(is_array($sidebar_replacements) && !empty($sidebar_replacements)){
								foreach($sidebar_replacements as $sidebar){
									if($selected_sidebar_replacement[$i] == $sidebar['name']){
										echo "<option value='".esc_attr($sidebar['name'])."' selected>".esc_attr($sidebar['name'])."</option>\n";
									}else{
										echo "<option value='".esc_attr($sidebar['name'])."'>".esc_attr($sidebar['name'])."</option>\n";
									}
								}
							}
							?>
							</select>
							</td>
							</tr>
							<tr>
								<td><?php esc_html_e("Show sidebars on mobile after main content", 'anps_theme_plugin'); ?></td>
								<td>
									<input id="anps_mobile_sidebar_after" value="" name="anps_mobile_sidebar_after" type="hidden">
									<input type="checkbox" name="anps_mobile_sidebar_after" value="1" <?php echo esc_attr($checked_sidebar_after); ?> />
								</td>
							</tr>
							<tr>
								<td><?php esc_html_e("Hide sidebars on mobile", 'anps_theme_plugin'); ?></td>
								<td>
									<input id="anps_mobile_sidebar_hide" value="" name="anps_mobile_sidebar_hide" type="hidden">
									<input type="checkbox" name="anps_mobile_sidebar_hide" value="1" <?php echo esc_attr($checked_sidebar_hide); ?>/>
								</td>
							</tr>
					<?php } ?>
					</table>
		</div>
	</div>
		<?php
	}

	/**
	 * called by the action get_sidebar. this is what places this into the theme
	*/
	public static function get_sidebar($name="0"){
		global $wp_query;
		$post = $wp_query->get_queried_object();
		$selected_sidebar = get_post_meta($post->ID, 'anps_left_sidebar', true);
		$selected_sidebar_replacement = get_post_meta($post->ID, 'anps_right_sidebar', true);
		$did_sidebar = false;

		if($selected_sidebar != '' && $selected_sidebar != "0"){
			if(is_array($selected_sidebar) && !empty($selected_sidebar)){
				for($i=0;$i<sizeof($selected_sidebar);$i++){
					if($name == "0" && $selected_sidebar[$i] == "0" &&  $selected_sidebar_replacement[$i] == "0"){
						dynamic_sidebar();
						$did_sidebar = true;
						break;
					}elseif($name == "0" && $selected_sidebar[$i] == "0"){
						dynamic_sidebar($selected_sidebar_replacement[$i]);//default behavior
						$did_sidebar = true;
						break;
					}elseif($selected_sidebar[$i] == $name){
						$did_sidebar = true;
						dynamic_sidebar($selected_sidebar_replacement[$i]);//default behavior
						break;
					} else {
                    	$did_sidebar = true;
                        dynamic_sidebar($selected_sidebar_replacement[$i]);//default behavior
                        break;
                    }
				}
			}
			if($did_sidebar == true) {
				return;
			}
			//go through without finding any replacements, lets just send them what they asked for
			if($name != "0") {
				dynamic_sidebar($name);
			} else {
				dynamic_sidebar();
			}

			return;
		} else {
			if($name != "0") {
				dynamic_sidebar($name);
			} else {
				dynamic_sidebar();
			}
		}
	}

	/**
	 * replaces array of sidebar names
	*/
	public static function update_sidebars($sidebar_array){
		$sidebars = update_option('sbg_sidebars', $sidebar_array);
	}

	/**
	 * gets the generated sidebars
	*/
	public static function get_sidebars(){
		$sidebars = get_option('sbg_sidebars');
		return $sidebars;
	}

	public static function name_to_slug($name){
		$slug = str_replace(array(',','.','"',"'",'/',"\\",'+','=',')','(','*','&','^','%','$','#','@','!','~','`','<','>','?','[',']','{','}','|',':',), '', $name);
		$slug = str_replace(' ', '-', $slug);
		$slug = strtolower($slug);
		return $slug;
	}
}

$anps_sbg = new anps_sidebar_generator;

/* Return the number of sidebars on the current post */

function anps_num_sidebars() {
	$num = 0;

	/* Check if sidebar is set, increment counter */
	$num += anps_sidebar('left')  ? 1 : 0;
	$num += anps_sidebar('right') ? 1 : 0;

	return $num;
}

/* Get the sidebar from post meta or global settings */

function anps_sidebar($side) {
	/* Global sidebar option, set in Theme Options */
	$sidebar = get_option('anps_page_sidebar_' . $side);

	if( (is_single() || is_archive() || is_tag() || is_home() || is_category()) && (!function_exists('is_woocommerce') || !is_woocommerce()) ) {
		$sidebar = get_option('anps_post_sidebar_' . $side);
	}

	/* Individual sidebar option, set on individual posts/pages */
	$id = get_the_ID();
	if( is_home() || is_archive() ) {
		$id = get_option( 'page_for_posts' );
	}

	if( function_exists('is_shop') && (is_shop() || is_product_category()) ) {
		$id = get_option('woocommerce_shop_page_id');
	}

	$meta = get_post_meta($id);
	$sidebar_name = 'anps_' . $side . '_sidebar';

	if( isset($meta[$sidebar_name]) && $meta[$sidebar_name][0] != "0" ) {
	    if( $meta[$sidebar_name][0] != "-1" ) {
	        $sidebar = $meta[$sidebar_name][0];
	    } else {
	    	/* Return if sidebar does NOT exist */
	    	return false;
	    }
	}

	/* Return if sidebar exits */
	return $sidebar;
}

/* Sidebar HTML */

function anps_get_sidebar($side) {
	if( anps_sidebar($side) ): ?>
	    <aside class="sidebar sidebar-<?php echo esc_attr($side); ?> col-md-3">
	        <?php dynamic_sidebar(anps_sidebar($side));?>
	    </aside>
	<?php endif;
}

/* Get LEFT sidebar */

function anps_left_sidebar() {
	anps_get_sidebar('left');
}

/* Get RIGHT sidebar */

function anps_right_sidebar() {
	anps_get_sidebar('right');
}
