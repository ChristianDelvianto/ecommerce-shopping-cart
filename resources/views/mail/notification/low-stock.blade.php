<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Low Stock Product</title>
    </head>
    <body>
        <div>
            One of our product:

            <div>
                {{ $product->name }}
                
                <!-- Other product info here -->
            </div>

            @if ($quantity > 0)
                Now only has {{ $quantity }} stock available.
            @else
                Now out of stock.
            @endif
        </div>
    </body>
</html>