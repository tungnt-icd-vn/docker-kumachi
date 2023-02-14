<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>
  <main class="site-main" id="main">
    <form id="searchForm" >
        <input type="search" name="keyword" id="keyword" aria-label="search keyword" placeholder="Search.." value=''>
        <select id='year' name="year" aria-label="search year">
          <option selected value=''>--Select Year--</option>
          <option value='2019'>2019</option>
          <option value='2020'>2020</option>
          <option value='2022'>2022</option>
          <option value='2023'>2023</option>
          <option value='2024'>2024</option>
          <option value='2025'>2025</option>
        </select>
        <select id='month' name="month" aria-label="search month" >
          <option selected value=''>--Select Month--</option>
          <option value='1'>Janaury</option>
          <option value='2'>February</option>
          <option value='3'>March</option>
          <option value='4'>April</option>
          <option value='5'>May</option>
          <option value='6'>June</option>
          <option value='7'>July</option>
          <option value='8'>August</option>
          <option value='9'>September</option>
          <option value='10'>October</option>
          <option value='11'>November</option>
          <option value='12'>December</option>
        </select>
        <fieldset>
          <input type="checkbox" aria-label="search category"  name="category" value="" checked /> All Post
          <?php
          $dataCategory = get_terms('category', array('hide_empty' => false));;
          foreach($dataCategory as $category) :
          ?>
           <input class="categoryCheckbox" type="checkbox" aria-label="search category <?php echo $category->name; ?>"  name="category" value="<?php echo $category->term_id; ?>" /><?php echo $category->name; ?>
          <?php
          endforeach;
          ?>
        </fieldset>
        <input type="hidden" value="1" id="formPagination" name="formPagination" hide>
        <input type="submit" value="Submit">
    </form>
    <select id='order_by' name="order_by" onchange="changeOrderBy()" aria-label="order by" >
          <option value='desc' selected>DESC</option>
          <option  value='asc'>ASC</option>
    </select>
    <div id="data-count"></div>
    <div id="data-search">xxx</div>
    <!-- //data -->

  </main>
<?php
get_footer();
