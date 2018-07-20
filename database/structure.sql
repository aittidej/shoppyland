CREATE TABLE brand
(
  brand_id serial NOT NULL,
  title character varying(255),
  status smallint DEFAULT 1,
  CONSTRAINT brand_pkey PRIMARY KEY (brand_id)
);
CREATE INDEX ON brand(title);
CREATE INDEX ON brand(status);


CREATE TABLE role
(
  role_id serial NOT NULL,
  title character varying(255),
  json jsonb,
  CONSTRAINT role_pkey PRIMARY KEY (role_id)
);
CREATE INDEX ON role(title);


CREATE TABLE "user"
(
  user_id serial NOT NULL,
  role_id integer,
  username character varying(255),
  password character varying(512),
  "name" character varying(255),
  email character varying(255),
  phone character varying(100),
  address text,
  last_login timestamp without time zone,
  creation_datetime timestamp without time zone DEFAULT now(),
  payment_method character varying(100),
  is_wholesale smallint DEFAULT 1,
  exchange_rate numeric(4,2) DEFAULT 34.00,
  status smallint DEFAULT 1,
  CONSTRAINT user_pkey PRIMARY KEY (user_id),
  CONSTRAINT user_role_id_fkey FOREIGN KEY (role_id)
      REFERENCES role (role_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);
CREATE INDEX ON "user"(name);
CREATE INDEX ON "user"(status);
CREATE INDEX ON "user"(role_id);
CREATE INDEX ON "user"(username);
CREATE INDEX ON "user"(email);
CREATE INDEX ON "user"(phone);
CREATE INDEX ON "user"(last_login);
CREATE INDEX ON "user"(creation_datetime);
CREATE INDEX ON "user"(payment_method);
CREATE INDEX ON "user"(is_wholesale);
CREATE INDEX ON "user"(exchange_rate);


CREATE TABLE product
(
  product_id serial NOT NULL,
  brand_id integer,
  upc character varying(100),
  model character varying(100),
  title character varying(255),
  category character varying(100),
  base_price numeric(6,2) DEFAULT 0,
  weight numeric(4,2) DEFAULT 0,
  description text,
  color character varying(100),
  size character varying(100),
  dimension character varying(100),
  image_path jsonb,
  json_data jsonb,
  status smallint DEFAULT 1,
  CONSTRAINT product_pkey PRIMARY KEY (product_id),
  CONSTRAINT product_brand_id_fkey FOREIGN KEY (brand_id)
      REFERENCES brand (brand_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);
CREATE INDEX ON product(upc);
CREATE INDEX ON product(model);
CREATE INDEX ON product(brand_id);
CREATE INDEX ON product(category);
CREATE INDEX ON product(base_price);
CREATE INDEX ON product(title);
CREATE INDEX ON product(weight);
CREATE INDEX ON product(status);
CREATE INDEX ON product(color);
CREATE INDEX ON product(size);
CREATE INDEX ON product(dimension);


CREATE TABLE open_order
(
  open_order_id serial NOT NULL,
  user_id integer,
  lot_number integer,
  number_of_box integer DEFAULT 0,
  total_weight numeric(4,2) DEFAULT 0,
  shipping_cost numeric(6,2) DEFAULT 0,
  creation_datetime timestamp without time zone DEFAULT now(),
  status smallint DEFAULT 1,
  CONSTRAINT open_order_pkey PRIMARY KEY (open_order_id),
  CONSTRAINT open_order_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES "user" (user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);
CREATE INDEX ON open_order(lot_number);
CREATE INDEX ON open_order(user_id);
CREATE INDEX ON open_order(creation_datetime);
CREATE INDEX ON open_order(number_of_box);
CREATE INDEX ON open_order(total_weight);
CREATE INDEX ON open_order(shipping_cost);
CREATE INDEX ON open_order(status);


CREATE TABLE open_order_rel
(
  open_order_rel_id serial NOT NULL,
  open_order_id integer,
  product_id integer,
  qty integer DEFAULT 1,
  unit_price numeric(6,2) DEFAULT 0.00,
  subtotal numeric(6,2) DEFAULT 0.00,
  CONSTRAINT open_order_rel_pkey PRIMARY KEY (open_order_rel_id),
  CONSTRAINT open_order_rel_open_order_id_fkey FOREIGN KEY (open_order_id)
      REFERENCES open_order (open_order_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT open_order_rel_product_id_fkey FOREIGN KEY (product_id)
      REFERENCES product (product_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);
CREATE INDEX ON open_order_rel(open_order_id);
CREATE INDEX ON open_order_rel(product_id);
CREATE INDEX ON open_order_rel(unit_price);
CREATE INDEX ON open_order_rel(subtotal);
CREATE INDEX ON open_order_rel(qty);