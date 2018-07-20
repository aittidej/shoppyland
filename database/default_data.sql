INSERT INTO brand (brand_id, title) VALUES (1, 'Coach');
INSERT INTO brand (brand_id, title) VALUES (2, 'Michael Kors');
INSERT INTO brand (brand_id, title) VALUES (3, 'Kate Spade');
INSERT INTO brand (brand_id, title) VALUES (4, 'Kipling');
ALTER SEQUENCE brand_brand_id_seq RESTART WITH 4;

INSERT INTO role (role_id, title) VALUES (1, 'Admin');
INSERT INTO role (role_id, title) VALUES (2, 'Client');
INSERT INTO role (role_id, title) VALUES (3, 'Vendor');
ALTER SEQUENCE role_role_id_seq RESTART WITH 3;

INSERT INTO "user" (user_id, name, status, role_id, username, password, email, phone, last_login, address, payment_method, is_wholesale, exchange_rate) VALUES (1, 'AJ Tan', 1, 1, 'aj', NULL, 'ettidej@gmail.com', NULL, NULL, NULL, NULL, 0, 0.00);
INSERT INTO "user" (user_id, name, status, role_id, username, password, email, phone, last_login, address, payment_method, is_wholesale, exchange_rate) VALUES (2, 'Aim', 1, 2, 'aim', NULL, NULL, NULL, NULL, NULL, 'USD', 1, 38.00);
INSERT INTO "user" (user_id, name, status, role_id, username, password, email, phone, last_login, address, payment_method, is_wholesale, exchange_rate) VALUES (3, 'Bird', 1, 2, 'bird', NULL, NULL, NULL, NULL, NULL, 'Baht', 1, 34.00);
INSERT INTO "user" (user_id, name, status, role_id, username, password, email, phone, last_login, address, payment_method, is_wholesale, exchange_rate) VALUES (4, 'Nok', 1, 2, 'nok', NULL, NULL, NULL, NULL, NULL, 'Baht', 1, 34.00);
INSERT INTO "user" (user_id, name, status, role_id, username, password, email, phone, last_login, address, payment_method, is_wholesale, exchange_rate) VALUES (5, 'Joy', 1, 2, 'joy', NULL, NULL, NULL, NULL, NULL, 'Baht', 0, 35.00);
ALTER SEQUENCE user_user_id_seq RESTART WITH 5;