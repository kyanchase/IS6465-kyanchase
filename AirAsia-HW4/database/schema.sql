PRAGMA foreign_keys = ON;

CREATE TABLE USER (
    userId INTEGER PRIMARY KEY,
    userName TEXT NOT NULL UNIQUE CHECK (length(trim(userName)) BETWEEN 1 AND 100),
    password TEXT NOT NULL CHECK (length(password) BETWEEN 1 AND 100),
    firstName TEXT NOT NULL CHECK (length(trim(firstName)) BETWEEN 1 AND 100),
    lastName TEXT NOT NULL CHECK (length(trim(lastName)) BETWEEN 1 AND 100),
    role TEXT NOT NULL CHECK (role IN ('Admin', 'Customer'))
);

CREATE TABLE ACCOUNT (
    accountId INTEGER PRIMARY KEY,
    userId INTEGER NOT NULL,
    accountType TEXT NOT NULL CHECK (length(trim(accountType)) BETWEEN 1 AND 100),
    points INTEGER NOT NULL CHECK (typeof(points) = 'integer' AND points >= 0),
    FOREIGN KEY (userId) REFERENCES USER (userId)
);

CREATE TABLE GIFTCARD (
    cardId INTEGER PRIMARY KEY,
    cardName TEXT NOT NULL CHECK (length(trim(cardName)) BETWEEN 1 AND 100),
    cardType TEXT NOT NULL CHECK (length(trim(cardType)) BETWEEN 1 AND 100),
    cardValue REAL NOT NULL CHECK (typeof(cardValue) IN ('integer', 'real') AND cardValue > 0),
    points INTEGER NOT NULL CHECK (typeof(points) = 'integer' AND points > 0)
);

CREATE TABLE REDEMPTION (
    redeemId INTEGER PRIMARY KEY,
    date TEXT NOT NULL CHECK (length(trim(date)) BETWEEN 1 AND 100),
    accountId INTEGER NOT NULL REFERENCES ACCOUNT (accountId),
    cardId INTEGER NOT NULL REFERENCES GIFTCARD (cardId),
    pointsRedeemed INTEGER NOT NULL CHECK (typeof(pointsRedeemed) = 'integer' AND pointsRedeemed > 0)
);
