<?php

namespace Modules\CompanyOkada\Http\ViewComposers\Pdf;

use Modules\Dashboard\Services\ViewComposer\ServiceComposer;
use Modules\Subsidiary\Repositories\OrderRepository;
use Modules\ProductStockNowAfter\Entities\Order;
use Modules\Subsidiary\Entities\Subsidiary;

class OrderComposer extends ServiceComposer {

    private $subsidiaries;

    public function assign($view)
    {
        $this->subsidiaries($view->order);
    }

    private function subsidiaries($order)
    {
        $order = Order::with(['order_client', 'order_client.order_client_address', 'order_saller', 'order_payment', 'items', 'items.item_product'])->find($order->id);
        $subsidiaries = $this->loadSubsidiaries($order);



        //dd($subsidiaries[0]);


        $this->subsidiaries = $subsidiaries;
    }

    public static function loadSubsidiaries($order)
    {
        $items_grouped_by_subsidiaries = $order->items->groupBy(function ($item, $key) {
            return $item->item_product->subsidiary_id;
        });

        //dd(($items_grouped_by_subsidiaries[2][0])->product_stock_now_after);

        $subsidiaries_id = $items_grouped_by_subsidiaries->keys();
        $subsidiaries = collect([]);

        foreach ($subsidiaries_id as $subsidiary_id) 
        {
            $subsidiary = Subsidiary::find($subsidiary_id);
            if(!$subsidiary)
            {
                $subsidiary = (object)['id' => 0, 'name' => 'SEM FILIAL'];
            }

            $subsidiary->items = $items_grouped_by_subsidiaries[$subsidiary_id];
            $subsidiary->items_now = collect([]);
            $subsidiary->items_after = collect([]);
            
            foreach ($subsidiary->items as $item) {
                if($item->item_product_stock_now_after->qty_now > 0)
                {
                    $subsidiary->items_now->push($item);
                }
                if($item->item_product_stock_now_after->qty_after > 0)
                {
                    $subsidiary->items_after->push($item);
                }
            }


            $total_gross = 0;
            $total_discount = 0;
            $total_addition = 0;
            $total_tax = 0;
            $total = 0;

            foreach ($subsidiary->items as $item) {
                $total_gross+= $item->total_gross;
                $total_discount+= $item->total_discount_value;
                $total_addition+= $item->total_addition_value;
                $total_tax+= $item->total_tax_value;
                $total+= $item->total;              
            }

            $subsidiary->total_gross = $total_gross;
            $subsidiary->total_discount = $total_discount;
            $subsidiary->total_addition = $total_addition;
            $subsidiary->total_tax = $total_tax;
            $subsidiary->total = $total;


            $subsidiaries->push($subsidiary);
        }

        return $subsidiaries;
    }    

    public function view($view)
    {
        $view->with('subsidiaries', $this->subsidiaries);
    }

} 



