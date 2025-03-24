select c.ID,CustomerName,count(*) as NumberOfOrders
from Customer as c
join Orders as o on o.CustomerID = c.ID
group by c.ID,CustomerName
having count(*) = (
	select top 1 count(*)
	from Customer as c
	join Orders as o on o.CustomerID = c.ID
	group by c.ID,CustomerName
	order by count(*) desc
)
order by count(*) desc