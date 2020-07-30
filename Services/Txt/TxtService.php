<?php

namespace Modules\CompanyOkada\Services\Txt;

use Illuminate\Support\Facades\Storage;
use Modules\Order\Repositories\OrderRepository;
use Modules\Dashboard\Services\Txt\TxtService as TxtServiceBase;
use  ZipArchive;

class TxtService extends TxtServiceBase
{

	public function build()
	{
		$footers = collect([]);

		$orders = OrderRepository::loadClosedOrders();
		foreach ($orders as $order) {
			foreach ($order->items as $item) {
				
				$file_path = $this->file_path($item);

				if(!Storage::exists($file_path)) 
				{
					$footers->push((object)['file_path' => $file_path, 'order' => $order]);
					$this->header($file_path, $order, $item);
				} 

				$this->item($file_path, $item);
			}
		}

		foreach ($footers as $footer) {
			$this->footer($footer->file_path, $footer->order);
		}
	}

	private function header($file_path, $order, $item)
	{
		Storage::append($file_path, 
			'*' .
			'HG' . addString($order->id, 3, '0') .
			'000' .
			addString($order->order_client->client_id, 6, '0') .
			substr($order->closing_date, 8, 2) .
			substr($order->closing_date, 5, 2) .
			substr($order->closing_date, 0, 4) .
			substr($order->closing_date, 11, 2) . 
			substr($order->closing_date, 14, 2) .
			addString($order->order_saller->saller_id, 3, '0') .
			addString($order->order_payment->payment_id, 2, '0') .
			'00000000000000000000' .
			addString($this->subsidiary_id($item), 6, '0') .
			$order->delivery_name_alias);
	}

	private function item($file_path, $item)
	{
		Storage::append($file_path, 
			addString($item->product->barcode, 20, ' ', false) . 
			addString($item->qty, 6, '0') . 
			addString(str_replace('.', '', $item->price), 8, '0') . 
			'0000000000001');
	}

	private function footer($file_path, $order)
	{

		Storage::append($file_path, "OBSERVACAO: " . $order->observation);
	}



	private function file_path($item)
	{
		$product = $item->product;
		$subsidiary_id = $this->subsidiary_id($item);


		if (substr($product->sku, 0, 2) != 'MO') {
			$from = 'importados';
		} else {
			$from = 'nacional';
		}

		return $this->path_base.'filial_'.$subsidiary_id.'/'.$from.'/'.addString($subsidiary_id, 6, '0').'_'.addString($item->order->id, 7, '0') . '.txt';
	}

	private function subsidiary_id($item)
	{
		if($item->product->subsidiaries_product){
			return $item->product->subsidiaries_product->subsidiary_id;
		} else {
			return '';
		}
	}

}