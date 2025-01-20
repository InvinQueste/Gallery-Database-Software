CREATE DATABASE GalleryDB;

USE GalleryDB;

CREATE TABLE Artist (
    ArtistID INT PRIMARY KEY,
    ArtistName VARCHAR(255),
    Birthplace VARCHAR(255),
    Style VARCHAR(255),
    Age INT
);

CREATE TABLE Artwork (
    ArtworkID INT PRIMARY KEY,
    ArtworkYear INT,
    Title VARCHAR(255),
    Type VARCHAR(255),
    Price DECIMAL(10, 2),
    ImagePath VARCHAR(255),
    ArtistID INT,
    FOREIGN KEY (ArtistID) REFERENCES Artist(ArtistID)
);

CREATE TABLE ArtGroup (
    GroupID INT PRIMARY KEY,
    GroupName VARCHAR(255)
);

CREATE TABLE Customer (
    CustomerID INT PRIMARY KEY,
    CustomerName VARCHAR(255),
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
