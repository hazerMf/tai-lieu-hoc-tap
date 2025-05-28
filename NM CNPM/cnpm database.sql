CREATE TABLE tblUser (
    id         VARCHAR(50) PRIMARY KEY,
    userName   VARCHAR(50) NOT NULL,
    password   VARCHAR(50) NOT NULL,
    fullName   VARCHAR(50),
    role       VARCHAR(50)
);

CREATE TABLE tblCustomer (
    id      VARCHAR(50) PRIMARY KEY,
    name    VARCHAR(50) NOT NULL,
    address VARCHAR(80),
    phone   VARCHAR(20),
    email   VARCHAR(50),
    note    VARCHAR(250)
);

CREATE TABLE tblBookingTicket (
    id             VARCHAR(50) PRIMARY KEY,
    dateOfContract DATE,
    depositAmount  INT,
    tblCustomerId  VARCHAR(50) REFERENCES tblCustomer(id),
    tblUserId      VARCHAR(50) REFERENCES tblUser(id)
);

CREATE TABLE tblCourt (
    id          VARCHAR(50) PRIMARY KEY,
    type        VARCHAR(50),
    price       INT,
);

CREATE TABLE tblBookedCourt (
    id              VARCHAR(50) PRIMARY KEY,
    startDate       DATE,
    endDate         DATE,
	startHour	    VARCHAR(50),
	endHour			VARCHAR(50),
    tblCourtId      VARCHAR(50) REFERENCES tblCourt(id),
    tblBookingTicketId VARCHAR(50) REFERENCES tblBookingTicket(id)
);

CREATE TABLE tblSession (
    id              VARCHAR(50) PRIMARY KEY,
    receptionTime   TIME(0),
    returnTime      TIME(0),
    date            DATE,
    tblBookedCourtId VARCHAR(50) REFERENCES tblBookedCourt(id)
);

CREATE TABLE tblItem (
    id     VARCHAR(50) PRIMARY KEY,
    name   VARCHAR(50),
    price  INT,
);

CREATE TABLE tblUsedItem (
    id            VARCHAR(50) PRIMARY KEY,
    currentPrice  INT,
	quantity	  INT,
    tblSessionId  VARCHAR(50) REFERENCES tblSession(id),
    tblItemId     VARCHAR(50) REFERENCES tblItem(id)
);

CREATE TABLE tblInvoice (
    id              VARCHAR(50) PRIMARY KEY,
    paymentDate     DATE,
    paymentMethod   VARCHAR(50),
	totalAmount		INT,
    tblUserId       VARCHAR(50) REFERENCES tblUser(id),
    tblCustomerId   VARCHAR(50) REFERENCES tblCustomer(id),
    tblBookingTicketId VARCHAR(50) REFERENCES tblBookingTicket(id)
);



INSERT INTO tblUser (id, userName, password, fullName, role) VALUES
('1','a','a','A','receptionist');

INSERT INTO tblCustomer (id, name, address, phone, email, note) VALUES
('1','An','Huynh Thuc Khang','9172386245','an@gmail.com',NULL),
('2','Anh','Lang Ha','1234685647','anh@gmail.com',NULL),
('3','B','Yen Hoa','1234145613','b@gmail.com',NULL);

INSERT INTO tblBookingTicket (id, dateOfContract, depositAmount, tblCustomerId, tblUserId) VALUES
('1','2024-03-30',0,'1','1'),
('2','2024-04-01',0,'2','1'),
('3','2024-06-20',0,'1','1');

INSERT INTO tblCourt (id, type, price) VALUES
('1','single',500000),
('2','single',500000);

INSERT INTO tblBookedCourt (id, startDate, endDate, startHour, endHour, tblCourtId, tblBookingTicketId) VALUES
('1','2024-04-01','2024-06-30','20:00','22:00','1','1'),
('2','2024-05-01','2024-08-31','14:00','16:00','2','3'),
('3','2024-07-01','2024-09-30','09:00','11:00','1','2');

INSERT INTO tblSession (id, receptionTime, returnTime, date, tblBookedCourtId) VALUES
('1','20:00','22:00','2024-04-07','1'),
('2','20:00','22:00','2024-04-08','1'),
('3','20:00','22:00','2024-04-15','1'),
('4','20:00','22:00','2024-04-22','1'),
('5','20:00','22:00','2024-04-29','1'),
('6','20:00','22:00','2024-05-06','1'),
('7','20:00','22:00','2024-05-13','1'),
('8','20:00','22:00','2024-05-20','1'),
('9','20:00','22:00','2024-05-27','1'),
('10','20:00','22:00','2024-06-03','1'),
('11','20:00','22:00','2024-06-10','1'),
('12','20:00','22:00','2024-06-17','1'),
('13','20:00','22:00','2024-06-24','1'),
('14','14:00','16:00','2024-05-03','2'),
('15','14:00','16:00','2024-05-10','2'),
('16','14:00','16:00','2024-05-17','2'),
('17','14:00','16:00','2024-05-24','2'),
('18','14:00','16:00','2024-05-31','2'),
('19','14:00','16:00','2024-06-07','2'),
('20','14:00','16:00','2024-06-14','2'),
('21','14:00','16:00','2024-06-21','2'),
('22','14:00','16:00','2024-06-28','2'),
('23','14:00','16:00','2024-07-05','2'),
('24','14:00','16:00','2024-07-12','2'),
('25','14:00','16:00','2024-07-19','2'),
('26','14:00','16:00','2024-07-26','2'),
('27','14:00','16:00','2024-08-02','2'),
('28','14:00','16:00','2024-08-09','2'),
('29','14:00','16:00','2024-08-16','2'),
('30','14:00','16:00','2024-08-23','2'),
('31','14:00','16:00','2024-08-30','2'),
('32','09:00','11:00','2024-07-06','3'),
('33','09:00','11:00','2024-07-13','3'),
('34','09:00','11:00','2024-07-20','3'),
('35','09:00','11:00','2024-07-27','3'),
('36','09:00','11:00','2024-08-03','3'),
('37','09:00','11:00','2024-08-10','3'),
('38','09:00','11:00','2024-08-17','3'),
('39','09:00','11:00','2024-08-24','3'),
('40','09:00','11:00','2024-08-31','3'),
('41','09:00','11:00','2024-09-07','3'),
('42','09:00','11:00','2024-09-14','3'),
('43','09:00','11:00','2024-09-21','3'),
('44','09:00','11:00','2024-09-28','3');

INSERT INTO tblItem (id, name, price) VALUES
('1','Pepsi',10000),
('2','Revive',10000);

INSERT INTO tblUsedItem (id, quantity, currentPrice, tblSessionId, tblItemId) VALUES
('1',10,10000,'1','1'),
('2',5,10000,'1','2'),
('3',5,10000,'2','2');

