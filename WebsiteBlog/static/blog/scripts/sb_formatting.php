<?php

  // The Simple PHP Blog is released under the GNU Public License.
  //
  // You are free to use and modify the Simple PHP Blog. All changes
  // must be uploaded to SourceForge.net under Simple PHP Blog or
  // emailed to apalmo <at> bigevilbrain <dot> com

  // --------------------
  // Entry Format Parsing
  // --------------------

  function clean_post_text( $str ) {
    // Cleans post text input.
    //
    // Strip out and replace pipes with colons. HTML-ize entities.
    // Use charset from the language file to make sure we're only
    // encoding stuff that needs to be encoded.
    //
    // This makes entries safe for saving to a file (since the data
    // format is pipe delimited.)
    global $lang_string;
    $str = str_replace( '|', '&#124;', $str );
    $str = @htmlspecialchars( $str, ENT_QUOTES, $lang_string[ 'php_charset' ] );

    return ( $str );
  }

  function htmlDecode( $temp_str ) {
    $trans_str = get_html_translation_table(HTML_ENTITIES);
    foreach($trans_str as $k => $v){
      $ttr[$v] = utf8_encode($k);
    }
    $temp_str = strtr($temp_str, $ttr);

    $temp_str = str_replace( '&#039;', '\'', $temp_str );

    return( $temp_str );
  }

  function blog_to_html( $str, $comment_mode, $strip_all_tags, $add_no_follow=false, $emoticon_replace=false ) {
    // Convert Simple Blog tags to HTML.
    //
    // Search and replace simple tags. These tags don't have any
    // special attributes so we can do a str_replace() on them.
    //
    // ( Could use str_ireplace() but it's only supported in PHP 5. )
    global $blog_config;

    $str = str_replace( '&amp;#124;', '|', $str );

    if ( $comment_mode ) {
      $tag_arr = array();
      if ( in_array( 'i', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'i' ); }
      if ( in_array( 'b', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'b' ); }
      if ( in_array( 'blockquote', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'blockquote' ); }
      if ( in_array( 'strong', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'strong' ); }
      if ( in_array( 'em', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'em' ); }
      if ( in_array( 'hN', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ); }
      if ( in_array( 'del', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'del' ); }
      if ( in_array( 'ins', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'ins' ); }
      if ( in_array( 'strike', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'strike' ); }
      if ( in_array( 'pre', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'pre' ); }
      if ( in_array( 'code', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'code' ); }
			if ( in_array( 'center', $blog_config[ 'comment_tags_allowed' ] ) ) { array_push( $tag_arr, 'center' ); }
    } else {
      $tag_arr = array('i', 'b', 'blockquote', 'strong', 'em', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'del', 'ins', 'strike', 'pre', 'code', 'center' );
    }

    // Build search and replace arrays.
		$search_arr = array();
    $replace_arr = array();
    for ( $i = 0; $i < count( $tag_arr ); $i++ ) {
      $tag = $tag_arr[$i];
      array_push( $search_arr,  '[' . strtolower( $tag ) . ']',  '[' . strtoupper( $tag ) . ']' );
      if ( $strip_all_tags ) {
        array_push( $replace_arr, '',  '' );
      } else {
        array_push( $replace_arr, '<' . strtolower( $tag ) . '>',  '<' . strtoupper( $tag ) . '>' );
      }
      array_push( $search_arr,  '[/' . strtolower( $tag ) . ']', '[/' . strtoupper( $tag ) . ']' );
      if ( $strip_all_tags ) {
        array_push( $replace_arr, '',  '' );
      } else {
        array_push( $replace_arr, '</' . strtolower( $tag ) . '>', '</' . strtoupper( $tag ) . '>' );
      }
    }

    // Special QUOTE shortcut to BLOCKQUOTE tag.
    if ( $comment_mode ) {
      if ( in_array( 'blockquote', $blog_config[ 'comment_tags_allowed' ] ) ) {
        array_push( $search_arr, '[quote]', '[QUOTE]' );
        if ( $strip_all_tags ) {
          array_push( $replace_arr, '',  '' );
        } else {
          array_push( $replace_arr, '<blockquote>', '<BLOCKQUOTE>' );
        }
        array_push( $search_arr, '[/quote]', '[/QUOTE]' );
        if ( $strip_all_tags ) {
          array_push( $replace_arr, '',  '' );
        } else {
          array_push( $replace_arr, '</blockquote>', '</BLOCKQUOTE>' );
        }
      }
    } else {
        array_push( $search_arr, '[quote]', '[QUOTE]' );
        if ( $strip_all_tags ) {
          array_push( $replace_arr, '',  '' );
        } else {
          array_push( $replace_arr, '<blockquote>', '<BLOCKQUOTE>' );
        }
        array_push( $search_arr, '[/quote]', '[/QUOTE]' );
        if ( $strip_all_tags ) {
          array_push( $replace_arr, '',  '' );
        } else {
          array_push( $replace_arr, '</blockquote>', '</BLOCKQUOTE>' );
        }
    }

    // Emoticons
    if ( !$strip_all_tags ) {
      if ($emoticon_replace) {
        $emote_arr = emoticons_load_tags();

        for ( $i = 0; $i < count($emote_arr); $i++) {

          $emotetag_arr = explode( ' ', $emote_arr[$i]['TAGS'] );
          for ( $j = 0; $j < count( $emotetag_arr ); $j++ ) {
            $html_safe_tag = @htmlspecialchars( addslashes($emotetag_arr[$j]), ENT_QUOTES, $lang_string[ 'php_charset' ] );

            array_push( $search_arr, $emotetag_arr[$j] );
            array_push( $replace_arr, '<img src="' . $emote_arr[$i]['PATH'] . '" alt="' . $html_safe_tag . '" />' );
          }
        }
      }
    }

    $str = str_replace( $search_arr, $replace_arr, $str);

    // Replace [url] Tags:
    // The [url] tag has an optional "new" attribute. The "new"
    // attribute determines whether to open the link in the
    // same window or a new window.
    // new   - (true/false)
    //
    // [url=http://xxx]xxx[/url]
    // [url=http://xxx new=true]xxx[/url]
    if ( $comment_mode ) {
      if ( in_array( 'url', $blog_config[ 'comment_tags_allowed' ] ) && $strip_all_tags === false ) {
        $str = replace_url_tag( $str, '[url=', ']', '[/url]', false, $add_no_follow );
        $str = replace_url_tag( $str, '[URL=', ']', '[/URL]', false, $add_no_follow );
      } else {
        $str = replace_url_tag( $str, '[url=', ']', '[/url]', true, $add_no_follow );
        $str = replace_url_tag( $str, '[URL=', ']', '[/URL]', true, $add_no_follow );
      }
    } else {
      if ( $strip_all_tags ) {
        $str = replace_url_tag( $str, '[url=', ']', '[/url]', true, $add_no_follow );
        $str = replace_url_tag( $str, '[URL=', ']', '[/URL]', true, $add_no_follow );
      } else {
        $str = replace_url_tag( $str, '[url=', ']', '[/url]', false, $add_no_follow );
        $str = replace_url_tag( $str, '[URL=', ']', '[/URL]', false, $add_no_follow );
      }
    }

    // Replace [img] Tags:
    // The [img] tag has an number of optional attributes -
    // width  - width of image in pixels
    // height - height of image in pixels
    // popup  - (true/false)
    // float  - (left/right)
    //
    // [img=http://xxx]
    // [img=http://xxx width=xxx height=xxx popup=true float=left]
    if ( $comment_mode ) {
      if ( in_array( 'img', $blog_config[ 'comment_tags_allowed' ] ) && $strip_all_tags === false  ) {
        $str = replace_img_tag( $str, '[img=', ']', false );
        $str = replace_img_tag( $str, '[IMG=', ']', false );
      } else {
        $str = replace_img_tag( $str, '[img=', ']', true );
        $str = replace_img_tag( $str, '[IMG=', ']', true );
      }
    } else {
      if ( $strip_all_tags ) {
        $str = replace_img_tag( $str, '[img=', ']', true );
        $str = replace_img_tag( $str, '[IMG=', ']', true );
      } else {
        $str = replace_img_tag( $str, '[img=', ']', false );
        $str = replace_img_tag( $str, '[IMG=', ']', false );
      }
    }

    // Selectively replace line breaks and/or decode html entities.
    if ( $comment_mode ) {
      if ( in_array( 'html', $blog_config[ 'comment_tags_allowed' ] ) && $strip_all_tags === false ) {
        $str = replace_html_tag( $str, false );
      } else {
        $str = replace_html_tag( $str, true );
      }
    } else {
      if ( $strip_all_tags ) {
        $str = replace_html_tag( $str, true );
      } else {
        $str = replace_html_tag( $str, false );
      }
    }

    return ( $str );
  }

  function replace_html_tag( $str, $strip_tags ) {
    // Replacements for HTML tags. Sub-function of blog_to_html.
    //
    // This function decodes HTML entities that are located between
    // HTML tags. Also, inserts <br />'s for new lines only if blocks
    // are outside the HTML tags.
    global $lang_string;

    $str_out = NULL;
    $tag_begin = '[html]';
    $tag_end = '[/html]';

    // Search for the openning HTML tag. Tag could be either upper or
    // lower case so we want to find the nearest one.
    //
    // Get initial $str_offset value.
    $temp_lower = strpos( $str, strtolower( $tag_begin ) );
    $temp_upper = strpos( $str, strtoupper( $tag_begin ) );
    if ( $temp_lower === false ) {
      if ( $temp_upper === false ) {
        $str_offset = false;
      } else {
        $str_offset = $temp_upper;
      }
    } else {
      if ( $temp_upper === false ) {
        $str_offset = $temp_lower;
      } else {
        $str_offset = min( $temp_upper, $temp_lower );
      }
    }

    // Loop
    while ( $str_offset !== false ) {
      // Store all the text BEFORE the openning HTML tag.
      $temp_str = substr( $str, 0, $str_offset );
      //
      // Replace hard returns in string with '<br />' tags.
      // "\r\n" - WINDOWS
      // "\n"   - UNIX
      // "\r"   - MACINTOSH
      $temp_str = str_replace( "\r\n", '<br />', $temp_str );
      $temp_str = str_replace( "\n", '<br />', $temp_str );
      $temp_str = str_replace( "\r", '<br />', $temp_str );
      // $temp_str = str_replace( chr(10), '<br />', $temp_str );
      $str_out  .= $temp_str;

      // Store all text AFTER the tag
      $str = substr( $str, $str_offset + strlen( $tag_begin ) );

      // Search for the closing HTML tag. Find the nearest one.
      $temp_lower = strpos( $str, strtolower( $tag_end ) );
      $temp_upper = strpos( $str, strtoupper( $tag_end ) );
      if ( $temp_lower === false ) {
        if ( $temp_upper === false ) {
          $str_offset = false;
        } else {
          $str_offset = $temp_upper;
        }
      } else {
        if ( $temp_upper === false ) {
          $str_offset = $temp_lower;
        } else {
          $str_offset = min( $temp_upper, $temp_lower );
        }
      }

      if ( $str_offset !== false ) {
        // Store all the text BETWEEN the HTML tags.
        $temp_str = substr( $str, 0, $str_offset );
        //
        // Decode HTML entities between the tags.
        if ( $strip_tags === false ) {
          /*
          $trans_str = get_html_translation_table(HTML_ENTITIES);
          foreach($trans_str as $k => $v){
            $ttr[$v] = utf8_encode($k);
          }
          $temp_str = strtr($temp_str, $ttr);

          $str_out  .= $temp_str;
          */
          $str_out  .= htmlDecode($temp_str);
        }

        // Store sub_string after the tag.
        $str = substr( $str, $str_offset + strlen( $tag_end ) );

        // Search for openning HTML tag again.
        $temp_lower = strpos( $str, strtolower( $tag_begin ) );
        $temp_upper = strpos( $str, strtoupper( $tag_begin ) );
        if ( $temp_lower === false ) {
          if ( $temp_upper === false ) {
            $str_offset = false;
          } else {
            $str_offset = $temp_upper;
          }
        } else {
          if ( $temp_upper === false ) {
            $str_offset = $temp_lower;
          } else {
            $str_offset = min( $temp_upper, $temp_lower );
          }
        }
      }
    }

    // Append remainder of text.
    //
    // All this text will be outside of any HTML tags so
    // we need to encode the line breaks.
    // "\r\n" - WINDOWS
    // "\n"   - UNIX
    // "\r"   - MACINTOSH
    $str = str_replace( "\r\n", '<br />', $str );
    $str = str_replace( "\n", '<br />', $str );
    $str = str_replace( "\r", '<br />', $str );
    // $str = str_replace( chr(10), '<br />', $str );
    $str = $str_out . $str;

    return ( $str );
  }

  function replace_url_tag( $str, $tag_begin, $tag_end, $tag_close, $strip_tags, $add_no_follow = false ) {
    // Replacements for URL tags. Sub-function of blog_to_html.
    //
    // If $strip_tags == true then it will strip out the tag
    // instead of making them HTML.
    $str_out = NULL;

    // Search for the beginning part of the tag.
    $str_offset = strpos( $str, $tag_begin );
    while ( $str_offset !== false ) {
      // Store sub_string before the tag.
      $str_out  .= substr( $str, 0, $str_offset );
      // Store sub_string after the tag.
      $str = substr( $str, $str_offset + strlen( $tag_begin ) );

      // Search for the ending part of the tag.
      $str_offset = strpos( $str, $tag_end );
      if ( $str_offset !== false ) {

        if ( $strip_tags == false ) {
          // Store attribues BETWEEN between the tags.
          $attrib_array = explode( ' ', substr( $str, 0, $str_offset ) );
          $attrib_new = NULL;

          if ( is_array( $attrib_array ) ) {
            $str_url = $attrib_array[0];

            for ( $i = 1; $i < count( $attrib_array ); $i++ ) {
              $temp_arr = explode( '=', $attrib_array[$i] );
              if ( is_array( $temp_arr ) && count( $temp_arr ) == 2 ) {
                switch ( $temp_arr[0] ) {
                  case 'new';
                    $attrib_new = $temp_arr[1];
                    break;
                }
              }
            }
          } else {
            $str_url = $attrib_array;
          }

          // Append HTML tag.
          if ( isset( $attrib_new ) ) {
            if ( $attrib_new == 'false' ) {
              $str_out  .= "<a href=\"" . $str_url . "\" ";
              if ( $add_no_follow == true ) {
                $str_out  .= "rel=\"nofollow\">";
              } else {
                $str_out  .= ">";
              }
            } else {
              $str_out  .= "<a href=\"" . $str_url . "\" target=\"_blank\" ";
              if ( $add_no_follow == true ) {
                $str_out  .= "rel=\"nofollow\">";
              } else {
                $str_out  .= ">";
              }
            }
          } else {
            $str_out  .= "<a href=\"" . $str_url . "\" target=\"_blank\" ";
            if ( $add_no_follow == true ) {
              $str_out  .= "rel=\"nofollow\">";
            } else {
              $str_out  .= ">";
            }
          }
        }

        // Store sub_string AFTER the tag.
        $str = substr( $str, $str_offset + strlen( $tag_end ) );

        /*
        // Look for closing tag.
        $str_offset = strpos( $str, $tag_close );
        if ( $str_offset !== false ) {
          $str_link = substr( $str, 0, $str_offset );
          if ( $strip_tags == false ) {
            $str_out  .= $str_link . '</a>';
          } else {
            $str_out  .= $str_link;
          }
          $str = substr( $str, $str_offset + strlen( $tag_close ) );
        }
        */

        // Look for closing tag.
        // HACK "CUT-URL" BY DRUDO ( drudo3  jumpy  it )
        $str_offset = strpos( $str, $tag_close );
        if ( $str_offset !== false ) {

          // If the address contains more than 56 characters and begins with "HTTP://"
          if ($str_offset >= 56 && (substr( $str, 0, 7)) == "http://"){
            // Store the URL up to the 39th character
            $str_link = substr( $str, 0, 39 );
            // Store the final part of the URL
            $str_link_fine = substr( $str_url, -10 );
          } else {
            // If the URL is less than 56 characters, store the whole URL
            $str_link = substr( $str, 0, $str_offset );
          }

          if ( $strip_tags == false ) {
            // More than 56 characters
            if ($str_offset >= 56 && (substr( $str, 0, 7)) == "http://"){
              $str_out  .= $str_link . ' ... ' . $str_link_fine . '</a>';
            } else{
              // Less than 56 characters
              $str_out  .= $str_link . '</a>';
            }
          } else {
            // Strip tags...
            $str_out  .= $str_link;
          }

          $str = substr( $str, $str_offset + strlen( $tag_close ) );
        }

        // Search for next beginning tag.
        $str_offset = strpos( $str, $tag_begin );
      }
    }

    // Append remainder of tag.
    $str = $str_out . $str;

    return ( $str );
  }

  function replace_img_tag( $str, $tag_begin, $tag_end, $strip_tags ) {
    // Replacements for IMG tags. Sub-function of blog_to_html.
    //
    // I made this another function because I wanted to be able
    // to call it for upper and lower case '[img=]' tags...
    //
    // If $strip_tags == true then it will strip out the tag
    // instead of making them HTML.
    global $theme_vars;

    $str_out = NULL;

    // Search for the beginning part of the tag.
    $str_offset = strpos( $str, $tag_begin );
    while ( $str_offset !== false ) {

      // Store sub_string before the tag.
      $str_out  .= substr( $str, 0, $str_offset );

      // Store sub_string after the tag.
      $str = substr( $str, $str_offset + strlen( $tag_begin ) );

      // Search for the ending part of the tag.
      $str_offset = strpos( $str, $tag_end );
      if ( $str_offset !== false ) {

        if ( $strip_tags == true ) {

          // Store sub_string after the tag.
          $str = substr( $str, $str_offset + strlen( $tag_end ) );

          // Search for next beginning tag.
          $str_offset = strpos( $str, $tag_begin );

        } else {

          // Store attribues between between the tags.
          $attrib_array = explode( ' ', substr( $str, 0, $str_offset ) );
          $attrib_width = NULL;
          $attrib_height = NULL;
          $attrib_popup = NULL;
          $attrib_float = NULL;

          if ( is_array( $attrib_array ) ) {
            $str_url = $attrib_array[0];

            for ( $i = 1; $i < count( $attrib_array ); $i++ ) {
              $temp_arr = explode( '=', $attrib_array[$i] );
              if ( is_array( $temp_arr ) && count( $temp_arr ) == 2 ) {
                switch ( $temp_arr[0] ) {
                  case 'width';
                    $attrib_width = intval( $temp_arr[1] );
                    break;
                  case 'height';
                    $attrib_height = intval( $temp_arr[1] );
                    break;
                  case 'popup';
                    $attrib_popup = $temp_arr[1];
                    break;
                  case 'float';
                    $attrib_float = $temp_arr[1];
                    break;
                }
              }
            }
          } else {
            $str_url = $attrib_array;
          }

          // Grab image size and calculate scaled sizes

          // if ( file_exists( $str_url ) !== false ) {
          $img_size = @getimagesize( $str_url );
          if ( $img_size !== false ) {
            $width = $img_size[0];
            $height = $img_size[1];

            $max_image_width = $theme_vars[ 'max_image_width' ];

            $auto_resize = true;
            if ( isset( $attrib_width ) && isset( $attrib_height ) ) {
              // Both width and height are set.
              $width = $attrib_width;
              $height = $attrib_height;
              $auto_resize = false;
            } else {
              if ( isset( $attrib_width ) ) {
                // Only width is set. Calculate relative height.
                $height = round( $height * ( $attrib_width / $width ) );
                $width = $attrib_width;
                $auto_resize = false;
              }

              if ( isset( $attrib_height ) ) {
                // Only height is set. Calculate relative width.
                $width = round( $width * ( $attrib_height / $height ) );
                $height = $attrib_height;
                $auto_resize = false;
              }
            }

            if ( $auto_resize == true ) {
              if ( $width > $max_image_width ) {
                $height = round( $height * ( $max_image_width / $width ) );
                $width = $max_image_width;
              }
            }

            if ( !isset( $attrib_popup ) ) {
              if ( $width != $img_size[0] || $height != $img_size[1] ) {
                $attrib_popup = 'true';
              } else {
                $attrib_popup = 'false';
              }
            }

            if ( $attrib_popup == 'true' ) {
              // Pop Up True
              $str_out  .= '<a href="javascript:openpopup(\'' . $str_url . '\','.$img_size[0].','.$img_size[1].',false);"><img src="' . $str_url . '" width="'.$width.'" height="'.$height.'" border="0" alt=""';
              if ( isset( $attrib_float ) ) {
                switch ( $attrib_float ) {
                  case 'left';
                    $str_out  .= ' id="img_float_left"';
                    break;
                  case 'right';
                    $str_out  .= ' id="img_float_right"';
                    break;
                }
              }
              $str_out  .= ' /></a>';
            } else {
              // Pop Up False
              $str_out  .= '<img src="' . $str_url . '" width="'.$width.'" height="'.$height.'" border="0" alt=""';
              if ( isset( $attrib_float ) ) {
                switch ( $attrib_float ) {
                  case 'left';
                    $str_out  .= ' id="img_float_left"';
                    break;
                  case 'right';
                    $str_out  .= ' id="img_float_right"';
                    break;
                }
              }
              $str_out  .= ' />';
            }

            // Store sub_string after the tag.
            $str = substr( $str, $str_offset + strlen( $tag_end ) );
            // Search for next beginning tag.
            $str_offset = strpos( $str, $tag_begin );
          } else {
            // Append HTML tag.
            if ( isset( $attrib_popup ) ) {
              if ( $attrib_popup == 'true' ) {
                $str_out  .= '<a href="javascript:openpopup(\'' . $str_url . '\',800,600,false);"><img src="' . $str_url . '" border="0" alt="" /></a>';
              } else {
                $str_out  .= '<img src="' . $str_url . '" border="0" alt="" />';
              }
            } else {
              $str_out  .= '<a href="javascript:openpopup(\'' . $str_url . '\',800,600,false);"><img src="' . $str_url . '" border="0" alt="" /></a>';
            }

            // Store sub_string after the tag.
            $str = substr( $str, $str_offset + strlen( $tag_end ) );
            // Search for next beginning tag.
            $str_offset = strpos( $str, $tag_begin );
          }
        }
      }
    }

    // Append remainder of tag.
    $str = $str_out .  $str;

    return ( $str );
  }

  function sb_parse_url ( $text ) {
      // Con espacios
      $text = eregi_replace("([[:space:]])((f|ht)tps?:\/\/[a-z0-9~#%@\&:=?+\/\.,_-]+[a-z0-9~#%@\&=?+\/_.;-]+)", "\\1[url=\\2]\\2[/url]", $text); //http
      $text = eregi_replace("([[:space:]])(www\.[a-z0-9~#%@\&:=?+\/\.,_-]+[a-z0-9~#%@\&=?+\/_.;-]+)", "\\1[url=http://\\2]\\2[/url]", $text); // www.
      $text = eregi_replace("([[:space:]])([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6})","\\1[url=mailto:\\2]\\2[/url]", $text); // mail
      // Al principio de una cadena
      $text = eregi_replace("^((f|ht)tps?:\/\/[a-z0-9~#%@\&:=?+\/\.,_-]+[a-z0-9~#%@\&=?+\/_.;-]+)", "[url=\\1]\\1[/url]", $text); //http
      $text = eregi_replace("^(www\.[a-z0-9~#%@\&:=?+\/\.,_-]+[a-z0-9~#%@\&=?+\/_.;-]+)", "[url=http://\\1]\\1[/url]", $text); // www
      $text = eregi_replace("^([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6})","[url=mailto:\\1]\\1[/url]", $text); // mail
      return ( $text );
  }

  function replace_more_tag ( $string, $strip_tags=true, $url='', $trim_off_end=false ) {
    global $lang_string;

    $tagpos = strpos( strtoupper($string), '[MORE]' );
    if ( $tagpos != false ) {
      if ( $strip_tags == true ) {
        $tagstart = strpos( strtoupper($string), '[MORE]' );
        $tagend = $tagstart + strlen( '[MORE]' );
        $tmpstr = substr( $string, 0, $tagpos );
        if ( $trim_off_end == true ) {
          $string = $tmpstr;
        } else {
          $tmpstr  .= substr( $string, $tagend, strlen( $string ) );
          $string = $tmpstr;
        }
      } else {
        $string = substr( $string, 0, $tagpos );
        //Now put in the More link
        if ( $url != '' ){
          $read_more = isset( $lang_string['read_more'] ) ? $lang_string['read_more'] : 'Read more...';
          $string  .= ' <a href="' . $url . '">' . $read_more . '</a><br />';
        }
      }
    }
    return ( $string );
  }

  function get_init_code ( ) {
    global $blog_config, $lang_string, $blog_theme, $sb_info;
    ob_start();

    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo( $lang_string[ 'html_charset' ] ); ?>" />

    <!-- added by W.B.: google analytics code -->
      <script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
  </script>
  <script type="text/javascript">
_uacct = "UA-77730-1";
urchinTracker();
  </script>
  
  
    <!-- Meta Data -->
    <meta name="generator" content="Simple PHP Blog" />
    <link rel="alternate" type="application/rss+xml" title="Get RSS 2.0 Feed" href="rss.php" />
    <link rel="alternate" type="application/rdf+xml" title="Get RDF 1.0 Feed" href="rdf.php" />
    <link rel="alternate" type="application/atom+xml" title="Get Atom 0.3 Feed" href="atom.php" />

    <!-- Meta Data -->
    <!-- http://dublincore.org/documents/dces/ -->
    <meta name="dc.title"       content="<?php echo( $blog_config[ 'blog_title' ] ); ?>" />
    <meta name="author"         content="<?php echo( $blog_config[ 'blog_author' ] ); ?>" />
    <meta name="dc.creator"     content="<?php echo( $blog_config[ 'blog_author' ] ); ?>" />
    <meta name="dc.subject"     content="<?php echo( $blog_config[ 'info_keywords' ] ); ?>" />
    <meta name="keywords"       content="<?php echo( $blog_config[ 'info_keywords' ] ); ?>" />
    <meta name="dc.description" content="<?php echo( $blog_config[ 'info_description' ] ); ?>" />
    <meta name="description"    content="<?php echo( $blog_config[ 'info_description' ] ); ?>" />
    <meta name="dc.type"        content="weblog" />
    <meta name="dc.type"        content="blog" />
    <meta name="resource-type"  content="document" />
    <meta name="dc.format"      scheme="IMT" content="text/html" />
    <meta name="dc.source"      scheme="URI" content="<?php if ( ( dirname($_SERVER[ 'PHP_SELF' ]) == '\\' || dirname($_SERVER[ 'PHP_SELF' ]) == '/' ) ) { echo( 'http://'.$_SERVER[ 'HTTP_HOST' ].'/index.php' ); } else { echo( 'http://'.$_SERVER[ 'HTTP_HOST' ].dirname($_SERVER[ 'PHP_SELF' ]).'/index.php' ); } ?>" />
    <meta name="dc.language"    scheme="RFC1766" content="<?php echo( str_replace('_', '-', $lang_string[ 'locale' ]) ); ?>" />
    <meta name="dc.coverage"    content="global" />
    <meta name="distribution"   content="GLOBAL" />
    <meta name="dc.rights"      content="<?php echo( $blog_config[ 'info_copyright' ] ); ?>" />
    <meta name="copyright"      content="<?php echo( $blog_config[ 'info_copyright' ] ); ?>" />

    <!-- Robots -->
    <meta name="robots" content="ALL,INDEX,FOLLOW,ARCHIVE" />
    <meta name="revisit-after" content="7 days" />

    <!-- Fav Icon -->
    <link rel="shortcut icon" href="../favicon.ico" />

    <link rel="stylesheet" type="text/css" href="themes/<?php echo( $blog_theme ); ?>/style.css" />

    <?php require_once('scripts/sb_javascript.php'); ?>
    <script language="javascript" src="scripts/sb_javascript.js" type="text/javascript"></script>
  

    <?php
    return ( ob_get_clean() );
  }
?>
