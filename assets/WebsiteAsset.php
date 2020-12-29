<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class WebsiteAsset extends AssetBundle
{
	//public $basePath = '@webroot';
	//public $baseUrl = '@web';
	public $sourcePath = '@app/assets/theme';
	
    public $css = [
		'css/bootstrap.css',
        'vendors/linericon/style.css',
        'css/font-awesome.min.css',
        'vendors/owl-carousel/owl.carousel.min.css',
        'vendors/nice-select/css/nice-select.css',
        'vendors/animate-css/animate.css',
        'vendors/popup/magnific-popup.css',
        'vendors/swiper/css/swiper.min.css',
        'vendors/scroll/jquery.mCustomScrollbar.css',
		'css/website-style.css',
        'css/responsive.css',
    ];
	
    public $js = [
		'js/popper.js',
		'js/stellar.js',
		'js/jquery.lightbox.js',
		'vendors/nice-select/js/jquery.nice-select.min.js',
		'vendors/isotope/imagesloaded.pkgd.min.js',
		'vendors/isotope/isotope-min.js',
		'vendors/owl-carousel/owl.carousel.min.js',
		'js/jquery.ajaxchimp.min.js',
		'vendors/counter-up/jquery.waypoints.min.js',
		'vendors/counter-up/jquery.counterup.js',
		'js/mail-script.js',
		'vendors/popup/jquery.magnific-popup.min.js',
		'vendors/swiper/js/swiper.js',
		'vendors/scroll/jquery.mCustomScrollbar.js',
		'js/theme.js',
    ];
	
    public $depends = [
        'yii\web\YiiAsset',
		'yii\bootstrap4\BootstrapAsset',
    ];
}
