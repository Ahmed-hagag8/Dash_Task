## Customers Table
The customers table stores the personal and contact information of each customer, including the primary key id, name, email, phone, and city. Each customer can place many orders, forming a one-to-many relationship with the orders table.

## Orders Table
The orders table records each order placed by a customer, containing the primary key id, the foreign key customer_id referencing customers, the order date, status, and total amount. Each order belongs to exactly one customer, but a customer may have multiple orders.

## Order_items Table
The order_items table is the junction (linking) table that resolves the many-to-many relationship between orders and products. It holds the foreign keys order_id and product_id, along with the quantity sold and the unit_price captured at the time of sale, so historical prices are preserved even if product prices change later.

## Products Table
The products table stores the details of each product for sale, including the primary key id, the foreign key category_id, name, price, and stock quantity. Each product belongs to one category, while a category can contain many products (one-to-many).

## Categories Table
The categories table groups products into classifications using a primary key id, a unique name, and an optional description. It serves as the parent table in a one-to-many relationship, where each category can have multiple associated products.

## Reports Table
The reports table stores pre-aggregated daily statistics for the dashboard, such as the report date, total orders, total sales, and new customers. Instead of running heavy queries on live order data, the dashboard reads from this table, which is populated periodically by a scheduled job to keep performance fast.




## Answering the Questions
1- Data Type = Decimal for the Exact value -> why not float because it is approximate.
2- because the current price may change after ordering the order
3- it should show him a error message that the category still have products inside / it should delete the customer but his order still exist
4- category -> products (cascade) ?-> there is no product without category
4- orders -> order_items (cascade) ?-> there is no order_items without order
4- products -> order_items (restrict) ?-> there is products have not orderd or it's order deleted or product deleted but before it it has been ordered
4- customers -> orders (restrict) ?-> the customer may blocked or deleted any time so we do restricted to have his order history