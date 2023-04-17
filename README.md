1. docker compose up database

2. symfony server:start

3. Create Test Products with command:

    php bin/console app:add-product "Barbell" 149.99 "Olympic Barbell, 20kg, 220cm length, 28mm bar"

    php bin/console app:add-product "25Kg Plate" 79.99 "Steel Plate, 25Kg, red"

4. or POST /products
    ```
    curl --location 'http://localhost:8000/products' \
    --header 'Content-Type: application/json' \
    --data '{"name":"test product","price":42}'
    ```

5. TODO: POST /carts

6. TODO: PUT /carts