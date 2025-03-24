select OrderId,OrderDate,sum(Quantity*SalePrice*(1-Discount)) as TotalAmount
from Orders
join OrderDetails on Orders.ID = OrderDetails.OrderID
group by OrderDetails.OrderID,Orders.OrderDate
having sum(Quantity*SalePrice*(1-Discount)) > 8000

order by sum(Quantity*SalePrice*(1-Discount)) desc