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
	Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function(){
		Route::get('/', 'Admin\DashboardController@index')->name('admin.dashboard');
		Route::resource('/manageuser', 'Admin\UserManagementController');
		Route::get('/manageuser/delete/{user}', 'Admin\UserManagementController@delete');
		Route::get('/manageuser/settings', 'Admin\UserManagementController@settings')->name('user.settings');
		Route::resource('/promocode', 'Admin\PromocodeController');
		Route::get('/promocode/delete/{promocode}', 'Admin\PromocodeController@delete');
		Route::post('/promocode/checkPromocode', 'Admin\PromocodeController@checkPromocode')->name('promocode.checkPromocode');

	});


	Route::group(['prefix' => 'paypal'], function(){
		Route::post('payment/{plan}', 'PayPalController@payment')->name('paypal.payment');
		Route::get('cancel/{billing}', 'PayPalController@cancel')->name('paypal.payment.cancel');
		Route::get('payment/success/{billing}', 'PayPalController@success')->name('paypal.payment.success');
		Route::post('payment/confirm/{billing}', 'PayPalController@confirm')->name('paypal.payment.confirm');
	});




	Route::get('/', 'DashboardController@index')->name('dashboard');
	
	//Own(Anyone can access)
	// Route::get('/user/edit_profile/', 'UserController@editProfile')->name('user.editProfile');
	Route::get('/user/settings', 'UserController@settings')->name('user.settings');
	Route::post('/user/update_profile/', 'UserController@updateProfile')->name('user.updateProfile');
	// Route::get('/user/change_password/', 'UserController@changePassword')->name('user.changePassword');
	Route::post('/user/update_password/', 'UserController@updatePassword')->name('user.updatePassword');

	

	//Ajax
	Route::post('ajax/get_notification', 'AjaxController@get_notification')->name('ajax_get_notification');
	Route::get('/lazop/receive', 'LazopController@receive')->name('lazop.receive');
	Route::get('/shop/shopeeGetLogistics/{shop}', 'ShopController@shopeeGetLogistics');
	Route::post('/barcode/view_barcode', 'BarcodeController@viewBarcode')->name('barcode.viewBarcode');


	//application
	Route::get('/application', 'ApplicationController@index');
	Route::get('/application/{package}', 'ApplicationController@show');



	Route::group(['middleware' => 'permission:shop.manage'], function()
	{
		Route::get('/shop/form', 'ShopController@form')->name('shop.form');
		Route::resource('/shop', 'ShopController');
	});
	
	Route::group(['middleware' => 'permission:barcode.manage'], function()
	{
		Route::resource('/barcode', 'BarcodeController')->only(['index']);
		Route::post('/barcode/check_barcode', 'BarcodeController@checkBarcode')->name('barcode.checkBarcode');
		Route::post('/barcode/packed_items', 'BarcodeController@packedItems')->name('barcode.packedItems');
	});

	Route::group(['middleware' => 'permission:product.manage'], function()
	{
		Route::resource('/product', 'ProductController')->only(['index', 'edit', 'update']);
		Route::post('/product/upload_image', 'ProductController@upload_image')->name('product.upload_image');
		Route::post('/product/ajax_duplicate_modal', 'ProductController@ajax_duplicate_modal')->name('product.ajax_duplicate_modal');
		Route::post('/product/process_duplicate_product', 'ProductController@process_duplicate_product')->name('product.process_duplicate_product');
		Route::post('/product/mass_copy', 'ProductController@mass_copy')->name('product.mass_copy');
		Route::post('/product/bulkremove', 'ProductController@bulkremove')->name('product.bulkremove');
		Route::post('/product/ajaxlistproduct', 'ProductController@ajaxlistproduct')->name('product.ajaxlistproduct');
		Route::get('/product/duplicateForm', 'ProductController@duplicateForm')->name('product.duplicateForm');
		Route::post('/product/duplicateProudcts', 'ProductController@duplicateProudcts')->name('product.duplicateProudcts');
		Route::get('/product/searchProduct', 'ProductController@searchProduct')->name('product.searchProduct');
		Route::get('/product/headers', 'ProductController@headers')->name('product.headers');
	});
	
	Route::group(['middleware' => 'permission:sku.manage'], function()
	{
		Route::get('/sku/import/', 'SkuController@import')->name('sku.import');
		Route::post('/sku/import/', 'SkuController@submitImport')->name('sku.submitImport');
		Route::resource('/sku', 'SkuController');
		Route::get('/sku/create/', 'SkuController@create')->name('sku.create');
		Route::post('/sku/add/', 'SkuController@add')->name('sku.add');
		Route::get('/sku/edit/{id}', 'SkuController@edit')->name('sku.edit');
		Route::post('/sku/update/', 'SkuController@update')->name('sku.update');
		Route::get('/sku/delete/{id}', 'SkuController@delete')->name('sku.delete');
		Route::post('/sku/bulkremove', 'SkuController@bulkremove')->name('sku.bulkremove');
		Route::get('/sku/skuproducts/{sku}', 'SkuController@skuproducts')->name('sku.skuproducts');
		Route::post('/sku/addproduct/', 'SkuController@addproduct')->name('sku.addproduct');
		Route::post('/sku/addproductmodal/', 'SkuController@addproductmodal')->name('sku.addproductmodal');
		Route::post('/sku/removeskuproduct/', 'SkuController@removeskuproduct')->name('sku.removeskuproduct');
		Route::post('/sku/quickupdate/', 'SkuController@quickUpdate')->name('sku.quickUpdate');
		Route::post('/sku/syncskuproducts/', 'SkuController@syncSkuProducts')->name('sku.syncSkuProducts');
		Route::get('/sku/search/{warehouse?}/{search?}/{customer?}/{withQTY?}', 'SkuController@search')->name('sku.search');
	});

	// Route::group(['middleware' => 'permission:warehouse.manage'], function()
	// {
		Route::resource('/warehouse', 'WarehouseController');
		Route::get('/warehouse/delete/{warehouse}', 'WarehouseController@delete');
		Route::post('/warehouse/addWarehouseModal', 'WarehouseController@addWarehouseModal')->name('warehouse.addWarehouseModal');
		Route::post('/warehouse/addWarehouseAjax', 'WarehouseController@addWarehouseAjax')->name('warehouse.addWarehouseAjax');
	// });

	// Route::group(['middleware' => 'permission:adjustment.manage'], function()
	// {
		Route::get('/adjustment/import/', 'AdjustmentController@import')->name('adjustment.import');
		Route::post('/adjustment/import/', 'AdjustmentController@submitImport')->name('adjustment.submitImport');
		Route::get('/adjustment/first', 'AdjustmentController@first'); //temporary remove this after update
		Route::resource('/adjustment', 'AdjustmentController');
		Route::post('/adjustment/viewAdjustmentModal/{adjustment}', 'AdjustmentController@viewAdjustmentModal')->name('adjustment.viewAdjustmentModal');
		Route::get('/adjustment/delete/{adjustment}', 'AdjustmentController@delete');

	// });

	// Route::group(['middleware' => 'permission:transfer.manage'], function()
	// {
		Route::resource('/transfer', 'TransferController');
		Route::post('/transfer/viewTransferModal/{transfer}', 'TransferController@viewTransferModal')->name('transfer.viewTransferModal');
		Route::get('/transfer/delete/{transfer}', 'TransferController@delete');

	// });

	Route::group(['middleware' => 'permission:supplier.manage'], function()
	{
		Route::resource('/supplier', 'SupplierController');
		Route::get('/supplier/delete/{id}', 'SupplierController@delete')->name('supplier.delete');
		Route::post('/supplier/bulkremove', 'SupplierController@bulkremove')->name('supplier.bulkremove');
		Route::post('/supplier/add_ajax', 'SupplierController@add_ajax')->name('supplier.add_ajax');
	});

	Route::group(['middleware' => 'permission:category.manage'], function()
	{
		Route::resource('/category', 'CategoryController');
		Route::get('/category/create/', 'CategoryController@create')->name('category.create');
		Route::post('/category/add/', 'CategoryController@add')->name('category.add');
		Route::get('/category/edit/{id}', 'CategoryController@edit')->name('category.edit');
		Route::post('/category/update/', 'CategoryController@update')->name('category.update');
		Route::get('/category/delete/{id}', 'CategoryController@delete')->name('category.delete');
		Route::post('/category/bulkremove', 'CategoryController@bulkremove')->name('category.bulkremove');
		Route::post('/category/add_ajax/', 'CategoryController@add_ajax')->name('category.add_ajax');
	});
	

	Route::group(['middleware' => 'permission:category.manage'], function()
	{
		Route::resource('/brand', 'BrandController');
		Route::get('/brand/create/', 'BrandController@create')->name('brand.create');
		Route::post('/brand/add/', 'BrandController@add')->name('brand.add');
		Route::get('/brand/edit/{id}', 'BrandController@edit')->name('brand.edit');
		Route::post('/brand/update/', 'BrandController@update')->name('brand.update');
		Route::get('/brand/delete/{id}', 'BrandController@delete')->name('brand.delete');
		Route::post('/brand/bulkremove', 'BrandController@bulkremove')->name('brand.bulkremove');
		Route::post('/brand/add_ajax/', 'BrandController@add_ajax')->name('brand.add_ajax');
	});

	Route::group(['middleware' => 'permission:user.manage'], function()
	{
		Route::resource('/user', 'UserController');
		Route::get('/user/delete/{user}', 'UserController@delete');
	});


	// Route::group(['middleware' => 'permission:customer.manage'], function()
	// {
		Route::resource('/customer', 'CustomerController');
		Route::get('/customer/delete/{customer}', 'CustomerController@delete');
		Route::post('/customer/addCustomerModal', 'CustomerController@addCustomerModal')->name('customer.addCustomerModal');
		Route::post('/customer/addCustomerAjax', 'CustomerController@addCustomerAjax')->name('customer.addCustomerAjax');
	// });

	// Route::group(['middleware' => 'permission:deposit.manage'], function()
	// {
		Route::resource('/deposit', 'DepositController');
		Route::get('/deposit/delete/{deposit}', 'DepositController@delete');
		Route::post('/deposit/viewDepositModal/{customer}', 'DepositController@viewDepositModal')->name('deposit.viewDepositModal');
		Route::post('/deposit/addDepositModal/{customer}', 'DepositController@addDepositModal')->name('deposit.addDepositModal');
		Route::post('/deposit/addDepositAjax', 'DepositController@addDepositAjax')->name('deposit.addDepositAjax');
	// });

	// Route::group(['middleware' => 'permission:sales.manage'], function()
	// {
		Route::resource('/sales', 'SalesController');
		Route::get('/sales/delete/{sales}', 'SalesController@delete');
		Route::post('/sales/viewSalesModal/{sales}', 'SalesController@viewSalesModal')->name('sales.viewSalesModal');
	// });

	// Route::group(['middleware' => 'permission:payment.manage'], function()
	// {
		Route::resource('/payment', 'PaymentController');
		Route::get('/payment/delete/{paymment}', 'PaymentController@delete');
		Route::post('/payment/viewPaymentModal/{sales}', 'PaymentController@viewPaymentModal')->name('payment.viewPaymentModal');
		Route::post('/payment/addPaymentModal/{sales}', 'PaymentController@addPaymentModal')->name('payment.addPaymentModal');
		Route::post('/payment/addPaymentAjax', 'PaymentController@addPaymentAjax')->name('payment.addPaymentAjax');
	// });

	// Route::group(['middleware' => 'permission:settings.manage'], function()
	// {
		Route::resource('/settings', 'SettingsController');

		Route::resource('/price_group', 'PriceGroupController');
		Route::get('/price_group/delete/{price_group}', 'PriceGroupController@delete');
		Route::get('/price_group/get_sku', 'PriceGroupController@getSku')->name('price_group.getSku');
	// });
	Route::resource('/plan', 'PlanController')->only(['index', 'show']);
	Route::get('/plan/subscribe/{plan}/{billing?}', 'PlanController@subscribe');
	Route::get('/plan/confirm/{billing}', 'PlanController@confirm')->name('plan.confirm');
	Route::post('/plan/cancel', 'PlanController@cancel')->name('plan.cancel');

	Route::group(['middleware' => 'permission:report.manage'], function()
	{
		Route::get('/reports/', 'ReportsController@index')->name('reports.index');
		Route::get('/reports/outofstock', 'ReportsController@outOfStock')->name('reports.outOfStock');
		Route::get('/reports/productalert', 'ReportsController@productAlert')->name('reports.productAlert');
		Route::get('/reports/topSellingProducts', 'ReportsController@topSellingProducts');
		Route::get('/reports/dailySales', 'ReportsController@dailySales');
	});
	
	Route::group(['middleware' => 'permission:order.manage'], function()
	{
		Route::get('/order/readyToShip/{order}', 'OrderController@readyToShip')->name('order.readyToShip');
		Route::post('/order/readyToShipMultiple/', 'OrderController@readyToShipMultiple')->name('order.readyToShipMultiple');
		Route::get('/order/cancelModal/{order}', 'OrderController@cancelModal');
		Route::post('/order/cancelSubmit/{order}', 'OrderController@cancelSubmit');
		Route::get('/order/cancel/{order}', 'OrderController@cancel')->name('order.cancel');
		Route::get('/order/printPackingList', 'OrderController@printPackingList')->name('order.printPackingList');
		Route::resource('/order', 'OrderController')->only(['index']);
		Route::get('/order/print_shipping/{id}', 'OrderController@print_shipping')->name('order.print_shipping');
		Route::post('/order/print_shipping_mass', 'OrderController@print_shipping_mass')->name('order.print_shipping_mass');
		Route::get('/order/readyToShipShopee/{order}', 'OrderController@readyToShipShopee');
		Route::get('/order/pickupDetailsShopee/{order}', 'OrderController@pickupDetailsShopee');
		Route::post('/order/pickupDetailsPostShopee/{order}', 'OrderController@pickupDetailsPostShopee');
		Route::get('/order/readyToShipDropOff/{order}', 'OrderController@readyToShipDropOff');
		Route::get('/order/headers', 'OrderController@headers')->name('order.headers');
	});

	// Return Reconciliation
	Route::group(['middleware' => 'permission:returnRecon.manage'], function()
	{
		Route::get('/order/reconciliation/returned', 'ReturnController@index')->name('order.returnReconciliation');
		Route::post('/order/reconciliation/returned/reconcile', 'ReturnController@returnReconcile');
		Route::get('/order/reconciliation/returned/headers', 'ReturnController@headers');
		Route::get('/order/reconciliation/returned/reconcileSingle/{order}', 'ReturnController@returnReconcileSingle');
	});

	// Payout Reconciliation
	Route::group(['middleware' => 'permission:payoutRecon.manage'], function()
	{
		//lazada
		Route::get('/order/reconciliation/payout/laz', 'PayoutController@indexLaz');
		Route::get('/order/reconciliation/payout/laz/headers', 'PayoutController@headersLaz');
		Route::post('/order/reconciliation/payout/laz/reconcile', 'PayoutController@payoutReconcileLaz');
		Route::get('/order/reconciliation/payout/laz/reconcileSingle/{LazadaPayout}', 'PayoutController@payoutReconcileSingleLaz');


		
		
		Route::get('/order/reconciliation/payout/laz/{LazadaPayout}', 'PayoutController@showLaz');

		// shopee

		
		Route::get('/order/reconciliation/payout/shopee', 'PayoutController@indexShopee');
		Route::get('/order/reconciliation/payout/shopee/headers', 'PayoutController@headersShopee');
		Route::post('/order/reconciliation/payout/shopee/reconcile', 'PayoutController@payoutReconcileShopee');
		Route::get('/order/reconciliation/payout/shopee/reconcileSingle/{ShopeePayout}', 'PayoutController@payoutReconcileSingleShopee');
	});

	// Shipping fee Reconciliation
	Route::group(['middleware' => 'permission:shippingFeeRecon.manage'], function()
	{
		Route::get('/order/reconciliation/shippingFee', 'ShippingFeeController@index')->name('shippingfee.index');
		Route::get('/order/reconciliation/shippingFee/headers', 'ShippingFeeController@headers')->name('shippingfee.headers');
		Route::get('/order/reconciliation/shippingFee/filed/{order}', 'ShippingFeeController@filed');
		Route::get('/order/reconciliation/shippingFee/resolved/{order}', 'ShippingFeeController@resolved');
		Route::post('/order/reconciliation/shippingFee/massReconcile/', 'ShippingFeeController@massReconcile');
		
	});

	// simple crud
	Route::get('/crud/listView', 'CrudController@listView')->name('crud.listView');
	Route::get('crud/delete/{crud}', 'CrudController@delete');
	Route::post('crud/massDelete/', 'CrudController@massDelete')->name('crud.massDelete');
	Route::post('crud/massArchived/', 'CrudController@massArchived')->name('crud.massArchived');
	Route::resource('/crud', 'CrudController');





	// Route Dashboards
	Route::get('/dashboard-analytics', 'DashboardController@dashboardAnalytics');
	Route::get('/dashboard-ecommerce', 'DashboardController@dashboardEcommerce');

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

Route::get('/ipn/test', 'Api\IpnController@test')->name('ipn.test');
Route::post('ipn/notify','Api\IpnController@postNotify');
Route::post('/login/validate', 'Auth\LoginController@validate_api');