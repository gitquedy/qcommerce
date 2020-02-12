<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route url

Route::group(['middleware' => 'auth'], function()
{
	Route::get('/', 'DashboardController@index')->name('dashboard');
	
	//Ajax
	Route::post('ajax/get_notification', 'AjaxController@get_notification')->name('ajax_get_notification');

	// simple crud
	Route::get('/crud/listView', 'CrudController@listView')->name('crud.listView');
	Route::get('crud/delete/{crud}', 'CrudController@delete');
	Route::post('crud/massDelete/', 'CrudController@massDelete')->name('crud.massDelete');
	Route::post('crud/massArchived/', 'CrudController@massArchived')->name('crud.massArchived');
	Route::resource('/crud', 'CrudController');

	Route::get('/lazop/receive', 'LazopController@receive')->name('lazop.receive');
	Route::get('/shop/form', 'ShopController@form')->name('shop.form');
	Route::resource('/shop', 'ShopController');
	
	Route::resource('/barcode', 'BarcodeController');
	Route::post('/barcode/check_barcode', 'BarcodeController@checkBarcode')->name('barcode.checkBarcode');
	Route::post('/barcode/packed_items', 'BarcodeController@packedItems')->name('barcode.packedItems');
	
	Route::resource('/product', 'ProductController');
	Route::get('/product/edit/{id}', 'ProductController@edit')->name('product.edit');
	Route::post('/product/update', 'ProductController@update')->name('product.update');
	Route::post('/product/upload_image', 'ProductController@upload_image')->name('product.upload_image');
	Route::post('/product/ajax_duplicate_modal', 'ProductController@ajax_duplicate_modal')->name('product.ajax_duplicate_modal');
	Route::post('/product/process_duplicate_product', 'ProductController@process_duplicate_product')->name('product.process_duplicate_product');
	Route::post('/product/mass_copy', 'ProductController@mass_copy')->name('product.mass_copy');
	Route::post('/product/bulkremove', 'ProductController@bulkremove')->name('product.bulkremove');
	Route::post('/product/ajaxlistproduct', 'ProductController@ajaxlistproduct')->name('product.ajaxlistproduct');
	
	
	Route::resource('/sku', 'SkuController');
	Route::get('/sku/create/', 'SkuController@create')->name('sku.create');
	Route::post('/sku/add/', 'SkuController@add')->name('sku.add');
	Route::get('/sku/edit/{id}', 'SkuController@edit')->name('sku.edit');
	Route::post('/sku/update/', 'SkuController@update')->name('sku.update');
	Route::get('/sku/delete/{id}', 'SkuController@delete')->name('sku.delete');
	Route::post('/sku/bulkremove', 'SkuController@bulkremove')->name('sku.bulkremove');
	Route::get('/sku/skuproducts/{id}', 'SkuController@skuproducts')->name('sku.skuproducts');
	Route::post('/sku/addproduct/', 'SkuController@addproduct')->name('sku.addproduct');
	Route::post('/sku/addproductmodal/', 'SkuController@addproductmodal')->name('sku.addproductmodal');
	Route::post('/sku/removeskuproduct/', 'SkuController@removeskuproduct')->name('sku.removeskuproduct');
	Route::post('/sku/quickupdate/', 'SkuController@quickUpdate')->name('sku.quickUpdate');
	
	Route::resource('/category', 'CategoryController');
	Route::get('/category/create/', 'CategoryController@create')->name('category.create');
	Route::post('/category/add/', 'CategoryController@add')->name('category.add');
	Route::get('/category/edit/{id}', 'CategoryController@edit')->name('category.edit');
	Route::post('/category/update/', 'CategoryController@update')->name('category.update');
	Route::get('/category/delete/{id}', 'CategoryController@delete')->name('category.delete');
	Route::post('/category/bulkremove', 'CategoryController@bulkremove')->name('category.bulkremove');
	Route::post('/category/add_ajax/', 'CategoryController@add_ajax')->name('category.add_ajax');
	
	
	Route::resource('/brand', 'BrandController');
	Route::get('/brand/create/', 'BrandController@create')->name('brand.create');
	Route::post('/brand/add/', 'BrandController@add')->name('brand.add');
	Route::get('/brand/edit/{id}', 'BrandController@edit')->name('brand.edit');
	Route::post('/brand/update/', 'BrandController@update')->name('brand.update');
	Route::get('/brand/delete/{id}', 'BrandController@delete')->name('brand.delete');
	Route::post('/brand/bulkremove', 'BrandController@bulkremove')->name('brand.bulkremove');
	Route::post('/brand/add_ajax/', 'BrandController@add_ajax')->name('brand.add_ajax');
	
	
	Route::get('/exp1', 'ExpController@exp1')->name('exp1');

	Route::get('/order/readyToShip/{order}', 'OrderController@readyToShip')->name('order.readyToShip');
	Route::get('/order/cancel/{order}', 'OrderController@cancel')->name('order.cancel');
	Route::resource('/order', 'OrderController');
	Route::get('/orders_pending', 'OrderController@orders_pending')->name('order.pending');
	Route::get('/orders_printing', 'OrderController@orders_printing')->name('order.printing');
	Route::get('/orders_ready_to_ship', 'OrderController@orders_ready_to_ship')->name('order.ready_to_ship');
	Route::get('/orders_shipped', 'OrderController@orders_shipped')->name('order.shipped');
	Route::get('/orders_delivered', 'OrderController@orders_delivered')->name('order.delivered');
	Route::get('/order/print_shipping/{id}', 'OrderController@print_shipping')->name('order.print_shipping');
	Route::post('/order/print_shipping_mass', 'OrderController@print_shipping_mass')->name('order.print_shipping_mass');
	
	


	// Route Dashboards
	Route::get('/dashboard-analytics', 'DashboardController@dashboardAnalytics');
	Route::get('/dashboard-ecommerce', 'DashboardController@dashboardEcommerce');
	Route::get('/testr', 'DashboardController@testr');

	// Route Apps
	Route::get('/app-email', 'EmailAppController@emailApp');
	Route::get('/app-chat', 'ChatAppController@chatApp');
	Route::get('/app-todo', 'ToDoAppController@todoApp');
	Route::get('/app-calender', 'CalenderAppController@calenderApp');
	Route::get('/app-user-stats', 'UserStatsAppController@user_stats');
	Route::get('/app-ecommerce-shop', 'EcommerceAppController@ecommerce_shop');
	Route::get('/app-ecommerce-wishlist', 'EcommerceAppController@ecommerce_wishlist');
	Route::get('/app-ecommerce-checkout', 'EcommerceAppController@ecommerce_checkout');

	// Route Data List
	Route::resource('/data-list-view','DataListController');
	Route::resource('/data-thumb-view', 'DataThumbController');


	// Route Content
	Route::get('/content-grid', 'ContentController@grid');
	Route::get('/content-typography', 'ContentController@typography');
	Route::get('/content-text-utilities', 'ContentController@text_utilities');
	Route::get('/content-syntax-highlighter', 'ContentController@syntax_highlighter');
	Route::get('/content-helper-classes', 'ContentController@helper_classes');

	// Route Color
	Route::get('/colors', 'ContentController@colors');

	// Route Icons
	Route::get('/icons-feather', 'IconsController@icons_feather');
	Route::get('/icons-font-awesome', 'IconsController@icons_font_awesome');

	// Route Cards
	Route::get('/card-basic', 'CardsController@card_basic');
	Route::get('/card-advance', 'CardsController@card_advance');
	Route::get('/card-statistics', 'CardsController@card_statistics');
	Route::get('/card-analytics', 'CardsController@card_analytics');
	Route::get('/card-actions', 'CardsController@card_actions');

	// Route Components
	Route::get('/component-alert', 'ComponentsController@alert');
	Route::get('/component-buttons', 'ComponentsController@buttons');
	Route::get('/component-breadcrumbs', 'ComponentsController@breadcrumbs');
	Route::get('/component-carousel', 'ComponentsController@carousel');
	Route::get('/component-collapse', 'ComponentsController@collapse');
	Route::get('/component-dropdowns', 'ComponentsController@dropdowns');
	Route::get('/component-list-group', 'ComponentsController@list_group');
	Route::get('/component-modals', 'ComponentsController@modals');
	Route::get('/component-pagination', 'ComponentsController@pagination');
	Route::get('/component-navs', 'ComponentsController@navs');
	Route::get('/component-navbar', 'ComponentsController@navbar');
	Route::get('/component-tabs', 'ComponentsController@tabs');
	Route::get('/component-pills', 'ComponentsController@pills');
	Route::get('/component-tooltips', 'ComponentsController@tooltips');
	Route::get('/component-popovers', 'ComponentsController@popovers');
	Route::get('/component-badges', 'ComponentsController@badges');
	Route::get('/component-pill-badges', 'ComponentsController@pill_badges');
	Route::get('/component-progress', 'ComponentsController@progress');
	Route::get('/component-media-objects', 'ComponentsController@media_objects');
	Route::get('/component-spinner', 'ComponentsController@spinner');
	Route::get('/component-toast', 'ComponentsController@toast');

	// Route Extra Components
	Route::get('/ex-component-avatar', 'ExtraComponentsController@avatar');
	Route::get('/ex-component-chips', 'ExtraComponentsController@chips');
	Route::get('/ex-component-divider', 'ExtraComponentsController@divider');

	// Route Forms
	Route::get('/form-select', 'FormsController@select');
	Route::get('/form-switch', 'FormsController@switch');
	Route::get('/form-checkbox', 'FormsController@checkbox');
	Route::get('/form-radio', 'FormsController@radio');
	Route::get('/form-input', 'FormsController@input');
	Route::get('/form-input-groups', 'FormsController@input_groups');
	Route::get('/form-number-input', 'FormsController@number_input');
	Route::get('/form-textarea', 'FormsController@textarea');
	Route::get('/form-date-time-picker', 'FormsController@date_time_picker');
	Route::get('/form-layout', 'FormsController@layouts');
	Route::get('/form-wizard', 'FormsController@wizard');
	Route::get('/form-validation', 'FormsController@validation');

	// Route Tables
	Route::get('/table', 'TableController@table');
	Route::get('/table-datatable', 'TableController@datatable');
	Route::get('/table-ag-grid', 'TableController@ag_grid');

	// Route Pages
	Route::get('/page-user-profile', 'PagesController@user_profile');
	Route::get('/page-faq', 'PagesController@faq');
	Route::get('/page-knowledge-base', 'PagesController@knowledge_base');
	Route::get('/page-kb-category', 'PagesController@kb_category');
	Route::get('/page-kb-question', 'PagesController@kb_question');
	Route::get('/page-search', 'PagesController@search');
	Route::get('/page-invoice', 'PagesController@invoice');
	Route::get('/page-user-settings', 'PagesController@user_settings');

	// Route Miscellaneous Pages
	Route::get('/page-coming-soon', 'MiscellaneousController@coming_soon');
	Route::get('/error-404', 'MiscellaneousController@error_404');
	Route::get('/error-500', 'MiscellaneousController@error_500');
	Route::get('/page-not-authorized', 'MiscellaneousController@not_authorized');
	Route::get('/page-maintenance', 'MiscellaneousController@maintenance');

	// Route Charts & Google Maps
	Route::get('/chart-apex', 'ChartsController@apex');
	Route::get('/chart-chartjs', 'ChartsController@chartjs');
	Route::get('/chart-echarts', 'ChartsController@echarts');
	Route::get('/maps-google', 'ChartsController@maps_google');

	// Route Extension Components
	Route::get('/ext-component-sweet-alerts', 'ExtensionController@sweet_alert');
	Route::get('/ext-component-toastr', 'ExtensionController@toastr');
	Route::get('/ext-component-noui-slider', 'ExtensionController@noui_slider');
	Route::get('/ext-component-file-uploader', 'ExtensionController@file_uploader');
	Route::get('/ext-component-quill-editor', 'ExtensionController@quill_editor');
	Route::get('/ext-component-drag-drop', 'ExtensionController@drag_drop');
	Route::get('/ext-component-tour', 'ExtensionController@tour');
	Route::get('/ext-component-clipboard', 'ExtensionController@clipboard');
	Route::get('/ext-component-plyr', 'ExtensionController@plyr');
	Route::get('/ext-component-context-menu', 'ExtensionController@context_menu');
	Route::get('/ext-component-i18n', 'ExtensionController@i18n');
});

// Route Authentication Pages
// Route::get('/auth-login', 'AuthenticationController@login');
// Route::get('/auth-register', 'AuthenticationController@register');
// Route::get('/auth-forgot-password', 'AuthenticationController@forgot_password');
// Route::get('/auth-reset-password', 'AuthenticationController@reset_password');
// Route::get('/auth-lock-screen', 'AuthenticationController@lock_screen');
Auth::routes();

Route::post('/login/validate', 'Auth\LoginController@validate_api');
