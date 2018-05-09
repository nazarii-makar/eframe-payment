<?php

namespace EFrame\Payment\Http\Controllers;

use Illuminate\Http\Request;
use EFrame\Payment\Events\OrderPurchased;
use EFrame\Payment\Jobs\WayForPayProcessPurchase;
use Laravel\Lumen\Routing\Controller as BaseController;
use EFrame\Payment\Http\Requests\WayForPayPurchaseRequest;

class WayForPayController extends BaseController
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchase(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        /** @var WayForPayPurchaseRequest $purchase_request */
        $purchase_request = WayForPayPurchaseRequest::createFrom(
            $request->replace($data)
        );

        $purchase_request->setContainer(
            app()
        )->validateResolved();

        return $this->dispatchNow(new WayForPayProcessPurchase($purchase_request));
    }
}