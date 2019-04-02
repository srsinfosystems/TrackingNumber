<?php
namespace TrackingNumber\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;

/**
 * Class TrackingNumberRouteServiceProvider
 * @package TrackingNumber\Providers
 */
class TrackingNumberRouteServiceProvider extends RouteServiceProvider
{
	/**
	 * @param Router $router
	 */
	public function map(Router $router)
	{
		$router->get('order_tracking_number', 'TrackingNumber\Controllers\ContentController@cgi_order_tracking_number');
	}

}
