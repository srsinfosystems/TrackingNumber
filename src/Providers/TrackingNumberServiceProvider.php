<?php
namespace TrackingNumber\Providers;

use Plenty\Plugin\ServiceProvider;
use Plenty\Modules\Cron\Services\CronContainer;
use TrackingNumber\Crons\TrackingNumberCron;

/**
 * Class TrackingNumberServiceProvider
 * @package TrackingNumber\Providers
 */
class TrackingNumberServiceProvider extends ServiceProvider
{

	public function boot(CronContainer $container) {
		$container->add(CronContainer::EVERY_FIFTEEN_MINUTES, TrackingNumberCron::class);
	}
	/**
	 * Register the service provider.
	 */
	public function register()
	{
		$this->getApplication()->register(TrackingNumberRouteServiceProvider::class);
	}
}
