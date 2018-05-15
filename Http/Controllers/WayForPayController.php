<?php

namespace EFrame\Payment\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use EFrame\Payment\Http\Requests\WayForPayPurchaseRequest;
use EFrame\Payment\Commands\WayForPayProcessPurchaseCommand;

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

        return WayForPayProcessPurchaseCommand::dispatchNow($purchase_request);
    }
}