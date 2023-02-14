<?php
/**
 * GeneratePress.
 *
 * Please do not make any edits to this file. All edits should be done in a child theme.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Set our theme version.
define( 'GENERATE_VERSION', '3.2.4' );

if ( ! function_exists( 'generate_setup' ) ) {
	add_action( 'after_setup_theme', 'generate_setup' );
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since 0.1
	 */
	function generate_setup() {
		// Make theme available for translation.
		load_theme_textdomain( 'generatepress' );

		// Add theme support for various features.
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link', 'status' ) );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );

		$color_palette = generate_get_editor_color_palette();

		if ( ! empty( $color_palette ) ) {
			add_theme_support( 'editor-color-palette', $color_palette );
		}

		add_theme_support(
			'custom-logo',
			array(
				'height' => 70,
				'width' => 350,
				'flex-height' => true,
				'flex-width' => true,
			)
		);

		// Register primary menu.
		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'generatepress' ),
			)
		);

		/**
		 * Set the content width to something large
		 * We set a more accurate width in generate_smart_content_width()
		 */
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 1200; /* pixels */
		}

		// Add editor styles to the block editor.
		add_theme_support( 'editor-styles' );

		$editor_styles = apply_filters(
			'generate_editor_styles',
			array(
				'assets/css/admin/block-editor.css',
			)
		);

		add_editor_style( $editor_styles );
	}
}

/**
 * Get all necessary theme files
 */
$theme_dir = get_template_directory();

require $theme_dir . '/inc/theme-functions.php';
require $theme_dir . '/inc/defaults.php';
require $theme_dir . '/inc/class-css.php';
require $theme_dir . '/inc/css-output.php';
require $theme_dir . '/inc/general.php';
require $theme_dir . '/inc/customizer.php';
require $theme_dir . '/inc/markup.php';
require $theme_dir . '/inc/typography.php';
require $theme_dir . '/inc/plugin-compat.php';
require $theme_dir . '/inc/block-editor.php';
require $theme_dir . '/inc/class-typography.php';
require $theme_dir . '/inc/class-typography-migration.php';
require $theme_dir . '/inc/class-html-attributes.php';
require $theme_dir . '/inc/class-theme-update.php';
require $theme_dir . '/inc/class-rest.php';
require $theme_dir . '/inc/deprecated.php';

if ( is_admin() ) {
	require $theme_dir . '/inc/meta-box.php';
	require $theme_dir . '/inc/class-dashboard.php';
}

/**
 * Load our theme structure
 */
require $theme_dir . '/inc/structure/archives.php';
require $theme_dir . '/inc/structure/comments.php';
require $theme_dir . '/inc/structure/featured-images.php';
require $theme_dir . '/inc/structure/footer.php';
require $theme_dir . '/inc/structure/header.php';
require $theme_dir . '/inc/structure/navigation.php';
require $theme_dir . '/inc/structure/post-meta.php';
require $theme_dir . '/inc/structure/sidebars.php';


function normalizeInputString($data) {
  // Trim whitespaces
  $data = trim($data);
  // Convert to lowercase
  $data = strtolower($data);
  // Remove any malicious characters
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}
function normalizeInputNumber($data) {
  // Trim whitespaces
  $data = trim($data);
  // Convert to lowercase
  $data = strtolower($data);
  // Remove any malicious characters
  $data = filter_var($data, FILTER_SANITIZE_NUMBER_INT);
	// Initialize data type
	$data = intval($data);
  return $data;
}

function normalizeInputPaged($data){
	// Trim whitespaces
	$data = trim($data);
	// Convert to lowercase
	$data = strtolower($data);
	// remote ?
	$data = str_replace('?', '', $data);
	// Remove any malicious characters
	$data = filter_var($data, FILTER_SANITIZE_NUMBER_INT);
	// Initialize data type
	$data = intval($data);
	return $data;
}

/**
 * Load search ajax
 */
function searchHandlerData() {
	//initialize && normalized data input
	$page_size = 10;
	$offset = ( $page - 1 ) * $page_size;
	$paramsKeyword= $_POST['paramsKeyword'] ? normalizeInputString($_POST['paramsKeyword']) : null;
	$paramsYear= $_POST['paramsYear'] ? normalizeInputNumber($_POST['paramsYear']) : null;
	$paramsMonth= $_POST['paramsMonth'] ? normalizeInputNumber($_POST['paramsMonth']) : null;
	$paramsCategory= array_filter($_POST['paramsCategory']) != null ? $_POST['paramsCategory'] : null;
	$paramsOrderBy = $_POST['paramsOrderBy'] ? normalizeInputString($_POST['paramsOrderBy']) : 'DESC';
	$paged = $_POST['paramsPaged'] ? normalizeInputPaged($_POST['paramsPaged']) : '1';
	//!initialize && normalized data input
	$params = array(
    'post_type' => 'post',
		'post_status' => 'publish',
		'monthnum' => $paramsMonth,
		'year' => $paramsYear,
		's' => $paramsKeyword,
		'orderby' => 'ID',
		'order' => $paramsOrderBy,
		'paged' => $paged,
		'posts_per_page' => $page_size,
  );
	if(is_array($paramsCategory)){
		$params['tax_query']= array(
			array(
					'taxonomy' => 'category', //double check your taxonomy name in you dd
					'field'    => 'id',
					'terms'    => $paramsCategory,
			),
		);
	}
  $dataPost = new WP_Query($params);
  $results  = array();
  if ( $dataPost->have_posts() ) :
    while ( $dataPost->have_posts() ) : $dataPost->the_post();
      $result  = array(
				'title' => get_the_title(),
				'permalink' => get_permalink(),
				'excerpt' => get_the_excerpt(),
				'images' => get_the_post_thumbnail()
			);
			array_push($results, $result);
    endwhile;
  else  :
     $results  = null;
  endif;
	$total_posts  = $dataPost->found_posts;
	$total_pages = ceil( $total_posts / $page_size );
	$nextpage = $paged+1;
			$prevouspage = $paged-1;
			$total = $dataPost->max_num_pages;
			$pagination_args = array(
			'base'               => '%_%',
			'format'             => '?%#%',
			'total'              => $total,
			'current'            => $paged,
			'show_all'           => false,
			'end_size'           => 1,
			'mid_size'           => 2,
			'prev_next'          => true,
			'prev_text'       => __('<span class="prev-next" data-attr="'.$prevouspage.'">&laquo;</span>'),
			'next_text'       => __('<span class="prev-next" data-attr="'.$nextpage.'">&raquo;</span>'),
			'type'               => 'plain',
			'add_args'           => false,
			'add_fragment'       => '',
			'before_page_number' => '',
			'after_page_number'  => ''
	);
	$paginate_links = paginate_links($pagination_args);
	$paginate_links = str_replace('href=', 'href="#" data-paged=', $paginate_links);
	// if ($paginate_links) {
	// // 		echo "<div id='pagination' class='pagination ajax_pagination'>";
	// // 		echo $paginate_links;
	// // 		echo "</div>";
	// }
	$response = array(
    'total_posts' => $total_posts,
    'total_pages' => $total_pages,
    'current_page' => $paged,
    'posts' => $results,
		'pagination' => $paginate_links
	);
	echo json_encode( $response );
  wp_reset_query();
  exit;
}
add_action('wp_ajax_searchHandlerData', 'searchHandlerData');
add_action('wp_ajax_nopriv_searchHandlerData', 'searchHandlerData');

/**
 * add js handler search
 */
add_action( 'wp_footer', 'searchjs' );
function searchjs(){
  ?>
  <script>
		$ = jQuery.noConflict(false);
		$("#searchForm").submit(function(event) {
		event.preventDefault(); // prevent default form submit
		// get form data
		let dataKeyword = $("#keyword").val() ? $("#keyword").val() : null;
		let dataYear = $("#year").val() ? $("#year").val() : null;
		let dataMonth = $("#month").val() ? $("#month").val() : null;
		let dataCategory = $('input:checked').map(function(i, e) {return e.value}).toArray();
		let dataOrderBy = $("#order_by").val() ? $("#order_by").val() : 'desc';
		let dataPaged = $("#formPagination").val() ? $("#formPagination").val() : '1';
		jQuery.ajax({
				type: 'POST',
				url: '/wp-admin/admin-ajax.php',
				dataType: 'json',
				cache: false,
				data: {
					action: 'searchHandlerData',
					paramsKeyword: dataKeyword,
					paramsYear: dataYear,
					paramsMonth: dataMonth,
					paramsCategory: dataCategory,
					paramsOrderBy: dataOrderBy,
					paramsPaged: dataPaged,
				},
				beforeSend: function() {
					console.log(dataPaged);
        },
				success: function (response ) {
					// data post
						var postsContainer = document.getElementById( 'data-search' );
						var postsHtml = '';
						if(response.posts instanceof Array){
							for ( var i = 0; i < response.posts.length; i++ ) {
									var post = response.posts[i];
									var postHtml = '<div class="post">';
									postHtml += '<h2>' + post.title + '</h2>';
									postHtml += '<p>' + post.excerpt + '</p>';
									postHtml += '<a href="' + post.permalink + '">Read more</a>';
									postHtml += '</div>';
									postsHtml += postHtml;
							}
						}
						else {
							var postHtml = '<p class="post"> No Post </p>';
							postsHtml += postHtml;
						}
						postsContainer.innerHTML = postsHtml;
					// !data post
					// data count
						var postsCount = document.getElementById( 'data-count' );
						var countHtml = '';
						if(response.total_posts){
							var countHtml = '<h2>' + response.total_posts + '</h2>';
						}
						else{
							var countHtml = 0;
						}
						postsCount.innerHTML = countHtml;
					// !data count
					//data pagination
						var postsPagination = document.getElementById( 'data-pagination' );
						var PaginationHtml = '';
						if(response.pagination){
							var PaginationHtml = '<div id="pagination" class="pagination ajax_pagination">';
									PaginationHtml += response.pagination;
									PaginationHtml += '</div>';
						}

					//!data pagination
						postsPagination.innerHTML = PaginationHtml;
					//$('#data-search').empty().append(res);
				}
			});
		});
		// run order by change event
		function changeOrderBy(event) {
			$("#searchForm").submit();
    }
		// pagination on change event
		$(document).ajaxComplete(function() {
				$('#pagination>a.page-numbers[href="#"]').on("click",function(e) {
				e.preventDefault();
				var pageClick = $(this).attr('data-paged');
				$('#formPagination').val(pageClick);
				$("#searchForm").submit();
			});
		});
		// reset pagination if reselect form on change event
		$(document).ready(function() {
			$('#year, #month, #keyword, .categoryCheckbox').change(function() {
				$('#formPagination').val(1);
			});
		});
	</script>
  <?php
}
