select Customer.ID,CustomerName,City,State
from Customer 
join Orders as o on o.CustomerID = Customer.ID
where OrderDate between '2017-12-05' and '2017-12-10' and o.ShipDate-o.OrderDate <= 3
order by State asc, City desc