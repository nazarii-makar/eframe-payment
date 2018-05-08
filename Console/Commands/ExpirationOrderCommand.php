<?php

namespace EFrame\Payment\Console\Commands;

use EFrame\Payment\Order;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;

/**
 * Class ExpirationOrderCommand
 * @package EFrame\Payment\Console\Commands
 */
class ExpirationOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:expiration-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expiration orders';

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * UpdateSubscriptionCommand constructor.
     *
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Order::expired()->update([
            'status' => Order::STATUS_EXPIRED,
        ]);

        $this->info('Expiration orders finished');
    }
}
