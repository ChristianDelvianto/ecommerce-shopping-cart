<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daily Sales Report</title>
    </head>
    <body>
        <div>
            Date: {{ $date }}

            <div>Total sales: ${{ $grandTotal }}</div>
        </div>

        <div>
            Products that were sold:

            @foreach ($soldProducts as $product)
            <div>
                {{ $product->name }},
                <br />
                amount: {{ $product->total_quantity }}
                <br />
                total sales: {{ $product->total_sales }}
            </div>
            @endforeach
        </div>
    </body>
</html>