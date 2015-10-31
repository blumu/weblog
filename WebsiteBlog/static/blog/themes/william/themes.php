<?php
  // --------------------------
  // Simple PHP Blog Theme File
  // --------------------------
  //
  // Name: Default/Classic Theme
  // Author: Alexander Palmo
  // Version: 0.5.0
  //
  // Description:
  // This the is default theme for Simple PHP Blog. You can use
  // this as a template for your own themes.
  //
  // All graphic will be relative to the base-url (i.e. the folder
  // where index.php is located.)

  theme_init();

  // ---------------
  // Theme Variables
  // ---------------
  function theme_init () {
    global $theme_vars;

    $theme_vars = array();

    // Optional:
    // "content_width" and "menu_width" area used internally
    // within the theme only. (optional but recommended.)
    $theme_vars[ 'content_width' ] = 550;
    $theme_vars[ 'menu_width' ] = 200;

    // Required:
    // "popup_window" "width" and "height" are used to determine
    // the size of window to open for the comment view.
    $theme_vars[ 'popup_window' ][ 'width' ] = $theme_vars[ 'content_width' ] + 50;
    $theme_vars[ 'popup_window' ][ 'height' ] = 600;

    // Optional:
    // "popup_window" "content_width" is only used internally.
    $theme_vars[ 'popup_window' ][ 'content_width' ] = $theme_vars[ 'content_width' ];

    // Required:
    // Determines the maximum with of images within a page.
    // Make sure this value is less then "content_width" or you
    // might have wrapping problems.
    $theme_vars[ 'max_image_width' ] = $theme_vars[ 'content_width' ] - 38;

    // ------------
    // CUSTOMIZATION
    // ------------
    // New 0.3.8
    $theme_vars[ 'menu_align' ] = 'right'; // Valid values are 'left' or 'right'
  }

  // Function:
  // theme_blogentry( $entry_array )
  //
  // Theme for Blog Entries
  // ----------------------
  // All data is stored the $entry_array associative array and
  // passed to the function. Keep in mind that multiple languages
  // are used. So, try not to hard-code 'english' strings. If
  // you are creating graphics for buttons, try to use icons
  // instead of words.
  //
  // (Please note, the "\n" at the end of the echo() command adds
  // a return charater in the HTML source. This is standard PHP
  // behavior. Note the '\n' will not work for this. It has to be
  // surrounded by double-quotes.)
  //
  // The array could contains the following keys:
  // $entry_array[ 'subject' ]          = String: Subject line
  // $entry_array[ 'date' ]             = String: Date posted in the appropriate language and format.
  // $entry_array[ 'entry' ]            = String: The body of the blog entry
  // $entry_array[ 'logged_in' ]       = Boolean: True if user is logged in (used to determine whether to show 'edit' and 'delete' buttons)
  // $entry_array[ 'edit' ][ 'url' ]      = String: URL
  // $entry_array[ 'edit' ][ 'name' ]     = String: The word 'edit' in the appropriate language.
  // $entry_array[ 'delete' ][ 'url' ]    = String: URL
  // $entry_array[ 'delete' ][ 'name' ]   = String: The word 'delete' in the appropriate language.
  // $entry_array[ 'comment' ][ 'url' ]   = String: URL
  // $entry_array[ 'comment' ][ 'name' ]  = String: This will be 'add comment', '1 comment', or '2 comments' in the appropriate language.
  // $entry_array[ 'comment' ][ 'count' ] = String: The number of 'views' in the appropriate language.
  // $entry_array[ 'count' ]            = Integer: Index of current entry (i.e. use this if you want to add a line after every entry except the last one...)
  // $entry_array[ 'maxcount' ]         = Integer: Total number of entries
  function theme_blogentry ( $entry_array, $mode='entry' ) { // New 0.4.8
    global $blog_config, $user_colors;

    $blog_content = "\n";

    // TRACKBACKS (RDF)
    if ( $blog_config[ 'blog_trackback_enabled' ] ) {
      $blog_content  .= '<!--' . "\n";
      $blog_content  .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"' . "\n";
      $blog_content  .= '         xmlns:dc="http://purl.org/dc/elements/1.1/"' . "\n";
      $blog_content  .= '         xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">' . "\n";
      $blog_content  .= '<rdf:Description' . "\n";
      $blog_content  .= '    rdf:about="' . $entry_array[ 'permalink' ][ 'url' ] . '"' . "\n";
      $blog_content  .= '    dc:identifier="' . $entry_array[ 'permalink' ][ 'url' ] . '"' . "\n";
      $blog_content  .= '    dc:title="' . $entry_array[ 'subject' ] . '"' . "\n";
      $blog_content  .= '    trackback:ping="' . $entry_array[ 'trackback' ][ 'ping_url' ] . '" />' . "\n";
      $blog_content  .= '</rdf:RDF>' . "\n";
      $blog_content  .= '-->' . "\n";
    }

    $blog_content .= '<div class="blog_subject">';
    if ( $entry_array[ 'avatarurl' ] != '' ) {
      $blog_content .= '<img src="' . $entry_array[ 'avatarurl'] . '" alt="" border="0" align="left" />';  }

    // SUBJECT
    $blog_content  .= $entry_array[ 'subject' ]  . '<a name="' . $entry_array[ 'id' ] . '">&nbsp;</a></div>' . "\n";


    // DATE
    if ( $mode != 'static' ) { // New 0.4.8
      $blog_content  .= "<div class=\"blog_byline\">" . $entry_array[ 'date' ];

      // CATEGORIES
      if ( array_key_exists( "categories", $entry_array ) ) {
        $blog_content  .= " - ";
        for ( $i = 0; $i < count( $entry_array[ 'categories' ] ); $i++ ) {
          $blog_content .= '<a href="index.php?category=' . $entry_array[ 'categories_id' ][$i] . '">' . $entry_array[ 'categories' ][$i] . '</a>';
          if ( $i < count( $entry_array[ 'categories' ] ) - 1 ) {
            $blog_content .= ', ';
          }
        }
      }

      // IP ADDRESS
      // New 0.4.8
      if ( isset( $entry_array[ 'logged_in' ] ) && $entry_array[ 'logged_in' ] == true ) {
        if ( array_key_exists( 'ip-address', $entry_array ) && $mode == 'comment' ) {
          $blog_content  .= ' <span class="blog_ip_address">&lt;&nbsp;' . $entry_array[ 'ip-address' ] . '&nbsp;&gt;</span>' . "\n";
        }
      }

      if ( isset( $entry_array[ 'createdby' ][ 'text' ] )) {
          $blog_content = $blog_content . '<br />' . $entry_array[ 'createdby' ][ 'text'];
      }

      $blog_content  .= "</div>\n\t\t";
    }

    // EDIT/DELETE BUTTONS
    if ( isset( $entry_array[ 'logged_in' ] ) && $entry_array[ 'logged_in' ] == true ) {
      if ( isset( $entry_array[ 'edit' ][ 'url' ] ) ) {
        $blog_content  .= '<a href="' . $entry_array[ 'edit' ][ 'url' ] . '">[ ' . $entry_array[ 'edit' ][ 'name' ] . ' ]</a>' . "\n";
      }
      if ( isset( $entry_array[ 'delete' ][ 'url' ] ) ) {
        $blog_content  .= '<a href="' . $entry_array[ 'delete' ][ 'url' ] . '">[ ' . $entry_array[ 'delete' ][ 'name' ] . ' ]</a>' . "\n";
      }

      if ( isset( $entry_array[ 'ban' ][ 'url' ] ) ) {
        $blog_content  .= '<a href="' . $entry_array[ 'ban' ][ 'url' ] . '">[ ' . $entry_array[ 'ban' ][ 'name' ] . ' ]</a><br /><br />' . "\n";
      }
    }

    // BLOG ENTRY
    $blog_content  .= $entry_array[ 'entry' ] . "\n";

    // COMMENT ADD
    if ( isset( $entry_array[ 'comment' ][ 'url' ] ) ) {
      // Show 'add comment' button if set...
      $blog_content  .= '<br /><a href="' . $entry_array[ 'comment' ][ 'url' ] . '">[ ' . $entry_array[ 'comment' ][ 'name' ] . ' ]</a>' . "\n";
    }

    // COMMENT COUNT
    if ( isset( $entry_array[ 'comment' ][ 'count' ] ) ) {
      // Show '( x views )' string...
      $blog_content  .= ' ( ' . $entry_array[ 'comment' ][ 'count' ] . ' )' . "\n";
    }

    // TRACKBACK
    if ( isset( $entry_array[ 'trackback' ][ 'url' ] ) ) {
      // Show 'trackback' symbol
      $blog_content  .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="' . $entry_array[ 'trackback' ][ 'url' ] . '">[ ' . $entry_array[ 'trackback' ][ 'name' ] . ' ]</a>' . "\n";;
    }

    // PERMALINK
    if ( $blog_config['blog_enable_permalink']){// New for 0.4.6
      if ( isset( $entry_array[ 'permalink' ][ 'url' ] ) ) {
        // Show 'permalink' symbol
        $blog_content  .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="' . $entry_array[ 'permalink' ][ 'url' ] . '">' . $entry_array[ 'permalink' ][ 'name' ] . '</a>';
      }
    }

    // RELATED LINK
    if ( isset( $entry_array['relatedlink']['url'] ) ) {
      // Show 'relatedlink' symbol - New to 0.4.6
      $blog_content  .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="' . $entry_array['relatedlink']['url'] . '">' . $entry_array['relatedlink']['name'] . '</a>';
    }

    // RATING
    if ( isset( $entry_array[ 'stars' ] ) ) {
      // Show 'permalink' symbol
      $blog_content  .= '&nbsp;&nbsp;|&nbsp;&nbsp;' . $entry_array[ 'stars' ];
    }

    // END
    $blog_content  .= '<hr />' . "\n";

    return $blog_content;
  }

  function theme_genericentry ( $entry_array, $style='normal' ) { // New 0.5.0
    global $user_colors, $blog_theme, $blog_config;

    // init vars
    $img_path = "themes/" . $blog_theme . "/images/";
    $blog_content = '';
    $blog_content = $blog_content . "\n<!-- STATIC NO HEADER ENTRY START -->\n";

    // Text of entry

    if ( $style == 'solid' ) {
      $blog_content = $blog_content . '<div class="blog_body_solid">' . "\n\t";
    } elseif ( $style == 'clear' ) {
      $blog_content = $blog_content . '<div class="blog_body_clear">' . "\n\t";
    } else {
      $blog_content = $blog_content . '<div class="blog_body_framed">' . "\n\t";
    }

    $blog_content = $blog_content . $entry_array[ 'entry' ];
    $blog_content = $blog_content . "\n\t</div>";

    $blog_content = $blog_content . "<br />";

    $blog_content = $blog_content . "\n<!-- STATIC NO HEADER ENTRY END -->\n";

    return $blog_content;
  }

  function theme_staticentry ( $entry_array ) {
    $blog_content = theme_blogentry( $entry_array, 'static' ); // New 0.4.8
    return $blog_content;
  }

  function theme_commententry ( $entry_array ) {
    $blog_content = theme_blogentry( $entry_array, 'comment' ); // New 0.4.8
    return $blog_content;
  }

  function theme_trackbackentry ( $entry_array ) {
    global $blog_config, $user_colors;

     $blog_content = "\n";

    $blog_content  .= '<div class="blog_subject">' . $entry_array[ 'title' ] . '</div>' . "\n";
    $blog_content  .= '<div class="blog_date">' . $entry_array[ 'date' ] . '</div>' . "\n";

    if ( isset( $entry_array[ 'logged_in' ] ) && $entry_array[ 'logged_in' ] == true ) {
      // Show 'delete' button if the user is logged-in...
      if ( isset( $entry_array[ 'delete' ][ 'url' ] ) ) {
        $blog_content  .= '<a href="' . $entry_array[ 'delete' ][ 'url' ] . '">[ ' . $entry_array[ 'delete' ][ 'name' ] . ' ]</a><br /><br />' . "\n";
      }
    }

    $blog_content  .= $entry_array[ 'excerpt' ] . "<p>\n";

    if ( (isset( $entry_array[ 'blog_name' ] ) ) && ($entry_array[ 'blog_name' ] != "") ) {
       $blog_content = $blog_content . '<a href="'.$entry_array[ 'url' ].'">[ ' . $entry_array[ 'blog_name' ] . " ]</a><p>\n";
    } else {
       $blog_content = $blog_content . '<a href="'.$entry_array[ 'url' ].'">[ ' . $entry_array[ 'url' ] . " ]</a><p>\n";
    }

    $blog_content  .= '<hr />' . "\n";

    return $blog_content;
  }

  // Function:
  // theme_default_colors( )
  //
  // Default Base Colors
  // -------------------
  // $user_colors is an associative array that stores
  // all color information. These are the default colors
  // for the theme. These values are read in, and then
  // get overwritten when the user saved colors are
  // read from file.
  //
  // Note
  // ----
  // You can create your own "keys" but they will not
  // show up in the "colors.php" document yet...
  //
  // Also, only these default keys have translations for
  // different languages. If something is missing, email
  // me and I'll add it for future releases.
  //
  // Eventually you'll have the option of disabling keys
  // and added keys will appear on the "color.php" page.
  function theme_default_colors () {
    global $lang_string;

    $color_def = array();

    array_push( $color_def, array( 'id' => 'bg_color',
                'string' => $lang_string[ 'bg_color' ],
                'default' => 'CCCC99' ) );
    array_push( $color_def, array( 'id' => 'border_color',
                'string' => $lang_string[ 'border_color' ],
                'default' => '4D4D45' ) );
    array_push( $color_def, array( 'id' => 'main_bg_color',
                'string' => $lang_string[ 'main_bg_color' ],
                'default' => 'FFFFFF' ) );
    array_push( $color_def, array( 'id' => 'menu_bg_color',
                'string' => $lang_string[ 'menu_bg_color' ],
                'default' => 'F2F2F2' ) );
    array_push( $color_def, array( 'id' => 'inner_border_color',
                'string' => $lang_string[ 'inner_border_color' ],
                'default' => 'D9D9D9' ) );
    array_push( $color_def, array( 'id' => 'link_reg_color',
                'string' => $lang_string[ 'link_reg_color' ],
                'default' => '993333' ) );
    array_push( $color_def, array( 'id' => 'link_hi_color',
                'string' => $lang_string[ 'link_hi_color' ],
                'default' => 'FF3333' ) );
    array_push( $color_def, array( 'id' => 'link_down_color',
                'string' => $lang_string[ 'link_down_color' ],
                'default' => '3333FF' ) );
    array_push( $color_def, array( 'id' => 'header_bg_color',
                'string' => $lang_string[ 'header_bg_color' ],
                'default' => '999966' ) );
    array_push( $color_def, array( 'id' => 'header_txt_color',
                'string' => $lang_string[ 'header_txt_color' ],
                'default' => 'FFFFFF' ) );
    array_push( $color_def, array( 'id' => 'footer_bg_color',
                'string' => $lang_string[ 'footer_bg_color' ],
                'default' => 'EEEEEE' ) );
    array_push( $color_def, array( 'id' => 'footer_txt_color',
                'string' => $lang_string[ 'footer_txt_color' ],
                'default' => '666666' ) );
    array_push( $color_def, array( 'id' => 'txt_color',
                'string' => $lang_string[ 'txt_color' ],
                'default' => '666633' ) );
    array_push( $color_def, array( 'id' => 'headline_txt_color',
                'string' => $lang_string[ 'headline_txt_color' ],
                'default' => '666633' ) );
    array_push( $color_def, array( 'id' => 'date_txt_color',
                'string' => $lang_string[ 'date_txt_color' ],
                'default' => '999999' ) );

    return ( $color_def );
  }

  // Function:
  // theme_pagelayout( )
  //
  // Page Layout Container/Wrapper
  // -----------------------------
  // This function controls all HTML output to the browser.
  //
  // Invoking the page_content() fuction inserts the actual
  // contents of the page.
  //
  function theme_pagelayout () {
    global $user_colors, $blog_config, $blog_theme, $theme_vars;

    $content_width = $theme_vars[ 'content_width' ];
    $menu_width = $theme_vars[ 'menu_width' ];
    $page_width = $content_width + $menu_width;

    // Default image path.
    $img_path = "themes/" . $blog_theme . "/images/";

    // Begin Page Layout HTML
    ?>

    <!-- added by W.B. -->
    <body id="threecolumn">
    <div id="container">
        <?php $cursection="home";$cursubsection=""; include("../common/header.html"); ?>

    	<div id="sidebar">
    		<?php include("../common/sidebar.html"); ?>
    	</div>

    	<div id="sidebar-alternate">
    		<?php include("../common/altsidebar.html"); ?>
    	</div>
    		
    	<div id="main-content">
		<h1 id="page-title">News</h1>
		<p>
		This is my personal home page. 
		If you are looking for the Cracklock utility go to <a href="/software/cracklock/index.html">this section</a>.
		
		You can download other freewares
		that I have developed on <a href="/software/index.html"> this page</a>, and if you want to know about my PhD research then go to <a href="/research/index.html">this page</a>.
	    </p>
    <!-- end of block added by W.B. -->
    
      <!-- removed by W.B. -->
      <table border="0"  cellspacing="0" cellpadding="0">
      
        <tr align="left" valign="top">
          <td width="<?php echo( $page_width ); ?>" colspan="2">
      <!--
            <div id="header_image"><img src="<?php echo( $img_path ); ?>header750x100.jpg" alt="" border="0" /></div>
            <?php
            if ( $blog_config['blog_enable_title']) { // New for 0.4.6
            echo('<div id="header">' . $blog_config[ 'blog_title' ] . '</div>');
            }?>
            -->
 
            <div id="pagebody">
              <!-- <table border="0"  cellspacing="0" cellpadding="0" align="left">
                <tr valign="top"> -->
                  <!--<?php if ( $theme_vars[ 'menu_align' ] == 'left' ) { // New 0.3.8 - Left Menu ?>
                   <td width="<?php echo( $menu_width ); ?>" bgcolor="#<?php echo(get_user_color('menu_bg_color')); ?>">
                    <div id="sidebar">
                      <?php theme_menu(); ?>
                    </div>
                  </td> 
                  <?php } ?>
                  <td width="<?php echo( $content_width ); ?>" bgcolor="#<?php echo(get_user_color('main_bg_color')); ?>">-->
                    <div id="maincontent">
                      <?php page_content(); ?>
                    </div>
                  <!--
                  </td>
                  <?php if ( $theme_vars[ 'menu_align' ] == 'right' ) { // New 0.3.8 - Right Menu ?>
                          
                  <?php } ?> --> 
        <!-- removed by W.B. 
                </tr>
                
                <tr align="left" valign="top">
                  <td width="<?php echo( $page_width ); ?>" bgcolor="#<?php echo(get_user_color('footer_bg_color')); ?>" colspan="2">
                    <div id="footer">
                    <?php echo( '<a href="http://sourceforge.net/projects/sphpblog/"><img style="margin-bottom: 5px;" src="interface/button_sphpblog.png" alt="Powered by Simple PHP Blog" title="Powered by Simple PHP Blog" border="0" /></a> ' );
                    echo($blog_config[ 'blog_footer' ]); ?> - <?php echo( page_generated_in() ); ?></div>
                  </td>
                </tr>
              </table>
                end of block --> 
            </div>
          </td>
          <td><script type="text/javascript"><!--
google_ad_client = "pub-7250791356906762";
//160x600, date de création 20/01/08
google_ad_slot = "6508637641";
google_ad_width = 160;
google_ad_height = 600;
//--></script><script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<br/>
<br/>
<script type="text/javascript"><!--
google_ad_client = "pub-7250791356906762";
/* 160x600, date de création 14/05/08 */
google_ad_slot = "0542169599";
google_ad_width = 160;
google_ad_height = 600;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<br/>
<br/>
<script type="text/javascript"><!--
google_ad_client = "pub-7250791356906762";
/* 120x90, date de création 14/05/08 */
google_ad_slot = "6392583102";
google_ad_width = 120;
google_ad_height =90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
          </td>
        </tr>
      </table>
      <br />

    <!-- added by W.B. -->
        </div>
    
        <?php include("../common/footer.html"); ?>
    </div>    
    <!-- ----------------- -->
      </body>
    <?php
    // End Page Layout HTML
  }

  // Function:
  // theme_popuplayout( )
  //
  // Popup Layout Container/Wrapper
  // -----------------------------
  // This function controls all HTML output to the browser.
  //
  // Same as above, but for the pop-up comment window and
  // the image list pop-up.
  //
  function theme_popuplayout () {
    global $user_colors, $blog_config, $theme_vars;

    $popup_width = $theme_vars[ 'popup_window' ][ 'content_width' ];

    // Begin Popup Layout HTML
    ?>
    <body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0">
      <br />
      <table border="0" width="<?php echo( $popup_width ); ?>" cellspacing="0" cellpadding="0" align="center" style="border: 0px solid #<?php echo(get_user_color('border_color')); ?>;">
        <tr align="left" valign="top">
          <td bgcolor="#<?php echo(get_user_color('header_bg_color')); ?>">
            <div id="header">
              <?php echo($blog_config[ 'blog_title' ]); ?><br />
            </div>
          </td>
        </tr>
        <tr align="left" valign="top">
          <td bgcolor="#<?php echo(get_user_color('main_bg_color')); ?>">
            <div id="maincontent">
              <?php page_content(); ?>
            </div>
          </td>
        </tr>
        <tr align="left" valign="top">
          <td bgcolor="#<?php echo(get_user_color('footer_bg_color')); ?>">
            <div id="footer"><?php echo($blog_config[ 'blog_footer' ]); ?> - <?php echo( page_generated_in() ); ?></div>
          </td>
        </tr>
      </table>
      <br />
    </body>
    <?php
    // End Popup Layout HTML
  }

  function theme_menu_block ($blockArray, $comment='MENU BLOCK', $toggleDiv=null) {
    global $blog_theme;

    // This function creates the menu "blocks" in the sidebar.
    //
    // If you don't want the block to have a "twisty" arrow, then don't pass in a value for $toggleDiv

    // With "twisty" arrow
    /*
      <!-- LINKS -->
      <a id="linkSidebarLinks" href="javascript:toggleBlock('SidebarLinks');"><img src="themes/default/images/minus.gif" name="twisty"> <span class="menu_title">Links</span></a><br />
      <div id="toggleSidebarLinks" class="menu_body">
      <a href="index.php">Home</a><br />
      </div><br />
    */

    // Without "twisty" arrow
    /*
      <!-- LINKS -->
      <span class="menu_title">Links</span><br />
      <div>
      <a href="index.php">Home</a><br />
      </div><br />
    */

    if ( isset( $blockArray[ 'content' ] ) && $blockArray[ 'content' ] != '' ) {
      // Default image path.
      $img_path = "themes/" . $blog_theme . "/images/";
      $img_show = $img_path . 'plus.gif';
      $img_hide = $img_path . 'minus.gif';

      echo( "\n<!-- " . $comment . " -->\n" );

      echo( '<div class="menu_title">' );
      if ( isset( $toggleDiv ) ) {
        echo( '<a id="link' . $toggleDiv . '" href="javascript:toggleBlock(\'' . $toggleDiv . '\');"><img src="' . $img_hide . '" name="twisty" alt="" /> ' );
      }
      echo( $blockArray[ 'title' ] );
      if ( isset( $toggleDiv ) ) {
        echo( '</a>' );
      }
      echo( "</div>\n" );

      if ( isset( $toggleDiv ) ) {
        echo( '<div id="toggle' . $toggleDiv . '" class="menu_body">' . "\n" );
      } else {
        echo( '<div class="menu_body">' . "\n" );
      }
      echo( $blockArray[ 'content' ] . "\n" );
      echo( "</div><br />\n" );

      return true;
    } else {
      return false;
    }
  }

  function theme_menu () {
    global $user_colors, $lang_string, $theme_vars, $logged_in, $sb_info, $blog_config;

    // This function creates the sidebar menu.
    //
    // Move blocks of code up/down to change the order.
    //
    // The "\n" that you see is a RETURN character.
    // This is just to make the HTML code look prettier.
    // It will not show up on the page.
    //
    //  Please note that \n must be used within " quotes...
    //    echo( "\n" ); // <-- This is a return character
    //    echo( '\n' ); // <-- This will print \n on your page...
    //
    // You can use either ' or " in your echo() statements.
    // But keep in mind that might need to use a backslash --> \
    // to print a double or single quote:
    //
    //  These are equivalent: (note the \" or \'  escape chracter...)
    //    echo( 'this "is" a test' ); // displays: this "is" a test
    //    echo( "this \"is\" a test" );  // displays: this "is" a test
    //    echo( "this 'is' a test" );  // displays: this 'is' a test
    //    echo( 'this \'is\' a test' );  // displays: this 'is' a test

    echo( "\n<!-- SIDEBAR MENU BEGIN -->\n" );

    // AVATAR
    theme_menu_block( menu_display_avatar(), 'AVATAR', 'SidebarAvatar' );

    // LINKS
    $result = menu_display_links();
    $loginString = menu_display_login();
    if ( $loginString ) {
      $result[ 'content' ]  .= '<hr />' . $loginString;
    }
    theme_menu_block( $result, 'LINKS' );
    // theme_menu_block( $result, 'LINKS', 'SidebarLinks' ); <-- Use this if you want to be able to Expand/Collapse links.

    // MENU
    theme_menu_block( menu_display_user(), 'USER MENU', 'SidebarMenu' );

    // SETUP
    theme_menu_block( menu_display_setup(), 'SETUP MENU', 'SidebarPreferences' );

    // CUSTOM BLOCKS
    $array = read_blocks($logged_in);
    for ($i=0 ; $i<count($array) ; $i+=2) {
      $result = Array();
      $result[ 'title' ] = $array[$i];
      $result[ 'content' ] = $array[$i+1];
      theme_menu_block( $result, 'CUSTOM BLOCK' );
    }

    // CALENDAR
    theme_menu_block( menu_display_blognav(), 'CALENDAR', 'SidebarCalendar' );

    // RANDOM ENTRY
    theme_menu_block( menu_random_entry(), 'RANDOM ENTRY', 'SidebarRandomEntry' );

    // ARCHIVE TREE
    theme_menu_block( menu_display_blognav_tree(), 'ARCHIVE TREE', 'SidebarArchives' );

    // CATEGORIES
    theme_menu_block( menu_display_categories(), 'CATEGORIES', 'SidebarCategories' );

    // SEARCH
    theme_menu_block( menu_search_field(), 'SEARCH', 'SidebarSearch' );

    // Counter Totals
    theme_menu_block( menu_display_countertotals(), 'COUNTER', 'SidebarCounter');

    // RECENT ENTRIES
    theme_menu_block( menu_most_recent_entries(), 'RECENT ENTRIES', 'SidebarRecentEntries' );

    // RECENT COMMENTS
    theme_menu_block( menu_most_recent_comments(), 'RECENT COMMENTS', 'SidebarRecentComments' );

    // RECENT TRACKBACKS
    theme_menu_block( menu_most_recent_trackbacks(), 'RECENT TRACKBACKS', 'SidebarRecentTrackbacks' );

    echo( '<p />' );

    // WEB BADGES
    echo( '<div align="center">' );
    echo( '<a href="http://sourceforge.net/projects/sphpblog/"><img style="margin-bottom: 5px;" src="interface/button_sphpblog.png" alt="Powered by Simple PHP Blog" title="Powered by Simple PHP Blog" border="0" /></a> ' );
    echo( '<a href="rss.php"><img style="margin-bottom: 5px;" src="interface/button_rss20.png" alt="Get RSS 2.0 Feed" title="Get RSS 2.0 Feed" border="0" /></a><br />' );
    echo( '<a href="http://php.net/"><img style="margin-bottom: 5px;" src="interface/button_php.png" alt="Powered by PHP ' . phpversion() . '" title="Powered by PHP ' . phpversion() . '" border="0" /></a> ' );
    echo( '<a href="atom.php"><img style="margin-bottom: 5px;" src="interface/button_atom03.png" alt="Get Atom 0.3 Feed" title="Get Atom 0.3 Feed" border="0" /></a><br />' );
    echo( '<img style="margin-bottom: 5px;" src="interface/button_txt.png" alt="Powered by Plain text files" title="Powered by Plain text files" border="0" /> ' );
    echo( '<a href="rdf.php"><img style="margin-bottom: 5px;" src="interface/button_rdf10.png" alt="Get RDF 1.0 Feed" title="Get RDF 1.0 Feed" border="0" /></a><br />' );
    echo( '</div>' );
  }

?>
