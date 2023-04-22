# Setup

1. Run `docker compose up database` to start the database.
2. Run `symfony server:start` to start the Symfony server.
3. Make and run migration.

# Add some products

1. Use the following command to create test products:

    `php bin/console app:add-product "Barbell" 149.99 "Olympic Barbell, 20kg, 220cm length, 28mm bar"`

    `php bin/console app:add-product "25Kg Plate" 79.99 "Steel Plate, 25Kg, red"`

2. Alternatively, you can add products by sending a POST request to `/products` endpoint:

    ```
    curl --location 'http://localhost:8000/products' \
    --header 'Content-Type: application/json' \
    --data '{"name":"test product","price":42}'
    ```

# Sample Cart Flow

1. Create a new cart:
   ```
   curl --location --request POST 'http://localhost:8000/carts' \
   --header 'Content-Type: application/json' \
   ```

2. Add a product with a quantity to the cart:
   ```
   curl --location 'http://localhost:8000/carts/1' \
   --header 'Content-Type: application/json' \
   --data '{"product_id":1,"quantity":1}'
   ```

3. Change the quantity of a product in the cart:
   ```
   curl --location 'http://localhost:8000/carts/1' \
   --header 'Content-Type: application/json' \
   --data '{"product_id":1,"quantity":2}'
   ```

4. Remove an item from the cart:
   ```
   curl --location --request DELETE 'http://localhost:8000/carts/1/items/1' \
   --header 'Content-Type: application/json'
   ```