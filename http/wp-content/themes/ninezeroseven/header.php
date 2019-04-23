<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?>> <!--<![endif]-->

<div id="topbar">
  <?php if ( is_active_sidebar('left-topbar') ) : ?>
  <div id="left-topbar-widget">
    <?php dynamic_sidebar( 'left-topbar' ); ?>
  </div>
  <?php endif; // end left topbar widget area ?>
  <?php if ( is_active_sidebar('right-topbar') ) : ?>
  <div id="right-topbar-widget">
    <?php dynamic_sidebar( 'right-topbar' ); ?>
  </div>
  <?php endif; // end right topbar widget area ?>
</div>
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KW4R25K');</script>
<!-- End Google Tag Manager -->
<style>
.site-logo-title.logo-text, .primary-menu .wbc_menu > #menu-item-1013{

}
.container {
    width: 100% !important;
}
.wbc_menu > #menu-item-779
 > a {
    display: block;
    height: 20%;
    padding-left: 14px;
    padding-right: 14px;
    font-size: 15px;
    color: #FFF !important;
    font-weight: normal;
    text-decoration: none;
    background-color: #3fae2a;
    border-color: #3fae2a;
}
#sfsi_holder{
    
    height: 40px !important;
   
}
.has-fixed-menu:not(.has-transparent-menu) .page-wrapper {
    padding: 10px 0 0!important;
}
@media screen and (max-width:800px) {
.vc_custom_1520950436294{padding-right: 0 !important;}

.vc_custom_1520950771158{padding-left:  0 !important
;}
.vc_custom_1520951006543{padding-right:  0 !important
;}
.vc_custom_1520951406622{padding-left: 0 !important;}
	}
</style>



	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<!-- Mobile Specific Metas
  ================================================== -->

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

<?php
global $wbc907_data;
wp_head();
?>
<meta name="google-site-verification" content="unkH-oQIOzLNmQH8MYLZeyoR6btqP0QzvevXwaPrfRo" />
</head>

<body <?php body_class(); ?>> <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KW4R25K"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div style="padding-left:85%;">
<?php 
          dynamic_sidebar( apply_filters( 'wbc907_custom_sidebars' , 'header' ) ); ?> 

</div>

	<?php do_action( 'wbc907_before_page_content' ); ?>

	<!-- Up Anchor -->
	<span class="anchor-link wbc907-top" id="up"></span>

	<?php wbc907_menu_bar_output(); ?>

	<!-- Page Wrapper -->
	<div class="page-wrapper">

	<?php do_action( 'wbc907_after_wrapper' );?>
