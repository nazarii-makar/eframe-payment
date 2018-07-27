<?php

namespace EFrame\Payment\Http\Controllers;

use EFrame\Payment\Http\Requests\{
    WayForPayPurchaseRequest,
    WayForPayVerifyRequest
};
use EFrame\Payment\Commands\{
    WayForPayProcessPurchaseCommand,
    WayForPayProcessVerifyCommand
};
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

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

        /** @var WayForPayPurchaseRequest $purchaseRequest */
        $purchaseRequest = WayForPayPurchaseRequest::createFrom(
            $request->replace($data)
        );

        $purchaseRequest->setContainer(
            app()
        )->validateResolved();

        return WayForPayProcessPurchaseCommand::dispatchNow($purchaseRequest);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        /** @var WayForPayVerifyRequest $verifyRequest */
        $verifyRequest = WayForPayVerifyRequest::createFrom(
            $request->replace($data)
        );

        $verifyRequest->setContainer(
            app()
        )->validateResolved();

        return WayForPayProcessVerifyCommand::dispatchNow($verifyRequest);
    }
}