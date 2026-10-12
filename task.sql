#part1 Q1
SELECT * FROM productlines;
#part1 Q2
SELECT customerName,phone,country FROM customers;
#part1 Q3
SELECT firstName,lastName FROM employees AS full_name;
#part1 Q4
SELECT DISTINCT country FROM customers;
#part1 Q5
SELECT productCode, productName,buyPrice FROM products ORDER BY productCode DESC LIMIT 10;
#part1 Q6
SELECT productName, buyPrice,MSRP,(MSRP-buyPrice)AS profit FROM products;
#part1 Q7
SELECT DISTINCT status FROM orders;
#part1 Q8
SELECT COUNT(*) FROM customers;
SELECT COUNT(*) FROM products;
SELECT COUNT(*) FROM orders;
-------------------------------------------------------------------------------------------------
#part2 Q9
SELECT * FROM `customers` WHERE `country`= "France" ORDER BY `creditLimit`DESC
#part2 Q10
SELECT * FROM `products` WHERE `quantityInStock`< 1000 ORDER BY `quantityInStock`ASC;
#part2 Q11
SELECT * FROM `products` WHERE `productName`LIKE '%ford%';
#part2 Q12
SELECT * FROM `customers` WHERE `country` IN ('USA','UK','Germany');
#part2 Q13
SELECT * FROM `products` WHERE `buyPrice`BETWEEN 50 AND 100;
#part2 Q14
SELECT * FROM `customers` WHERE `creditLimit`=0;
#part2 Q15
SELECT * FROM `customers` WHERE `salesRepEmployeeNumber` IS NULL;
#part2 Q16
SELECT * FROM `orders` WHERE `orderDate` BETWEEN 2004-01-01 AND 2004-12-31;
SELECT * FROM `orders` WHERE `orderDate`LIKE '%2004%';
#part2 Q17
SELECT * FROM `orders` WHERE `shippedDate` IS NULL;
#part2 Q18
SELECT status,comments FROM `orders` WHERE `status` IN ('cancelled','on hold');
#part2 Q19
SELECT * FROM `employees` WHERE `jobTitle` LIKE 'sales%';
#part2 Q20
SELECT * FROM `products` ORDER BY `MSRP` DESC LIMIT 5;
#part2 Q21
SELECT * FROM `orders` WHERE `shippedDate`>`requiredDate`;
#part2 Q22
-----------------------------------------------------------------------------------------------------
#part3 Q23
SELECT country, COUNT(*) FROM customers GROUP BY `country` Order BY COUNT(*) DESC;;
#part3 Q24
SELECT productLine,COUNT(*) FROM products GROUP BY `productLine`;
#part3 Q25
SELECT productLine, ROUND(AVG(buyPrice),2) FROM products GROUP BY productLine;
#part3 Q26
SELECT status, COUNT(*) FROM orders GROUP BY status;
#part3 Q27
SELECT YEAR(paymentDate) AS payment_year, SUM(amount) AS total_amount FROM payments GROUP BY payment_year ORDER BY payment_year;
#part3 Q28
SELECT productLine, MIN(buyPrice), MAX(buyPrice) FROM products GROUP BY productLine;
#part3 Q29
SELECT COUNT(*) As numberofcustomers FROM customers GROUP BY country HAVING numberofcustomers>5;
#part3 Q30
SELECT orderNumber,SUM(`quantityOrdered`*`priceEach`) FROM orderdetails GROUP BY orderNumber;
#part3 Q31
SELECT orderNumber,(`quantityOrdered`*`priceEach`) as finalprice FROM orderdetails GROUP BY finalprice DESC;
#part3 Q32
SELECT COUNT(*) as number_of_orders,month(orderDate) from orders WHERE orderDate LIKE '%2004%' GROUP BY month(orderDate);
----------------------------------------------------------------------------------------------------------------------------------
#part4 Q33
SELECT Customers.customerName, Orders.orderNumber FROM customers LEFT JOIN orders ON customers.customerNumber = Orders.customerNumber ORDER BY Customers.customerName;
#part4 Q34
SELECT c.customerName, CONCAT(e.firstName, ' ', e.lastName) AS salesRepName FROM customers c LEFT JOIN employees e ON c.salesRepEmployeeNumber = e.employeeNumber GROUP BY customerName; ------?
#part4 Q35
SELECT CONCAT(employees.firstName+ ' '+employees.lastName) AS employee_name, CONCAT(offices.city+', '+offices.country) AS Location FROM employees LEFT JOIN offices ON employees.officeCode=offices.officeCode;  #concat problem -> solved
#part4 Q36
SELECT products.productName, orderdetails.quantityOrdered, orderdetails.priceEach, (orderdetails.quantityOrdered*orderdetails.priceEach) AS total FROM orderdetails, products, orders WHERE orders.orderNumber=10100; #??????
#part4 Q37
SELECT products.productName, productlines.textDescription FROM productlines LEFT JOIN products ON products.productLine=productlines.productLine GROUP BY products.productName;
#part4 Q38
SELECT customers.customerNumber, COUNT(orders.orderNumber) AS orderNumber FROM orders LEFT JOIN customers ON customers.customerNumber=orders.customerNumber GROUP BY customerNumber;
#part4 Q39
SELECT customers.customerNumber, COUNT(orders.orderNumber) AS ordercount FROM orders LEFT JOIN customers ON customers.customerNumber=orders.customerNumber WHERE ordercount = 0: #??????
#part4 Q40
SELECT CONCAT(employees.firstName, ' ', employees.lastName) AS Name, employees.reportsTo FROM employees ORDER BY Name; #retrieve manager name
#part4 Q41
SELECT customers.customerName, payments.amount FROM payments LEFT JOIN customers ON customers.customerNumber= payments.customerNumber ORDER BY payments.amount DESC LIMIT 10;
#part4 Q42

#part4 Q43

#part4 Q44

#part5 Q45








































