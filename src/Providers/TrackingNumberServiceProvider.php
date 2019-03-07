<?php
namespace TrackingNumber\Providers;

use Plenty\Plugin\ServiceProvider;

/**
 * Class TrackingNumberServiceProvider
 * @package TrackingNumber\Providers
 */
class TrackingNumberServiceProvider extends ServiceProvider
{

	/**
	 * Register the service provider.
	 */
	public function register()
	{
		$this->getApplication()->register(TrackingNumberRouteServiceProvider::class);
	}
}
