CREATE DATABASE GalleryDB;

USE GalleryDB;

CREATE TABLE Artist (
    ArtistID INT PRIMARY KEY,
    ArtistName VARCHAR(255) UNIQUE,
    Birthplace VARCHAR(255),
    Style VARCHAR(255),
    BirthYear INT
);

CREATE TABLE Artwork (
    ArtworkID INT PRIMARY KEY,
    ArtworkYear INT,
    Title VARCHAR(255) UNIQUE,
    Type VARCHAR(255),
    Price DECIMAL(10, 2),
    ArtistID INT,
    FOREIGN KEY (ArtistID) REFERENCES Artist(ArtistID)
);

CREATE TABLE ArtGroup (
    GroupID INT PRIMARY KEY,
    GroupName VARCHAR(255)
);

CREATE TABLE Customer (
    CustomerID INT PRIMARY KEY,
    CustomerName VARCHAR(255) UNIQUE,
    Username VARCHAR(255) UNIQUE,
    Password VARCHAR(255),
    Address VARCHAR(255),
    TotalSpending DECIMAL(10, 2)
);

CREATE TABLE Belongs (
    ArtworkID INT,
    GroupID INT,
    PRIMARY KEY (ArtworkID, GroupID),
    FOREIGN KEY (ArtworkID) REFERENCES Artwork(ArtworkID),
    FOREIGN KEY (GroupID) REFERENCES ArtGroup(GroupID)
);

CREATE TABLE Buys (
    ArtworkID INT,
    CustomerID INT,
    PRIMARY KEY (ArtworkID, CustomerID),
    FOREIGN KEY (ArtworkID) REFERENCES Artwork(ArtworkID),
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID)
);

CREATE TABLE PrefersArtist (
    CustomerID INT,
    ArtistID INT,
    Priority INT,
    PRIMARY KEY (CustomerID, ArtistID),
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID),
    FOREIGN KEY (ArtistID) REFERENCES Artist(ArtistID)
);

CREATE TABLE PrefersGroup (
    CustomerID INT,
    GroupID INT,
    Priority INT,
    PRIMARY KEY (CustomerID, GroupID),
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID),
    FOREIGN KEY (GroupID) REFERENCES ArtGroup(GroupID)
);

INSERT INTO Artist (ArtistID, ArtistName, Birthplace, Style, BirthYear) VALUES
(2001, 'Vincent van Gogh', 'Zundert, Netherlands', 'Impressionism', 1853),
(2002, 'Claude Monet', 'Paris, France', 'Impressionism', 1840),
(2003, 'Maurits Cornelis Escher', 'Leeuwarden, Netherlands', 'Modern Art', 1898),
(2004, 'Ansel Adams', 'San Francisco, USA', 'Landscape Photography', 1902),
(2005, 'Pablo Picasso', 'Malaga, Spain', 'Cubism', 1881),
(2006, 'Edvard Munch', 'Adalsbruk, Norway', 'Expressionism', 1863),
(2007, 'Auguste Rodin', 'Paris, France', 'Impressionism', 1840),
(2008, 'Salvador Dali', 'Figueres, Spain', 'Surrealism', 1904),
(2009, 'Danny O''Connor', 'Liverpool, UK', 'Modern Art', 1981);

INSERT INTO Artwork (ArtworkID, ArtworkYear, Title, Type, Price, ArtistID) VALUES
(3001, 1889, 'The Starry Night', 'painting', 400000, 2001),
(3002, 1888, 'The Red Vineyard', 'painting', 100000, 2001),
(3003, 1890, 'Vase with Pink Roses', 'painting', 50000, 2001),
(3004, 1887, 'Two cut Sunflowers', 'painting', 140000, 2001),
(3005, 1867, 'Garden at Sainte-Adresse', 'painting', 300000, 2002),
(3006, 1873, 'The Poppy Field near Argenteuil', 'painting', 200000, 2002),
(3007, 1869, 'La Grenouillere', 'painting', 100000, 2002),
(3008, 1935, 'Self-Portrait in Spherical Mirror', 'lithograph', 30000, 2003),
(3009, 1953, 'Relativity', 'lithograph', 20000, 2003),
(3010, 1956, 'Bond of Union', 'lithograph', 35000, 2003),
(3011, 1948, 'Sand Dune, Sunrise', 'photograph', 7000, 2004),
(3012, 1968, 'Eagle Peak and Middle Brother', 'photograph', 8400, 2004),
(3013, 1958, 'Self-Portrait, Monument Valley', 'photograph', 5000, 2004),
(3014, 1901, 'Self-Portrait', 'painting', 75000, 2005),
(3015, 1955, 'Don Quixote', 'painting', 130000, 2005),
(3016, 1904, 'The Old Guitarist', 'painting', 530000, 2005),
(3017, 1937, 'Guernica', 'painting', 401000, 2005),
(3018, 1954, 'Sylvette', 'sculpture', 375000, 2005),
(3019, 1911, 'Ma Jolie', 'painting', 230000, 2005),
(3020, 1893, 'The Scream', 'painting', 650000, 2006),
(3021, 1902, 'Vampire II', 'painting', 120000, 2006),
(3022, 1874, 'Suzon', 'sculpture', 150000, 2007),
(3023, 1904, 'The Thinker', 'sculpture', 500000, 2007),
(3024, 1879, 'The Call to Arms', 'sculpture', 220000, 2007),
(3025, 1952, 'Galatea of the Spheres', 'painting', 90000, 2008),
(3026, 1931, 'The Persistence of Memory', 'painting', 380000, 2008),
(3027, 2018, 'Herculean Conquest', 'painting', 8000, 2009),
(3028, 2019, 'Graceful Power', 'painting', 5000, 2009);


INSERT INTO ArtGroup (GroupID, GroupName) VALUES
(4001, 'Still Life'),
(4002, 'Self Portraits'),
(4003, 'Works of the 19th Century'),
(4004, 'Works of the 20th Century'),
(4005, 'Landscapes');

INSERT INTO Belongs (ArtworkID, GroupID) VALUES
(3003, 4001),
(3004, 4001),
(3005, 4005),
(3002, 4005),
(3006, 4005),
(3007, 4005),
(3008, 4002),
(3013, 4002),
(3014, 4002),
(3001, 4003),
(3002, 4003),
(3003, 4003),
(3004, 4003),
(3005, 4003),
(3006, 4003),
(3007, 4003),
(3022, 4003),
(3024, 4003),
(3008, 4004),
(3009, 4004),
(3010, 4004),
(3011, 4004),
(3012, 4004),
(3013, 4004),
(3014, 4004),
(3015, 4004),
(3016, 4004),
(3017, 4004),
(3018, 4004),
(3019, 4004),
(3020, 4003),
(3021, 4004),
(3023, 4004),
(3025, 4004),
(3026, 4004);

INSERT INTO Customer (CustomerID, CustomerName, Username, Password, Address) VALUES
(1001, 'John Brown', 'cust1', 'cust1pass', 'Shanghai'),
(1002, 'Alice Smith', 'cust2', 'cust2pass', 'Cape Town'),
(1003, 'Bob Johnson', 'cust3', 'cust3pass', 'Mumbai'),
(1004, 'Charlie Davis', 'cust4', 'cust4pass', 'Glasgow'),
(1005, 'Diana Lee', 'cust5', 'cust5pass', 'Amsterdam'),
(1006, 'Fiona Taylor', 'cust6', 'cust6pass', 'Sacramento'),
(1007, 'George Anderson', 'cust7', 'cust7pass', 'Melbourne'),
(1008, 'Hannah Wilson', 'cust8', 'cust8pass', 'Moscow'),
(1009, 'Ethan Miller', 'cust9', 'cust9pass', 'Kolkata'),
(1010, 'Jane Frown', 'cust10', 'cust10pass', 'Toronto'),
(1011, 'Julia Martin', 'cust11', 'cust11pass', 'Paris'),
(1012, 'Ian Clark', 'cust12', 'cust12pass', 'Kolkata');

INSERT INTO Buys (ArtworkID, CustomerID) VALUES 
(3009, 1001),
(3025, 1001),
(3016, 1003),
(3020, 1003);

/*
INSERT INTO PrefersArtist (CustomerID, ArtistID) VALUES 
(1009, 3001),
(1025, 3001),
(1016, 3003),
(1020, 3003);

INSERT INTO PrefersGroup (CustomerID, GroupID) VALUES 
(1009, 4001),
(1025, 4001),
(1016, 4003),
(1020, 4003);
*/
