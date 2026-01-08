<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stock available</title>
    </head>
    <body>
        <div>
            Hi {{ $userName }}
        </div>

        <div>
            Product stock now available

            <div>
                {{ $product->name }}

                <!-- Add product's URL link here so user can visit directly from email -->
            </div>
        </div>
    </body>
</html>