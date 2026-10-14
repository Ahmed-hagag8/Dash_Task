-- 1. Total sales and number of orders per category, highest sales first
SELECT
    c.name AS category_name,
    COUNT(DISTINCT o.id) AS orders_count,
    SUM(oi.line_total) AS total_sales
FROM categories c
JOIN products p ON p.category_id = c.id
JOIN order_items oi ON oi.product_id = p.id
JOIN orders o ON o.id = oi.order_id
WHERE o.status != 'cancelled'
GROUP BY c.id, c.name
ORDER BY total_sales DESC;

-- 2. Top 5 customers by total spent, with city and number of orders
SELECT
    cu.name AS customer_name,
    cu.city AS customer_city,
    COUNT(DISTINCT o.id) AS order_count,
    SUM(oi.line_total) AS total_spent
FROM customers cu
JOIN orders o ON o.customer_id = cu.id
JOIN order_items oi ON oi.order_id = o.id
WHERE o.status != 'cancelled'
GROUP BY cu.id, cu.name, cu.city
ORDER BY total_spent DESC
LIMIT 5;

-- 3. Customers who have never placed an order
SELECT
    cu.id,
    cu.name,
    cu.email,
    cu.city
FROM customers cu
LEFT JOIN orders o
    ON o.customer_id = cu.id
   AND o.status != 'cancelled'
WHERE o.id IS NULL
ORDER BY cu.name;

-- 4. Products that have never been ordered, and total quantity sold for all other products
SELECT
    p.name AS product_name,
    COALESCE(SUM(oi.quantity), 0) AS total_quantity_sold
FROM products p
LEFT JOIN order_items oi ON oi.product_id = p.id
LEFT JOIN orders o ON o.id = oi.order_id AND o.status != 'cancelled'
GROUP BY p.id, p.name
ORDER BY total_quantity_sold ASC;

-- 5. Sales per month for the current year, in month order
WITH months AS (
    SELECT 1 AS month_number
    UNION ALL SELECT 2
    UNION ALL SELECT 3
    UNION ALL SELECT 4
    UNION ALL SELECT 5
    UNION ALL SELECT 6
    UNION ALL SELECT 7
    UNION ALL SELECT 8
    UNION ALL SELECT 9
    UNION ALL SELECT 10
    UNION ALL SELECT 11
    UNION ALL SELECT 12
),
month_bounds AS (
    SELECT
        month_number,
        STR_TO_DATE(
            CONCAT(YEAR(CURRENT_DATE), '-', LPAD(month_number, 2, '0'), '-01'),
            '%Y-%m-%d'
        ) AS month_start
    FROM months
)
SELECT
    m.month_number,
    YEAR(CURRENT_DATE) AS sales_year,
    COALESCE(SUM(oi.line_total), 0) AS total_sales
FROM month_bounds m
LEFT JOIN orders o
    ON o.date >= m.month_start
   AND o.date < DATE_ADD(m.month_start, INTERVAL 1 MONTH)
   AND o.status != 'cancelled'
LEFT JOIN order_items oi ON oi.order_id = o.id
GROUP BY m.month_number
ORDER BY m.month_number ASC;

-- 6. Categories whose total sales are above 10,000 using HAVING
SELECT
    c.name AS category_name,
    SUM(oi.line_total) AS total_sales
FROM categories c
JOIN products p ON p.category_id = c.id
JOIN order_items oi ON oi.product_id = p.id
JOIN orders o ON o.id = oi.order_id
WHERE o.status != 'cancelled'
GROUP BY c.id, c.name
HAVING SUM(oi.line_total) > 10000
ORDER BY total_sales DESC;
