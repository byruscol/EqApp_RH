<?php
/**
 * Edit user administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

wp_reset_vars( array( 'action', 'user_id', 'wp_http_referer' ) );

$user_id = (int) $user_id;
$current_user = wp_get_current_user();
if ( ! defined( 'IS_PROFILE_PAGE' ) )
	define( 'IS_PROFILE_PAGE', ( $user_id == $current_user->ID ) );

if ( ! $user_id && IS_PROFILE_PAGE )
	$user_id = $current_user->ID;
elseif ( ! $user_id && ! IS_PROFILE_PAGE )
	wp_die(__( 'Invalid user ID.' ) );
elseif ( ! get_userdata( $user_id ) )
	wp_die( __('Invalid user ID.') );

wp_enqueue_script('user-profile');

$title = IS_PROFILE_PAGE ? __('SGC') : __('Edit User');
if ( current_user_can('edit_users') && !IS_PROFILE_PAGE )
	$submenu_file = 'Gulf_SGC.php';
else
	$submenu_file = 'Gulf_SGC.php';

if ( current_user_can('edit_users') && !is_user_admin() )
	$parent_file = 'Gulf_SGC.php';
else
	$parent_file = 'Gulf_SGC.php';


$profileuser = get_user_to_edit($user_id);



if ( !current_user_can('edit_user', $user_id) )
	wp_die(__('You do not have permission to edit this user.'));

include (ABSPATH . 'wp-admin/admin-header.php');
if ( !IS_PROFILE_PAGE && is_super_admin( $profileuser->ID ) && current_user_can( 'manage_network_options' ) ) { ?>
	<div class="updated"><p><strong><?php _e('Important:'); ?></strong> <?php _e('This user has super admin privileges.'); ?></p></div>
<?php } ?>

<div class="wrap" id="profile-page">
	<?php screen_icon(); ?>
	<h2>
		<?php echo esc_html( $title );?>
	</h2>
</div>

<?php
include( ABSPATH . 'wp-admin/admin-footer.php');
