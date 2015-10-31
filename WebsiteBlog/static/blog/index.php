<?php
  // ---------------
  // INITIALIZE PAGE
  // ---------------
  require_once('scripts/sb_functions.php');
  
  // Login
  global $logged_in;
  $logged_in = logged_in( false, true );
  
  // Create a session for the anti-spam cookie
  if ( !session_id() ) {
    session_start();
  }
  $_SESSION['cookies_enabled'] = '1';
  
  // Read configuration file
  read_config();
  
  // Load language strings
  require_once('languages/' . $blog_config[ 'blog_language' ] . '/strings.php');
  sb_language( 'index' );
  
  // ---------------
  // POST PROCESSING
  // ---------------
  
  // Verify information being passed in:
  //
  // index.php?d=12&m=11&y=05
  // index.php?entry=entry051128-213804
  // index.php?d=28&m=11&y=05&category=3
  // index.php?category=3
  //
  global $is_permalink;
  $is_permalink = true;
  
  $temp_year = null;
  if ( array_key_exists( 'y', $_GET ) ) {
    $is_permalink = false;
    if ( strpos( $_GET[ 'y' ], array( '/', '.', '\\', '%', '#', ';' ) ) === false && strlen( $_GET[ 'y' ] ) == 2 ) {
      $temp_year = $_GET[ 'y' ];
    }
  }
  $temp_month = null;
  if ( array_key_exists( 'm', $_GET ) ) {
    $is_permalink = false;
    if ( strpos( $_GET[ 'm' ], array( '/', '.', '\\', '%', '#', ';' ) ) === false && strlen( $_GET[ 'm' ] ) == 2 ) {
      $temp_month = $_GET[ 'm' ];
    }
  }
  $temp_day = null;
  if ( array_key_exists( 'd', $_GET ) ) {
    $is_permalink = false;
    if ( strpos( $_GET[ 'd' ], array( '/', '.', '\\', '%', '#', ';' ) ) === false && strlen( $_GET[ 'd' ] ) == 2 ) {
      $temp_day = $_GET[ 'd' ];
    }
  }
  $temp_entry = null;
  if ( array_key_exists( 'entry', $_GET ) ) {
    if ( strpos( $_GET[ 'entry' ], array( '/', '.', '\\', '%', '#', ';' ) ) === false && strlen( $_GET[ 'entry' ] ) == 18 ) {
      $temp_entry = $_GET[ 'entry' ];
    }
  } else {
    $is_permalink = false;
    
    // This checks to index.php?entry061209-224649 or just ?entry061209-224649
    if (isset($_GET) && count($_GET)==1) {
      $keys = array_keys($_GET);
      $temp_entry = $keys[0];
      if ( strpos( $temp_entry, array( '/', '.', '\\', '%', '#', ';' ) ) === false && strlen( $temp_entry ) == 18 ) {
        $is_permalink = true;
      } else {
        $temp_entry = null;
      }
    }
  }
  
  // Month / Year
  if ( !isset( $temp_year ) || !isset( $temp_month ) ) {
    // Set the $month, $year, $day globals...
    get_latest_entry();
  } else {
    // Grab $year and $month from URL
    global $month, $year;
    $year = $temp_year;
    $month = $temp_month;
  }
  
  // Day
  if ( isset( $temp_day ) ) {
    global $day;
    $day = $temp_day;
  }
  
  // Entry
  if ( isset( $temp_entry) ) {
    global $entry;
    $entry = $temp_entry;
  }
	
	// Category
  if ( array_key_exists( 'category', $_GET ) ) {
    global $category;
    $category = $_GET[ 'category' ];
    $is_permalink = false;
  }
  
	global $lang_string, $sb_info, $blog_config;
	
	// Check the option for specific category on first page...
	// If nothing was passed into this page, then use the default
	// category (cause it has to be the first page). WILL NEVER
	// OVERRIDE THE CATEGORY IF PASSED IN
	if ( array_key_exists( 'category', $_GET ) == FALSE ) {
		if ( $blog_config[ 'blog_enable_start_category' ] == 1 ) { 
		  $category = $blog_config[ 'blog_enable_start_category_selection' ];
		}
	}    
  
  // ------------
  // PAGE CONTENT
  // ------------
  function page_content() {
    global $month, $year, $day, $category, $logged_in, $entry, $is_permalink;
    
    $content = read_entries( $month, $year, $day, $logged_in, $entry, $category, $is_permalink );
    echo( $content );
  }
  
  // ----
  // HTML
  // ----
?>
  <?php echo( get_init_code() ); ?>
  <?php require_once('themes/' . $blog_theme . '/user_style.php'); ?>

  <?php
    if (!isset($_GET['entry'])) {
      echo( '<title>' . $blog_config[ 'blog_title' ] . '</title>');
    } else {
      echo( '<title>' . $blog_config[ 'blog_title' ] . ' - ' . get_entry_title( substr( $_GET[ 'entry' ], 5, 2 ), substr ( $_GET[ 'entry' ], 7, 2 ), $_GET[ 'entry' ] ) . '</title>');
    }
  ?>
</head>
  <?php 
    // ------------
    // BEGIN OUTPUT
    // ------------
    theme_pagelayout();
  ?>
  
  <div id="disqus_thread"></div>
<script>
	var t = <?php echo isset($_GET['entry']) ?>;
    /**
     *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
     *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables
     */
   
    var disqus_config = function () {
        this.page.url = 'http://william.famille-blum.org/blog/index.php?entry=<?php echo $_GET[ 'entry' ] ?>';  // Replace PAGE_URL with your page's canonical URL variable
        this.page.identifier = '<?php echo  $_GET[ 'entry' ] ?>'; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
    };
	
	(function() {  // DON'T EDIT BELOW THIS LINE
		if(t) {
			var d = document, s = d.createElement('script');
			
			s.src = '//williamblum.disqus.com/embed.js';
			
			s.setAttribute('data-timestamp', +new Date());
			(d.head || d.body).appendChild(s);
		}
	})();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>

</html>
