<?php
include INC_DIR.'doctype.php';


// generate wp-admin link
$folders = explode( '/', $_SERVER['REQUEST_URI'] );
$folders_len = count( $folders );
$wp_install_folder = '';

for( $i = 0; $i < $folders_len; $i++ ){
  if ( $folders[$i] === 'wp-content' ){
    break;
  }

  // if we got this far:
  $wp_install_folder .= $folders[$i] . '/' ;

}

$wp_admin_link = '//' . $_SERVER['HTTP_HOST'] . $wp_install_folder . 'wp-admin/' ;

?>

<head>
  <?php include INC_DIR.'header-base.php'; ?>
</head>

<body>

  <!-- Added by me   -->
  <div id="go_back_to_wp_div">
    <span>&#10094;</span>
    <a href="<?php echo $wp_admin_link; ?>">Go back to WordPress dashboard</a>
    <style type="text/css">
      #go_back_to_wp_div{
        color:#ccc; background:#23282d; font-family: sans-serif; margin-bottom: 15px;
      }
      #go_back_to_wp_div span{
        font-weight: bold; padding-left: 10px; padding-right: 0; 
      }
      #go_back_to_wp_div a{
        color: inherit; display: inline-block; padding: 10px; text-decoration: none;
      }
      #go_back_to_wp_div a:hover{
        color:#00b9eb; background: #32373c;
      }
    </style>
  </div>


  <div id="header" class="foothead">
      
    <h1><strong>(smt)<sup>2</sup></strong> &middot; simple mouse tracking</h1>
    
    
    <p id="logged"><a href="<?=ABS_PATH?>">Logged in</a> as <strong><?=$_SESSION['login']?></strong> &mdash;
    <a id="logout" class="smallround" href="<?=ADMIN_PATH?>sys/logout.php">disconnect</a></p>
    
  </div><!-- end header -->
    
  <div id="nav">
    <ul>
      <?php 
      $basedir = filename_to_str( ext_name() );
      $basecss = ($basedir == "admin") ? ' class="current"' : null;
      // display always the dashboard
      echo '<li'.$basecss.'><a href="'.ADMIN_PATH.'">Dashboard</a></li>';
      // display allowed sections
      echo ext_format();    
      ?>
    </ul>
  </div><!-- end nav -->
  
  <div id="global">
  
  <?php 
    // Custom admin content ("extension" from here onwards) should start here 
  ?>