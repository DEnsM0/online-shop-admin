-- tables
create table vendor (
    vendor_id int primary key auto_increment,
    vendor_name varchar(50) unique not null,
    login_email varchar(100) unique not null,
    password varchar(100) not null,
    company_name varchar(255) not null,
    phone varchar(255) not null,
    street varchar(255) not null,
    house varchar(10) not null,
    zip int not null,
    city varchar(255) not null,
    country varchar(255) not null
);

create table category (
    category_id int primary key auto_increment,
    category_name varchar(255) not null,
    overcategory_id int,
    foreign key (overcategory_id) references category(category_id) on delete cascade
);

create table item (
    item_id int primary key auto_increment,
    item_name varchar(255) unique not null,
    price decimal(10,2) not null check (price >= 0),
    size varchar(255),
    color varchar(255),
    brand varchar(255),
    description text,
    availability int default 0,
    discount decimal(5,2) default 0 check (discount >= 0 and discount <= 100.00),
    vendor_id int not null,
    category_id int not null,
    foreign key (category_id) references category(category_id) on delete cascade,
    foreign key (vendor_id) references vendor(vendor_id) on delete cascade
);

create table client (
    client_id int primary key auto_increment,
    client_name varchar(50) unique not null,
    login_email varchar(100) unique not null,
    password varchar(100) not null,
    name varchar(255) not null,
    surname varchar(255) not null,
    phone varchar(255)
);

create table review (
    item_id int,
    review_id int,
    comment text not null,
    stars int check (stars >= 1 and stars <= 5),
    client_id int,
    primary key (item_id, review_id),
    foreign key (item_id) references item(item_id) on delete cascade,
    foreign key (client_id) references client(client_id) on delete cascade
);

create table supplier (
    supplier_id int primary key auto_increment,
    company_name varchar(255) unique not null,
    email varchar(100) not null,
    phone varchar(255) not null,
    street varchar(255) not null,
    house varchar(10) not null,
    zip varchar(10) not null,
    city varchar(255) not null,
    country varchar(255) not null
);

create table shopping_cart (
    shopping_cart_id int primary key auto_increment,
    total_price decimal(10,2) default 0 check (total_price >=0),
    total_number int default 0 check (total_number >= 0)
);

create table contains (
    shopping_cart_id int,
    item_id int,
    primary key (shopping_cart_id, item_id),
    foreign key (shopping_cart_id) references shopping_cart(shopping_cart_id) on delete cascade,
    foreign key (item_id) references item(item_id) on delete cascade
);

create table order_ (
    client_id int,
    supplier_id int,
    shopping_cart_id int,
    order_date date not null,
    delivery_date date not null,
    delivery_street varchar(255) not null,
    delivery_house varchar(10) not null,
    delivery_zip varchar(10) not null,
    delivery_city varchar(255) not null,
    delivery_country varchar(255) not null,
    billing_street varchar(255) not null,
    billing_house varchar(10) not null,
    billing_zip varchar(10) not null,
    billing_city varchar(255) not null,
    billing_country varchar(255) not null,
    cardholder varchar(100) not null,
    card_number varchar(16) not null,
    expiry_date date not null,
    check_digit varchar(3) not null,
    primary key (client_id, supplier_id, shopping_cart_id),
    foreign key (client_id) references client(client_id) on delete cascade,
    foreign key (supplier_id) references supplier(supplier_id) on delete cascade,
    foreign key (shopping_cart_id) references shopping_cart(shopping_cart_id) on delete cascade
);

-- triggers
delimiter //

create trigger contains_trigger
after insert on contains
for each row
begin
    update shopping_cart
    set total_number = total_number + 1,
        total_price = total_price + (
            select price from item where item_id = NEW.item_id
        )
    where shopping_cart_id = NEW.shopping_cart_id;
end;
//

delimiter //

create trigger review_trigger
before insert on review
for each row
set NEW.review_id = unix_timestamp(now())
//

-- procedures
delimiter //
drop procedure if exists getarticlesinorder
//

create procedure getarticlesinorder(
    in p_benutzerid int,
    in p_lieferantid int,
    in p_warenkorbid int
)
begin
    select
        i.item_name,
        i.price
    from
        order_ o
    join
        shopping_cart s on o.shopping_cart_id = s.shopping_cart_id
    join
        contains c on s.shopping_cart_id = c.shopping_cart_id
    join
        item i on c.item_id = i.item_id
    where
        o.client_id = p_benutzerid
        and o.supplier_id = p_lieferantid
        and o.shopping_cart_id = p_warenkorbid;
end;

//
