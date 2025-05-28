select * from tblBookingTicket
select * from tblCustomer
select * from tblCourt
select * from tblBookedCourt

SELECT * FROM tblCourt JOIN tblBookedCourt ON tblBookedCourt.tblCourtId = tblCourt.id WHERE tblBookedCourt.id = 1

SELECT * FROM tblCourt  WHERE id = 1

select * from tblSession

select * from tblUsedItem

select * from tblInvoice

insert into tblUser(id,userName,password,fullName,role) values (2,'test','test','user_testing','receptionist')

SELECT * FROM tblBookingTicket 
left join tblInvoice on tblBookingTicket.Id = tblInvoice.tblBookingTicketId 
WHERE tblBookingTicket.tblCustomerid = 1 and tblInvoice.Id = null

delete from tblInvoice where id = 1;

ALTER TABLE tblInvoice
ADD totalAmount FLOAT;

INSERT INTO tblInvoice(tblBookingTicketId, paymentDate, totalAmount, paymentMethod, tblUserId, tblCustomerId, id) VALUES(1,'2023-01-01',69,'cash',1,1,1)

INSERT INTO tblInvoice(tblBookingTicketId, paymentDate, totalAmount, paymentMethod, tblUserId, tblCustomerId, id) VALUES(1,'2023-01-01',69,'cash',1,1,1)

select * from tblBookedCourt

insert into tblBookedCourt(id,price,tblCourtId,tblBookingTicketId) values (4,500000,2,1)

select * from tblSession

insert into tblSession(id,tblBookedCourtId) vaues(45,1)

select * from tblUser

SELECT * FROM tblUser

SELECT * FROM tblCustomer

SELECT * FROM tblBookingTicket

SELECT * FROM tblCourt

SELECT * FROM tblBookedCourt

SELECT * FROM tblSession

SELECT * FROM tblItem

SELECT * FROM tblUsedItem

SELECT * FROM tblInvoice