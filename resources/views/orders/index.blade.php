@foreach ($orders as $order)
    <div class="order">
        <h3>Order ID: {{ $order->id }}</h3>
        <p>User: {{ $order->user->name }}</p> <!-- Causes N+1 -->
        <p>Total: {{ $order->total_amount }}</p>

        <ul>
            @foreach ($order->items as $item)
               
                 <!-- Also causes N+1 -->
                <li>{{ $item->product->name }} (x {{ $item->quantity }})</li>
            @endforeach
        </ul>
    </div>
@endforeach
