BEGIN;

INSERT INTO ACCOUNT (userId, accountType, points) VALUES
    (1, 'Rewards', 12000),
    (2, 'Rewards', 8500),
    (3, 'Rewards', 22500),
    (4, 'Rewards', 5000),
    (5, 'Rewards', 16000);

INSERT INTO GIFTCARD (cardName, cardType, cardValue, points) VALUES
    ('Airport Cafe $10', 'Dining', 10, 1000),
    ('Airport Cafe $25', 'Dining', 25, 2500),
    ('City Bistro $20', 'Dining', 20, 2000),
    ('City Bistro $50', 'Dining', 50, 5000),
    ('Noodle House $15', 'Dining', 15, 1500),
    ('Noodle House $30', 'Dining', 30, 3000),
    ('Bookshop $10', 'Retail', 10, 1000),
    ('Bookshop $25', 'Retail', 25, 2500),
    ('Travel Gear $25', 'Retail', 25, 2500),
    ('Travel Gear $50', 'Retail', 50, 5000),
    ('Market Gift Card $15', 'Retail', 15, 1500),
    ('Market Gift Card $40', 'Retail', 40, 4000),
    ('Airport Shuttle $15', 'Travel', 15, 1500),
    ('Airport Shuttle $30', 'Travel', 30, 3000),
    ('Hotel Voucher $50', 'Travel', 50, 5000),
    ('Hotel Voucher $100', 'Travel', 100, 10000),
    ('City Tour $25', 'Travel', 25, 2500),
    ('City Tour $50', 'Travel', 50, 5000),
    ('Luggage Storage $10', 'Travel', 10, 1000),
    ('Luggage Storage $20', 'Travel', 20, 2000);

COMMIT;