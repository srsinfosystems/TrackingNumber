<?php
namespace TrackingNumber\Crons;
use Plenty\Modules\Cron\Contracts\CronHandler as Cron;

use TrackingNumber\Controllers\ContentController;
use Plenty\Plugin\Log\Loggable;

class TrackingNumberCron extends Cron {
	use Loggable;
	public function handle(ContentController $contentController) {
		$contentController->order_tracking_number();
		//App::call('StockUpdatePlugin\Controllers\ContentController@update_stock');

	}
}
