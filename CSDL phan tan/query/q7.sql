SELECT * FROM (
    SELECT top 5 * 
    From Product
    ORDER BY UnitPrice DESC
) SQ1
UNION
SELECT * FROM (
    SELECT top 5 *
    From Product
    ORDER BY UnitPrice Asc
) SQ2
order by UnitPrice desc