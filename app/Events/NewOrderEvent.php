<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('restaurant-orders'),
            new PrivateChannel('admin-notifications'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new.order';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'table_id' => $this->order->table_id,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
            'customer_name' => $this->order->customer_name,
            'dishes' => $this->order->orderDishes->map(function ($orderDish) {
                return [
                    'dish_name' => $orderDish->dish->name,
                    'quantity' => $orderDish->quantity,
                    'price' => $orderDish->price,
                ];
            }),
            'created_at' => $this->order->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
