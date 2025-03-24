if OBJECT_ID ('TotalAmount','p') is not null
	 drop procedure TotalAmount;
go

create procedure TotalAmount @OrderID nvarchar(255) , @TotalAmount float output as
begin
select @TotalAmount = sum(Quantity*SalePrice*(1-Discount))
from Orders
join OrderDetails on Orders.ID = OrderDetails.OrderID
group by OrderDetails.OrderID,Orders.OrderDate
having OrderID = @OrderID
end;
go

declare @t float
exec TotalAMount 'CA-2014-100006',@t output
print @t