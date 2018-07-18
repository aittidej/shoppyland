INSERT INTO brand (brand_id, title) VALUES (1, 'Coach');
ALTER SEQUENCE brand_brand_id_seq RESTART WITH 1;

INSERT INTO role (role_id, title) VALUES (1, 'Admin');
INSERT INTO role (role_id, title) VALUES (2, 'Client');
INSERT INTO role (role_id, title) VALUES (3, 'Vendor');
ALTER SEQUENCE role_role_id_seq RESTART WITH 3;