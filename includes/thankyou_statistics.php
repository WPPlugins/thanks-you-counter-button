<?php
/* 
 * Statistics form of Thank You Counter Button WordPress Plugin
 * 
 */

if (!defined('THANKS_PLUGIN_URL')) {
  die('Direct call is prohibited');
}

if (!is_user_logged_in() or !current_user_can('level_9')) {
  die('Access is prohibited');
}

global $wpdb, $wp_locale;

// quant of rows per page in stat table
if (!isset($_GET['rowsperstatpage'])) {
  $rowsPerStatPage = get_option('thanks_rows_per_stat_page');
  if (!$rowsPerStatPage) {
    $rowsPerStatPage = 15;
    update_option('thanks_rows_per_stat_page', $rowsPerStatPage);
  }
} else {
  $rowsPerStatPage = $_GET['rowsperstatpage'];
  $temp = get_option('thanks_rows_per_stat_page');
  if (!is_numeric($rowsPerStatPage)) {
    $rowsPerStatPage = $temp;
  } else {
    if ($rowsPerStatPage!=$temp) {
      update_option('thanks_rows_per_stat_page', $rowsPerStatPage);
    }
  }
}

// Expecting a specific post detail
if (isset($_GET['post_id']) && is_numeric($_GET['post_id'])) {
	if (isset($_GET['paged']) && is_numeric($_GET['paged'])) {
		$page_number = $_GET['paged'];
	} else {
		$page_number = 1;
	}
?>
<div class="tablenav">
<form id="posts-filter" action="" method="get">
  <input type="hidden" name="page" value="thankyou.php" />
  <input type="hidden" name="paged" value="<?php echo $page_number; ?>" />
<script language="javascript" type="text/javascript">
  function resetCounter(post_id, message, page) {
    if (!confirm(message)) {
      return false;
    }

    el = document.getElementById('ajax_loader_stat');
    if (el!=undefined) {
      el.style.visibility = 'visible';
    }

    jQuery.ajax({
      type: "POST",
      url: ajaxurl,
      data: { post_id: post_id,
              action: 'thanks_button',
              subaction: 'reset',
              _ajax_nonce: ThanksSettings.ajax_nonce
    },
    success: function(msg){
      if (msg.indexOf('error')<0) {
        el = document.getElementById('thanksQuant_'+ post_id);
        if (el!=undefined) {
          el.innerHTML = '0';
        }
        // Remove IPs
        el = document.getElementById('thanks_IPs_body');
        if (el!=undefined) {
          el.innerHTML = '<tr><td class="txt_center"><?php _e('None', 'thanks-you-counter-button'); ?></td><td>&nbsp;</td></tr>';
        }
      } else {
	        alert(msg);
      }
      el = document.getElementById('ajax_loader_stat');
      if (el!=undefined) {
        el.style.visibility = 'hidden';
      }
    },
    error: function(msg) {
      alert(msg);
      el = document.getElementById('ajax_loader_stat');
      if (el!=undefined) {
        el.style.visibility = 'hidden';
      }
    }
    });

  }
  // resetCounter()

</script>
<?php
  $post_id = $_GET['post_id'];
  $post_title = get_the_title($post_id);
  $query = "select quant
              from $thanksCountersTable
              where post_id=$post_id";
  $thanksQuant = $wpdb->get_var($query);
  if ($wpdb->last_error) {
     thanks_logEvent(THANKS_ERROR.":\n".$wpdb->last_error);
     return;
  }

  $query = "select count(id)
              from $thanksPostReadersTable
              where post_id=$post_id limit 0, 1";
  $thankIPs = $wpdb->get_var($query);
  if ($wpdb->last_error) {
    thanks_logEvent(THANKS_ERROR.":\n".$wpdb->last_error);
    return;
  }

  if (!isset($_GET['pageips']) || !$_GET['pageips'] || !is_numeric($_GET['pageips'])) {
    $_GET['pageips'] = 1;
  }
  $maxNumPages = (int) ($thankIPs / $rowsPerStatPage);
  $rest = $thankIPs / $rowsPerStatPage - $maxNumPages;
  if ($rest>0) {
    $maxNumPages += 1;
  }
  if ($_GET['pageips']>$maxNumPages) {
    $_GET['pageips'] = $maxNumPages;
  }
  $fromRecord = max(0,($_GET['pageips'] - 1))*$rowsPerStatPage;
  $query = "SELECT ip_address, updated
              FROM $thanksPostReadersTable
              WHERE post_id=$post_id
              ORDER BY updated DESC
              LIMIT $fromRecord, $rowsPerStatPage";
  $records = $wpdb->get_results($query);
  if ($wpdb->last_error) {
    thanks_logEvent(THANKS_ERROR.":\n".$wpdb->last_error);
    return;
	}
?>
<div style="float: right; margin-top: 10px;">
	<a href="./tools.php?page=thankyou.php&amp;paged=<?php echo $page_number; ?>#statistics">&laquo;<?php _e('Back to main statistics', 'thanks-you-counter-button'); ?></a>
</div>
<?php
	if (!$records) {
?>
		<p>
			<strong><?php _e('Not available', 'thanks-you-counter-button'); ?></strong>
		</p>
<?php
    return;
  }
?>
<h4>
	<?php printf(__('Details for &ldquo;%s&rdquo;', 'thanks-you-counter-button'), $post_title); ?>
</h4>
<table border="0" width="50%">
		<tr>
			<td>
<table class="widefat fixed" cellspacing="0">
	<thead>
		<tr>
		  <th style="width: 70px;text-align:center;"><?php echo _e('Post Id', 'thanks-you-counter-button'); ?></th>
		  <th style="text-align:center;"><?php _e('Post Title', 'thanks-you-counter-button'); ?></th>
	    <th style="width: 130px;text-align:right;"><?php echo __('Thanks Quant', 'thanks-you-counter-button'); ?></th>
		</tr>
	</thead>
  <tr>
    <td class="txt_right"><?php echo $post_id; ?></td>
    <td class="txt_center" style="padding-left:10px;"><a title="<?php _e('View Post', 'thanks-you-counter-button'); ?>" href="<?php echo get_permalink($post_id);?>"><?php echo $post_title; ?></a>
<?php
					$thankyou_actions = array();					
					$thankyou_actions['view'] = '<span class="view"><a href="'.get_permalink($post_id).'" title="' . esc_attr(sprintf(__('View "%s"', 'thanks-you-counter-button'), $post_title)) . '" rel="permalink">' . __('View Post', 'thanks-you-counter-button') . '</a>';
          $thankyou_actions['edit'] = '<span class="view"><a href="'.THANKS_WP_ADMIN_URL.'/post.php?action=edit&post='.$post_id.'" title="' . esc_attr(sprintf(__('Edit "%s"', 'thanks-you-counter-button'), $post_title)) . '" rel="permalink">' . __('Edit Post', 'thanks-you-counter-button') . '</a>';
          if ( current_user_can('edit_post', $post_id) ) {
						$thankyou_actions['reset'] = '<span class="view"><a class="submitdelete warning" title="'.esc_attr(__('Reset this post counter', 'thanks-you-counter-button')).'"
                                            href="javascript:void(0);" onclick="resetCounter('.$post_id.',\''.thanks_esc_js(sprintf( __("You are about to reset this post '%s' thanks counter. Click 'Cancel' to do nothing, 'OK' to reset it.", 'thanks-you-counter-button'), $post_title )).'\',1);">'.__('Reset Counter', 'thanks-you-counter-button').'</a>';
					}
					echo '<div class="row-actions">';
					echo implode(' | </span>', $thankyou_actions);
					echo '</div>';
?>
    </td>
    <td class="txt_right" ><?php echo ($thanksQuant) ? $thanksQuant : 0; ?></td>
  </tr>
</table>
			</td>
		</tr>
	  <tr>
			<td>
<div class="tablenav">
<?php
  $page_links = paginate_links(array(
    'base' => add_query_arg('pageips', '%#%'),
    'format' => '',
    'prev_text' => '&laquo;',
    'next_text' => '&raquo;',
    'total' => $maxNumPages,
    'current' => $_GET['pageips']
  ));

  if ( $page_links ) {         
?>
  <div style="clear: both;"></div>
<div class="tablenav-pages">
<?php
  $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s','thanks-you-counter-button' ) . '</span>%s',
	number_format_i18n(max($_GET['pageips'] - 1, 0)*$rowsPerStatPage + 1),
	number_format_i18n(min( $_GET['pageips']*$rowsPerStatPage, $thankIPs)),
	number_format_i18n($thankIPs),	$page_links );
  echo $page_links_text;
?>
</div>
<?php
  }
?>
</div>
<table class="widefat fixed" cellspacing="0">
	<thead>
		<tr>
		  <th style="width: 130px;text-align:center;"><?php _e('IP address', 'thanks-you-counter-button'); ?></a></th>
		  <th style="width: 160px;text-align:center;"><?php _e('Last Thank Date', 'thanks-you-counter-button'); ?></a></th>
		</tr>
	</thead>
	<tbody id="thanks_IPs_body">
<?php
$date_format = get_option('date_format');
$time_format = get_option('time_format');
$i = 0;
foreach ($records as $record) {
  if ($i & 1) {
    $rowClass = 'alternate';
  } else {
    $rowClass = '';
  }
  $i++;
  $updated = mysql2date($date_format.' '.$time_format, $record->updated, true);
?>
  <tr class="<?php echo $rowClass; ?>">
    <td class="txt_center"><?php echo ($record->ip_address) ? '<a title="'.__('Look up IP country', 'thanks-you-counter-button').'" href="http://www.shinephp.com/ip-to-country/?ip='.$record->ip_address.'" target="_blank">'.$record->ip_address.'</a>' : __('Not available', 'thanks-you-counter-button'); ?></td>
    <td class="txt_center"><?php echo ($updated)?$updated:'&nbsp;'; ?></td>
  </tr>
<?php
}
?>
	</tbody>
</table>
<?php
if ( $page_links ) {
?>
   <div class="tablenav-pages">
      <?php echo $page_links_text; ?>
   </div>
<?php
}
?>
			</td>
		</tr>
</table>
	</form>
</div>

<?php
} else {

if (!isset($_GET['paged']) || !$_GET['paged'] || !is_numeric($_GET['paged'])) {
  if (isset($_SESSION['thanks_paged']) && $_SESSION['thanks_paged'] && is_numeric($_SESSION['thanks_paged'])) {
    $page_number = $_SESSION['thanks_paged'];
  } else {
    $page_number = 1;
    $_SESSION['thanks_paged'] = 1;
  }
} else {
	$page_number = $_GET['paged'];
  $_SESSION['thanks_paged'] = $page_number;
}

if (!isset($_GET['sortfield'])) {
  if (isset($_SESSION['thanks_sortfield'])) {
    $sortField = $_SESSION['thanks_sortfield'];
  } else {
    $sortField = 'quant';
    $_SESSION['thanks_sortfield'] = $sortField;
  }
} else {
  $sortField = $_GET['sortfield'];
  $_SESSION['sortfield'] = $sortField;
}

if (!isset($_GET['sortdir'])) {
  $sortDir = 'desc';
} else {
  $sortDir = $_GET['sortdir'];
  if ($sortDir!='asc' && $sortDir!='desc') {
    $sortDir = 'desc';
  }
}

$where1 = '';
if (isset($_GET['month'])) {
  $month = (int)$_GET['month'];
  $_SESSION['thanks_month'] = $month;
} else if (isset($_SESSION['thanks_month'])) {
  $month = $_SESSION['thanks_month'];
}  else {
  $month = 0;
  $_SESSION['thanks_month'] = 0;
}
if ($month) {
  $where1 = " and DATE_FORMAT(post_date, '%Y%m')=$month";
}

$where2 = '';
if (isset($_GET['cat'])) {
  $cat_id = (int)$_GET['cat'];
  $_SESSION['thanks_cat'] = $cat_id;
} else if (isset($_SESSION['thanks_cat'])) {
  $cat_id = $_SESSION['thanks_cat'];
}  else {
  $cat_id = 0;
  $_SESSION['thanks_cat_id'] = 0;
}

if (!isset($_GET['zeroshow'])) {
  $where1 .= ' and counters.quant>0';
}

if ($cat_id) {
  $query = "select distinct posts.ID
              from $wpdb->posts posts
                left join $wpdb->term_relationships term_relationships on (posts.ID=term_relationships.object_id)
                left join $wpdb->term_taxonomy term_taxonomy on (term_relationships.term_taxonomy_id=term_taxonomy.term_taxonomy_id)
              where (posts.post_type not in ($thanksNotCountablePostTypes)) and
                   (term_taxonomy.term_id=$cat_id or term_taxonomy.parent=$cat_id) and term_taxonomy.taxonomy='category'";
  $records = $wpdb->get_results($query);
  if ($wpdb->last_error) {
    thanks_logEvent(THANKS_ERROR.":\n".$wpdb->last_error);
    return;
  }
  $posts = array();
  foreach ($records as $record) {
    $posts[] = $record->ID;
  }
  $where2 = implode($posts, ',');
  if ($where2) {
    $where2 = ' and posts.ID in ('.$where2.') ';
  }
}

$query = "select count(posts.ID)
            from $wpdb->posts posts
              left join $thanksCountersTable counters on counters.post_id=posts.ID
            where 1=1 $where1 $where2 and (posts.post_type not in ($thanksNotCountablePostTypes))";
$thankedPosts = $wpdb->get_var($query);
if ($wpdb->last_error) {
  thanks_logEvent(THANKS_ERROR.":\n".$wpdb->last_error);
  return;
}

$maxNumPages = (int) ($thankedPosts / $rowsPerStatPage);
$rest = $thankedPosts / $rowsPerStatPage - $maxNumPages;
if ($rest>0) {
  $maxNumPages += 1;
}
if ($page_number>$maxNumPages) {
  $page_number = $maxNumPages;
}
$fromRecord = max(0,($page_number - 1))*$rowsPerStatPage;

$query = "select posts.ID, posts.post_title, counters.quant, counters.updated
            from $wpdb->posts posts
              left join $thanksCountersTable counters on counters.post_id=posts.ID
            where 1=1 $where1 $where2 and (posts.post_type not in ($thanksNotCountablePostTypes))
            order by $sortField $sortDir
            limit $fromRecord, $rowsPerStatPage";
$records = $wpdb->get_results($query);
if ($wpdb->last_error) {
  thanks_logEvent(THANKS_ERROR.":\n".$wpdb->last_error);
  return;
}
$foundPosts = count($records);

?>

<div class="tablenav">
<?php
$base = remove_query_arg('updated');
$page_links = paginate_links(array(
	'base' => add_query_arg('paged', '%#%', $base),
	'format' => '',
	'prev_text' => '&laquo;',
	'next_text' => '&raquo;',
	'total' => $maxNumPages,
	'current' => $page_number
));

?>
<div class="alignleft actions">
<form id="posts-filter" action="" method="get">
  <input type="hidden" name="page" value="thankyou.php" />
  <input type="hidden" name="paged" value="<?php echo $page_number; ?>" />

<?php // view filters
if (!is_singular()) {
$arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth
                FROM $wpdb->posts
                WHERE (post_type='post' or post_type='page')
                ORDER BY post_date DESC";

$arc_result = $wpdb->get_results( $arc_query );
if ($wpdb->last_error) {
  thanks_logEvent(THANKS_ERROR.":\n".$wpdb->last_error);
  return;
}
$month_count = count($arc_result);
if ($month_count && !(1 == $month_count && 0 == $arc_result[0]->mmonth)) {
?>
<select name='month'>
<option<?php selected( $month, 0 ); ?> value='0'><?php _e('Show all post dates','thanks-you-counter-button'); ?></option>
<?php
foreach ($arc_result as $arc_row) {
	if ( $arc_row->yyear == 0 )
		continue;
	$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

	if ( $arc_row->yyear . $arc_row->mmonth == $month )
		$default = ' selected="selected"';
	else
		$default = '';

	echo "<option$default value='" . esc_attr("$arc_row->yyear$arc_row->mmonth") . "'>";
	echo $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear";
	echo "</option>\n";
}
?>
</select>
<?php 
} 
?>

<?php
$dropdown_options = array('show_option_all' => __('View all categories','thanks-you-counter-button'), 'hide_empty' => 1, 'hierarchical' => 1,
	'show_count' => 1, 'orderby' => 'name', 'selected' => $cat_id);
wp_dropdown_categories($dropdown_options);

if (isset($_GET['zeroshow'])) {
  $checked = 'checked="checked"';
} else {
  $checked = '';
}
?>
&nbsp;<input type="checkbox" id="zeroshow" name="zeroshow" value="1" <?php echo $checked; ?>/>&nbsp;<span style="font-size:0.85em;"><?php _e('Show Posts without Thanks', 'thanks-you-counter-button');?></span>&nbsp;&nbsp;&nbsp;
<span style="font-size:0.85em;"><?php echo _e('Rows per page: ', 'thanks-you-counter-button'); ?></span>
<select name="rowsperstatpage" id="rowsperstatpage">
  <option value="5" <?php echo thanks_optionSelected($rowsPerStatPage, 5); ?> >5</option>
  <option value="10" <?php echo thanks_optionSelected($rowsPerStatPage, 10); ?> >10</option>
  <option value="15" <?php echo thanks_optionSelected($rowsPerStatPage, 15); ?> >15</option>
  <option value="20" <?php echo thanks_optionSelected($rowsPerStatPage, 20); ?> >20</option>
  <option value="25" <?php echo thanks_optionSelected($rowsPerStatPage, 25); ?> >25</option>
  <option value="30" <?php echo thanks_optionSelected($rowsPerStatPage, 30); ?> >30</option>
  <option value="50" <?php echo thanks_optionSelected($rowsPerStatPage, 50); ?> >50</option>
</select>
&nbsp;&nbsp;&nbsp;
<input type="submit" id="post-query-submit" value="<?php _e('Refresh','thanks-you-counter-button'); ?>" class="button-secondary" />
</form>
</div>
<?php
  if ( $page_links ) {         
?>
  <div style="clear: both;"></div>
<div class="tablenav-pages">
<?php
  $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s','thanks-you-counter-button' ) . '</span>%s',
	number_format_i18n(max($page_number - 1, 0)*$rowsPerStatPage + 1),
	number_format_i18n(min( $page_number*$rowsPerStatPage, $thankedPosts)),
	number_format_i18n($thankedPosts),	$page_links );
  echo $page_links_text;
?>
</div>
<?php
  }
}
?>
</div>

<?php
if ($foundPosts) {
  $link = remove_query_arg(array('updated', 'sortdir','sortfield'));  // remove 'updated=true' from URL params in case user go to Statistics tab after saving Settings
  $link = remove_query_arg('updated', $link);
  if (strpos($link, 'paged')===false) {
    $link = add_query_arg('paged', $page_number, $link);
  }
  $linkQuant = $link;
  $linkUpdated = $link;
  if ($sortField=='quant') {
    if ($sortDir=='asc') {
      $newSortDir = 'desc';
      $newSortDirTitle = __('descending', 'thanks-you-counter-button');
      $currentSortDirTitle = __('Ascending order', 'thanks-you-counter-button');
      $currentSortDirImg = '<img src="'.THANKS_PLUGIN_URL.'/images/asc-sort-arrow.png" alt="'.$currentSortDirTitle.'" title="'.$currentSortDirTitle.'" />';
    } else {
      $newSortDir = 'asc';
      $newSortDirTitle = __('ascending', 'thanks-you-counter-button');
      $currentSortDirTitle = __('Descending order', 'thanks-you-counter-button');
      $currentSortDirImg = '<img src="'.THANKS_PLUGIN_URL.'/images/desc-sort-arrow.png" alt="'.$currentSortDirTitle.'" title="'.$currentSortDirTitle.'" />';
    }
    $quantSortDirImg = $currentSortDirImg;
    $updatedSortDirImg = '';
    $linkQuant = add_query_arg('sortdir', $newSortDir, $link);
  } else {  
    if ($sortDir=='asc') {
      $newSortDir = 'desc';
      $newSortDirTitle = __('descending', 'thanks-you-counter-button');
      $currentSortDirTitle = __('Ascending order', 'thanks-you-counter-button');
      $currentSortDirImg = '<img src="'.THANKS_PLUGIN_URL.'/images/asc-sort-arrow.png" alt="'.$currentSortDirTitle.'" title="'.$currentSortDirTitle.'" />';
    } else {
      $newSortDir = 'asc';
      $newSortDirTitle = __('ascending', 'thanks-you-counter-button');
      $currentSortDirTitle = __('Descending order', 'thanks-you-counter-button');
      $currentSortDirImg = '<img src="'.THANKS_PLUGIN_URL.'/images/desc-sort-arrow.png" alt="'.$currentSortDirTitle.'" title="'.$currentSortDirTitle.'" />';
    }
    $linkUpdated = add_query_arg('sortdir', $newSortDir, $link);
    $quantSortDirImg = '';
    $updatedSortDirImg = $currentSortDirImg;
  }
  $newSortDirTitle = sprintf(__('Click to sort in %s order','thanks-you-counter-button'), $newSortDirTitle);
  
  $linkQuant = add_query_arg('sortfield', 'quant', $linkQuant);
  $linkUpdated = add_query_arg('sortfield', 'updated', $linkUpdated);
 
  
function thShow($linkQuant, $linkUpdated, $newSortDirTitle, $quantSortDirImg, $updatedSortDirImg) {
?>
  <tr>
    <th style="width: 70px;text-align:center;"><?php echo _e('Post Id', 'thanks-you-counter-button'); ?></th>
    <th style="text-align:center;"><?php _e('Post Title', 'thanks-you-counter-button'); ?></th>
    <th style="width: 130px;text-align:center;"><a href="<?php echo $linkQuant; ?>" title="<?php echo $newSortDirTitle; ?>"><?php echo __('Thanks Quant', 'thanks-you-counter-button').' '.$quantSortDirImg; ?></a></th>
    <th style="width: 160px;text-align:center;"><a href="<?php echo $linkUpdated; ?>" title="<?php echo $newSortDirTitle; ?>"><?php echo __('Last Thank Date', 'thanks-you-counter-button').' '.$updatedSortDirImg; ?></a></th>
  </tr>
<?php
}
// end of thShow()

?>
<script language="javascript" type="text/javascript">
  function resetCounter(post_id, message, page) {
    if (!confirm(message)) {
      return false;
    }

    el = document.getElementById('ajax_loader_stat');
    if (el!=undefined) {
      el.style.visibility = 'visible';
    }

    jQuery.ajax({
      type: "POST",
      url: ajaxurl,
      data: { post_id: post_id,
              action: 'thanks_button',
              subaction: 'reset',
              _ajax_nonce: ThanksSettings.ajax_nonce
    },
    success: function(msg){
      if (msg.indexOf('error')<0) {
        el = document.getElementById('thanksQuant_'+ post_id);
        if (el!=undefined) {
          el.innerHTML = '0';
        }
      } else {
        alert(msg);
      }
      el = document.getElementById('ajax_loader_stat');
      if (el!=undefined) {
        el.style.visibility = 'hidden';
      }
    },
    error: function(msg) {
      alert(msg);
      el = document.getElementById('ajax_loader_stat');
      if (el!=undefined) {
        el.style.visibility = 'hidden';
      }
    }
    });

  }
  // resetCounter()

</script>
<div id="ajax_loader_stat" style="display:inline;visibility: hidden;"><img alt="ajax loader" src="<?php echo THANKS_PLUGIN_URL.'/images/ajax-loader.gif';?>" /></div>
<table class="widefat fixed" cellspacing="0">
   <thead>
<?php thShow($linkQuant, $linkUpdated, $newSortDirTitle, $quantSortDirImg, $updatedSortDirImg); ?>
   </thead>
<?php
$date_format = get_option('date_format');
$time_format = get_option('time_format');
$i = 0;
foreach ($records as $record) {
  if ($i & 1) {
    $rowClass = 'alternate';
  } else {
    $rowClass = '';
  }
  $i++;
  $updated = mysql2date($date_format, $record->updated, true).'<br/>'.mysql2date($time_format, $record->updated, true);
?>
  <tr class="<?php echo $rowClass; ?>">
    <td class="txt_right" width="5%"><?php echo $record->ID; ?></td>
    <td class="txt_left" style="padding-left:10px;"><a title="<?php echo esc_attr(sprintf(__('View statistics details for "%s"', 'thanks-you-counter-button'), $record->post_title)); ?>" href="<?php echo './tools.php?page=thankyou.php&post_id='.$record->ID.'&paged='.$page_number.'#statistics';?>"><?php echo $record->post_title; ?></a>
<?php
					$thankyou_actions = array();
					$thankyou_actions['details'] = '<span class="view"><a href="./tools.php?page=thankyou.php&post_id='.$record->ID.'&paged='.$page_number.'#statistics" title="' . esc_attr(sprintf(__('View statistics details for "%s"', 'thanks-you-counter-button'), $record->post_title)) . '" rel="permalink">' . __('View details', 'thanks-you-counter-button') . '</a>';
          $thankyou_actions['view'] = '<span class="view"><a href="'.get_permalink($record->ID).'" title="' .esc_attr(sprintf(__('View "%s"', 'thanks-you-counter-button'), $record->post_title)) . '" rel="permalink">' . __('View Post', 'thanks-you-counter-button') . '</a>';
          $thankyou_actions['edit'] = '<span class="view"><a href="'.THANKS_WP_ADMIN_URL.'/post.php?action=edit&post='.$record->ID.'" title="' . esc_attr(sprintf(__('Edit "%s"', 'thanks-you-counter-button'), $record->post_title)) . '" rel="permalink">' . __('Edit Post', 'thanks-you-counter-button') . '</a>';
          if ( current_user_can('edit_post', $record->ID) ) {
						$thankyou_actions['reset'] = '<span class="delete"><a class="submitdelete" title="'.esc_attr(__('Reset this post counter', 'thanks-you-counter-button')).'"
                                            href="javascript:void(0);" onclick="resetCounter('.$record->ID.',\''.thanks_esc_js(sprintf( __("You are about to reset this post '%s' thanks counter.\n Click 'Cancel' to do nothing, 'OK' to reset it.", 'thanks-you-counter-button'), $record->post_title )).'\','.$page_number.');">'.__('Reset Counter', 'thanks-you-counter-button').'</a>';
					}
					echo '<div class="row-actions">';
					echo implode(' | </span>', $thankyou_actions);
					echo '</div>';
?>
    </td>
    <td class="txt_right" width="5%" id="<?php echo 'thanksQuant_'.$record->ID; ?>"><?php echo ($record->quant) ? $record->quant : 0; ?></td>
    <td class="txt_center" width="5%"><?php echo $updated; ?></td>
  </tr>
<?php
}

?>
  <tfoot>
<?php thShow($linkQuant, $linkUpdated, $newSortDirTitle, $quantSortDirImg, $updatedSortDirImg); ?>
  </tfoot>
</table>

<?php
if ( $page_links ) {     
?>
		<div class="tablenav">
				<div class="tablenav-pages">
					<?php
					$page_links_text = sprintf('<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s', 'thanks-you-counter-button') . '</span>%s', number_format_i18n(( $page_number - 1 ) * $rowsPerStatPage + 1), number_format_i18n(min($page_number * $rowsPerStatPage, $thankedPosts)), number_format_i18n($thankedPosts), $page_links
					);
					echo $page_links_text;
					?>
				</div>
			</div>
<?php
}

} else { // if ($foundPosts) ...
?>
<div class="clear"></div>
<p>  <?php _e('No posts with thanks found','thanks-you-counter-button') ?></p>

<?php
}

}
