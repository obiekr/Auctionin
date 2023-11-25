-- drop TABLE product;
-- drop TABLE user;
create TABLE user(
    user_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    username varchar(255) NOT NULL UNIQUE,
    email varchar(255) NOT NULL UNIQUE,
    password varchar(255) NOT NULL,
    balance FLOAT8 NOT NULL,
    alamat VARCHAR(500) NOT NULL
);
create TABLE product(
    product_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    product_name varchar(255) NOT NULL,
    product_price FLOAT8 NOT NULL,
    product_image varchar(255) NOT NULL,
    product_desc TEXT(60000) NOT NULL,
    product_register datetime NOT NULL,
    product_expired datetime NOT NULL,
    sentToAddress BOOLEAN NOT NULL,
    isOwnerPaid BOOLEAN NOT NULL,
    owner_id int NOT NULL,
    highest_bidder_id int,
    FOREIGN KEY (owner_id) REFERENCES user(user_id),
    FOREIGN KEY (highest_bidder_id) REFERENCES user(user_id)
);
-- select * from product;
-- select * from user;